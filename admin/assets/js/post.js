/**************************************************************************************************/
/*
/*		File:
/*			post.js
/*		Description:
/*			This file contains Javascript for the administrative aspects of the plugin.
/*		Date:
/*			Added on December 29, 2015
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
	Shortcode
------------------------------------------------------------------------------------------------*/
	
	$('#WP-ml-shortcode-add').bind('click',function (e) {
		e.preventDefault();		
		tb_show('','#TB_inline?inlineId=WP-ml-shortcode&width=753&height=400');
	});
	$('#WP-ml-shortcode input[type=submit]').bind('click',function (e) {
		e.preventDefault();
		var v = $('#WP-ml-shortcode-fields select').val();
		console.log(v);
		if(typeof(v) != 'undefined' && v.length > 0) {
			tinyMCE.get('content').selection.setContent('[members-list id='+v+']');
		}
		else {
			add_error('Please select a valid list to add.');
		}
		window.tb_remove();
	});
	

/****************************************Terminate Script******************************************/

});})(jQuery);