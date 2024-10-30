/**************************************************************************************************/
/*
/*		File:
/*			forms.js
/*		Description:
/*			This file contains Javascript for the administrative aspects of the plugin.
/*		Copyright:
/*			Copyright (c) 2021 Ternstyle LLC.
/*		License:
/*			This software is licensed under the terms of the End User License Agreement (EULA)
/*			provided with this software. In the event the EULA is not present with this software
/*			or you have not read it, please visit: http://www.ternstyle.us/license.html
/*
/**************************************************************************************************/

/****************************************Commence Script*******************************************/

var form_values_to_string = function(){};

(function ($) {

	$.fn.extend({
		form_values_to_string : function () {
			var d = '',o = $(this).get(0).elements;
			for(var i=0;i<o.length;i++) {
				if($.inArray($(o[i]).attr('type'),['checkbox','radio']) != -1 && !$(o[i]).prop('checked')) {
					continue;
				}
				if(typeof($(o[i]).attr('name')) == 'undefined') {
					continue;
				}
				d += '&'+$(o[i]).attr('name')+'='+$(o[i]).val();
			}
			return d;
		}
	});

/****************************************Terminate Script******************************************/

})(jQuery);
