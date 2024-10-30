<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			user.php
//		Description:
//			This file compiles and processes the plugin's configurable users.
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

use MLP\ternpress\tern_option as tern_option;

/*------------------------------------------------------------------------------------------------
	For good measure
------------------------------------------------------------------------------------------------*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*------------------------------------------------------------------------------------------------
	Common User Functions
------------------------------------------------------------------------------------------------*/

class ML_user_common {

	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;

		add_action('user_register',[$this,'register'],10,1);

		return $this;
	}
	public function register($user_id) {
		global $ml_options;

		$user = new WP_User($user_id);

		if(isset($ml_options['lists'])) {
			foreach((array)$ml_options['lists'] as $v) {
				if(!isset($v['add_new']) or !(int)$v['add_new']) {
					continue;
				}

				if(isset($v['all']) and (int)$v['all']) {
					add_user_meta($user_id,'_WP_ML',$v['name'],false);
				}
				elseif(isset($v['role']) and !empty($v['role']) and count(array_intersect($user->roles,$v['role'])) > 0) {
					add_user_meta($user_id,'_WP_ML',$v['name'],false);
				}
			}
		}
	}
	public function is_in_list($u,$l) {
		$m = get_user_meta($u,'_WP_ML');
		$m = is_array($m) ? $m : array($m);
		foreach($m as $v) {
			if($v == $l) {
				return true;
			}
		}
		return false;
	}
	public function get_username($user_id=0) {
		$n = get_user_meta($user_id,'first_name',true);
		$l = get_user_meta($user_id,'last_name',true);
		if($n and $l) {
			$n .= ' '.$l;
		}
		else {
			$n = get_the_author_meta('user_nicename',$user_id);
		}
		return $n;
	}
	public function get_users_by_role($r) {
		$x = '';
		foreach($r as $v) {
			$x .= empty($x) ? " ".$this->db->usermeta.".meta_value LIKE '%$v%' " : " or ".$this->db->usermeta.".meta_value LIKE '%$v%' ";
		}
		return $this->db->get_results("select ID from ".$this->db->users." inner join ".$this->db->usermeta." on(".$this->db->users.".ID = ".$this->db->usermeta.".user_id) where ".$this->db->usermeta.".meta_key='".$this->db->prefix."capabilities' and ($x)");
	}



	public function get_user_db_fields() {
		global $WP_ml_user_fields,$WP_ml_user_meta_fields,$WP_ml_user_hidden_meta_fields;



		foreach((array)$WP_ml_user_fields as $k => $v) {
			$a['Standard Fields'][] = array($k,$v);
		}
		foreach($WP_ml_user_meta_fields as $k => $v) {
			$a['Standard Meta Fields'][] = array($k,$v);
		}
		$r = $this->db->get_col('select distinct meta_key from '.$this->db->usermeta);
		foreach($r as $v) {
			if(in_array($v,$WP_ml_user_fields) or in_array($v,$WP_ml_user_meta_fields) or in_array($v,$WP_ml_user_hidden_meta_fields)) {
				continue;
			}
			$a['Available Meta Fields'][] = array($v,$v);
		}
		return $a;
	}
	public function get_author_fields() {
		global $ml_options;
		if(isset($ml_options['author_field']) and !empty($ml_options['author_field']) and isset($ml_options['author_field_title']) and !empty($ml_options['author_field_title'])) {
			return array_combine($ml_options['author_field_title'],$ml_options['author_field']);
		}
		return false;
	}

	public function  add_all_users_to_list($list_name) {
		$users = $this->db->get_results('select ID from '.$this->db->users);
		$this->add_users_to_list($users,$list_name);
	}
	public function add_users_by_role($role,$list_name) {
		$users = $this->get_users_by_role($role);
		$this->add_users_to_list($users,$list_name);
	}
	public function add_users_to_list($users,$list_name) {
		if(!empty($users)) {
			foreach($users as $v) {
				$m = get_user_meta($v->ID,'_WP_ML');
				$m = is_array($m) ? $m : array($m);
				$t = false;
				foreach($m as $w) {
					if($w == $list_name) {
						break;
					}
				}
				if(!$t) {
					add_user_meta($v->ID,'_WP_ML',$list_name,false);
				}
			}
		}
	}
	public function remove_all_users($list_name) {
		return $this->db->query('delete from '.$this->db->usermeta.' where meta_key="_WP_ML" and meta_value="'.$list_name.'"');
	}
	public function update_list_name_for_all_users($old_list_name,$list_name) {
		return $this->db->query('update '.$this->db->usermeta.' set meta_value="'.$list_name.'" where meta_key="_WP_ML" and meta_value="'.$old_list_name.'"');
	}

}
new ML_user_common();

/****************************************Terminate Script******************************************/
?>
