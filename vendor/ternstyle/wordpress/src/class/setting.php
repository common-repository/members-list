<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			setting.php
//		Description:
//			This class performs functions for WordPress menus.
//		Version:
//			1.0.2
//		Copyright:
//			Copyright (c) 2019 Ternstyle LLC.
//		License:
//			The license for this software is called license.rtf and is included within this plugin.
//
////////////////////////////////////////////////////////////////////////////////////////////////////

namespace MLP\ternpress;
use MLP\ternpress\tern_option as tern_option;

/****************************************Commence Script*******************************************/

/*------------------------------------------------------------------------------------------------
	For good measure
------------------------------------------------------------------------------------------------*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*------------------------------------------------------------------------------------------------
	Settings
------------------------------------------------------------------------------------------------*/

class tern_setting {

	public $page = '';
	public $field = [];

	public function __construct($namespace='',$defaults=[],$options=[],$directory='') {

		if(!$this->is_page()) {
			return false;
		}

		$this->namespace = $namespace;
		$this->defaults = $defaults;
		$this->options = $options;
		$this->directory = $directory;
		$this->action();
		return $this;
	}
	public function action() {
		add_action('init',[$this,'save'],99);
	}
	public function view() {
		include($this->directory.'/settings.php');
	}
	public function save() {

		//action
		$action = 'update_'.sanitize_title($this->namespace);

		//we're supposed to be saving these settings
		if(isset($_POST['action']) and $_POST['action'] == $action) {

			//security
			if(!isset($_REQUEST['_wpnonce']) or !wp_verify_nonce($_REQUEST['_wpnonce'],$this->options['nonce'])) {

				//error message

				return false;
			}

			//user permissions
			if(!current_user_can('manage_options')) {

				//error message

				return false;
			}

			switch($_REQUEST['action']) {

				case $action :

					$option = (new tern_option())->update_from_post($this->namespace,$this->field);
					if($option) {

						//success message

						$this->options = $option;
						return $option;


					}
					else {
						//error message
					}

					break;

				default :
					break;

			}

			return false;

		}
	}
	public function is_page() {
		if(
			!empty($this->page)
			and is_array($this->page)
			and isset($GLOBALS['pagenow'])
			and in_array($GLOBALS['pagenow'],$this->page)
		) {
			return true;
		}
		elseif(
			!empty($this->page)
			and isset($_GET['page'])
			and $_GET['page'] == $this->page
		) {
			return true;
		}
		elseif(
			!empty($this->page)
			and isset($_GET['page'])
			and $_GET['page'] == $this->page
			and isset($GLOBALS['pagenow'])
			and $GLOBALS['pagenow'] == 'admin-ajax.php'
		) {
			return true;
		}
		return false;
	}

}

/****************************************Terminate Script******************************************/

?>
