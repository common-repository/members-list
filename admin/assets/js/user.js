/**************************************************************************************************/
/*
/*		File:
/*			users.js
/*		Description:
/*			This file contains Javascript for the administrative aspects of the plugin.
/*		Date:
/*			Added on January 4th 2016
/*		Copyright:
/*			Copyright (c) 2019 Ternstyle LLC.
/*		License:
/*			This file (software) is licensed under the terms of the End User License Agreement (EULA)
/*			provided with this software. In the event the EULA is not present with this software
/*			or you have not read it, please visit:
/*			http://www.ternstyle.us/members-list-plugin-for-wordpress/license.html
/*
/**************************************************************************************************/

/****************************************Commence Script*******************************************/

(function ($) {$(document).ready(function () {

/*------------------------------------------------------------------------------------------------
	List Select
------------------------------------------------------------------------------------------------*/

	$('select[name=list-select]').bind('change',function () {
		location.href = ml_root+'/wp-admin/admin.php?page=ml-list-user&list='+$(this).val();
	});


/****************************************Terminate Script******************************************/

});})(jQuery);
