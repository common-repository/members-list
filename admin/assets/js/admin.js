/**************************************************************************************************/
/*
/*		File:
/*			admin.js
/*		Description:
/*			This file contains Javascript for ternstyle's Automatic Video Posts Plugin.
/*		Copyright:
/*			Copyright (c) 2021 Ternstyle LLC.
/*		License:
/*			This software is licensed under the terms of the End User License Agreement (EULA)
/*			provided with this software. In the event the EULA is not present with this software
/*			or you have not read it, please visit:
/*			http://www.ternstyle.us/members-list-plugin-for-wordpress/license.html
/*
/**************************************************************************************************/

/****************************************Commence Script*******************************************/

(function($) { $(document).ready(function () {

/*------------------------------------------------------------------------------------------------
	Tabs
------------------------------------------------------------------------------------------------*/

	if($('.tern-tabs').get(0)) {
		$('.tern-tabs').tabs({
			create : function (e,ui) {
				setTimeout(function () {
					$('.ui-tabs-nav').css('height',$('.ui-tabs-nav').parent().outerHeight());
				},200);
			},
			activate : function (e,ui) {
				$('.ui-tabs-nav').css('height',$('.ui-tabs-nav').parent().outerHeight());
			}
		}).addClass('ui-tabs-vertical ui-helper-clearfix');
	}

/*------------------------------------------------------------------------------------------------
	Checkbox Toggles
------------------------------------------------------------------------------------------------*/

	$('.switchery').each(function () {
		$(this).data('switch',new Switchery(this));
	});

/*------------------------------------------------------------------------------------------------
	Color Picker
------------------------------------------------------------------------------------------------*/

	if(typeof($.spectrum) != 'undefined') {
		$('.color-picker').spectrum({
			preferredFormat : 'hex',
			showInput : true,
		});
	}

/****************************************Terminate Script******************************************/

}); })(jQuery);
