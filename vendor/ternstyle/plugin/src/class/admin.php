<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			admin.php
//		Description:
//			Genereic plugin admin class
//		Version:
//			1.0.3
//		Copyright:
//			Copyright (c) 2021 Ternstyle LLC.
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
	Admin parent class
------------------------------------------------------------------------------------------------*/

class TERNPLUGIN_admin {

	public $page = '';
	public $nonce = '';
	public $root = null;
	public $script_slug = '';
	public $admin_dir = '';
	static $include = [];

	public function __construct($run=true) {
		if(!is_admin()) {
			return $this;
		}
		if($run and $this->is_page()) {
			$this->actions();
		}
		return $this;
	}
	public function actions() {
		add_action('init',[$this,'save'],9);
		add_action('init',[$this,'enqueue'],9);
		add_action('wp_print_scripts',[$this,'js']);
	}
	public function enqueue() {
		wp_enqueue_style($this->script_slug.'-admin');
		wp_enqueue_script('easing');
	}
	public function js() {
		echo '<script type="text/javascript">var '.$this->script_slug.'_root = "'.$this->root.'";</script>'."\n";
	}
	public function page() {
		foreach((array)static::$include as $include) {
			include($this->admin_dir.'/view/'.$include);
		}
	}
	public function save($id=null) {

		if(!current_user_can('manage_options')) {
			return false;
		}

		if(
			(
				!isset($_REQUEST['_wpnonce'])
				or !wp_verify_nonce($_REQUEST['_wpnonce'],$this->nonce)
			)
			and (
				!isset($_REQUEST[$this->nonce])
				or !wp_verify_nonce($_REQUEST[$this->nonce],$this->nonce)
			)
		) {
			return false;
		}
		return true;
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
	public function get_action() {
		if(isset($_REQUEST['action']) or isset($_REQUEST['action2'])) {
			return empty($_REQUEST['action']) ? $_REQUEST['action2'] : $_REQUEST['action'];
		}
		return false;
	}
	public function generate_id($n) {
		$c = "abcdefghijklmnopqrstuvwyxz0123456789";
		$s = '';
		for($i=0;$i<$n;$i++) {
			$s .= substr($c,rand(0,37),1);
		}
		return $s;
	}

}
