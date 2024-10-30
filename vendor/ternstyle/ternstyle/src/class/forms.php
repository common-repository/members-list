<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
////	File:
////		forms.php
////	Actions:
////		1) parse form submission into query statement
////	Account:
////		Created on May 7th 2006 for ternstyle (tm) v1.0.0
////		Overhauled on June 25th 2008 for ternstyle (tm) v3.0.0
////	Version:
////		2.8.1
////
////	Written by Matthew Praetzel. Copyright (c) 2016 Ternstyle LLC.
////////////////////////////////////////////////////////////////////////////////////////////////////

namespace MLP\ternstyle;

/****************************************Commence Script*******************************************/

class parseForm {

	var $a = array();
	var $post = NULL;

	function __construct($t,$e=array(),$r=array()) {
		$this->post = $t == 'post' ? $_POST : $_GET;
		$r = $this->cleanArray($r);
		$e = is_array($e) ? $e : explode(",",$e);
		foreach($this->post as $k => $v) {
			foreach($r as $w) {
				if(preg_match("/".$w."/",$k)) {
					continue 2;
				}
			}
			if(!in_array($k,$e)) {
				$this->a[$k] = $v;
			}
		}
	}
	function addField($k,$v) {
		$this->a[$k] = $v;
	}
	function mergeTwoFields($k,$f,$l,$s='') {
		$this->a[$k] = $this->post[$f] . $s . $this->post[$l];
	}
	function mergeFields($l,$k,$s='') {
		$l = is_array($l) ? $l : explode(",",$l);
		foreach($l as $v) {
			$this->a[$k] .= empty($this->a[$k]) ? $this->post[$v] : $s . $this->post[$v];
		}
	}
	function mergeFieldsByRegEx($r=array(),$s='') {
		$r = is_array($r) ? $r : array($r=>$k);
		foreach($this->post as $k => $v) {
			foreach($r as $l => $w) {
				if(preg_match("/".$l."/",$k)) {
					$this->a[$w] .= empty($this->a[$w]) ? $v : $s . $v;
				}
			}
		}
	}
	function fixFieldByRegEx($r) {
		$r = $this->cleanArray($r);
		foreach($this->post as $k => $v) {
			foreach($r as $l => $w) {
				if(preg_match("/".$l."/",$k)) {
					$this->a[$w] = $v;
				}
			}
		}
	}
	function insertQuery($t) {
		foreach($this->a as $k => $v) {
			if(get_magic_quotes_gpc()) {
				$v = parseForm::addSlashesForQuotes(stripslashes($v));
			}
			$s .= empty($s) ? $k : "," . $k;
			$w .= empty($w) ? "'" . $v . "'" : ",'" . $v . "'";
		}
		return "insert into " . $t . " (" . $s . ") values (" . $w . ")";
	}
	function updateQuery($t,$w) {
		foreach($this->a as $k => $v) {
			if(get_magic_quotes_gpc()) {
				$v = parseForm::addSlashesForQuotes(stripslashes($v));
			}
			$u .= empty($u) ? $k . "='" . $v . "'" : "," . $k . "='" . $v . "'";
		}
		return "update " . $t . " set " . $u . " where " . $w;
	}
	function cleanArray($a) {
		if($a) {
			return is_array($a) ? $a : array($a);
		}
		return array();
	}
	function addSlashesForQuotes($s) {
		return str_replace("'","\'",$s);
	}

	function noHTML($s) {
		return htmlentities($s);
	}
	function asURL($s) {
		return rawurlencode($s);
	}
	function escape($s) {
		if(extension_loaded('PDO')) {
			$p = new PDO('mysql:dbname='.DB_NAME.';host='.DB_HOST,DB_USER,DB_PASSWORD);
			return substr($p->quote($s),1,strlen($p->quote($s))-2);
		}
		elseif(function_exists('mysql_real_escape_string')) {
			return mysql_real_escape_string($s);
		}
		return $s;
	}

}

/****************************************Terminate Script******************************************/
?>
