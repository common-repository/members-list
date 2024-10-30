<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			shortcode.php
//		Description:
//			Render the members list.
//		Date:
//			Added on Decemnber 29th, 2015
//		Copyright:
//			Copyright (c) 2021 Ternstyle LLC.
//		License:
//			This file (software) is licensed under the terms of the End User License Agreement (EULA)
//			provided with this software. In the event the EULA is not present with this software
//			or you have not read it, please visit:
//			http://www.ternstyle.us/members-list-plugin-for-wordpress/license.html
//
////////////////////////////////////////////////////////////////////////////////////////////////////

use MLP\ternplugin\tern_members as tern_members;

/****************************************Commence Script*******************************************/

/*------------------------------------------------------------------------------------------------
	For good measure
------------------------------------------------------------------------------------------------*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*------------------------------------------------------------------------------------------------
	Render List
------------------------------------------------------------------------------------------------*/

class ML_shortcode {

	public function __construct() {
		add_shortcode('members-list',[$this,'render']);
	}
	function render($a) {
		global $ml_options;

		if(isset($ml_options['lists'][$a['id']])) {

			if(isset($ml_options['lists'][$a['id']]['map']) and $ml_options['lists'][$a['id']]) {
				wp_enqueue_script('ml-map');
			}

			if(isset($ml_options['css']) and $ml_options['css']) {
				wp_enqueue_style('ml-style');
				wp_enqueue_script('pseudo-select');
				add_action('wp_print_footer_scripts',['ML_script','print_custom_css']);
			}
			wp_enqueue_script('ml-scripts');
			add_action('wp_print_footer_scripts',['ML_script','print_gmaps_js']);

			$members = new tern_members($a['id'],$ml_options);
			return $members->render();
		}
	}
}
new ML_shortcode();

/****************************************Terminate Script******************************************/
?>
