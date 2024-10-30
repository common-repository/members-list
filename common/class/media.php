<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			media.php
//		Description:
//			This file adds media functionality.
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

/*------------------------------------------------------------------------------------------------
	For good measure
------------------------------------------------------------------------------------------------*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*------------------------------------------------------------------------------------------------
	Media Functions
------------------------------------------------------------------------------------------------*/

class ML_media {
	public function __construct() {
		add_action('init',function () {
			add_image_size('WP-ml-thumb',648,300,true);
		});
		return $this;
	}
}
new ML_media();

/****************************************Terminate Script******************************************/
?>
