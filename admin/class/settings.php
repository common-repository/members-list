<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			settings.php
//		Description:
//			This file compiles and processes the plugin's various settings pages.
//		Copyright:
//			Copyright (c) 2021 Ternstyle LLC.
//		License:
//			This software is licensed under the terms of the End User License Agreement (EULA)
//			provided with this software. In the event the EULA is not present with this software
//			or you have not read it, please visit:
//			http://www.ternstyle.us/members-list-plugin-for-wordpress/license.html
//
////////////////////////////////////////////////////////////////////////////////////////////////////

/****************************************Commence Script*******************************************/

use MLP\ternpress\tern_setting as tern_setting;

/*------------------------------------------------------------------------------------------------
	For good measure
------------------------------------------------------------------------------------------------*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*------------------------------------------------------------------------------------------------
	Theme Settings
------------------------------------------------------------------------------------------------*/

class ML_setting extends tern_setting {

	public $page = 'ml-settings';
	private $preserve = [
		'verified',
		'serial',
		'lists',
	];

	public function __construct($o=[]) {
		global $ml_options,$WP_ml_defaults;
		$this->field = array_keys($WP_ml_defaults);
		return parent::__construct(
			'ml_options',
			$WP_ml_defaults,
			array_merge($ml_options,[
				'nonce'	=>	'WP_ml_nonce',
			]),
			ML_ADMIN_DIR.'/view'
		);
	}
	public function save() {
		global $ml_options,$getWP;
		$_POST = array_merge($_POST,array_intersect_key((array)$ml_options,array_flip($this->preserve)));

		//validate Google API key
		if(isset($_POST['api_key']) and !empty($_POST['api_key'])) {
			$h = new WP_Http();
			$r = $h->get('https://maps.googleapis.com/maps/api/geocode/json?components=country&key='.$_POST['api_key'].'&address=90210');
			$r = json_decode($r['body']);
			if(isset($r->error_message)) {
				$getWP->addError('There is something wrong with your Google API Key. Here is the message from Google: '.$r->error_message);
			}
		}

		//compile post author fields
		if(isset($_POST['author_field']) and !empty($_POST['author_field'])) {
			$a = array();
			foreach((array)$_POST['author_field'] as $k => $v) {
				$a[] = array(
					'field'	=>	$v,
					'name'	=>	(isset($_POST['author_field_title'][$k]) and !empty($_POST['author_field_title'][$k])) ? $_POST['author_field_title'][$k] : $v
				);
			}
			$_POST['author_fields'] = $a;
		}

		//var_dump($_POST);die();


		$option = parent::save();
		if($option) {

			$ml_options = $option;

			//add author page
			if(isset($ml_options['author_page']) and $ml_options['author_page'] and !get_page_by_path('author')) {
				wp_insert_post([
					'post_type'		=>	'page',
					'post_status'		=>	'publish',
					'post_name'		=>	'author',
					'post_title'		=>	'Author'
				]);
			}


		}
	}
	static function page() {
		wp_enqueue_style('jquery-ui');
		wp_enqueue_style('switchery');
		wp_enqueue_style('spectrum');
		wp_enqueue_style('fontawesome');
		wp_enqueue_style('ml-admin');

		wp_enqueue_script('jquery-ui');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('spectrum');
		wp_enqueue_script('clonable');
		wp_enqueue_script('switchery');
		wp_enqueue_script('ml-admin');

		$parent = wp_get_theme()->parent();
		$files = wp_get_theme()->get_files();
		if($parent) {
			$files = $parent->get_files();
		}
		$templates = [];
		foreach((array)$files as $file_name => $file) {
			if(preg_match("/\.php$/",$file_name) and !in_array($file_name,['404.php','archive.php','comments.php','footer.php','functions.php','header.php','image.php','search.php','searchform.php'])) {
				$templates[] = $file_name;
			}
		}

		include(ML_ADMIN_DIR.'/view/settings.php');
	}
}
new ML_setting();

/****************************************Terminate Script******************************************/
?>
