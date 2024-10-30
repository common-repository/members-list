<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			user.php
//		Description:
//			This file is responsible for user-related actions.
//		Copyright:
//			Copyright (c) 2021 Ternstyle LLC.
//		License:
//			This software is licensed under the terms of the End User License Agreement (EULA)
//			provided with this software. In the event the EULA is not present with this software
//			or you have not read it, please visit:
//			http://www.ternstyle.us/members-list-plugin-for-wordpress/license.html
//
////////////////////////////////////////////////////////////////////////////////////////////////////

use MLP\ternplugin\TERNPLUGIN_admin as TERNPLUGIN_admin;
use MLP\ternplugin\tern_members as tern_members;

/****************************************Commence Script*******************************************/

/*------------------------------------------------------------------------------------------------
	For good measure
------------------------------------------------------------------------------------------------*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*------------------------------------------------------------------------------------------------
	Internal Functions
------------------------------------------------------------------------------------------------*/

class ML_user extends TERNPLUGIN_admin {

	public $page = 'ml-list-user';
	public $nonce = 'WP_ml_nonce';
	public $root = ML_ROOT;
	public $script_slug = 'ml';
	public $admin_dir = ML_ADMIN_DIR;
	static $include = [
		'user.php',
	];
	public $list = null;

	public function __construct($run=true) {
		parent::__construct($run);
		return $this;
	}
	public function save($id=null) {
		global $wpdb,$ml_options,$getWP;

		if(parent::save($id)) {

			//if it's empty list
			$list = (isset($_REQUEST['list']) and isset($ml_options['lists'][$_REQUEST['list']])) ? $ml_options['lists'][$_REQUEST['list']] : false;
			if(!$list) {
				return $getWP->addError(__('Please select a list.','members-list-pro'));
			}

			switch($this->get_action()) {

				case 'remove' :
					if(!isset($_REQUEST['user']) or empty($_REQUEST['user'])) {
						return $getWP->addError(__('Please select a user or users to remove.','members-list-pro'));
					}
					foreach((array)$_REQUEST['user'] as $v) {
						$wpdb->query('delete from '.$wpdb->usermeta.' where user_id="'.$v.'" and meta_key="_WP_ML" and meta_value="'.$list['name'].'"');
					}
					$getWP->addAlert(__('You have successfully removed users.','members-list-pro'));
					break;

				case 'add' :
					if(!isset($_REQUEST['user']) or empty($_REQUEST['user'])) {
						return $getWP->addError(__('Please select a user or users to remove.','members-list-pro'));
					}
					foreach((array)$_REQUEST['user'] as $v) {
						add_user_meta($v,'_WP_ML',$list['name'],false);
					}
					$getWP->addAlert(__('You have successfully added a user or users.','members-list-pro'));
					break;

				default :
					break;
			}

		}

	}
	public function page() {
		global $ml_options;

		wp_enqueue_style('fontawesome');
		wp_enqueue_style('ml-admin');
		wp_enqueue_script('ml-user');

		if(
			isset($_GET['list']) and
			!empty($_GET['list']) and
			isset($ml_options['lists'][$_GET['list']])
		) {
			$this->list_id = $_GET['list'];
			$this->list = $ml_options['lists'][$_GET['list']];

			$not = false;
			if(isset($_GET['notin']) and $_GET['notin']) {
				$not = true;
			}

			$members = new tern_members($_GET['list'],$ml_options,$not);
			$members->query(true);
			$users = $members->get();

		}

		include(ML_ADMIN_DIR.'/view/user.php');
	}
	public function get_lists() {
		global $ml_options;
		$lists = [];
		foreach((array)$ml_options['lists'] as $k => $v) {
			$lists[$v['name']] = $k;
		}
		return $lists;
	}

}
new ML_user();

/****************************************Terminate Script******************************************/
?>
