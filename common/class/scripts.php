<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			scripts.php
//		Description:
//			This file includes the necerssary CSS and Javascript files.
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

/*------------------------------------------------------------------------------------------------
	For good measure
------------------------------------------------------------------------------------------------*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*------------------------------------------------------------------------------------------------
	Plugin Scripts
------------------------------------------------------------------------------------------------*/

class ML_script {

	public function __construct() {
		add_action('init',[$this,'register'],1);
		add_action('init',[$this,'enqueue'],2);
	}
	public function register() {

		wp_register_style('font-source-sans-pro','https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;600&display=swap',[],'1.0','all');
		wp_register_script('pseudo-select',	ML_URL.'/vendor/ternstyle/ternstyle/src/js/jquery.pseudo-select.js',['jquery'],'0.8',true);
		wp_register_style('ml-style',			ML_PUBLIC_URL.'/assets/css/style.css',['font-source-sans-pro'],ML_VERSION);

		if(is_admin()) {
			wp_register_style('jquery-ui',	ML_ADMIN_URL.'/assets/css/jquery-ui.min.css',[],'1.12.1');
			wp_register_style('switchery',	ML_ADMIN_URL.'/assets/css/switchery.min.css',[],'0.8.2');
			wp_register_style('spectrum',		ML_ADMIN_URL.'/assets/css/spectrum.css',[],'1.8',false);
			wp_register_style('fontawesome',	ML_ADMIN_URL.'/assets/fontawesome/css/all.min.css',[],'5.15.2',false);
			wp_register_style('ml-admin',		ML_ADMIN_URL.'/assets/css/style.css',[],ML_VERSION.time(),'all');

			wp_register_script('easing',		ML_ADMIN_URL.'/assets/js/jquery.easing.js',['jquery'],'1.3',true);
			wp_register_script('spectrum',	ML_ADMIN_URL.'/assets/js/spectrum.js',['jquery'],'1.8',true);
			wp_register_script('switchery',	ML_ADMIN_URL.'/assets/js/switchery.min.js',[],'0.8.2',true);
			wp_register_script('clonable',	ML_URL.'/vendor/ternstyle/ternstyle/src/js/jquery.clonable.js',['jquery'],'1.0',true);
			wp_register_script('tern-errors',	ML_URL.'/vendor/ternstyle/ternstyle/src/js/errors.js',['jquery','easing'],ML_VERSION.time(),true);
			wp_register_script('tern-form',	ML_URL.'/vendor/ternstyle/ternstyle/src/js/form.js',['jquery'],ML_VERSION.time(),true);
			wp_register_script('tern-modal',	ML_URL.'/vendor/ternstyle/ternstyle/src/js/modal.js',['jquery','clonable','switchery'],ML_VERSION.time(),true);

			wp_register_script('ml-admin',	ML_ADMIN_URL.'/assets/js/admin.js',['jquery'],ML_VERSION.time(),true);
			wp_register_script('ml-list',		ML_ADMIN_URL.'/assets/js/list.js',['jquery','switchery','thickbox','tern-errors','tern-modal','tern-form','ml-admin'],ML_VERSION.time(),true);
			wp_register_script('ml-list-field',ML_ADMIN_URL.'/assets/js/list-field.js',['jquery','switchery','thickbox','jquery-ui-sortable','tern-errors','tern-modal','tern-form','ml-admin'],ML_VERSION.time(),true);
			wp_register_script('ml-user-meta',	ML_ADMIN_URL.'/assets/js/user-meta.js',['jquery'],ML_VERSION.time(),true);
			wp_register_script('ml-post',		ML_ADMIN_URL.'/assets/js/post.js',['jquery','tern-errors'],ML_VERSION,true);
			wp_register_script('ml-user',		ML_ADMIN_URL.'/assets/js/user.js',['jquery'],ML_VERSION,true);
		}
		else {
			wp_register_script('ml-map',		ML_PUBLIC_URL.'/assets/js/map.js',['jquery'],ML_VERSION,true);
			wp_register_script('ml-scripts',	ML_PUBLIC_URL.'/assets/js/scripts.js',['jquery'],ML_VERSION,true);
		}
	}
	public function enqueue() {
		if(is_admin()) {
			wp_enqueue_style('ml-admin');
		}
	}
	static function print_custom_css() {
		global $ml_options;
		if(isset($ml_options['color']) and !empty($ml_options['color']) and preg_match("/^#[0-9a-zA-Z]+$/",$ml_options['color'])) {
			echo '<style type="text/css">';
			echo 'html > body .WP-ml-members .WP-ml-button, html > body .WP-ml-map .gm-style-iw a { background-color:'.$ml_options['color'].'; }';
			echo 'html > body .WP-ml-members .WP-ml-filters .WP-ml-filter .WP-ml-active { color:'.$ml_options['color'].'; }';
			echo 'html > body .WP-ml-members .WP-ml-filters .WP-ml-filter .WP-ml-active:before { background-color:'.$ml_options['color'].'; }';
			echo 'html > body .WP-ml-members .WP-ml-filter-alpha-form ul li a { color:'.$ml_options['color'].'; }';
			echo 'html > body .WP-ml-members .WP-ml-filter-search-form form .pseudo-select .pseudo-selected { color:'.$ml_options['color'].'; }';
			echo 'html > body .WP-ml-members .WP-ml-filter-search-form form button { background-color:'.$ml_options['color'].'; }';
			echo 'html > body .WP-ml-members .WP-ml-sort a.WP-ml-active { color:'.$ml_options['color'].'; }';
			echo 'html > body .WP-ml-members .WP-ml-sort a.WP-ml-active:before { background-color:'.$ml_options['color'].'; }';
			echo 'html > body .WP-ml-members .WP-ml-sort a.WP-ml-active.WP-ml-sorted-d:after { border-top:8px solid '.$ml_options['color'].'; }';
			echo 'html > body .WP-ml-members .WP-ml-sort a.WP-ml-active.WP-ml-sorted-a:after { border-bottom:8px solid '.$ml_options['color'].'; }';
			echo 'html > body .WP-ml-members .WP-ml-members-list .WP-ml-member .WP-ml-member-markup .WP-ml-field a { color:'.$ml_options['color'].'; }';
			echo 'html > body .WP-ml-members .WP-ml-pagination ul li.WP-ml-active .WP-ml-pagination-item { background-color:'.$ml_options['color'].'; }';
			echo 'html > body .WP-ml-map .gm-style-iw h3 { color:'.$ml_options['color'].'; }';
			echo 'html > body .WP-ml-author-post header h2, html > body .WP-ml-author-post header h2 a { color:'.$ml_options['color'].'; }';
			echo 'html > body .WP-ml-author-post header .WP-ml-author-post-meta i, .WP-ml-author-post header .WP-ml-author-post-meta a { color:'.$ml_options['color'].'; }';
			echo 'html > body .WP-ml-author-post .WP-ml-author-post-content .WP-ml-author-post-more { background-color:'.$ml_options['color'].'; }';
			echo 'html > body .WP-ml-author-posts .WP-ml-author-post header h2 a { color:'.$ml_options['color'].'; }';
			echo 'html > body .WP-ml-author .WP-ml-member .WP-ml-member-markup .WP-ml-field a { color:'.$ml_options['color'].'; }';
			echo 'html > body .WP-ml-author-posts .WP-ml-author-post header .WP-ml-author-post-meta a { color:'.$ml_options['color'].'; }';
			echo 'html > body .WP-ml-author-posts .WP-ml-author-post header .WP-ml-author-post-meta a i::before { color:'.$ml_options['color'].'; }';
			echo '</style>';
		}
	}
	static function print_gmaps_js() {
		global $ml_options;
		echo '<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key='.((isset($ml_options['api_key_browser']) and !empty($ml_options['api_key_browser'])) ? $ml_options['api_key_browser'] : '').'"></script>';
	}

}
new ML_script();

/****************************************Terminate Script******************************************/
?>
