<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QM_Widget_Test_Categories extends QM_Widget {

	/**
	 * Category ancestors.
	 *
	 * @var array
	 */
	public $cat_ancestors;

	/**
	 * Current Category.
	 *
	 * @var bool
	 */
	public $current_cat;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'quizmaker widget_test_categories';
		$this->widget_description = __( 'A list or dropdown of product categories.', 'quizmaker' );
		$this->widget_id          = 'quizmaker_test_categories';
		$this->widget_name        = __( 'Quizmaker Test Categories', 'quizmaker' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Test Categories', 'quizmaker' ),
				'label' => __( 'Title', 'quizmaker' )
			),			
			'count' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Show test counts', 'quizmaker' )
			),
			'hierarchical' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Show hierarchy', 'quizmaker' )
			),
			'show_children_only' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Only show children of the current category', 'quizmaker' )
			),
			'hide_empty' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Hide empty categories', 'quizmaker' )
			)
		);

		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		global $wp_query, $post;

		$count              = isset( $instance['count'] ) ? $instance['count'] : $this->settings['count']['std'];
		$hierarchical       = isset( $instance['hierarchical'] ) ? $instance['hierarchical'] : $this->settings['hierarchical']['std'];
		$show_children_only = isset( $instance['show_children_only'] ) ? $instance['show_children_only'] : $this->settings['show_children_only']['std'];
		// $orderby            = isset( $instance['orderby'] ) ? $instance['orderby'] : $this->settings['orderby']['std'];
		$hide_empty         = isset( $instance['hide_empty'] ) ? $instance['hide_empty'] : $this->settings['hide_empty']['std'];
		$dropdown_args      = array( 'hide_empty' => $hide_empty );
		$list_args          = array( 'show_count' => $count, 'hierarchical' => $hierarchical, 'taxonomy' => 'test_cat', 'hide_empty' => $hide_empty );
		$layout		        = 'list-layout';

		$orderby			=	'';
		
		// Menu Order
		$list_args['menu_order'] = false;
		if ( $orderby == 'order' ) {
			$list_args['menu_order'] = 'asc';
		} else {
			$list_args['orderby']    = 'title';
		}

		// Setup Current Category
		$this->current_cat   = false;
		$this->cat_ancestors = array();

		if ( is_tax( 'test_cat' ) ) {

			$this->current_cat   = $wp_query->queried_object;
			$this->cat_ancestors = get_ancestors( $this->current_cat->term_id, 'test_cat' );

		} // elseif ( is_singular( 'test' ) ) {
 //
 // 			$test_category = wc_get_product_terms( $post->ID, 'test_cat', array( 'orderby' => 'parent' ) );
 //
 // 			if ( $test_category ) {
 // 				$this->current_cat   = end( $test_category );
 // 				$this->cat_ancestors = get_ancestors( $this->current_cat->term_id, 'test_cat' );
 // 			}
 //
 // 		}

		// Show Siblings and Children Only
		if ( $show_children_only && $this->current_cat ) {

			// Top level is needed
			$top_level = get_terms(
				'test_cat',
				array(
					'fields'       => 'ids',
					'parent'       => 0,
					'hierarchical' => true,
					'hide_empty'   => false
				)
			);

			// Direct children are wanted
			$direct_children = get_terms(
				'test_cat',
				array(
					'fields'       => 'ids',
					'parent'       => $this->current_cat->term_id,
					'hierarchical' => true,
					'hide_empty'   => false
				)
			);

			// Gather siblings of ancestors
			$siblings  = array();
			if ( $this->cat_ancestors ) {
				foreach ( $this->cat_ancestors as $ancestor ) {
					$ancestor_siblings = get_terms(
						'test_cat',
						array(
							'fields'       => 'ids',
							'parent'       => $ancestor,
							'hierarchical' => false,
							'hide_empty'   => false
						)
					);
					$siblings = array_merge( $siblings, $ancestor_siblings );
				}
			}

			if ( $hierarchical ) {
				$include = array_merge( $top_level, $this->cat_ancestors, $siblings, $direct_children, array( $this->current_cat->term_id ) );
			} else {
				$include = array_merge( $direct_children );
			}

			$dropdown_args['include'] = implode( ',', $include );
			$list_args['include']     = implode( ',', $include );

			if ( empty( $include ) ) {
				return;
			}

		} elseif ( $show_children_only ) {
			$dropdown_args['depth']        = 1;
			$dropdown_args['child_of']     = 0;
			$dropdown_args['hierarchical'] = 1;
			$list_args['depth']            = 1;
			$list_args['child_of']         = 0;
			$list_args['hierarchical']     = 1;
		}

		$this->widget_start( $args, $instance );

		include_once( QM()->plugin_path() . '/includes/walkers/class-test-cat-list-walker.php' );

		$list_args['walker']                     = new QM_Test_Cat_List_Walker;
		$list_args['title_li']                   = '';
		$list_args['pad_counts']                 = 1;
		$list_args['show_option_none']           = __('No test categories exist.', 'quizmaker' );
		$list_args['current_category']           = ( $this->current_cat ) ? $this->current_cat->term_id : '';
		$list_args['current_category_ancestors'] = $this->cat_ancestors;

		echo '<div class="content"><ul class="list-group test-categories ' . $layout . '">';

		wp_list_categories( apply_filters( 'quizmaker_test_categories_widget_args', $list_args ) );

		echo '</ul></div>';


		
		$this->widget_end( $args );
	}
}
