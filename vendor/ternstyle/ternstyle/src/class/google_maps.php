<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			google_maps.php
//		Description:
//			Functions for dealing with google maps
//		Actions:
//			1) Get coordinates for an address
//		Date:
//			Added on June 18th 2010
//		Version:
//			1.2.1
//		Copyright:
//			Copyright (c) 2019 Ternstyle LLC.
//		License:
//			This software is licensed under the terms of the End User License Agreement (EULA)
//			provided with this software. In the event the EULA is not present with this software
//			or you have not read it, please visit: http://www.ternstyle.us/license.html
//
////////////////////////////////////////////////////////////////////////////////////////////////////

namespace MLP\ternstyle;
use WP_Http;

/****************************************Commence Script*******************************************/

class gMaps {

	function __construct() {}

	function geoLocate($a,$k=false) {
		$this->a = array_merge(array(
			'line1'		=>	'',
			'line2'		=>	'',
			'city'		=>	'',
			'state'		=>	'',
			'zip'		=>	'',
			'country'	=>	''
		),$a);

		$a = $this->format_address();
		if(empty($a)) {
			return false;
		}

		if(!$k or empty($k)) {
			$r = (new WP_Http())->get('http://maps.google.com/maps/api/geocode/json?sensor=false&language=en&address='.$a);
			$r = json_decode($r['body']);
			if(isset($r->results[0]->geometry->location)) {
				return $r->results[0]->geometry->location;
			}
			elseif(isset($r->error_message)) {
				return $r->error_message;
			}
		}
		else {
			$r = (new WP_Http())->get('https://maps.googleapis.com/maps/api/geocode/json?components=country&key='.$k.'&address='.$a);
			$r = json_decode($r['body']);
			if(isset($r->results[0]->geometry->location)) {
				return $r->results[0]->geometry->location;
			}
			elseif(isset($r->error_message)) {
				return $r->error_message;
			}
		}

		return false;
	}
	function format_address() {
		$this->sanitize_address();
		return urlencode(implode(', ',array_filter($this->a,'strlen')));
	}
	function sanitize_address() {
		foreach($this->a as $k => $v) {
			$this->a[$k] = preg_replace("/[^a-zA-Z0-9]+/",'+',trim($v));
		}
		$this->a = array_filter($this->a,'strlen');
	}

}

/****************************************Terminate Script******************************************/
?>
