/**************************************************************************************************/
/*
/*		File:
/*			scripts.js
/*		Description:
/*			This file contains Javascript for the front-end aspects of the plugin.
/*		Date:
/*			Added on January 29th 2009
/*		Copyright:
/*			Copyright (c) 2019 Ternstyle LLC.
/*		License:
/*			This file (software) is licensed under the terms of the GNU Lesser General Public License v3
/*			as published by the Free Software Foundation. You should have received a copy of of
/*			the GNU Lesser General Public License along with this software. In the event that you
/*			have not, please visit: http://www.gnu.org/licenses/gpl-3.0.txt
/*
/**************************************************************************************************/

/****************************************Commence Script*******************************************/

(function($) {$(document).ready(function () {
		
/*------------------------------------------------------------------------------------------------
	Initialize
------------------------------------------------------------------------------------------------*/
		
	$('.WP-ml-members').each(function () {
		
		var self = $(this);
		
/*------------------------------------------------------------------------------------------------
	Filters
------------------------------------------------------------------------------------------------*/
	
	self.find('select[name=alpha]').bind('change',function () {
		location.href = self.find('input[name=page]').val()+'&sort=last_name&type=alpha&search='+$(this).val();
	});
	if($(window).width() > 480) {
		self.pseudoSelect();
	}
	self.find('.WP-ml-filter a').bind('click',function (e) {
		self.find('.WP-ml-filter a,.WP-ml-filter-form').removeClass('WP-ml-active');
		$(this).addClass('WP-ml-active');
		if(typeof($(this).attr('data-target')) != 'undefined') {
			$('.'+$(this).attr('data-target')).addClass('WP-ml-active');
		}
	});
	if($(window).width() > 480) {
		var field_by = self.find('.WP-ml-filter-search-form input[name=by]');
		var field_radius = self.find('.WP-ml-filter-search-form .pseudo-radius');
	}
	else {
		var field_by = self.find('.WP-ml-filter-search-form select[name=by]');
		var field_radius = self.find('.WP-ml-filter-search-form select[name=radius]');
	}
	var search_value = field_by.val();
	if(search_value == 'radius') {
		field_radius.addClass('WP-ml-active');
	}
	field_by.bind('change',function (e) {
		if($(this).val() == 'radius') {
			field_radius.addClass('WP-ml-active');
		}
		else {
			field_radius.removeClass('WP-ml-active');
		}
		if((search_value == 'radius' && $(this).val() != 'radius') || (search_value != 'radius' && $(this).val() == 'radius')) {
			self.find('.WP-ml-filter-search-form input[name=search]').val('');
		}
		search_value = $(this).val();
	});
	
/*------------------------------------------------------------------------------------------------
	Controls
------------------------------------------------------------------------------------------------*/
	
	$('.WP-ml-member-admin-controls a.WP-ml-remove').bind('click',function () {
		$(this).parents('.WP-ml-member').remove();
	});
	
	
	});
		
/****************************************Terminate Script******************************************/
		
});})(jQuery);