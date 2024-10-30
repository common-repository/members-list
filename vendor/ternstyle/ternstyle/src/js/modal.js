/**************************************************************************************************/
/*
/*		File:
/*			modal.js
/*		Description:
/*			This file contains Javascript for the administrative aspects of the plugin.
/*		Date:
/*			Added on February 22 2021
/*		Copyright:
/*			Copyright (c) 2021 Ternstyle LLC.
/*		License:
/*			This software is licensed under the terms of the End User License Agreement (EULA)
/*			provided with this software. In the event the EULA is not present with this software
/*			or you have not read it, please visit: http://www.ternstyle.us/license.html
/*
/**************************************************************************************************/

/****************************************Commence Script*******************************************/

(function ($) {
	$.fn.ternModal = function (method) {
		var options = {};
		var methods = {
			init : function (o) {
				var self = this;
				if(o) {
					this.data('options',$.extend(options,o));
				}
				self.options = this.data('options');
				self.handle = $('[data-modal='+self.attr('id')+']');
				self.handle.bind('click.ternmodal',function (e) {
					populate.apply(self,[this]);
				});
			}
		};
		function populate(handle) {

			var self = this;
			var parent = $(handle).parent();
			var form = $(this).find('form:first');
			var fields = form.find('input[type=text],input[type=radio],input[type=checkbox],input[type=hidden],select,textarea');

			fields.each(function () {
				var field_name = $(this).attr('name').replace("\[",'').replace("\]",'');
				var list_value_input = parent.find('[name='+field_name+']');

				//skip protected values
				if($.inArray(field_name,self.options.protected) != -1) {
					return;
				}

				//reset empty values
				if(typeof(list_value_input.val()) == 'undefined') {
					reset_field(this);
					return;
				}

				//get value
				var int = new RegExp("^[0-9]*$");
				if($(this).attr('name').indexOf('[]') !== -1) {
					var list_value = list_value_input.val().split(',');
				}
				else if(int.test(list_value_input.val())) {
					var list_value = parseInt(list_value_input.val());
				}
				else {
					var list_value = list_value_input.val();
				}

				//we don't have a corresponding value
				if(!list_value_input.get(0)) {
					return;
				}

				//checkboxes/radios
				if($.inArray($(this).attr('type'),['checkbox','radio']) !== -1) {
					reset_field(this);
					var value = int.test($(this).val()) ? parseInt($(this).val()) : $(this).val();
					if($.isArray(list_value) && $.inArray(value,list_value) !== -1) {
						$(this).prop('checked','checked');
					}
					else	if(value === list_value) {
						$(this).prop('checked','checked');
					}
					if($(this).hasClass('switchery')) {
						$(this).data('switch').setPosition();
					}
				}
				//clonable fields
				else if(list_value_input.hasClass('array')) {
					var c = $(this).parents('.clonable');
					if(!c.hasClass('clones-added')) {
						c.addClass('clones-added');
						for(var x=0;x<list_value.length-1;x++) {
							$(c).clonable();
						}
					}
					var x = 0;
					form.find(':input[name="'+$(this).attr('name')+'"]').each(function () {
						$(this).val(list_value[x]);
						x++;
					});
				}
				//all other fields
				else {
					$(this).val(list_value);
				}

			});

		}
		function reset_field(field) {
			if($.inArray($(field).attr('type'),['checkbox','radio']) !== -1) {
				$(field).attr('checked',false);
				$(field).prop('checked',false);
				$(field).removeProp('checked');

				if($(field).hasClass('switchery')) {
					$(field).data('switch').setPosition();
				}
			}
			else {
				$(field).val('');
			}
		}
		if(methods[method]) {
			methods[method].apply(this,Array.prototype.slice.call(arguments,1));
		}
		else if(typeof method === 'object' || !method) {
			methods.init.apply(this,arguments);
		}
		return this;
	};

/****************************************Terminate Script******************************************/

})(jQuery);
