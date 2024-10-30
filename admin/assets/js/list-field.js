/**************************************************************************************************/
/*
/*		File:
/*			list-field.js
/*		Description:
/*			This file contains Javascript for the administrative aspects of the plugin.
/*		Date:
/*			Added on December 29th 2016
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
	List Select
------------------------------------------------------------------------------------------------*/

	$('select[name=list-select]').bind('change',function () {
		location.href = ml_root+'/wp-admin/admin.php?page=ml-list-field&list='+$(this).val();
	});

/*------------------------------------------------------------------------------------------------
	Edit Field
------------------------------------------------------------------------------------------------*/

	$('#WP_ml_field_edit').ternModal({
		protected : ['action','_wpnonce','list'],
	});

/*------------------------------------------------------------------------------------------------
	Sorting
------------------------------------------------------------------------------------------------*/

	$('table.sort tbody').sortable({
		handle : '.drag',
		placeholder : 'sortable-placeholder',
		forceHelperSize : true,
		stop : function () {
			var s = '';
			$('table.sort tbody input[name=name]').each(function () {
				s += '&name[]='+$(this).val();
			});
			$('table.sort tbody input[name=field]').each(function () {
				s += '&field[]='+$(this).val();
			});
			$.ajax({
				async : true,
				type : 'POST',
				url : ajaxurl,
				dataType : 'json',
				data : '_wpnonce='+$('input[name=_wpnonce]').val()+'&action=WP_ml_field_order&list='+$('input[name=list]').val()+s,
				success : function (r) {
					if(r.success) {
						add_alert(r.success);
					}
					else if(r.error) {
						add_alert(r.error);
					}
				},
				error : function (r) {
					console.log(r);
				}
			});
		}
	});

/****************************************Terminate Script******************************************/

});})(jQuery);
