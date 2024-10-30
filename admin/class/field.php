<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			field.php
//		Description:
//			This file is responsible for activating the plugin.
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

/****************************************Commence Script*******************************************/

/*------------------------------------------------------------------------------------------------
	For good measure
------------------------------------------------------------------------------------------------*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*------------------------------------------------------------------------------------------------
	Internal Functions
------------------------------------------------------------------------------------------------*/

class ML_list_field extends TERNPLUGIN_admin {

	public $page = 'ml-list-field';
	public $nonce = 'WP_ml_nonce';
	public $root = ML_ROOT;
	public $script_slug = 'ml';
	public $admin_dir = ML_ADMIN_DIR;
	static $include = [
		'list-field.php',
	];
	public $list = null;

	public function __construct($run=true) {
		parent::__construct($run);
		add_action('wp_ajax_WP_ml_field_order',[$this,'save']);
		return $this;
	}
	public function save($id=null) {
		global $wpdb,$ml_options,$getWP;

		if(parent::save($id)) {

			switch($this->get_action()) {

				case 'WP_ml_field_order' :
					$this->reorder_fields();
					die();
					exit;

				case 'edit' :
					$this->edit_field();
					wp_safe_redirect($this->list_url());
					exit;

				case 'add' :
					$this->add_field();
					wp_safe_redirect($this->list_url());
					exit;

				case 'delete' :
					$this->delete_field();
					wp_safe_redirect($this->list_url());
					exit;

				default :
					break;
			}

		}

	}
	private function list_url() {
		return admin_url().'/admin.php?page=ml-list-field&list='.((isset($_REQUEST['list']) and !empty($_REQUEST['list'])) ? $_REQUEST['list'] : '');
	}
	private function add_field() {
		global $ml_options,$getWP;

		//no list is selected
		if(!isset($_POST['list']) or empty($_POST['list'])) {
			$getWP->addError(__('Please select a valid list.','members-list-pro'));
			return false;
		}

		//required fields are missing
		if(
			!isset($_POST['name']) or
			empty($_POST['name']) or
			!isset($_POST['field']) or
			empty($_POST['field'])
		) {
			$getWP->addError(__('Please fill out all the requred fields.','members-list-pro'));
			return false;
		}

		//create field ID
		$id = $this->generate_id(7);

		//add the field to the list
		$a = [
			'name'		=>	$_POST['name'],
			'field'		=>	$_POST['field'],
		];
		if($_POST['field'] == 'description' and isset($_POST['truncate'])) {
			$a['truncate'] = $_POST['truncate'];
		}

		//save field
		$ml_options['lists'][$_POST['list']]['fields'][$id] = $a;
		$ml_options = $getWP->getOption('ml_options',$ml_options,true);

		return true;
	}
	private function delete_field() {
		global $ml_options,$getWP;

		//no list is selected
		if(!isset($_REQUEST['list']) or empty($_REQUEST['list'])) {
			$getWP->addError(__('Please select a valid list.','members-list-pro'));
			return false;
		}

		//required fields are missing
		if(
			!isset($_REQUEST['items']) or
			empty($_REQUEST['items'])
		) {
			$getWP->addError(__('Please fill out all the requred fields.','members-list-pro'));
			return false;
		}

		//delete the field
		$a = array();
		foreach((array)$ml_options['lists'][$_REQUEST['list']]['fields'] as $k => $v) {
			if(!in_array($k,$_REQUEST['items'])) {
				$a[] = $v;
			}
		}

		//save the list
		$ml_options['lists'][$_REQUEST['list']]['fields'] = $a;
		$ml_options = $getWP->getOption('ml_options',$ml_options,true);

		$getWP->addAlert(__('You have successfully deleted a field.','members-list-pro'));
	}
	private function edit_field() {
		global $ml_options,$getWP;

		//no list is selected
		if(!isset($_POST['list']) or empty($_POST['list'])) {
			$getWP->addError(__('Please select a valid list.','members-list-pro'));
			return false;
		}

		//required fields are missing
		if(
			!isset($_POST['field_id']) or
			!isset($_POST['name']) or
			empty($_POST['name']) or
			!isset($_POST['field'])
			or empty($_POST['field'])
		) {
			$getWP->addError(__('Please fill out all the requred fields.','members-list-pro'));
			return false;
		}

		//edit field
		$a = [
			'name'		=>	$_POST['name'],
			'field'		=>	$_POST['field'],
			'truncate'	=>	isset($_POST['truncate']) ? $_POST['truncate'] : 0,
		];

		//save field
		$ml_options['lists'][$_POST['list']]['fields'][(int)$_POST['field_id']] = $a;
		$ml_options = $getWP->getOption('ml_options',$ml_options,true);

		$getWP->addAlert(__('You have successfully edited a field.','members-list-pro'));
		return true;
	}
	private function reorder_fields() {
		global $ml_options,$getWP;

		//no list is selected
		if(!isset($_POST['list']) or empty($_POST['list'])) {
			echo json_encode(['error'=>__('Please select a valid list.','members-list-pro')]);
			return false;
		}

		//required fields are missing
		if(
			!isset($_POST['name']) or
			empty($_POST['name']) or
			!isset($_POST['field']) or
			empty($_POST['field'])
		) {
			echo json_encode(['error'=>__('Please fill out all the requred fields.','members-list-pro')]);
			return false;
		}

		//re-order
		$a = [];
		foreach((array)$_POST['name'] as $k => $v) {
			if(isset($_POST['field'][$k])) {
				$a[] = array(
					'name'	=>	$v,
					'field'	=>	isset($_POST['field'][$k]) ? $_POST['field'][$k] : '',
				);
			}
		}

		//save the list
		$ml_options['lists'][$_POST['list']]['fields'] = $a;
		$ml_options = $getWP->getOption('ml_options',$ml_options,true);

		echo json_encode(['success'=>__('You have successfully reoreder your fields.','members-list-pro')]);
		return true;

	}
	public function page() {
		global $ml_options;

		wp_enqueue_style('switchery');
		wp_enqueue_style('fontawesome');
		wp_enqueue_style('thickbox');
		wp_enqueue_style('ml-admin');
		wp_enqueue_style('ml-style');

		wp_enqueue_script('ml-list-field');

		if(
			isset($_GET['list']) and
			!empty($_GET['list']) and
			isset($ml_options['lists'][$_GET['list']])
		) {
			$this->list_id = $_GET['list'];
			$this->list = $ml_options['lists'][$_GET['list']];
		}

		include(ML_ADMIN_DIR.'/view/list-field.php');
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
new ML_list_field();

/****************************************Terminate Script******************************************/
?>
