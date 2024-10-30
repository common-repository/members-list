/**************************************************************************************************/
/*
/*		File:
/*			errors.js
/*		Version
/*			1.0.1
/*		Description:
/*			This file contains Javascript for the plugin's (Errors).
/*		Copyright:
/*			Copyright (c) 2019 Ternstyle LLC.
/*		License:
/*			This software is licensed under the terms of the End User License Agreement (EULA)
/*			provided with this software. In the event the EULA is not present with this software
/*			or you have not read it, please visit: http://www.ternstyle.us/license.html
/*
/**************************************************************************************************/

/****************************************Commence Script*******************************************/

/*------------------------------------------------------------------------------------------------
	Initialize
------------------------------------------------------------------------------------------------*/

	add_error = add_alert = null;

/*------------------------------------------------------------------------------------------------
	Errors
------------------------------------------------------------------------------------------------*/

	(function($) {

		add_error = function (e) {
			$('#wpbody-content').prepend('<div class="tern_error tern_errors tern_error_new"><div><p>'+e+'</p></div></div>');
			error_show();
		}
		add_alert = function (e) {
			$('#wpbody-content').prepend('<div class="tern_alert tern_alerts tern_error_new"><div><p>'+e+'</p></div></div>');
			error_show();
		}
		function error_show() {
			setTimeout(function () {
				$('.tern_error_new').stop().animate({ opacity:0,height:0 },{ duration:300,easing:'easeOutCirc',complete : function () {
					$(this).remove();
				}});
			},3000);
		}

	})(jQuery);


/****************************************Terminate Script******************************************/
