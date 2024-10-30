<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			members.php
//		Description:
//			This class controls the compilation of the members list and its pagination.
//		Date:
//			Added December 29th, 2015
//		Version:
//			2.o
//		Copyright:
//			Copyright (c) 2019 Ternstyle LLC.
//		License:
//			The license for this software is called license.rtf and is included within this plugin.
//
////////////////////////////////////////////////////////////////////////////////////////////////////

namespace MLP\ternplugin;
use MLP\ternstyle\tern_select as tern_select;
use MLP\ternstyle\gMaps as gMaps;

/****************************************Commence Script*******************************************/

/*------------------------------------------------------------------------------------------------
	For good measure
------------------------------------------------------------------------------------------------*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*------------------------------------------------------------------------------------------------
	Members List Functions
------------------------------------------------------------------------------------------------*/

class tern_members {

	//query
	private $q = '';
	private $limit = 20;
	private $page = 1;
	private $page_start = 0;
	private $page_per = 20;

	//search
	private $search_field = [
		'Last Name'		=>	'last_name',
		'First Name'		=>	'first_name',
		'Email Address'	=>	'email_address',
		'User Name'		=>	'user_nicename'
	];
	private $search_override = false;
	private $unit = 'mi';

	//sort
	private $sort_field = [
		'Last Name'		=>	'last_name',
		'First Name'		=>	'first_name',
		'Email Address'	=>	'email_address',
	];
	private $sort_by = 'user_registered';
	private $sort_order = 'desc';

	//view
	private $view = 'default';
	private $filter_form = '';
	private $search_form = '';
	private $alpha_form = '';
	private $map_form = '';
	private $viewing_form = '';
	private $sort_form = '';
	private $ml = '';
	private $img_size = 100;
	private $truncate = true;
	private $open = false;
	private $link_open = false;
	public $pagination = '';

/*------------------------------------------------------------------------------------------------
	Initialize
------------------------------------------------------------------------------------------------*/

	function __construct($l,$o,$b=false) {
		global $wpdb,$getMap,$WP_ml_user_fields,$WP_ml_user_meta_fields;

		$this->db = $wpdb;
		$this->map = new gMaps;
		$this->user_fields = $WP_ml_user_fields;
		$this->user_meta_fields = $WP_ml_user_meta_fields;
		$this->o = $o;
		$this->notin = $b;

		if(!isset($this->o['lists'])) {
			return 'not found';
		}
		$this->list = isset($this->o['lists'][$l]) ? $this->o['lists'][$l] : false;
		if(!$this->list) {
			return 'not found';
		}
		$this->list_id = $l;
		$this->vars_set();
		$this->paging_set();
	}

	public function author($a=0,$f=array()) {
		$i = is_object($a) ? $a->ID : $a;
		$this->member = $this->db->get_row('select * from '.$this->db->users.' where ID='.$i,'ARRAY_A');
		$this->fields = $f;
		$this->compile_author();
		return $this->ml;
	}
	public function render() {
		$this->query();
		$this->compile();
		$this->compile_css();
		return $this->members;
	}
	private function vars_set() {
		//$this->vars_search_fields_set();
		//$this->vars_sort_fields_set();

		//viewable fields
		$this->fields = [
			[
				'name'	=>	__('First Name','members-list-pro'),
				'field'	=>	'first_name',
			],
			[
				'name'	=>	__('Last Name','members-list-pro'),
				'field'	=>	'last_name',
			],
			[
				'name'	=>	__('Email Address','members-list-pro'),
				'field'	=>	'user_email',
			],
		];

		foreach(array('search_field','sort_field','sort_by','sort_order','unit','view') as $v) {

			//from list settings
			if(isset($this->list[$v]) and !empty($this->list[$v])) {
				$this->$v = $this->list[$v];
			}

			//global plugin settings
			elseif(isset($this->o[$v]) and !empty($this->o[$v])) {
				$this->$v = $this->o[$v];
			}

		}

		$this->fields = (isset($this->list['fields']) and !empty($this->list['fields'])) ? $this->list['fields'] : $this->fields;

		//set view by request
		if(isset($_GET['view']) and !empty($_GET['view'])) {
			$this->view = esc_attr($_GET['view']);
		}
	}

/*------------------------------------------------------------------------------------------------
	Paging
------------------------------------------------------------------------------------------------*/

	private function paging_set() {
		$this->paging_page_per_set();
		$this->paging_page_set();
		$this->page_start_set();
	}
	private function paging_page_per_set() {
		$this->page_per = isset($this->list['limit']) ? $this->list['limit'] : (isset($this->o['limit']) ? $this->o['limit'] : $this->limit);
	}
	private function paging_page_set() {
		$this->page = get_query_var('ml_page');
		$this->page = (empty($this->page) and isset($_GET['ml_page'])) ? (int)esc_attr($_GET['ml_page']) : $this->page;
		$this->page = empty($this->page) ? 1 : $this->page;
	}
	private function page_start_set() {
		$this->page_start = $this->page-1;
	}

/*------------------------------------------------------------------------------------------------
	Settings
------------------------------------------------------------------------------------------------*/

	private function has_geo() {
		if(isset($this->list['radius']) and $this->list['radius']) {
			return true;
		}
		return false;
	}
	private function has_hidden() {
		if(isset($this->list['hidden']) and $this->list['hidden']) {
			return true;
		}
		return false;
	}
	private function has_gravatar() {
		if(isset($this->list['img']) and $this->list['img']) {
			return true;
		}
		return false;
	}
	private function has_link() {
		if(isset($this->list['link']) and $this->list['link']) {
			return true;
		}
		return false;
	}
	private function has_labels() {
		if(isset($this->list['labels']) and $this->list['labels']) {
			return true;
		}
		return false;
	}
	private function has_search() {
		if(isset($this->list['search']) and $this->list['search']) {
			return true;
		}
		return false;
	}
	private function has_radius() {
		return $this->has_geo();
	}
	private function has_map() {
		if(isset($this->list['map']) and $this->list['map']) {
			return true;
		}
		return false;
	}
	private function has_alpha() {
		if(isset($this->list['alpha']) and $this->list['alpha']) {
			return true;
		}
		return false;
	}
	private function list_img_size_get() {
		return isset($this->list['img_size']) ? $this->list['img_size'] : $this->img_size;
	}

/*------------------------------------------------------------------------------------------------
	Query
------------------------------------------------------------------------------------------------*/

	public function get() {
		return $this->r;
	}
	public function get_count() {
		return $this->total;
	}
	public function get_count_total() {
		if(!isset($this->list['name'])) {
			return 0;
		}
		return (int)$this->db->get_var('select count(distinct(user_id)) from '.$this->db->usermeta.' where meta_key="_WP_ML" and meta_value="'.$this->list['name'].'"');
	}
	public function query($b=false) {

		if($b) {
			$this->search_override = true;
			$this->search_field = array(
				'Last Name'		=>	'last_name',
				'First Name'		=>	'first_name',
				'Email Address'	=>	'user_email',
				'User Name'		=>	'user_nicename'
			);
		}

		$this->query_vars_set();
		$this->query_sort_set();
		$this->query_order_set();
		$this->query_limit_set();
		$this->query_select();
		$this->query_geocode();
		$this->query_join();
		$this->query_where();
		$this->query_order();
		$this->query_count_compile();
		$this->query_limit();
		$this->query_compile();
		//echo $this->query;
		//echo $this->query_count;
		$this->query_run();
	}
	private function query_compile() {
		$this->query .= $this->q;
	}
	private function query_count_compile() {
		$this->query_count .= $this->q;
	}
	private function query_run() {
		$this->r = $this->db->get_results($this->query,ARRAY_A);
		$this->query_count();
	}
	private function query_count() {
		$this->total = intval($this->db->get_var($this->query_count));
	}
	private function query_vars_set() {
		foreach(array('by','search','radius','type','sort','order') as $k => $v) {
			$this->$v = isset($_GET[$v]) ? esc_attr($_GET[$v]) : false;//$this->sanitize($v);
		}
	}
	private function query_sort_set() {
		$this->sort = isset($_GET['sort']) ? esc_attr($_GET['sort']) : $this->sort_by;
	}
	private function query_order_set() {
		$this->order = isset($_GET['order']) ? esc_attr($_GET['order']) : $this->sort_order;
	}
	private function query_limit_set() {
		$this->limit_start = $this->page_start*$this->page_per;
	}
	private function query_select() {
		$this->query = 'select distinct(a.ID),a.* from '.$this->db->users.' as a ';
		$this->query_count = 'select COUNT(distinct a.ID) from '.$this->db->users.' as a ';
	}
	private function query_geocode() {
		if($this->has_geo() and $this->query_has_radius()) {
			$r = $this->map->geoLocate(array('zip'=>esc_attr($_GET['search'])),$this->o['api_key']);
			$this->lat = $r->lat;
			$this->lng = $r->lng;
		}
	}

/*----------------------------------------
	Joins
----------------------------------------*/

	private function query_join() {
		$this->query_join_list();
		$this->query_join_sort();
		$this->query_join_search();
		$this->query_join_radius();
	}
	private function query_join_list() {
		if($this->query_has_list()) {
			$this->q .= ' ,'.$this->db->usermeta.' as l ';
		}
	}
	private function query_join_sort() {
		if(!$this->query_field_is_natural($this->sort)) {
			$this->q .= ' ,'.$this->db->usermeta.' as b ';
		}
	}
	private function query_join_search() {
		if($this->query_has_search()) {
			$this->q .= ' ,'.$this->db->usermeta.' as c ';
		}
	}
	private function query_join_radius() {
		if($this->query_has_radius()) {
			$this->q .= ' ,'.$this->db->usermeta.' as g ';
			$this->q .= ' ,'.$this->db->usermeta.' as h ';
		}
	}

/*----------------------------------------
	Wheres
----------------------------------------*/

	private function query_where() {
		$this->query_where_start();
		$this->query_where_list();
		$this->query_where_sort();
		$this->query_where_sortby();
		$this->query_where_alpha();
		$this->query_where_search();
		$this->query_where_radius();
		$this->query_where_hidden();
	}
	private function query_where_start() {
		$this->q .= ' where 1=1 ';
	}
	private function query_where_list() {
		if($this->query_has_list()) {
			$this->q .= ' and l.user_id = a.ID ';
			if($this->notin) {
				$this->q .= ' and a.ID not in (select distinct(user_id) from '.$this->db->usermeta.' where meta_key="_WP_ML" and meta_value="'.$this->list['name'].'") ';
			}
			else {
				$this->q .= ' and l.meta_key="_WP_ML" and l.meta_value="'.$this->list['name'].'" ';
			}
		}
	}
	private function query_where_sort() {
		if(!$this->query_field_is_natural($this->sort)) {
			$this->q .= ' and b.user_id = a.ID ';
			$this->q .= ' and b.meta_key = "'.$this->sort.'" ';
		}
	}
	private function query_where_sortby() {
		if($this->query_has_by() and !$this->query_field_is_natural($this->by) and !$this->query_field_is_natural($this->sort)) {
			$this->q .= ' and c.user_id = a.ID ';
		}
	}
	private function query_where_alpha() {
		if($this->query_type_is_alpha() and !$this->query_field_is_natural($this->sort)) {
			$this->q .= ' and c.user_id = a.ID ';
			$this->q .= " and c.meta_key = 'last_name' and SUBSTRING(LOWER(c.meta_value),1,1) = '$this->search' ";
		}
	}
	private function query_where_search() {
		if($this->query_has_search() and !$this->query_field_is_natural($this->by)) {
			$this->q .= ' and c.user_id = a.ID ';
		}
		if($this->query_has_search() and !$this->query_type_is_alpha() and !$this->query_has_radius()) {

			if($this->query_has_by() and !$this->query_field_is_natural($this->by)) {
				$this->q .= ' and c.meta_key = "'.$this->by.'" and (instr(c.meta_value,"'.$this->search.'") != 0) ';
			}
			elseif($this->query_has_by() and $this->query_field_is_natural($this->by)) {
				$this->q .= ' and instr(a.'.$this->by.',"'.$this->search.'") != 0 ';
			}
			elseif(!$this->query_has_by()) {
				$w = $x = '';
				foreach($this->search_field as $v) {
					if(!$this->query_field_is_natural($v)) {
						$w .= empty($w) ? " c.meta_key = '$v'" : " or c.meta_key = '$v'";
					}
					else {
						$x .= empty($x) ? "a.$v" : ",a.$v";
					}
				}
				$this->q .= empty($x) ? ' and ' : 'and (';
				$this->q .= !empty($w) ? "(($w) and instr(c.meta_value,'$this->search') != 0) " : ' 1=0 ';
				$this->q .= empty($x) ? '' : " or instr(concat_ws(' ',$x),'$this->search') != 0) ";
			}
		}
	}
	private function query_where_radius() {
		if($this->query_has_radius()) {
			$this->q .= ' and g.user_id = a.ID ';
			$this->q .= ' and h.user_id = a.ID ';
			$this->q .= " and g.meta_key='_lat' and h.meta_key='_lng' ";
		}
	}
	private function query_where_hidden() {
		if($this->has_hidden()) {
			$this->q .= ' and a.ID NOT IN ('.implode(',',$o['hidden']).')';
		}
	}

/*----------------------------------------
	Order
----------------------------------------*/

	private function query_order() {
		$this->query_order_radius();
		if($this->query_field_is_natural($this->sort)) {
			$this->q .= " order by a.$this->sort $this->order";
		}
		else {
			$this->q .= " order by b.meta_value $this->order";
		}
	}
	private function query_order_radius() {
		if($this->query_has_radius()) {
			$d = 1.609344*(int)$_GET['radius'];
			$this->q .= " and 6371 * 2 * ASIN( SQRT( POWER( SIN( RADIANS( $this->lat - g.meta_value ) / 2 ), 2 ) + COS( RADIANS( $this->lat ) ) * COS( RADIANS( g.meta_value ) ) * POWER( SIN( RADIANS( $this->lng - h.meta_value ) / 2 ), 2 ) ) ) < $d";
		}
	}
	private function query_limit() {
		$this->q .= ' limit '.$this->limit_start.','.$this->page_per.' ';
	}

/*----------------------------------------
	Conditionals
----------------------------------------*/

	private function query_field_is_natural($f) {
		if(in_array($f,$this->user_fields)) {
			return true;
		}
		return false;
	}

	private function query_type_is_alpha() {
		if($this->query_has_type() and $this->type == 'alpha') {
			return true;
		}
		return false;
	}
	private function query_type_is_radius() {
		if($this->query_has_type() and $this->by == 'radius') {
			return true;
		}
		return false;
	}

	private function query_has_by() {
		if(isset($this->by) and !empty($this->by) and !$this->query_type_is_radius()) {
			return true;
		}
		return false;
	}
	private function query_has_list() {
		if(isset($this->list) and isset($this->list['name']) and !empty($this->list['name'])) {
			return true;
		}
		return false;
	}
	private function query_has_radius() {
		if(isset($_GET['by']) and $_GET['by'] == 'radius' and isset($_GET['search']) and !empty($_GET['search'])) {
			return true;
		}
		return false;
	}
	private function query_has_search() {
		if(isset($_GET['search']) and !empty($_GET['search'])) {
			return true;
		}
		return false;
	}
	private function query_has_type() {
		if(isset($this->type) and !empty($this->type)) {
			return true;
		}
		return false;
	}

/*------------------------------------------------------------------------------------------------
	Compile
------------------------------------------------------------------------------------------------*/

	private function compile() {
		$this->compile_permalink_set();

		$this->compile_filters();
		$this->compile_pagination();
		$this->compile_search();
		$this->compile_alpha();
		$this->compile_sort();
		$this->compile_map();
		//$this->compile_radius();
		$this->compile_viewing();
		$this->compile_list();

		$this->members = '<div class="WP-ml-members';
		$this->members .= $this->has_search() ? ' WP-ml-has-search' : ' WP-ml-no-search';
		$this->members .= $this->has_radius() ? ' WP-ml-has-radius' : ' WP-ml-no-radius';
		$this->members .= $this->has_alpha() ? ' WP-ml-has-alpha' : ' WP-ml-no-alpha';
		$this->members .= $this->has_labels() ? ' WP-ml-has-labels' : ' WP-ml-no-labels';
		$this->members .= '">';
		$this->members .= '<input type="hidden" name="page" value="'.$this->url.'" />';
		$this->members .= $this->filter_form.$this->search_form.$this->alpha_form.$this->map_form.$this->viewing_form.$this->sort_form.$this->ml.$this->pagination;
		$this->members .= '</div>';
	}
	private function compile_permalink_set() {
		$this->url = get_permalink();
		$this->url = strpos($this->url,'?') !== false ? $this->url : $this->url.'?';
	}
	private function compile_css() {
		$this->members .= '<style type="text/css">'.$this->o['custom_css'].'</style>';
	}

/*------------------------------------------------------------------------------------------------
	Filters
------------------------------------------------------------------------------------------------*/

	private function compile_filters() {
		if($this->has_search() or $this->has_radius() or $this->has_alpha()) {
			$this->filter_form = '<div class="WP-ml-filter-container"><div class="WP-ml-filters">';
			$this->filter_form .= '<div class="WP-ml-filter WP-ml-filter-all">';
			$this->filter_form .= '<a href="'.get_permalink().'" class="WP-ml-filter-btn WP-ml-filter-all-btn';
			if(!$this->query_has_search() and !$this->query_has_radius() and !$this->query_type_is_alpha()) {
				$this->filter_form .= ' WP-ml-active';
			}
			$this->filter_form .= '"><span>'.__('All','members-list-pro').'<strong> '.$this->o['noun'].'</strong></span></a>';
			$this->filter_form .= '</div>';
			if($this->has_search() or $this->has_radius()) {
				$this->filter_form .= '<div class="WP-ml-filter WP-ml-filter-search">';
				$this->filter_form .= '<a data-target="WP-ml-filter-search-form" class="WP-ml-filter-btn WP-ml-filter-search-btn';
				if(($this->query_has_search() or $this->query_has_radius()) and !$this->query_type_is_alpha()) {
					$this->filter_form .= ' WP-ml-active';
				}
				if($this->has_search()) {
					$this->filter_form .= '"><span>'.__('Search','members-list-pro').'</span></a>';
				}
				elseif($this->has_radius()) {
					$this->filter_form .= '"><span>'.__('Radius','members-list-pro').'</span></a>';
				}
				$this->filter_form .= '</div>';
			}
			if($this->has_alpha()) {
				$this->filter_form .= '<div class="WP-ml-filter WP-ml-filter-alpha">';
				$this->filter_form .= '<a data-target="WP-ml-filter-alpha-form" class="WP-ml-filter-btn WP-ml-filter-alpha-btn';
				if($this->query_type_is_alpha()) {
					$this->filter_form .= ' WP-ml-active';
				}
				$this->filter_form .= '"><span>'.__('Filter','members-list-pro').'</span></a>';
				$this->filter_form .= '</div>';
			}
			$this->filter_form .= '<div class="clearfix"></div>';
			$this->filter_form .= '</div></div>';
		}
	}

/*------------------------------------------------------------------------------------------------
	Pagination
------------------------------------------------------------------------------------------------*/

	public function compile_pagination($u=false) {
		if($u) {
			$this->url = $u;
		}
		$this->pagination_vars_set();
		$this->pagination_start();
		$this->pagintion_loop();
		$this->pagination_end();
	}
	private function pagination_vars_set() {
		$this->pagination_pages_total_set();
		$this->pagination_pages_start_set();
		$this->pagination_pages_end_set();
	}
	private function pagination_pages_total_set() {
		$this->pages_total = ceil($this->total/$this->page_per);
	}
	private function pagination_pages_start_set() {
		$this->pages_start = ($this->page-2) < 1 ? 1 : ($this->page-2);
		if($this->pages_start > ($this->pages_total-4) and ($this->pages_total-4) > 0) {
			$this->pages_start = ($this->pages_total-4);
		}
	}
	private function pagination_pages_end_set() {
		$this->pages_end = ($this->pages_start+4) > $this->pages_total ? $this->pages_total : ($this->pages_start+4);
	}
	private function pagination_start() {
		$this->pagination = '<nav class="WP-ml-pagination"><ul>';
		$this->pagination_first_set();
		$this->pagination_prev_set();
	}
	private function pagination_end() {
		$this->pagination_next_set();
		$this->pagination_last_set();
		$this->pagination .= '</ul></nav>';
	}
	private function pagination_first_set() {
		if($this->pages_start > 1) {
			$this->pagination_loop_href_set(1);
			$this->pagination .= '<li class="WP-ml-pagination-first"><a href="'.$this->pagination_href.'"><span>&larr; First</span></a></li>';
		}
	}
	private function pagination_prev_set() {
		if($this->page > 1) {
			$this->pagination_loop_href_set($this->page-1);
			$this->pagination .= '<li class="WP-ml-pagination-prev"><a href="'.$this->pagination_href.'"><span>Previous</span></a></li>';
		}
	}
	private function pagination_next_set() {
		if($this->page < $this->pages_total) {
			$this->pagination_loop_href_set($this->page+1);
			$this->pagination .= '<li class="WP-ml-pagination-next"><a href="'.$this->pagination_href.'"><span>Next</span></a></li>';
		}
	}
	private function pagination_last_set() {
		if($this->pages_end < $this->pages_total) {
			$this->pagination_loop_href_set($this->pages_total);
			$this->pagination .= '<li class="WP-ml-pagination-last"><a href="'.$this->pagination_href.'"><span>Last &rarr;</span></a></li>';
		}
	}
	private function pagintion_loop() {
		for($this->i=$this->pages_start;$this->i<=$this->pages_end;$this->i++) {
			$this->pagination_loop_add();
		}
	}
	private function pagination_loop_add() {
		$this->pagination_loop_class_set();
		$this->pagination_loop_li_add();
		$this->pagination_loop_href_set();
		$this->pagination_loop_a_add();
		$this->pagination_loop_num_add();
		$this->pagination_loop_a_end_add();
		$this->pagination_loop_li_end_add();
	}
	private function pagination_loop_li_add() {
		$this->pagination .= '<li'.(!empty($this->pagination_class) ? ' class="'.$this->pagination_class.'"' : '').'>';
	}
	private function pagination_loop_a_add() {
		$this->pagination .= '<a class="WP-ml-pagination-item" href="'.$this->pagination_href.'">';
	}
	private function pagination_loop_num_add() {
		$this->pagination .= $this->i;
	}
	private function pagination_loop_a_end_add() {
		$this->pagination .= '</a>';
	}
	private function pagination_loop_li_end_add() {
		$this->pagination .= '</li>';
	}

	private function pagination_loop_class_set() {
		$this->pagination_class = '';
		if($this->page == $this->i) {
			$this->pagination_class = "WP-ml-active";
		}
	}
	private function pagination_loop_href_set($i=false) {
		$i = $i ? $i : $this->i;
		$s = '';
		foreach(array('search','by','type','sort','order','radius') as $v) {
			if(isset($this->$v) and !empty($this->$v)) {
				$s .= '&amp;'.$v.'='.$this->$v;
			}
		}
		$this->pagination_href = $this->url.'&ml_page='.$i.$s;
	}
	private function has_pagination() {
		if($this->pages_total > 1) {
			return true;
		}
		return false;
	}

/*------------------------------------------------------------------------------------------------
	Search
------------------------------------------------------------------------------------------------*/

	private function compile_search() {
		$this->search_form = '<div class="WP-ml-filter-form WP-ml-filter-search-form';
		if(($this->query_has_search() or $this->query_has_radius()) and !$this->query_type_is_alpha()) {
			$this->search_form .= ' WP-ml-active';
		}
		$this->search_form .= '">';
		$this->search_form .= '<form method="get" action="'.$this->url.'">';
		$this->search_form .= '<label>'.__('Search','members-list-pro').'</label>';
		if(!$this->has_search() and $this->has_radius()) {
			$this->search_form .= '<input type="hidden" name="by" value="radius" />';
		}
		elseif($this->has_search()) {
			$this->search_form .= (new tern_select)->create(array(
				'type'			=>	'paired',
				'data'			=>	$this->has_geo() ? array_merge((array)$this->search_field,array(__('Radius','members-list-pro')=>'radius')) : $this->search_field,
				'name'			=>	'by',
				'select_value'		=>	__('All Fields','members-list-pro'),
				'selected'		=>	isset($_REQUEST['by']) ? array($_REQUEST['by']) : array(),
				'localization'		=>	'members-list-pro',
			));
		}
		$this->search_form .= '<input type="text" name="search" class="form-control" value="'.$this->search.'" placeholder="'.__('Type Search...','members-list-pro').'" />';

		if($this->has_geo()) {
			$u = $this->unit;
			$this->search_form .= (new tern_select)->create(array(
				'type'			=>	'select',
				'data'			=>	array('5'.$u,'10'.$u,'25'.$u,'50'.$u,'100'.$u,'250'.$u,'500'.$u),
				'name'			=>	'radius',
				//'select_value'	=>	'Radius',
				'selected'		=>	isset($_REQUEST['radius']) ? array($_REQUEST['radius']) : array(),
				'class'			=>	($this->query_has_radius() or (!$this->has_search() and $this->has_radius())) ? 'WP-ml-active' : ''
			));
		}

		$this->search_form .= '<button type="submit" class="WP-ml-button"><span>'.__('Search','members-list-pro').'</span></button>';
		$this->search_form .= '</form>';
		$this->search_form .= '</div>';
	}
	private function compile_alpha() {
		//$a = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
		$this->alpha_form = '<div class="WP-ml-filter-form WP-ml-filter-alpha-form';
		if($this->query_type_is_alpha()) {
			$this->alpha_form .= ' WP-ml-active';
		}
		$this->alpha_form .= '">';
		$this->alpha_form .= '<label>'.__('Search Alphabetically','members-list-pro').' <span>| '.__('Last Name','members-list-pro').'</span></label>';
		$this->alpha_form .= '<ul>';
		foreach(range('A','Z') as $v) {
			$c = '';
			if(isset($this->search) and $v == $this->search) {
				$c = 'class="active"';
			}
			$this->alpha_form .= '<li><a '.$c.' href="'.$this->url.'&search='.$v.'&type=alpha&sort=last_name">'.strtoupper($v).'</a></li>';
		}
		$this->alpha_form .= '</ul>';
		$this->alpha_form .= (new tern_select)->create(array(
				'type'			=>	'select',
				'data'			=>	range('A','Z'),
				'name'			=>	'alpha',
				'select_value'	=>	'select',
				'selected'		=>	isset($_REQUEST['search']) ? array($_REQUEST['search']) : array()
			));
		$this->alpha_form .= '</div>';
	}
	/*
	private function compile_radius() {
		$this->radius_form = '<div class="WP-ml-search">';
		$this->radius_form .= '<form method="get" action="'.$this->url.'" class="form-horizontal">';
		$this->radius_form .= '<div class="form-group">';
		$this->radius_form .= '<label class="col-sm-2 control-label">Search by Zipcode</label>';
		$this->radius_form .= '<div class="col-sm-3">';
		$this->radius_form .= '<input type="text" name="byradius" class="form-control" value="'.$v.'" />';
		$this->radius_form .= '</div>';
		$this->radius_form .= '<div class="col-sm-3">';
		$this->radius_form .= (new tern_select)->create(array(
			'type'			=>	'select',
			'data'			=>	array(5,10,25,50,100,250,500),
			'name'			=>	'radius',
			'select_value'	=>	'Radius',
			'selected'		=>	array((int)$_REQUEST['radius'])
		));
		$this->radius_form .= '</div>';
		$this->radius_form .= '<div class="col-sm-3">';
		$this->radius_form .= '<button type="submit" class="btn btn-default">Search</button>';
		$this->radius_form .= '</div>';
		$this->radius_form .= '</div>';
		$this->radius_form .= '</form>';
		$this->radius_form .= '</div>';

	}
	*/
	private function compile_sort() {
		$this->sort_form = '<div class="WP-ml-sort">';
		$this->sort_form .= '<label>Sort by:</label>';
		//$this->sort_form .= '<ul>';
		foreach((array)$this->sort_field as $k => $v) {
			//unset($c,$o);
			$o = 'asc';
			$c = '';
			if(isset($this->sort) and $this->sort == $v) {
				if(isset($this->order) and $this->order == 'asc') {
					$c =  ' class="WP-ml-active WP-ml-sorted-a" ';
					$o = 'desc';
				}
				else {
					$c = ' class="WP-ml-active WP-ml-sorted-d" ';
					$o = 'asc';
				}
			}

			//$this->sort_form .= '<li'.$c.'>';
			$this->pagination_loop_href_set(1);
			$this->sort_form .= '<a '.$c.' href="'.$this->pagination_href.'&sort='.$v.'&order='.$o.'">'.__($k,'members-list-pro').'</a>';
			//$this->sort_form .= '</li>';
		}
		//$this->sort_form .= '</ul>';
		$this->sort_form .= '</div>';
	}
	private function compile_viewing() {

		$this->from = ((($this->page-1)*$this->page_per)+1);
		$this->from = $this->from > $this->total ? $this->total : $this->from;
		$this->to = $this->from+$this->page_per-1;
		$this->to = $this->to > $this->total ? $this->total : $this->to;

		$this->viewing_form = '<div class="WP-ml-view">';
		$this->viewing_form .= __('Now viewing','members-list-pro').' <b>'.$this->from.'</b> '.__('through','members-list-pro').' <b>'.$this->to.'</b> '.__('of','members-list-pro').' <b>'.$this->total.'</b> '.$this->o['noun'];

		if(isset($this->type) and $this->type == 'alpha') {
			$this->viewing_form .= ' '.__('whose last names begin with the letter','members-list-pro').' "'.strtoupper($this->search).'".';
		}

		$this->viewing_form .= '</div>';
	}

/*------------------------------------------------------------------------------------------------
	Map
------------------------------------------------------------------------------------------------*/

	private function compile_map() {
		if(!$this->has_map()) {
			return;
		}
		$i = uniqid();
		$this->map_form .= '<div class="WP-ml-map" data-id="'.$i.'"></div>';
		$this->point = array();
		foreach((array)$this->r as $v) {
			$lat = get_user_meta($v['ID'],'_lat',true);
			$lng = get_user_meta($v['ID'],'_lng',true);
			if($lat and $lng) {
				$this->point[] = array(
					'lat'	=>	$lat,
					'lng'	=>	$lng,
					'name'	=>	$this->list_first_field_value($v),
					'url'	=>	$this->has_link() ? get_author_posts_url($v['ID']) : false
				);
			}
		}
		$this->map_form .= '<script type="text/javascript">';
		$this->map_form .= 'var points_'.$i.' = '.json_encode($this->point).';';
		$this->map_form .= '</script>';
	}

/*------------------------------------------------------------------------------------------------
	Author Page
------------------------------------------------------------------------------------------------*/

	private function compile_author() {
		$this->truncate = false;
		$this->ml = '<div class="WP-ml-author">';
		$this->list_loop_add_start();
		$this->list_loop_add_gravatar(true);
		$this->list_loop_add_markup(true);
		$this->list_loop_add_end();
		//$this->list_loop();
		//$this->list_end();
		$this->ml .= '</div>';
	}

/*------------------------------------------------------------------------------------------------
	Mark-up
------------------------------------------------------------------------------------------------*/

	private function compile_list() {
		$this->list_start();
		$this->list_loop();
		$this->list_end();
	}
	private function list_start() {
		if($this->view == 'table') {
			$this->ml = '<table class="Wp-ml-table" cellpadding=0 cellspacing=0><thead><tr>';
			$this->is_first_field = true;
			foreach((array)$this->fields as $this->field_id => $this->field) {
				$this->ml .= '<th>'.$this->field['name'].'</th>';
				$this->is_first_field = false;
			}
			$this->ml .= '</tr></thead><tbody>';
		}
		else {
			$this->ml = '<div class="WP-ml-members-list WP-ml-'.$this->view.'">';
		}
	}
	private function list_end() {
		if($this->view == 'table') {
			$this->ml .= '</tbody></table>';
		}
		else {
			$this->ml .= '<div class="clearfix"></div>';
			$this->ml .= '</div>';
		}
	}
	private function list_loop() {
		foreach((array)$this->r as $this->member) {
			$this->list_loop_add();
		}
	}
	private function list_loop_add() {
		$this->list_loop_add_start();
		if($this->view != 'table') {
			$this->list_loop_add_gravatar();
		}
		$this->list_loop_add_markup();
		$this->list_loop_add_end();
	}
	private function list_loop_add_start() {

		$this->field_last = null;

		if($this->view == 'table') {
			$this->ml .= '<tr>';
		}
		else {
			$this->ml .= '<div class="WP-ml-member"><div class="WP-ml-member-box">';
		}
	}
	private function list_loop_add_end() {
		if($this->view == 'table') {
			$this->ml .= '</tr>';
		}
		else {
			$this->ml .= '<div class="clearfix"></div>';
			$this->ml .= '</div></div>';
		}
	}
	private function list_loop_add_gravatar($b=false) {
		if($this->has_gravatar() or $b) {

			$this->ml .= $this->view == 'table' ? '<td>' : '';

			$this->ml .= '<div class="WP-ml-field-img">';
			$this->ml .= $this->has_link() ? '<a href="'.get_author_posts_url($this->member['ID']).'">' : '';
			$this->ml .= get_avatar($this->member['ID'],$this->list_img_size_get());
			$this->ml .= $this->has_link() ? '</a>' : '';
			$this->ml .= '</div>';

			$this->ml .= $this->view == 'table' ? '</td>' : '';
		}
	}
	private function list_loop_add_markup() {
		if($this->view != 'table') {
			$this->ml .= '<div class="WP-ml-member-markup">';
		}

		$this->is_first_field = true;
		foreach((array)$this->fields as $this->field_id => $this->field) {

			if(!isset($this->field['field']) or empty($this->field['field'])) {
				continue;
			}

			$this->list_loop_field_close();
			$this->list_loop_field_value_set();
			$this->list_loop_field_open();
			$this->list_loop_field_label();
			$this->list_loop_field_value();

			$this->field_last = $this->field;
			$this->is_first_field = false;
		}
		$this->list_loop_field_close();

		if($this->view != 'table') {
			$this->list_loop_controls_add();
			$this->ml .= '<div class="clearfix"></div>';
			$this->ml .= '</div>';
		}
	}
	private function list_loop_controls_add() {
		if(current_user_can('manage_options')) {
			$this->ml .= '<div class="WP-ml-member-admin-controls">';

			if(isset($this->list_id) and $this->list_id) {
				$this->ml .= '<a href="'.get_admin_url().'admin.php?page=ml-edit-users&action=remove&list='.$this->list_id.'&user[]='.$this->member['ID'].'&_wpnonce='.wp_create_nonce('WP_ml_nonce').'" target="_blank" class="WP-ml-button WP-ml-remove">&#x2212;</a>';
			}

			$this->ml .= '<a href="'.get_admin_url().'user-edit.php?user_id='.$this->member['ID'].'" target="_blank" class="WP-ml-button WP-ml-edit">&#x270e;</a>';
			$this->ml .= '</div>';
		}
	}
	private function list_loop_field_open() {
		$this->open = false;
		if(empty($this->value) and $this->view != 'table') {
			return;
		}
		$this->open = true;

		//first and last names
		if($this->field['field'] == 'last_name' and $this->field_last['field'] == 'first_name' and $this->view != 'table') {
			return $this->ml .= ' ';
		}
		if($this->field['field'] == 'first_name' and $this->field_last['field'] == 'last_name' and $this->view != 'table') {
			return $this->ml .= ' ';
		}

		if($this->view == 'table') {
			$this->ml .= '<td>';
		}
		else {
			$this->ml .= '<div class="WP-ml-field WP-ml-field-'.$this->field['field'];
			$this->ml .= $this->is_first_field ? ' WP-ml-field-first' : '';
			$this->ml .= '">';
		}

		if($this->has_link() and ($this->field['field'] == 'last_name' or $this->field['field'] == 'first_name')) {
			$this->ml .= $this->has_link() ? '<a href="'.get_author_posts_url($this->member['ID']).'">' : '';
			$this->link_open = true;
		}
	}
	private function list_loop_field_close() {
		if(!$this->open) {
			return;
		}

		//first and last names
		if($this->field['field'] == 'last_name' and $this->field_last['field'] == 'first_name' and $this->view != 'table') {
			return;
		}
		if($this->field['field'] == 'first_name' and $this->field_last['field'] == 'last_name' and $this->view != 'table') {
			return;
		}

		if($this->link_open) {
			$this->ml .= '</a>';
			$this->link_open = false;
		}

		if($this->view == 'table') {
			$this->ml .= '</td>';
		}
		else {
			$this->ml .= '</div>';
		}

		$this->open = false;
	}
	private function list_loop_field_label() {
		if($this->view == 'table') {
			return;
		}
		if($this->is_first_field) {
			return;
		}

		//first and last names
		if($this->field['field'] == 'last_name' and $this->field_last['field'] == 'first_name') {
			return;
		}
		if($this->field['field'] == 'first_name' and $this->field_last['field'] == 'last_name') {
			return;
		}

		if(empty($this->value)) {
			return;
		}
		$this->ml .=  '<label class="WP-ml-label"><span>'.$this->field['name'].'</span><span class="WP-ml-label-separator">:</span></label>';
	}
	private function list_loop_field_value() {
		$this->ml .= !empty($this->value) ? '<div class="WP-ml-field-value">'.$this->value.'</div>' : '';
	}
	private function list_loop_field_value_set() {
		$this->value = $this->query_field_is_natural($this->field['field']) ? $this->member[$this->field['field']] : get_user_meta($this->member['ID'],$this->field['field']);

		//arrays as values
		if(is_array($this->value) and count($this->value) == 1) {
			$this->value = $this->value[0];
		}
		elseif(is_array($this->value)) {
			$this->value = implode(', ',$this->value);
		}

		$this->list_loop_field_value_bio();
		$this->list_loop_field_value_email();
		$this->list_loop_field_value_url();
	}

	private function list_loop_field_value_bio() {
		if($this->field['field'] == 'description' and !empty($this->value) and $this->truncate) {
			if(isset($this->field['truncate']) and (int)$this->field['truncate'] === 1) {

				if($this->view == 'table') {
					$this->value = Html::trim($this->value,25);
				}
				else {
					$this->value = Html::trim($this->value,200);
				}
			}

			if($this->view != 'table') {
				$this->value .= $this->has_link() ? '<br /><a href="'.get_author_posts_url($this->member['ID']).'" class="WP-ml-button">&rarr;</a>' : '';
			}
		}
	}
	private function list_loop_field_value_email() {
		if($this->field['field'] == 'user_email' and !empty($this->value)) {
			$this->value = '<a href="mailto:'.$this->value.'">'.$this->value.'</a>';
		}
	}
	private function list_loop_field_value_url() {
		if($this->field['field'] == 'user_url' and !empty($this->value)) {
			$this->value = '<a href="'.$this->value.'" target="_blank">'.$this->value.'</a>';
		}
	}

	private function list_loop_field_value_get($f='',$u=false) {
		$u = !$u ? $this->member : $u;
		return $this->query_field_is_natural($f) ? $u[$f] : get_user_meta($u['ID'],$f,true);
	}
	private function list_first_field_value($u) {
		$s = '';
		if(isset($this->list['fields'][0]['field'])) {
			$s = $this->list_loop_field_value_get($this->list['fields'][0]['field'],$u);
			if($this->list['fields'][0]['field'] == 'first_name' and isset($this->list['fields'][1]['field']) and $this->list['fields'][1]['field'] == 'last_name') {
				$s .= ' '.$this->list_loop_field_value_get($this->list['fields'][1]['field'],$u);
			}
		}
		return $s;
	}


/*------------------------------------------------------------------------------------------------
	Miscellaneous
------------------------------------------------------------------------------------------------*/

	function sanitize($s) {

		if(extension_loaded('PDO')) {
			$p = new PDO('mysql:dbname='.DB_NAME.';host='.DB_HOST,DB_USER,DB_PASSWORD);
			return substr($p->quote($s),1,strlen($p->quote($s))-2);
		}
		elseif(function_exists('mysql_real_escape_string')) {
			return mysql_real_escape_string($s);
		}
		return $s;
	}

}

/****************************************Terminate Script******************************************/
?>
