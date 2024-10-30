<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			trouble.php
//		Description:
//			This file compiles helpful information.
//		Copyright:
//			Copyright (c) 2021 Ternstyle LLC.
//		License:
//			This software is licensed under the terms of the End User License Agreement (EULA)
//			provided with this software. In the event the EULA is not present with this software
//			or you have not read it, please visit:
//			http://www.ternstyle.us/members-list-plugin-for-wordpress/license.html
//
////////////////////////////////////////////////////////////////////////////////////////////////////

use MLP\ternplugin\TERNPLUGIN_admin as TERNPLUGIN_admin;

/****************************************Commence Script*******************************************/

/*------------------------------------------------------------------------------------------------
	For good measure
------------------------------------------------------------------------------------------------*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*------------------------------------------------------------------------------------------------
	Troubleshooting
------------------------------------------------------------------------------------------------*/

class ML_trouble extends TERNPLUGIN_admin {

	public $page = 'ml-trouble';
	static $include = [
		'trouble.php',
	];

	public function __construct($run=true) {
		parent::__construct($run);
	}
	public function page() {
		include(ML_ADMIN_DIR.'/view/trouble.php');
	}
}
new ML_trouble();


/****************************************Terminate Script******************************************/
?>
