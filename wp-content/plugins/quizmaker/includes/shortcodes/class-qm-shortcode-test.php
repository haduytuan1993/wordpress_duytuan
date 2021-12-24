<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

class QM_Shortcode_Test {
	
	public static function get( $atts ) {
		
		return QM_Shortcodes::shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}

	public static function output( $atts ){
		
		$atts = shortcode_atts( array(
			'id'		=>	'',
			'slug' 		=>	''
		), $atts );

		if ( ! $atts['id'] && ! $atts['slug'] ) {
			return '';
		}

		if( !$atts['id'] && $atts['slug'] ) {

			$test_object = get_page_by_path( $atts['slug'], OBJECT, 'test' );

			if( $test_object ){

				$atts['id'] = $test_object->ID;

			}else{

				return '';
			}
		}

		$test 	=	new QM_Test( absint( $atts['id'] ) );

		if( !$test->post ) return false;

		$params	=	array(
			'is_can_do_test'	=>	qm_can_do_test( $test->id ),
			'test_id'			=>	$test->id,
		);

		$params['test_info']	=	apply_filters( 'quizmaker_single_test_data_info', array(

				array( 
					'type'		=>	'text',
					'label'		=>	__('Duration', 'quizmaker'), 
					'value'	 	=> $test->get_duration() ),
				array( 
					'type'		=>	'text',
					'label'		=>	__('Attempt', 'quizmaker'), 
					'value'		=> $test->get_attempt() ),
				array( 
					'type'		=>	'text',
					'label'		=>	__('Questions', 'quizmaker'), 
					'value'		=>	count($test->get_questions()))

			), $test->id );

		if( $captcha = is_quizmaker_enable_captcha() ){

			array_push( $params['test_info'], array( 'type' => 'recaptcha', 'value' => $captcha['key'] ) );
		}
		
		qm_get_template( 'single-test/start.php', $params );
	}
}