/**************************************************************************************************/
/*
/*		File:
/*			jquery.pseudo-select.js
/*		Description:
/*			This file contains Javascript for the jQuery Pseudo Select Plugin.
/*		Date:
/*			Added on July 17, 2012
/*		Version:
/*			1.3
/*		Copyright:
/*			Copyright (c) 2019 Ternstyle LLC.
/*		License:
/*			This file (software) is licensed under the terms of the GNU Lesser General Public License v3
/*			as published by the Free Software Foundation. You should have received a copy of of
/*			the GNU Lesser General Public License along with this software. In the event that you
/*			have not, please visit: http://www.gnu.org/licenses/gpl-3.0.txt
/*
/**************************************************************************************************/

(function ($) {
	jQuery.fn.extend({
		pseudoSelect : function () {
			$(this).find('select').each(function () {
				var self = $(this);

				//var p = self.wrap('<span class="pseudo-select pseudo-'+self.attr('name')+'" />').parent().css('width',500);
				var p = $('<span class="pseudo-select pseudo-'+self.attr('name')+'" />').css({
					width : 500,
					opacity : 0
				}).append('<input type="hidden" name="'+self.attr('name')+'" value="'+self.find('option:selected').attr('value')+'" />');
				$(document.body).append(p);
				self.attr('name','').css('display','none');
				p.append('<span class="pseudo-selector"><span class="pseudo-selected">'+self.find('option:selected').text()+'</span><span class="pseudo-options" /></span>');
				var width = 0;
				self.find('option').each(function () {
					var o = p.find('.pseudo-options').append('<span class="pseudo-option" data-value="'+$(this).attr('value')+'">'+$(this).text()+'</span>').find('.pseudo-option:last').css('display','inline-block');
					width = o.outerWidth() > width ? o.outerWidth() : width;
					if($(this).is(':selected')) {
						o.addClass('pseudo-active');
					}
				});
				p.css({
					width : width+40,
					opacity : 1
				}).find('.pseudo-option').css('display','block');

				f = p.clone(true);
				f.insertAfter(self);
				p.remove();
				p = f;

				p.find('.pseudo-selected').bind('click',function (e) {
					e.preventDefault();
					var that = this;
					if($(that).hasClass('pseudo-active')) {
						p.css('overflow','hidden');
						$(that).removeClass('pseudo-active');
					}
					else {
						p.css('overflow','visible');
						$(that).addClass('pseudo-active');
						$(document).bind('mousedown.pseudo-select',function (e) {
							$(document).unbind('click.pseudo-select');
							if(e.target == that) {
								return;
							}
							p.css('overflow','hidden');
							$(that).removeClass('pseudo-active');
						});
					}
				});
				p.find('.pseudo-option').bind('mousedown',function (e) {
					p.find('.pseudo-option').removeClass('pseudo-active');
					$(this).addClass('pseudo-active');
					var v = $(this).attr('data-value');
					p.find('.pseudo-selected').text($(this).text());
					p.find('input[type=hidden]').val(v).trigger('change');
				});
			});
		}
	});
})(jQuery);
