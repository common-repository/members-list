<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			conf.php
//		Description:
//			This file configures the Wordpress Plugin - Members List
//		Actions:
//			1) initialize pertinent variables
//			2) load classes and functions
//		Date:
//			Added on June 12th 2010
//		Copyright:
//			Copyright (c) 2019 Ternstyle LLC.
//
////////////////////////////////////////////////////////////////////////////////////////////////////

/****************************************Commence Script*******************************************/

use MLP\ternpress\ternWP as ternWP;
use MLP\ternstyle\tern_dir as tern_dir;
use MLP\ternpress\tern_option as tern_option;
use MLP\ternplugin\tern_gutenberg as tern_gutenberg;

/*------------------------------------------------------------------------------------------------
	For good measure
------------------------------------------------------------------------------------------------*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*------------------------------------------------------------------------------------------------
	Global Variables
------------------------------------------------------------------------------------------------*/

define('ML_URL',plugin_dir_url('').'/members-list');
define('ML_ADMIN_URL',ML_URL.'/admin');
define('ML_PUBLIC_URL',ML_URL.'/public');
define('ML_ROOT',get_bloginfo('wpurl'));
define('ML_DIR',dirname(__FILE__));
define('ML_CLASS_DIR',dirname(__FILE__).'/class');
define('ML_COMMON_DIR',dirname(__FILE__).'/common');
define('ML_ADMIN_DIR',dirname(__FILE__).'/admin');
define('ML_PUBLIC_DIR',dirname(__FILE__).'/public');
define('ML_VERSION','4.3.7');
$ml_version = [4,3,7];

$WP_ml_current_author = false;
$WP_ml_author_render = false;
$WP_ml_defaults = array(
	'css'					=>	1,
	'custom_css'				=>	'',
	'hide_email'				=>	0,
	'noun_single'				=>	'Member',
	'noun'					=>	'Members',
	'lists'					=>	[],
	'allow_display'			=>	0,
	'api_key'					=>	'',
	'api_key_browser'			=>	'',
	'color'					=>	'#006c8c',
	'author_page'				=>	true,
	'author_template'			=>	'page.php',
	'author_field'				=>	[],
	'author_field_title'		=>	[],
	'author_fields'			=>	[],
	'author_posts'				=>	true
);
$WP_ml_user_fields = array(
	'User Name'			=>	'user_nicename',
	'Email Address'		=>	'user_email',
	'Display Name'			=>	'display_name',
	'URL'				=>	'user_url',
	'Registration Date'		=>	'user_registered'
);
$WP_ml_user_meta_fields = array(
	'Last Name'		=>	'last_name',
	'First Name'		=>	'first_name',
	'Description'		=>	'description'
);
$WP_ml_user_i18n = [
	__('User Name','members-list-pro'),
	__('Email Address','members-list-pro'),
	__('Display Name','members-list-pro'),
	__('URL','members-list-pro'),
	__('Registration Date','members-list-pro'),
	__('Last Name','members-list-pro'),
	__('First Name','members-list-pro'),
	__('Description','members-list-pro'),

	__('Standard Fields','members-list-pro'),
	__('Standard Meta Fields','members-list-pro'),
	__('Available Meta Fields','members-list-pro'),
];

$WP_ml_user_hidden_meta_fields = array(
	"rich_editing",
	"comment_shortcuts",
	"wp_capabilities",
	"wp_usersettings",
	"wp_usersettingstime",
	"wp_autosave_draft_ids",
	"screen_layout_dashboard",
	"use_ssl",
	"closedpostboxes_post",
	"metaboxhidden_post",
	"closedpostboxes_dashboard",
	"metaboxhidden_dashboard",
	"closedpostboxes_page",
	"metaboxhidden_page",
	"screen_layout_post",
	"edit_per_page",
	"edit_comments_per_page",
	"wp_user-settings",
	"wp_dashboard_quick_press_last_post_id",
	"default_password_nag",
	"meta-box-order_dashboard",
	"show_admin_bar_front",
	"dismissed_wp_pointers",
	"session_tokens",
	'_WP_ML',
	'wporg_favorites',
	'wp_user-settings-time'
);

$WP_ml_states = array('Alabama'=>'AL','Alaska'=>'AK','Arizona'=>'AZ','Arkansas'=>'AR','California'=>'CA','Colorado'=>'CO','Connecticut'=>'CT','Delaware'=>'DE','Florida'=>'FL','Georgia'=>'GA','Hawaii'=>'HI','Idaho'=>'ID','Illinois'=>'IL','Indiana'=>'IN','Iowa'=>'IA','Kansas'=>'KS','Kentucky'=>'KY','Louisiana'=>'LA','Maine'=>'ME','Maryland'=>'MD','Massachusetts'=>'MA','Michigan'=>'MI','Minnesota'=>'MN','Mississippi'=>'MS','Missouri'=>'MO','Montana'=>'MT','Nebraska'=>'NE','Nevada'=>'NV','New Hampshire'=>'NH','New Jersey'=>'NJ','New Mexico'=>'NM','New York'=>'NY','North Carolina'=>'NC','North Dakota'=>'ND','Ohio'=>'OH','Oklahoma'=>'OK','Oregon'=>'OR','Pennsylvania'=>'PA','Rhode Island'=>'RI','South Carolina'=>'SC','South Dakota'=>'SD','Tennessee'=>'TN','Texas'=>'TX','Utah'=>'UT','Vermont'=>'VT','Virginia'=>'VA','Washington'=>'WA','West Virginia'=>'WV','Wisconsin'=>'WI','Wyoming'=>'WY','Alberta '=>'AB','British Columbia '=>'BC','Manitoba '=>'MB','New Brunswick '=>'NB','Newfoundland and Labrador '=>'NL','Northwest Territories '=>'NT','Nova Scotia '=>'NS','Nunavut '=>'NU','Ontario '=>'ON','Prince Edward Island '=>'PE','Quebec '=>'QC','Saskatchewan '=>'SK','Yukon '=>'YT');

$WP_ml_countries = array('Afghanistan'=>'AF','Albania'=>'AL','Algeria'=>'DZ','American Samoa'=>'AS','Andorra'=>'AD','Angola'=>'AO','Anguilla'=>'AI','Antarctica'=>'AQ','Antigua And Barbuda'=>'AG','Argentina'=>'AR','Armenia'=>'AM','Aruba'=>'AW','Australia'=>'AU','Austria'=>'AT','Azerbaijan'=>'AZ','Bahamas'=>'BS','Bahrain'=>'BH','Bangladesh'=>'BD','Barbados'=>'BB','Belarus'=>'BY','Belgium'=>'BE','Belize'=>'BZ','Benin'=>'BJ','Bermuda'=>'BM','Bhutan'=>'BT','Bolivia'=>'BO','Bosnia And Herzegovina'=>'BA','Botswana'=>'BW','Bouvet Island'=>'BV','Brazil'=>'BR','British Indian Ocean Territory'=>'IO','Brunei'=>'BN','Bulgaria'=>'BG','Burkina Faso'=>'BF','Burundi'=>'BI','Cambodia'=>'KH','Cameroon'=>'CM','Canada'=>'CA','Cape Verde'=>'CV','Cayman Islands'=>'KY','Central African Republic'=>'CF','Chad'=>'TD','Chile'=>'CL','China'=>'CN','Christmas Island'=>'CX','Cocos (Keeling) Islands'=>'CC','Columbia'=>'CO','Comoros'=>'KM','Congo'=>'CG','Cook Islands'=>'CK','Costa Rica'=>'CR','Cote D\'Ivorie (Ivory Coast)'=>'CI','Croatia (Hrvatska)'=>'HR','Cuba'=>'CU','Cyprus'=>'CY','Czech Republic'=>'CZ','Democratic Republic Of Congo (Zaire)'=>'CD','Denmark'=>'DK','Djibouti'=>'DJ','Dominica'=>'DM','Dominican Republic'=>'DO','East Timor'=>'TP','Ecuador'=>'EC','Egypt'=>'EG','El Salvador'=>'SV','Equatorial Guinea'=>'GQ','Eritrea'=>'ER','Estonia'=>'EE','Ethiopia'=>'ET','Falkland Islands (Malvinas)'=>'FK','Faroe Islands'=>'FO','Fiji'=>'FJ','Finland'=>'FI','France'=>'FR','France, Metropolitan'=>'FX','French Guinea'=>'GF','French Polynesia'=>'PF','French Southern Territories'=>'TF','Gabon'=>'GA','Gambia'=>'GM','Georgia'=>'GE','Germany'=>'DE','Ghana'=>'GH','Gibraltar'=>'GI','Greece'=>'GR','Greenland'=>'GL','Grenada'=>'GD','Guadeloupe'=>'GP','Guam'=>'GU','Guatemala'=>'GT','Guinea'=>'GN','Guinea-Bissau'=>'GW','Guyana'=>'GY','Haiti'=>'HT','Heard And McDonald Islands'=>'HM','Honduras'=>'HN','Hong Kong'=>'HK','Hungary'=>'HU','Iceland'=>'IS','India'=>'IN','Indonesia'=>'ID','Iran'=>'IR','Iraq'=>'IQ','Ireland'=>'IE','Israel'=>'IL','Italy'=>'IT','Jamaica'=>'JM','Japan'=>'JP','Jordan'=>'JO','Kazakhstan'=>'KZ','Kenya'=>'KE','Kiribati'=>'KI','Kuwait'=>'KW','Kyrgyzstan'=>'KG','Laos'=>'LA','Latvia'=>'LV','Lebanon'=>'LB','Lesotho'=>'LS','Liberia'=>'LR','Libya'=>'LY','Liechtenstein'=>'LI','Lithuania'=>'LT','Luxembourg'=>'LU','Macau'=>'MO','Macedonia'=>'MK','Madagascar'=>'MG','Malawi'=>'MW','Malaysia'=>'MY','Maldives'=>'MV','Mali'=>'ML','Malta'=>'MT','Marshall Islands'=>'MH','Martinique'=>'MQ','Mauritania'=>'MR','Mauritius'=>'MU','Mayotte'=>'YT','Mexico'=>'MX','Micronesia'=>'FM','Moldova'=>'MD','Monaco'=>'MC','Mongolia'=>'MN','Montserrat'=>'MS','Morocco'=>'MA','Mozambique'=>'MZ','Myanmar (Burma)'=>'MM','Namibia'=>'NA','Nauru'=>'NR','Nepal'=>'NP','Netherlands'=>'NL','Netherlands Antilles'=>'AN','New Caledonia'=>'NC','New Zealand'=>'NZ','Nicaragua'=>'NI','Niger'=>'NE','Nigeria'=>'NG','Niue'=>'NU','Norfolk Island'=>'NF','North Korea'=>'KP','Northern Mariana Islands'=>'MP','Norway'=>'NO','Oman'=>'OM','Pakistan'=>'PK','Palau'=>'PW','Panama'=>'PA','Papua New Guinea'=>'PG','Paraguay'=>'PY','Peru'=>'PE','Philippines'=>'PH','Pitcairn'=>'PN','Poland'=>'PL','Portugal'=>'PT','Puerto Rico'=>'PR','Qatar'=>'QA','Reunion'=>'RE','Romania'=>'RO','Russia'=>'RU','Rwanda'=>'RW','Saint Helena'=>'SH','Saint Kitts And Nevis'=>'KN','Saint Lucia'=>'LC','Saint Pierre And Miquelon'=>'PM','Saint Vincent And The Grenadines'=>'VC','San Marino'=>'SM','Sao Tome And Principe'=>'ST','Saudi Arabia'=>'SA','Senegal'=>'SN','Seychelles'=>'SC','Sierra Leone'=>'SL','Singapore'=>'SG','Slovak Republic'=>'SK','Slovenia'=>'SI','Solomon Islands'=>'SB','Somalia'=>'SO','South Africa'=>'ZA','South Georgia And South Sandwich Islands'=>'GS','South Korea'=>'KR','Spain'=>'ES','Sri Lanka'=>'LK','Sudan'=>'SD','Suriname'=>'SR','Svalbard And Jan Mayen'=>'SJ','Swaziland'=>'SZ','Sweden'=>'SE','Switzerland'=>'CH','Syria'=>'SY','Taiwan'=>'TW','Tajikistan'=>'TJ','Tanzania'=>'TZ','Thailand'=>'TH','Togo'=>'TG','Tokelau'=>'TK','Tonga'=>'TO','Trinidad And Tobago'=>'TT','Tunisia'=>'TN','Turkey'=>'TR','Turkmenistan'=>'TM','Turks And Caicos Islands'=>'TC','Tuvalu'=>'TV','Uganda'=>'UG','Ukraine'=>'UA','United Arab Emirates'=>'AE','United Kingdom'=>'UK','United States'=>'US','United States Minor Outlying Islands'=>'UM','Uruguay'=>'UY','Uzbekistan'=>'UZ','Vanuatu'=>'VU','Vatican City (Holy See)'=>'VA','Venezuela'=>'VE','Vietnam'=>'VN','Virgin Islands (British)'=>'VG','Virgin Islands (US)'=>'VI','Wallis And Futuna Islands'=>'WF','Western Sahara'=>'EH','Western Samoa'=>'WS','Yemen'=>'YE','Yugoslavia'=>'YU','Zambia'=>'ZM','Zimbabwe'=>'ZW');

/*------------------------------------------------------------------------------------------------
	Vendors
------------------------------------------------------------------------------------------------*/

require_once(dirname(__FILE__).'/vendor/autoload.php');

/*------------------------------------------------------------------------------------------------
	Load Core Files
------------------------------------------------------------------------------------------------*/

(new tern_dir())->include(ML_COMMON_DIR);
if(is_admin()) {
	(new tern_dir())->include(ML_ADMIN_DIR);
}
else {
	(new tern_dir())->include(ML_PUBLIC_DIR);
}

/*------------------------------------------------------------------------------------------------
	Initialize Plugin
------------------------------------------------------------------------------------------------*/

add_action('init','WP_ml_init',-9999);
function WP_ml_init() {
	global $WP_ml_defaults,$ml_options,$WP_ml_gutenberg,$getWP;
	$ml_options = (new tern_option())->get('ml_options',$WP_ml_defaults);

	//set-up global objects
	$WP_ml_gutenberg = new tern_gutenberg($ml_options);
	$getWP = new ternWP;
}
function WP_ml_load_plugin_textdomain() {
	load_plugin_textdomain('members-list-pro',false,basename(dirname(__FILE__)).'/languages/');
}
add_action('plugins_loaded','WP_ml_load_plugin_textdomain');

/****************************************Terminate Script******************************************/
?>
