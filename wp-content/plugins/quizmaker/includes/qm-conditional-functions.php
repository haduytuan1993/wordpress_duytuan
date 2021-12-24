<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function is_quizmaker() {
	return apply_filters( 'is_quizmaker', ( is_quiz() || is_test_taxonomy() || is_test() ) ? true : false );
}

if ( ! function_exists( 'is_quiz' ) ) {

	function is_quiz() {
		return ( is_post_type_archive( 'test' ) || is_page( qm_get_page_id( 'archive_test' ) ) );
	}
}

if ( ! function_exists( 'is_myaccount' ) ) {

	function is_myaccount() {
		return is_page( qm_get_page_id( 'myaccount' ) ) || defined( 'QUIZMAKER_MYACCOUNT' );
	}
}

if ( ! function_exists( 'is_test_taxonomy' ) ) {

	function is_test_taxonomy() {
		return is_tax( get_object_taxonomies( 'test' ) );
	}
}

if ( ! function_exists( 'is_test_category' ) ) {

	function is_test_category( $term = '' ) {
		return is_tax( 'test_cat', $term );
	}
}

if ( ! function_exists( 'is_test_tag' ) ) {

	function is_test_tag( $term = '' ) {
		return is_tax( 'test_tag', $term );
	}
}

if ( ! function_exists( 'is_test' ) ) {

	function is_test() {
		return is_singular( array( 'test' ) );
	}
}

if ( ! function_exists( 'is_question' ) ) {

	function is_question() {
		return is_singular( array( 'question' ) );
	}
}

if ( ! function_exists( 'is_start' ) ) {
	function is_start() {
		global $wp_the_query;
		
		return isset($wp_the_query->query_vars['start']);
	}
}

if ( ! function_exists( 'is_doing' ) ) {
	function is_doing() {
		global $wp_the_query;
		
		return isset($wp_the_query->query_vars['doing']);
	}
}

if ( ! function_exists( 'is_doing_form' ) ) {
	function is_doing_form() {
		global $wp_the_query;
		
		return isset($wp_the_query->query_vars['doing-form']);
	}
}

if ( ! function_exists( 'is_result' ) ) {
	function is_result() {
		global $wp_the_query;
		
		return isset($wp_the_query->query_vars['result']);
	}
}

if ( ! function_exists( 'is_certificate' ) ) {
	function is_certificate() {
		global $wp_the_query;
		
		return isset($wp_the_query->query_vars['view-certificate']);
	}
}

if ( ! function_exists( 'is_ranking' ) ) {
	function is_ranking() {
		global $wp_the_query;
		
		return isset($wp_the_query->query_vars['ranking']);
	}
}

if ( ! function_exists( 'is_play_test_as_guest' ) ) {
	function is_play_test_as_guest() {
		
		$result = get_option('quizmaker_is_play_test_as_guest');

		if( $result == 'yes' ) {

			return true;
			
		}else{

			return false;
		}
	}
}

if ( ! function_exists( 'is_user_fillform_setting' ) ) {
	function is_user_fillform_setting() {
		
		$result = get_option('quizmaker_is_user_fillform_setting');

		if( $result == 'yes' ) {

			return true;
			
		}else{

			return false;
		}
	}
}

if ( ! function_exists( 'is_user_ranking_view_result_setting' ) ) {
	function is_user_ranking_view_result_setting() {
		
		$result = get_option('quizmaker_is_user_ranking_view_result_setting');

		if( $result == 'yes' ) {

			return true;
			
		}else{

			return false;
		}
	}
}

if ( ! function_exists( 'is_email_result' ) ) {
	function is_email_result( $test_id ) {
		
		if(!$test_id || qm_is_user_guest()) return false;
		
		$settings	=	qm_test_get_settings($test_id);
		
		return $settings['is_email_result'];
	}
}

if ( ! function_exists( 'is_show_badge_memberships' ) ) {
	function is_show_badge_memberships() {
		
		$is_show	=	qm_get_setting('is_show_badge_memberships');
		
		return $is_show == 'yes' ? true : false;
	}
}

if ( ! function_exists( 'is_quizmaker_container_class' ) ) {
	function is_quizmaker_container_class() {
		$is_left_sidebar	=	is_active_sidebar( 'quizmaker-left-sidebar' );
		$is_right_sidebar	=	is_active_sidebar( 'quizmaker-right-sidebar' );
		
		$container			=	'aws-sm-12';
		
		if( $is_left_sidebar && !$is_right_sidebar ) {
			$container	=	'aws-sm-9 last';
		}else if( !$is_left_sidebar && $is_right_sidebar ) {
			$container	=	'aws-sm-9';
		}else if( $is_left_sidebar && $is_right_sidebar ) {
			$container	=	'aws-sm-6';
		}
		
		return $container;
	}
}

function is_quizmaker_enable_captcha() {
	$is_enabled		=	get_option('quizmaker_is_captcha_on_test');
	$captcha_key	=	get_option('quizmaker_is_captcha_key');
	$captcha_secret	=	get_option('quizmaker_is_captcha_secret');
	
	if( $is_enabled == 'yes' && $captcha_key && $captcha_secret ) {
		
		return array( 'key' => $captcha_key, 'secret' => $captcha_secret );
	}
	
	return false;
}

function qm_is_user_guest(){
	$user_id	=	get_current_user_id();
	
	$is_guest = get_user_meta( $user_id, 'is_guest', true );
	
	return $is_guest == 1 ? true : false;
}