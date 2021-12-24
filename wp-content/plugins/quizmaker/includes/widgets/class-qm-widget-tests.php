<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QM_Widget_Tests extends QM_Widget {
	
	public function __construct() {
		$this->widget_cssclass    = 'quizmaker widget_tests';
		$this->widget_description = __( 'Display a list of your products on your site.', 'quizmaker' );
		$this->widget_id          = 'quizmaker_tests';
		$this->widget_name        = __( 'Quizmaker Tests', 'quizmaker' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Tests', 'quizmaker' ),
				'label' => __( 'Title', 'quizmaker' )
			),
			'category'	=>	array(
				'std'	=>	'',
				'type'	=>	'test_category',
				'label' => __( 'Category', 'quizmaker' )
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 5,
				'label' => __( 'Number of tests to show', 'quizmaker' )
			),
			'show' => array(
				'type'  => 'select',
				'std'   => '',
				'label' => __( 'Show', 'quizmaker' ),
				'options' => array(
					''         => __( 'All Tests', 'quizmaker' ),
					'featured' => __( 'Featured Tests', 'quizmaker' ),
				)
			),
			'orderby' => array(
				'type'  => 'select',
				'std'   => 'date',
				'label' => __( 'Order by', 'quizmaker' ),
				'options' => array(
					'date'   => __( 'Date', 'quizmaker' ),
					'rand'   => __( 'Random', 'quizmaker' ),
				)
			),
			'order' => array(
				'type'  => 'select',
				'std'   => 'desc',
				'label' => _x( 'Order', 'Sorting order', 'quizmaker' ),
				'options' => array(
					'asc'  => __( 'ASC', 'quizmaker' ),
					'desc' => __( 'DESC', 'quizmaker' ),
				)
			),
			'display_title' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Display Title', 'quizmaker' )
			),
			'display_thumbnail' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Display Thumbnail', 'quizmaker' )
			),
			'display_date' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Display Date', 'quizmaker' )
			),
			'display_description' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Display Description', 'quizmaker' )
			),
			'display_rating' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Display Rating', 'quizmaker' )
			),
		);
		
		parent::__construct();
	}
	
	public function get_tests( $args, $instance ) {
		$number  	= ! empty( $instance['number'] ) ? absint( $instance['number'] ) : $this->settings['number']['std'];
		$show    	= ! empty( $instance['show'] ) ? sanitize_title( $instance['show'] ) : $this->settings['show']['std'];
		$orderby 	= ! empty( $instance['orderby'] ) ? sanitize_title( $instance['orderby'] ) : $this->settings['orderby']['std'];
		$order   	= ! empty( $instance['order'] ) ? sanitize_title( $instance['order'] ) : $this->settings['order']['std'];
		$category	= ! empty( $instance['category'] ) ? absint( $instance['category'] ) : $this->settings['category']['std'];
		
		$query_args = array(
			'posts_per_page' => $number,
			'post_status'    => 'publish',
			'post_type'      => 'test',
			'no_found_rows'  => 1,
			'order'          => $order,
			'meta_query'     => array()
		);
		
		$query_args['meta_query']   = array_filter( $query_args['meta_query'] );
		
		switch ( $show ) {
			case 'featured' :
				$query_args['meta_query'][] = array(
					'key'   => '_featured',
					'value' => 'yes'
				);
				break;
		}
		
		switch ( $orderby ) {
			case 'rand' :
				$query_args['orderby']  = 'rand';
				break;
			default :
				$query_args['orderby']  = 'date';
		}
		
		if($category) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy'	=>	'test_cat',
					'field'		=>	'id',
					'terms'		=>	$category
				)
			);
		}
		
		return new WP_Query( apply_filters( 'quizmaker_tests_widget_query_args', $query_args ) );
	}
	
	public function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) ) {
			return;
		}
		
		ob_start();

		if ( ( $tests = $this->get_tests( $args, $instance ) ) && $tests->have_posts() ) {

			if( !isset($instance['display_rating']) || get_option('quizmaker_is_rating') == 'no' ) {

				$instance['display_rating'] = 0;

			}

			$this->widget_start( $args, $instance );
			
			$layout	=	isset($instance['layout']) ? $instance['layout'] : 'thumbnail-layout';

			echo apply_filters( 'quizmaker_before_widget_test_list', '<div class="content"><div class="list-group test_list_widget ' . $layout . '">' );

			while ( $tests->have_posts() ) {
				$tests->the_post();
				qm_get_template( 'content-widget-test.php', $instance );
			}
			
			echo apply_filters( 'quizmaker_after_widget_test_list', '</div></div>' );

			$this->widget_end( $args );
		}
		
		wp_reset_postdata();

		echo $this->cache_widget( $args, ob_get_clean() );
	}
}