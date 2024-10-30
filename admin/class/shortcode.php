<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			shortcode.php
//		Description:
//			This file adds shortcode and gutenberg capacilities for the post editor.
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
	Shortcode
------------------------------------------------------------------------------------------------*/

class ML_shortcode extends TERNPLUGIN_admin {

	public $page = [
		'post.php'
	];
	public $nonce = 'WP_ml_nonce';
	public $root = ML_ROOT;
	public $script_slug = 'ml';
	public $admin_dir = ML_ADMIN_DIR;

	public function __construct() {
		parent::__construct();
		return $this;
	}
	public function actions() {
		parent::actions();
		add_action('admin_footer',[$this,'shortcode_form']);
		add_action('media_buttons',[$this,'shortcode_button'],20);
	}
	public function enqueue() {
		wp_enqueue_style('thickbox');
		wp_enqueue_script('thickbox');
		wp_enqueue_script('ml-post');
	}
	public function save($id=null){}
	function shortcode_button() {
		include(ML_ADMIN_DIR.'/view/shortcode-button.php');
	}
	public function shortcode_form() {
		global $ml_options;
		$lists = array();
		foreach((array)$ml_options['lists'] as $k => $v) {
			$lists[$v['name']] = $k;
		}
		include(ML_ADMIN_DIR.'/view/shortcode-form.php');
	}


}
new ML_shortcode();

/****************************************Terminate Script******************************************/
?>
