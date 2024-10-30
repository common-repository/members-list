<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			activate.php
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

use MLP\ternstyle\tern_curl as tern_curl;
use MLP\ternstyle\tern_validation as tern_validation;
use MLP\ternplugin\TERNPLUGIN_admin as TERNPLUGIN_admin;

/****************************************Commence Script*******************************************/

/*------------------------------------------------------------------------------------------------
	For good measure
------------------------------------------------------------------------------------------------*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*------------------------------------------------------------------------------------------------
	Internal Functions
------------------------------------------------------------------------------------------------*/

class ML_list extends TERNPLUGIN_admin {

	public $page = 'ml-create-list';
	public $nonce = 'WP_ml_nonce';
	public $root = ML_ROOT;
	public $script_slug = 'ml';
	public $admin_dir = ML_ADMIN_DIR;
	static $include = [
		'list.php',
	];

	private $fields = [
		'name'			=>	[
			'type'		=>	'text',
			'label'		=>	'Name',
			'required'	=>	true,
			'sanitize'	=>	true
		],
		'all'			=>	[
			'type'		=>	'boolean',
			'label'		=>	'Add all users to list',
			'required'	=>	false,
		],
		'role'			=>	[
			'type'		=>	'array',
			'label'		=>	'Add all users of the following role',
			'required'	=>	false,
		],
		'add_new'			=>	[
			'type'		=>	'boolean',
			'label'		=>	'Add new users to this list when they register',
			'required'	=>	false,
		],
		'limit'			=>	[
			'type'		=>	'integer',
			'label'		=>	'How many users to display per page',
			'required'	=>	false,
		],
		'view'			=>	[
			'type'		=>	'text',
			'label'		=>	'List view',
			'required'	=>	false,
		],
		'search'			=>	[
			'type'		=>	'boolean',
			'label'		=>	'Allow users to search this list',
			'required'	=>	false,
		],
		'search_field'		=>	[
			'type'		=>	'array',
			'label'		=>	'Select fields to search by',
			'required'	=>	false,
			'keys'		=>	'search_field_title'
		],
		'radius'			=>	[
			'type'		=>	'boolean',
			'label'		=>	'Allow users to search by radius',
			'required'	=>	false,
		],
		'map'			=>	[
			'type'		=>	'boolean',
			'label'		=>	'Show map',
			'required'	=>	false,
		],
		'alpha'			=>	[
			'type'		=>	'boolean',
			'label'		=>	'Allow users to search by alpha',
			'required'	=>	false,
		],
		'sort'			=>	[
			'type'		=>	'boolean',
			'label'		=>	'Allow users to sort',
			'required'	=>	false,
		],
		'sort_by'			=>	[
			'type'		=>	'text',
			'label'		=>	'Sort list initially by',
			'required'	=>	false,
		],
		'sort_order'		=>	[
			'type'		=>	'text',
			'label'		=>	'Sort list initially in order',
			'required'	=>	false,
		],
		'sort_field'		=>	[
			'type'		=>	'text',
			'label'		=>	'Select fields to sort by',
			'required'	=>	false,
			'keys'		=>	'sort_field_title'
		],
		'img'			=>	[
			'type'		=>	'boolean',
			'label'		=>	'Display profile picture (gravatar)',
			'required'	=>	false,
		],
		'img_size'		=>	[
			'type'		=>	'integer',
			'label'		=>	'Profile picture width',
			'required'	=>	false,
		],
		'link'			=>	[
			'type'		=>	'boolean',
			'label'		=>	'Link to author profile pages',
			'required'	=>	false,
		],
		'labels'			=>	[
			'type'		=>	'boolean',
			'label'		=>	'Display field labels',
			'required'	=>	false,
		],
	];

	public function __construct($run=true) {
		parent::__construct($run);
	}
	public function save($id=null) {
		global $wpdb,$ml_options,$getWP;

		if(parent::save($id)) {

			if(isset($_REQUEST['action']) or isset($_REQUEST['action2'])) {
				$action = empty($_REQUEST['action']) ? $_REQUEST['action2'] : $_REQUEST['action'];
			}

			switch($action) {

				case 'WP_ml_list_edit' :
					$validate = new tern_validation($this->fields,'create');
					if($validate->validate($_POST)) {

						//the list can't be found for some reason
						if(!isset($ml_options['lists'][$_POST['list']])) {
							$getWP->addError(__('The list you are attempting to edit can not be found.','members-list-pro'));
							return;
						}

						//if the list name has changed, update it for all users
						if($ml_options['lists'][$_POST['list']]['name'] != $_POST['name']) {
							(new ML_user_common())->update_list_name_for_all_users($ml_options['lists'][$_POST['list']]['name'],$_POST['name']);
						}

						//edit list values
						$ml_options['lists'][$_POST['list']] = $validate->post;

						//update the lists
						$ml_options = $getWP->getOption('ml_options',$ml_options,true);

						$getWP->addAlert(__('You have successfully updated your list.','members-list-pro'));
					}
					break;

				case 'add' :

					if(in_array($_POST['name'],$ml_options['lists']) and empty($_POST['item'])) {
						$getWP->addError(__('This list has already been created.','members-list-pro'));
						return;
					}
					if(!isset($ml_options['lists'])) {
						$ml_options['lists'] = [];
					}

					$validate = new tern_validation($this->fields,'create');
					if($validate->validate($_POST)) {

						//create an ID for the list
						$id = $this->generate_id(7);

						//add the list data to the lists array
						$ml_options['lists'][$id] = $validate->post;

						//save the lists
						$ml_options = $getWP->getOption('ml_options',$ml_options,true);

						//add users to list
						if(isset($_POST['all']) and $_POST['all']) {
							(new ML_user_common())->add_all_users_to_list($_POST['name']);
						}
						elseif(isset($_POST['role']) and $_POST['role']) {
							(new ML_user_common())->add_users_by_role($_POST['role'],$_POST['name']);
						}

						$getWP->addAlert(__('You have successfully added a list.','members-list-pro'));
					}

					break;

				case 'delete' :

					$a = array();
					foreach((array)$ml_options['lists'] as $k => $v) {
						if(!in_array($k,$_REQUEST['items'])) {
							$a[$k] = $v;
						}
						else {
							$wpdb->query('delete from '.$wpdb->usermeta.' where meta_key="_WP_ML" and meta_value="'.$v['name'].'"');
						}
					}
					$ml_options['lists'] = $a;
					$ml_options = $getWP->getOption('ml_options',$ml_options,true);
					$getWP->addAlert(__('You have successfully deleted a list.','members-list-pro'));

					break;

				default :
					break;
			}

		}

	}
	public function page() {
		wp_enqueue_style('switchery');
		wp_enqueue_style('fontawesome');
		wp_enqueue_style('thickbox');
		wp_enqueue_style('ml-admin');

		wp_enqueue_script('ml-list');

		include(ML_ADMIN_DIR.'/view/list.php');
	}

}
new ML_list();

/****************************************Terminate Script******************************************/
?>
