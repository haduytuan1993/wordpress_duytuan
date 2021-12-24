<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'QM_Query' ) ) :
	
	class QM_Query {
		public $query_vars = array();
		
		public function __construct() {
			
			add_action( 'init', array( $this, 'add_endpoints' ) );
			
			if ( ! is_admin() ) {
				
				add_filter( 'query_vars', array( $this, 'add_query_vars'), 0 );
				add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
				
			}
			
			$this->init_query_vars();
		}
		
		public function init_query_vars() {
			
			
		}
		
		public function add_query_vars( $vars ) {
			
			$vars[]	=	'start';
			$vars[]	=	'doing';
			$vars[]	=	'doing-form';
			$vars[]	=	'result';
			$vars[]	=	'ranking';
			$vars[]	=	'qm-lost-password';
			$vars[]	=	'reset-password';
			$vars[]	=	'member-logout';
			$vars[]	=	'view-edit-account';
			$vars[]	=	'view-assigned-tests';
			$vars[]	=	'view-results';
			$vars[]	=	'view-result';
			$vars[]	=	'view-certificates';
			$vars[]	=	'view-certificate';
			$vars[]	=	'view-activation';

			return $vars;
		}
		
		public function add_endpoints() {

			add_rewrite_endpoint('start', EP_PERMALINK);
			add_rewrite_endpoint('doing', EP_PERMALINK);
			add_rewrite_endpoint('doing-form', EP_PERMALINK);
			add_rewrite_endpoint('result', EP_PERMALINK);
			add_rewrite_endpoint('ranking', EP_PERMALINK);
			add_rewrite_endpoint('view-certificates', EP_ROOT | EP_PAGES );
			add_rewrite_endpoint('view-certificate', EP_PERMALINK );
			add_rewrite_endpoint('member-logout', EP_ROOT | EP_PAGES );
			add_rewrite_endpoint('qm-lost-password', EP_ROOT | EP_PAGES );
			add_rewrite_endpoint('reset-password', EP_ROOT | EP_PAGES );
			add_rewrite_endpoint('view-edit-account', EP_ROOT | EP_PAGES );
			add_rewrite_endpoint('view-assigned-tests', EP_ROOT | EP_PAGES );
			add_rewrite_endpoint('view-results', EP_ROOT | EP_PAGES );
			add_rewrite_endpoint('view-result', EP_ROOT | EP_PAGES );
			add_rewrite_endpoint('view-activation', EP_ROOT | EP_PAGES );
			
			flush_rewrite_rules();
		}
		
		public function pre_get_posts( $q ) {
			
			if ( ! $q->is_main_query() ) {
				return;
			}
						
			if ( ! $q->is_post_type_archive( 'test' ) && ! $q->is_tax( get_object_taxonomies( 'test' ) ) ) {
						return;
			}
			
			$this->test_query( $q );
			
		}
		
		public function test_query( $q ) {
			
			$meta_query = $this->get_meta_query( $q->get( 'meta_query' ) );
						
			// Ordering
			$ordering   = $this->gett_test_ordering_args();

			// Ordering query vars
			$q->set( 'orderby', $ordering['orderby'] );
			$q->set( 'order', $ordering['order'] );
			if ( isset( $ordering['meta_key'] ) ) {
				$q->set( 'meta_key', $ordering['meta_key'] );
			}
			
			$q->set( 'meta_query', $meta_query );
			
			$q->set( 'posts_per_page', $q->get( 'posts_per_page' ) ? $q->get( 'posts_per_page' ) : apply_filters( 'loop_test_per_page', get_option( 'quizmaker_posts_per_page' ) ) );
			
			// Set a special variable
			$q->set( 'qm_query', 'test_query' );

			do_action( 'quizmaker_test_query', $q, $this );
		}
		
		public function gett_test_ordering_args( $orderby = '', $order = '' ) {
			global $wpdb;

			// Get ordering from query string unless defined
			if ( ! $orderby ) {
				$orderby_value = isset( $_GET['orderby'] ) ? qm_clean( $_GET['orderby'] ) : apply_filters( 'quizmaker_default_test_orderby', get_option( 'quizmaker_default_test_orderby' ) );

				// Get order + orderby args from string
				$orderby_value = explode( '-', $orderby_value );
				$orderby       = esc_attr( $orderby_value[0] );
				$order         = ! empty( $orderby_value[1] ) ? $orderby_value[1] : $order;
			}

			$orderby = strtolower( $orderby );
			$order   = strtoupper( $order );
			$args    = array();

			// default - menu_order
			$args['orderby']  = 'menu_order title';
			$args['order']    = $order == 'DESC' ? 'DESC' : 'ASC';
			$args['meta_key'] = '';
			
			switch ( $orderby ) {
				case 'rand' :
					$args['orderby']  = 'rand';
				break;
				case 'date' :
					$args['orderby']  = 'date ID';
					$args['order']    = $order == 'ASC' ? 'ASC' : 'DESC';
				break;
				case 'duration' :
					$args['orderby']  = "meta_value_num ID";
					$args['order']    = $order == 'DESC' ? 'DESC' : 'ASC';
					$args['meta_key'] = '_duration';
				break;
				case 'played' :
					$args['orderby']  = "meta_value_num ID";
					$args['order']    = $order == 'DESC' ? 'DESC' : 'ASC';
					$args['meta_key'] = '_played';
				break;
				case 'title' :
					$args['orderby']  = 'title';
					$args['order']    = $order == 'DESC' ? 'DESC' : 'ASC';
				break;
			}

			return apply_filters( 'quizmaker_get_catalog_ordering_args', $args );
		}
		
		public function get_meta_query( $meta_query = array() ) {
			if ( ! is_array( $meta_query ) )
				$meta_query = array();

			//$meta_query[] = $this->membership_meta_query();

			return array_filter( $meta_query );
		}
	}
	
endif;

return new QM_Query();