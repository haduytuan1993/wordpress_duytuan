<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


add_filter( 'body_class', 'qm_body_class' );

add_action( 'quizmaker_before_main_content', 		'quizmaker_output_content_wrapper_start', 10 );
add_action( 'quizmaker_after_main_content', 		'quizmaker_output_content_wrapper_end', 10 );

add_action( 'quizmaker_quiz_loop_item_summary', 'quizmaker_template_loop_test_thumbnail', 10 );

add_action( 'quizmaker_quiz_loop_item_summary', 'quizmaker_template_loop_test_wrapper_desc_start', 10);
add_action( 'quizmaker_quiz_loop_item_summary', 'quizmaker_template_loop_test_category', 10 );
add_action( 'quizmaker_quiz_loop_item_summary', 'quizmaker_template_loop_test_title', 10 );
add_action( 'quizmaker_quiz_loop_item_summary', 'quizmaker_template_loop_test_info', 10 );
add_action( 'quizmaker_quiz_loop_item_summary', 'quizmaker_template_loop_test_excerpt', 10 );
add_action( 'quizmaker_quiz_loop_item_summary', 'quizmaker_template_loop_test_action', 10);
add_action( 'quizmaker_quiz_loop_item_summary', 'quizmaker_template_loop_test_wrapper_desc_end', 10);

add_action( 'quizmaker_test_start', 'quizmaker_template_test_start', 10);
add_action( 'quizmaker_test_doing', 'quizmaker_template_test_doing', 10);

add_action( 'quizmaker_single_test_result_summary', 'quizmaker_template_single_test_result_summary', 10);

add_action( 'quizmaker_after_show_result', 'quizmaker_template_remove_guest', 10 );
add_filter( 'quizmaker_before_show_result', 'quizmaker_template_before_show_result_is_guest', 10, 3 );


add_action( 'quizmaker_single_test_summary', 'quizmaker_template_single_test_title', 10);
add_action( 'quizmaker_single_test_summary', 'quizmaker_template_single_test_content', 10);
add_action( 'quizmaker_single_test_summary', 'quizmaker_template_single_test_start', 10);
add_action( 'quizmaker_single_test_summary', 'quizmaker_template_single_test_ranking', 10);
add_action( 'quizmaker_single_test_summary', 'quizmaker_template_single_test_review', 10);

add_action( 'quizmaker_single_result_info', 'quizmaker_template_single_result_rating', 10, 2 );

add_filter( 'quizmaker_wrap_before_nav', 'quizmaker_template_filter_wrap_before_nav', 10);
add_filter( 'quizmaker_wrap_after_nav', 'quizmaker_template_filter_wrap_after_nav', 10);

add_action( 'quizmaker_before_test_loop', 'quizmaker_catalog_ordering', 10);

add_action( 'quizmaker_after_test_loop', 'quizmaker_pagination', 10 );

add_action( 'quizmaker_before_my_account', 'quizmaker_template_before_my_account', 10);
add_action( 'quizmaker_after_my_account', 'quizmaker_template_after_my_account', 10);

add_action( 'quizmaker_after_submit_result', 'quizmaker_template_update_user_score', 10, 2 );
add_action( 'quizmaker_after_submit_result', 'quizmaker_template_update_user_cert_id', 10, 2 );

add_filter( 'quizmaker_my_account_tabs_title', 'quizmaker_template_my_account_tabs_title', 10, 1 );
add_action( 'quizmaker_my_account_user_info', 'quizmaker_template_my_account_user_info', 10, 1 );

add_action( 'quizmaker_left_sidebar', 'quizmaker_get_left_sidebar', 10 );
add_action( 'quizmaker_right_sidebar', 'quizmaker_get_right_sidebar', 10 );

add_filter( 'quizmaker_single_test_start_test_info', 'quizmaker_template_single_test_start_test_info', 10, 2 );
add_filter( 'quizmaker_single_test_data_info', 'quizmaker_template_single_test_start_data_info_adaptive', 10, 2);
add_filter( 'quizmaker_single_test_data_info', 'quizmaker_template_single_test_start_data_info_infinite', 10, 2);


add_filter( 'quizmaker_can_store_result', 'quizmaker_template_can_store_result', 10, 3 );
add_filter( 'quizmaker_doing_submit_redirect', 'quizmaker_template_show_user_fill_form' );

add_action( 'quizmaker_before_doing', 'quizmaker_template_header_doing', 10, 1 );
add_action( 'quizmaker_after_doing', 'quizmaker_template_navigate_doing', 10, 1 );