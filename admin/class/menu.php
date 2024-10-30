<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			menu.php
//		Description:
//			This file initializes menus for the plugin's administrative tasks
//		Copyright:
//			Copyright (c) 2021 Ternstyle LLC.
//		License:
//			This software is licensed under the terms of the End User License Agreement (EULA)
//			provided with this software. In the event the EULA is not present with this software
//			or you have not read it, please visit:
//			http://www.ternstyle.us/members-list-plugin-for-wordpress/license.html
//
////////////////////////////////////////////////////////////////////////////////////////////////////

use MLP\ternpress\tern_menu as tern_menu;

/****************************************Commence Script*******************************************/

/*------------------------------------------------------------------------------------------------
	For good measure
------------------------------------------------------------------------------------------------*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*------------------------------------------------------------------------------------------------
	Admin Menus
------------------------------------------------------------------------------------------------*/

class ML_menu extends tern_menu {

	public function __construct ($o=[]) {
		parent::__construct($o);
		return $this;
	}
	public function action() {
		parent::action();
	}
	public function admin() {
		//if(ML_internal::is_activated()) {
			add_menu_page('Members List PRO',__('Members List','members-list-pro'),'manage_options','ml-settings',['ML_setting','page'],'dashicons-businessperson',99);
			add_submenu_page('ml-settings',__('Members List PRO','members-list-pro'),__('Settings','members-list-pro'),'manage_options','ml-settings',['ML_setting','page']);
			add_submenu_page('ml-settings',__('Create/Edit Lists','members-list-pro'),__('Create/Edit Lists','members-list-pro'),'manage_options','ml-create-list',[(new ML_list(false)),'page']);
			add_submenu_page('ml-settings',__('Edit List Fields','members-list-pro'),__('Edit List Fields','members-list-pro'),'manage_options','ml-list-field',[(new ML_list_field(false)),'page']);
			add_submenu_page('ml-settings',__('Edit List Users','members-list-pro'),__('Edit List Users','members-list-pro'),'manage_options','ml-list-user',[(new ML_user(false)),'page']);

			//add_submenu_page('ml-settings',__('Import Users','members-list-pro'),__('Import Users','members-list-pro'),'manage_options','ml-import-user',[(new ML_internal(false)),'upgrade']);
			//add_submenu_page('ml-settings',__('Export Users','members-list-pro'),__('Export Users','members-list-pro'),'manage_options','ml-export-user',[(new ML_internal(false)),'upgrade']);

			add_submenu_page('ml-settings',__('Trouble Shooting','members-list-pro'),__('Trouble Shooting','members-list-pro'),'manage_options','ml-trouble',[(new ML_trouble(false)),'page']);
			//add_submenu_page('ml-settings',__('Activated','members-list-pro'),__('Activated','members-list-pro'),'manage_options','ml-activate',[(new ML_activate(false)),'page']);
		//}
		//else {
		//	add_menu_page('Members List PRO',__('Members List PRO','members-list-pro'),'manage_options','ml-activate',[(new ML_activate(false)),'page'],'dashicons-businessperson',99);
		//}
	}
	public function register() {}

}
new ML_menu();

/****************************************Terminate Script******************************************/
?>
