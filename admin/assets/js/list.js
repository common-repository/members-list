/**************************************************************************************************/
/*
/*		File:
/*			lists.js
/*		Description:
/*			This file contains Javascript for the administrative aspects of the plugin.
/*		Date:
/*			Added on January 3rd 2009
/*		Copyright:
/*			Copyright (c) 2021 Ternstyle LLC.
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
	Edit List
------------------------------------------------------------------------------------------------*/

	$('#WP_ml_field_edit').ternModal({
		protected : ['action','_wpnonce'],
	});

/****************************************Terminate Script******************************************/

});})(jQuery);
