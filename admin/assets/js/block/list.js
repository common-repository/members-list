/**************************************************************************************************/
/*
/*		File:
/*			gutenberg.js
/*		Description:
/*			This file contains Javascript for the back-end Gutenberg aspects of the plugin.
/*		Date:
/*			Added on December 17th 2018
/*		Copyright:
/*			Copyright (c) 2018 Ternstyle LLC.
/*
/**************************************************************************************************/

/****************************************Commence Script*******************************************/

(function(wp){

/*------------------------------------------------------------------------------------------------
	Initialize
------------------------------------------------------------------------------------------------*/

	const { __ } = wp.i18n;
	const {
		registerBlockType,
		//RichText,
		//Editable,
		//MediaUpload,
		//BlockControls,
		//AlignmentToolbar,
	} = wp.blocks;
	const wpelem = wp.element.createElement;
	registerBlockType('members-list-pro/ml-gutenberg-shortcode',{
		title : __('Members List','members-list-pro'),
		description : __('Members List shortcode.','members-list-pro'),
		category : 'widgets',
		icon : 'admin-users',
		supportHTML : false,
		attributes : {
			list_id : {
				type : 'string',
			}
		},
		keywords : [
			__('members','members-list-pro'),
			__('list','members-list-pro'),
		],
		edit : ( { attributes, className, isSelected, setAttributes } ) => {
			var list_id = attributes.list_id || '';
			return [
				wpelem(wp.components.SelectControl,{
					label : __('Members List','members-list-pro'),
					value : list_id,
					onChange : function(v) {
						setAttributes({
							list_id : v
						});
					},
					options : WP_ml_lists
				}),
			];
		},
		save : props => {
			const {
				attributes : {
					list_id
				}
			} = props;
			return (
				wpelem('div',{ list_id : list_id },'[members-list id='+list_id+']')
			);

		},
	});

/****************************************Terminate Script******************************************/

})(window.wp);
