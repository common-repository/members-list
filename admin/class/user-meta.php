<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			user-meta.php
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
use MLP\ternstyle\gMaps as gMaps;

/****************************************Commence Script*******************************************/

/*------------------------------------------------------------------------------------------------
	For good measure
------------------------------------------------------------------------------------------------*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*------------------------------------------------------------------------------------------------
	User Meta Functions
------------------------------------------------------------------------------------------------*/

class ML_user_meta extends TERNPLUGIN_admin {

	public $page = [
		'profile.php',
		'user-edit.php',
	];
	public $nonce = 'WP_ml_nonce';
	public $root = ML_ROOT;
	public $script_slug = 'ml';
	public $admin_dir = ML_ADMIN_DIR;
	static $include = [
		//'user-meta.php',
	];

	public $address_field = ['line1','line2','city','state','zip','country'];

	public function __construct($run=true) {
		parent::__construct($run);
		return $this;
	}
	public function actions() {
		add_action('init',[$this,'enqueue'],9);
		add_action('wp_print_scripts',[$this,'js']);
		add_action('profile_update',[$this,'save'],-9);

		add_action('show_user_profile',[$this,'render_editor'],99);
		add_action('show_user_profile',[$this,'render_meta'],999,1);
		add_action('edit_user_profile',[$this,'render_editor'],99);
		add_action('edit_user_profile',[$this,'render_meta'],999,1);

		add_filter('sanitize_user_meta_description',[$this,'filter_description'],999999,3);
	}
	public function enqueue() {
		parent::enqueue();
		wp_enqueue_style('thickbox');

		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_enqueue_script('editor');
		wp_enqueue_media();

		wp_enqueue_script('ml-user-meta');
	}
	public function save($user_id=null) {
		global $wpdb,$ml_options,$getWP;

		if(
			parent::save($user_id)
			and (
				current_user_can('edit_users')
				or (
					$ml_options['allow_display']
					and $current_user->ID == $user_id
				)
			)
		) {

			//save the wysiwyg description
			update_user_meta($user_id,'description',$_POST['wysiwyg-description']);

			//add user to any lists
			delete_user_meta($user_id,'_WP_ML');
			if(isset($_REQUEST['lists'])) {
				foreach((array)$_REQUEST['lists'] as $v) {
					add_user_meta($user_id,'_WP_ML',$v);
				}
			}

			//add the user's address
			$address = [];
			foreach((array)$this->address_field as $v) {
				delete_user_meta($user_id,'_'.$v);
				if(isset($_POST[$v]) and !empty($_POST[$v])) {
					add_user_meta($user_id,'_'.$v,$_POST[$v]);
					$address[$v] = $_POST[$v];
				}
			}

			//geocode the address
			delete_user_meta($user_id,'_lat');
			delete_user_meta($user_id,'_lng');
			if(!empty($address)) {
				$l = (new gMaps)->geoLocate($address,$ml_options['api_key']);
				if(isset($l->lat) and isset($l->lng)) {
					add_user_meta($user_id,'_lat',$l->lat);
					add_user_meta($user_id,'_lng',$l->lng);
				}
			}

		}

	}
	public function render_editor($user) {
		ob_start();
		wp_editor(get_user_meta($user->ID,'description',true),'wysiwyg-description',[]);
		$html = ob_get_contents();
		ob_end_clean();
		echo '<div id="WP_ml_wysiwyg_description" style="display:none;">'.$html.'</div>';
	}
	public function render_meta($user) {
		global $ml_options;

		if(
			!current_user_can('edit_users')
			and (
				(
					$ml_options['allow_display']
					and $current_user->ID != $user->ID
				)
				or !$ml_options['allow_display']
			)
		) {
			return;
		}

		include(ML_ADMIN_DIR.'/view/user-meta.php');
	}
	public function filter_description($bio,$key,$type) {
		return $bio;
	}

}
new ML_user_meta();

/****************************************Terminate Script******************************************/
?>
