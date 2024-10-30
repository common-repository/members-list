/**************************************************************************************************/
/*
/*		File:
/*			clonable.js
/*		Version:
/*			1.0
/*		Description:
/*			This file contains Javascript for the jQuery plugin Clonbable.
/*		Date:
/*			Added on January 4th 2016
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

(function ($) {$(document).ready(function () {

/*------------------------------------------------------------------------------------------------
	Cloneable
------------------------------------------------------------------------------------------------*/

	$.fn.clonable = function() {
		clone.apply(this);
	};
	function clone() {
		var x = $(this).clone(true).addClass('cloned');
		x.find('.remove').css('display','inline-block');
		x.insertAfter(this);
	}

	$('.clonable .remove').css('display','none');
	$('.clonable.cloned .remove').css('display','inline-block');
	$('.clone').bind('click',function (e) {
		e.preventDefault();
		var c = $(this).parents('.clonable');
		var x = c.clone(true).addClass('cloned');
		x.find('.remove').css('display','inline-block');
		x.insertAfter(c);
	});
	$('.remove').bind('click',function (e) {
		e.preventDefault();
		$(this).parents('.clonable').remove();
	});

/****************************************Terminate Script******************************************/

});})(jQuery);
