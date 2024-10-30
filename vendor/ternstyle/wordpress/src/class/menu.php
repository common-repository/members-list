<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			menu.php
//		Description:
//			This class performs functions for WordPress menus.
//		Version:
//			1.0.1
//		Copyright:
//			Copyright (c) 2019 Ternstyle LLC.
//		License:
//			The license for this software is called license.rtf and is included within this plugin.
//
////////////////////////////////////////////////////////////////////////////////////////////////////

namespace MLP\ternpress;

/****************************************Commence Script*******************************************/

/*------------------------------------------------------------------------------------------------
	For good measure
------------------------------------------------------------------------------------------------*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*------------------------------------------------------------------------------------------------
	Menus
------------------------------------------------------------------------------------------------*/

class tern_menu {

	public function __construct() {
		add_theme_support('title-tag');

		$this->action();
		return $this;
	}
	public function action() {
		add_action('admin_init',[$this,'separator']);
		add_action('admin_menu',[$this,'admin']);
		add_action('admin_menu',[$this,'admin_remove']);

		add_action('init',[$this,'register'],9);
	}
	public function admin() {

	}
	public function admin_remove() {

	}
	public function separator() {

	}
	public function add_separator($pos=0) {
		global $menu;
		$index = 0;
		foreach($menu as $offset => $section) {
			if(substr($section[2],0,9) == 'separator') {
				$index++;
			}
			if($offset >= $pos) {
				$menu[(string)((float)$pos+0.1)] = array('','read',"separator{$index}",'','wp-menu-separator');
				break;
			}
		}
		ksort($menu);
	}
	public function register() {
		register_nav_menus([
			'main'		=>	'Main Navigation',
			'eyebrow'		=>	'Eyebrow Navigation',
			'social'		=>	'Social Navigation',
			'footer'		=>	'Footer Navigation',
			'all'		=>	'All Navigation'	,
		]);
	}

}

/****************************************Terminate Script******************************************/

?>
