<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			option.php
//		Description:
//			This class performs functions for WordPress wp_options.
//		Version:
//			1.0.2
//		Copyright:
//			Copyright (c) 2019 Ternstyle LLC.
//		License:
//			The license for this software is called license.rtf and is included within this plugin.
//
////////////////////////////////////////////////////////////////////////////////////////////////////

namespace MLP\ternpress;
use MLP\ternstyle\parseForm as parseForm;

/****************************************Commence Script*******************************************/

/*------------------------------------------------------------------------------------------------
	For good measure
------------------------------------------------------------------------------------------------*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*------------------------------------------------------------------------------------------------
	Options
------------------------------------------------------------------------------------------------*/

class tern_option {

	public function __construct() {
		$this->action();
		return $this;
	}
	private function action() {

	}
	public function get($name='',$default=[],$force_default=false) {
		$o = get_option($name);

		$updated = false;

		//the option hasn't been added to the DB yet, and there are default values, add the option
		if(!$o and !empty($default)) {
			add_option($name,$default);
			$updated = true;
		}

		//there is an option in the DB but it's empty or we're forcing a reset to default values, and there are defult values, add the option
		elseif($o and (empty($o) or $force_default) and !empty($default)) {
			update_option($name,$default);
			$updated = true;
		}

		//the option exists in the DB, but the defaults aren't empty so ensure any of the default values not already set in the DB are added to the DB
		elseif($o and !empty($default)) {

			foreach($default as $k => $v) {
				if(!isset($o[$k])) {
					$o[$k] = $v;
					$updated = true;
				}
			}

			if($updated) {
				update_option($name,$o);
			}
		}

		if($updated) {
			return get_option($name);
		}

		return $o;
	}
	public function update_from_post($name='',$field=[],$nonce=null,$priv=true) {

		//if a nonce is set, verify it before continuing
		if($nonce and !wp_verify_nonce($_POST['_wpnonce'],$nonce)) {
			return false;
		}

		//if we're checking privileges and the user doesn't have them
		if($priv and !current_user_can('manage_options')) {
			return false;
		}

		//get the option from the DB
		$o = $this->get($name);

		//get the form values
		$f = new parseForm('post','_wp_http_referer,_wpnonce,action,submit,page,page_id');

		//add the form values to the option
		foreach($field as $v) {

			if(isset($f->a[$v])) {
				$o[$v] = $this->sanitize($f->a[$v]);
			}
			if(!isset($f->a[$v])) {
				$o[$v] = '';
			}
		}

		//return the updated option
		return $this->get($name,$o,true);

	}
	private function sanitize($v) {

		//arrays
		if(is_array($v)) {
			foreach((array)$v as $k => $x) {
				$v[$k] = $this->sanitize($x);
			}
		}

		//integer
		elseif(preg_match("/^[0-9]+$/",$v)) {
			return (int)$v;
		}

		//float
		elseif(preg_match("/^[0-9]+\.[0-9]+$/",$v)) {
			return (float)$v;
		}

		return $v;

	}

}

/****************************************Terminate Script******************************************/

?>
