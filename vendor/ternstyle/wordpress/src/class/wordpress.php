<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			wordpress.php
//		Description:
//			This is a generic class for dealing with various Wordpress plugin tasks.
//		Actions:
//			1) get/set/update Wordpress options
//			2) serve posts and post related items
//			3) handle WordPress errors
//		Date:
//			Created April 21st, 2009 for WordPress
//		Version:
//			2.1.1
//		Copyright:
//			Copyright (c) 2017 Ternstyle LLC.
//		License:
//			The license for this software is called license.rtf and  is included within this plugin.
//
////////////////////////////////////////////////////////////////////////////////////////////////////

/****************************************Commence Script*******************************************/

namespace MLP\ternpress;
use MLP\ternstyle\parseForm as parseForm;

/*------------------------------------------------------------------------------------------------
	For good measure
------------------------------------------------------------------------------------------------*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*------------------------------------------------------------------------------------------------
	WordPress
------------------------------------------------------------------------------------------------*/

class ternWP {

	var $errors = array();
	var $alerts = array();
	var $warnings = array();

	public function __construct() {
		$this->actions();
	}
	private function actions() {
		add_action('init',[$this,'session'],1);
		add_action('all_admin_notices',[$this,'admin_notices']);
	}
	public function session() {
		if(!session_id()) {
			session_start([
				'read_and_close'	=>	true,
			]);
		}
		foreach(['errors','alerts','warnings'] as $v) {
			if(isset($_SESSION['tern_'.$v]) and !empty($_SESSION['tern_'.$v])) {
				$this->{$v} = $_SESSION['tern_'.$v];
				//unset($_SESSION['tern_'.$v]);
			}
		}
	}
	private function reset_session() {
		foreach(['errors','alerts','warnings'] as $v) {
			unset($_SESSION['tern_'.$v]);
		}
	}

/*------------------------------------------------------------------------------------------------
	Options
------------------------------------------------------------------------------------------------*/

	function getOption($n,$d='',$v=false) {
		$o = get_option($n);
		if(!$o and !empty($d)) {
			add_option($n,$d);
		}
		elseif($o and (empty($o) or $v) and !empty($d)) {
			update_option($n,$d);
		}
		elseif($o and !empty($d)) {
			foreach($d as $k => $v) {
				if(!isset($o[$k])) {
					$o[$k] = $v;
				}
			}
			update_option($n,$o);
		}
		return get_option($n);
	}
	function updateOption($n,$d,$w) {
		$o = $this->getOption($n,$d);
		if(wp_verify_nonce($_REQUEST['_wpnonce'],$w) and $_REQUEST['action'] == 'update' and current_user_can('administrator')) {
			$f = new parseForm('post','_wp_http_referer,_wpnonce,action,submit,page,page_id');

			foreach($o as $k => $v) {
				if(is_string($v) and isset($f->a[$k])) {
					$f->a[$k] = preg_match("/^[0-9]+$/",$f->a[$k]) ? (int)$f->a[$k] : $f->a[$k];
				}
				if(!isset($f->a[$k])) {
					$f->a[$k] = $v;
				}
			}
			return $this->getOption($n,$f->a,true);
			$this->addAlert('You have successfully updated your settings.');
		}
		else {
			return $this->getOption($n,$d);
		}
	}

/*------------------------------------------------------------------------------------------------
	Alerts / Errors
------------------------------------------------------------------------------------------------*/

	function admin_notices() {
		$e = $this->renderErrors();
		if($e) {
			echo '<div class="tern_errors">'.$e.'</div>';
		}

		$e = $this->renderWarnings();
		if($e) {
			echo '<div class="tern_warnings">'.$e.'</div>';
		}

		$a = $this->renderAlerts();
		if($a) {
			echo '<div class="tern_alerts">'.$a.'</div>';
		}
		$this->reset_session();
	}


	function addError($e) {
		$this->errors[] = $e;
		$_SESSION['tern_errors'] = $this->errors;
	}
	function renderErrors() {
		if(empty($this->errors)) {
			return false;
		}
		$notice = '';
		foreach($this->errors as $v) {
			$notice .= '<p>'.$v.'</p>';
		}
		$this->errors = array();
		return $notice;
	}
	function addWarning($e) {
		$this->warnings[] = $e;
		$_SESSION['tern_warnings'] = $this->warnings;
	}
	function renderWarnings() {
		if(empty($this->warnings)) {
			return false;
		}
		$notice = '';
		foreach($this->warnings as $v) {
			$notice .= '<p>'.$v.'</p>';
		}
		$this->warnings = array();
		return $notice;
	}
	function addAlert($e) {
		$this->alerts[] = $e;
		$_SESSION['tern_alerts'] = $this->alerts;
	}
	function renderAlerts() {
		if(empty($this->alerts)) {
			return false;
		}
		$notice = '';
		foreach($this->alerts as $v) {
			$notice .= '<p>'.$v.'</p>';
		}
		$this->alerts = array();
		return $notice;
	}

}

/****************************************Terminate Script******************************************/
?>
