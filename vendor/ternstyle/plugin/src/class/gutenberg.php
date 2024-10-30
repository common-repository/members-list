<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			gutenberg.php
//		Description:
//			This class performs functions for the Gutenberg editor. Ugh.
//		Version:
//			1.0
//		Copyright:
//			Copyright (c) 2018 Ternstyle LLC.
//		License:
//			The license for this software is called license.rtf and is included within this plugin.
//
////////////////////////////////////////////////////////////////////////////////////////////////////

namespace MLP\ternplugin;

/****************************************Commence Script*******************************************/

/*------------------------------------------------------------------------------------------------
	For good measure
------------------------------------------------------------------------------------------------*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*------------------------------------------------------------------------------------------------
	Gutenbarf
------------------------------------------------------------------------------------------------*/

class tern_gutenberg {

	public function __construct($o=[]) {

		$this->o = $o;
		$this->actions();

		return $this;
	}
	public function actions() {
		add_action('init',[$this,'scripts']);
	}
	public function scripts() {
		if(function_exists('register_block_type')) {
			wp_register_script('ml-gutenberg-shortcode',ML_ADMIN_URL.'/assets/js/block/list.js',['wp-i18n','wp-blocks','wp-element'],ML_VERSION.time(),true);
			if(function_exists('wp_set_script_translations')) {
				wp_set_script_translations('ml-gutenberg-shortcode','members-list-pro',ML_DIR.'/languages/');
			}
			register_block_type('members-list-pro/ml-gutenberg-shortcode',[
				'editor_script'	=>	'ml-gutenberg-shortcode',
			]);
			add_action('admin_footer',[$this,'lists']);
		}
	}
	public function lists() {
		$lists = [
			[
				'value'	=>	'',
				'label'	=>	'select',
			]
		];
		foreach((array)$this->o['lists'] as $k => $v) {
			$lists[] = [
				'value'	=>	$k,
				'label'	=>	$v['name'],
			];
		}
		echo '<script type="text/javascript">var WP_ml_lists = '.json_encode($lists).'</script>';
	}


}

/****************************************Terminate Script******************************************/
?>
