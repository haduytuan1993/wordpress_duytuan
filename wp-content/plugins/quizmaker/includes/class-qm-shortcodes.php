<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

class QM_Shortcodes {
	
	public static function init() {
		$shortcodes = array(
			'quizmaker_result_test'		=>	__CLASS__ . '::result_test',
			'quizmaker_my_account'		=>	__CLASS__ . '::my_account',
			'quizmaker_register'		=>	__CLASS__ . '::register',
			'quizmaker_login'			=>	__CLASS__ . '::login',
			'quizmaker_test'			=>	__CLASS__ . '::test',
			'quizmaker_tests'			=>	__CLASS__ . '::tests',
			'quizmaker_test_category'	=>	__CLASS__ . '::test_category',
			'quizmaker_viral'			=>	__CLASS__ . '::viral',
			'quizmaker_quiz'			=>	__CLASS__ . '::quiz',
		);
		
		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}
		
	}
	
	public static function shortcode_wrapper(
		
		$function,
		$atts    = array(),
		$wrapper = array(
			'class'  => 'quizmaker',
			'before' => null,
			'after'  => null
		)
	) {
		ob_start();
		
		echo empty( $wrapper['before'] ) ? '<div class="' . esc_attr( $wrapper['class'] ) . '">' : $wrapper['before'];
		call_user_func( $function, $atts );
		echo empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];
		
		return ob_get_clean();
	}
	
	private static function test_loop( $atts ) {
		
		$query_args	=	wp_parse_args( $atts, array(
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'post_type'      => 'test',
			'no_found_rows'  => 1,
			'orderby'		 =>	'title',
			'order'			 =>	'asc',
			'meta_query'     => array(
				array( 'key' => '_publish_for', 'value' => 0 )
			)
		) );
		
		$tests	=	new WP_Query( $query_args );
		
		ob_start();
		
		if($tests->have_posts()){ 
			while ( $tests->have_posts() ) : $tests->the_post(); 
				qm_get_template_part( 'content', 'test' );
			endwhile; 
		}
		
		wp_reset_postdata();

		return '<ul class="list-tests">' . ob_get_clean() . '</ul>';
	}
	
	public static function result_test() {
		return self::shortcode_wrapper( array( 'QM_Shortcode_Result_Test', 'output' ) );
	}
	
	public static function my_account() {
		return self::shortcode_wrapper( array( 'QM_Shortcode_My_Account', 'output' ) );
	}
	
	public static function register() {
		return self::shortcode_wrapper( array( 'QM_Shortcode_Register', 'output' ) );
	}
	
	public static function login() {
		return self::shortcode_wrapper( array( 'QM_Shortcode_Login', 'output' ) );
	}
	
	public static function test( $atts ) {

		return self::shortcode_wrapper( array( 'QM_Shortcode_Test', 'output' ), $atts );
	}
	
	public static function viral( $atts ) {

		return self::shortcode_wrapper( array( 'QM_Shortcode_Viral', 'output' ), $atts );
	}

	public static function quiz( $atts ) {

		return self::shortcode_wrapper( array( 'QM_Shortcode_Quiz', 'output' ), $atts );
	}

	public static function tests( $atts ) {
		
		$atts = shortcode_atts( array(
			'ids'		=>	'',
			'columns'	=>	4,
			'orderby'	=>	'title',
			'order'		=>	'asc'
		), $atts );
		
		if ( ! $atts['ids'] ) {
			$atts['ids'] = [];
		}else{

			$atts['ids'] = explode(',', $atts['ids']);
		}
		
		$args	=	array(
			'post__in'	=>	$atts['ids'],
			'orderby'	=>	$atts['orderby'],
			'order'		=>	$atts['order']
		);
			
		return self::test_loop( $args );
	}
	
	public static function test_category( $atts ) {
		
		$atts = shortcode_atts( array(
			'category'	=>	'',
			'orderby'	=>	'title',
			'order'		=>	'asc'
		), $atts );
		
		if ( ! $atts['category'] ) {
			return '';
		}
		
		$args	=	array(
			'orderby'		=>	$atts['orderby'],
			'order'			=>	$atts['order'],
			'tax_query'	=>	array(
				array(
					'taxonomy'	=>	'test_cat',
					'field'		=>	'slug',
					'terms'		=>	$atts['category']
				)
			)
		);
		
		return self::test_loop( $args );
	}
}