<?php

function qm_get_template_part( $slug, $name = '' ) {
	$template = '';

	// Look in yourtheme/slug-name.php and yourtheme/woocommerce/slug-name.php
	if ( $name ) {
		$template = locate_template( array( "{$slug}-{$name}.php", QM()->template_path() . "{$slug}-{$name}.php" ) );
	}
	
	// Get default slug-name.php
	if ( ! $template && $name && file_exists( QM()->plugin_path() . "/templates/{$slug}-{$name}.php" ) ) {
		$template = QM()->plugin_path() . "/templates/{$slug}-{$name}.php";
	}
	
	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/woocommerce/slug.php
	if ( ! $template ) {
		$template = locate_template( array( "{$slug}.php", QM()->template_path() . "{$slug}.php" ) );
	}

	// Allow 3rd party plugins to filter template file from their plugin.
	$template = apply_filters( 'qm_get_template_part', $template, $slug, $name );

	if ( $template ) {
		load_template( $template, false );
	}
}

function qm_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( $args && is_array( $args ) ) {
		extract( $args );
	}

	$located = qm_locate_template( $template_name, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		// _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
		return;
	}

	$located = apply_filters( 'qm_get_template', $located, $template_name, $args, $template_path, $default_path );

	do_action( 'quizmaker_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'quizmaker_after_template_part', $template_name, $template_path, $located, $args );
}

function qm_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = QM()->template_path();
	}

	if ( ! $default_path ) {
		$default_path = QM()->plugin_path() . '/templates/';
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);
	
	// Get default template/
	if ( ! $template ) {
		$template = $default_path . $template_name;
	}

	// Return what we found.
	return apply_filters( 'quizmaker_locate_template', $template, $template_name, $template_path );
}

function qm_setup_test_data( $post ) {
	unset( $GLOBALS['test'] );

	if ( is_int( $post ) )
		$post = get_post( $post );
	
	if ( empty( $post->post_type ) || ! in_array( $post->post_type, array( 'test' ) ) )
		return;

	$GLOBALS['test'] = qm_get_test( $post );

	return $GLOBALS['test'];
}
add_action( 'the_post', 'qm_setup_test_data' );

function qm_body_class( $classes ) {
	$classes = (array) $classes;
	
	if ( is_quizmaker() ) {
		$classes[] = 'quizmaker';
		$classes[] = 'quizmaker-page';
	}
	
	return array_unique( $classes );
}
 
	
function quizmaker_container_class( $class = '' ) {
	echo 'class="' . join( ' ', quizmaker_get_container_class( $class ) ) . '"';
}

function quizmaker_get_container_class( $class = '' ) {
	
	$classes	=	[];
	
	if( !$class ) {
		if( is_array( $class ) ){
			
			$classes	=	array_merge($classes, $class);
		}else{
			
			array_push( $classes, $class );
			$classes	=	array_unique( $classes );
		}
	}
	
	return apply_filters('quizmaker_container_class', $classes, $class );
}

function quizmaker_container_column_class( $classes = array(), $class = '' ){
	
	$is_left_sidebar	=	is_active_sidebar( 'quizmaker-left-sidebar' );
	$is_right_sidebar	=	is_active_sidebar( 'quizmaker-right-sidebar' );
	
	$container_class	=	'col';
	
	if( $is_left_sidebar && !$is_right_sidebar ) {
		$container_class	=	'col-md-9 last';
	}else if( !$is_left_sidebar && $is_right_sidebar ) {
		$container_class	=	'col-md-9';
	}else if( $is_left_sidebar && $is_right_sidebar ) {
		$container_class	=	'col-sm-6';
	}	
	
	array_push( $classes, $container_class );
	
	return array_unique( $classes );
}
add_filter('quizmaker_container_class', 'quizmaker_container_column_class', 10, 2);

if ( ! function_exists( 'quizmaker_get_left_sidebar' ) ) {
	
	function quizmaker_get_left_sidebar() {
		qm_get_template( 'global/left-sidebar.php' );
	}
}

if ( ! function_exists( 'quizmaker_get_right_sidebar' ) ) {
	
	function quizmaker_get_right_sidebar() {
		qm_get_template( 'global/right-sidebar.php' );
	}
}

if ( ! function_exists( 'quizmaker_output_content_wrapper_start' ) ) {

	function quizmaker_output_content_wrapper_start() {
		
		qm_get_template( 'global/wrapper-start.php' );
	}
}

if ( ! function_exists( 'quizmaker_output_content_wrapper_end' ) ) {

	function quizmaker_output_content_wrapper_end() {
		qm_get_template( 'global/wrapper-end.php' );
	}
}

if ( ! function_exists( 'quizmaker_test_loop_start' ) ) {
	
	function quizmaker_test_loop_start( $echo = true ) {
		ob_start();
		qm_get_template( 'loop/loop-start.php' );
		if ( $echo )
			echo ob_get_clean();
		else
			return ob_get_clean();
	}
}

if ( ! function_exists( 'quizmaker_test_loop_end' ) ) {
	
	function quizmaker_test_loop_end( $echo = true ) {
		ob_start();
		qm_get_template( 'loop/loop-end.php' );

		if ( $echo )
			echo ob_get_clean();
		else
			return ob_get_clean();
	}
}

if( ! function_exists( 'quizmaker_template_filter_wrap_before_nav') ) {

	function quizmaker_template_filter_wrap_before_nav() {

		echo '<nav class="navbar navbar-expand-lg navbar-light bg-light mb-3"><ul class="navbar-nav mr-auto">';
	}
}

if( ! function_exists( 'quizmaker_template_filter_wrap_after_nav') ) {

	function quizmaker_template_filter_wrap_after_nav() {

		echo '</ul></nav>';
	}
}

if ( ! function_exists( 'quizmaker_catalog_ordering' ) ) {
	
	function quizmaker_catalog_ordering() {
		global $wp_query;

		if ( 1 === $wp_query->found_posts ) {
			return;
		}

		$orderby                 = isset( $_GET['orderby'] ) ? qm_clean( $_GET['orderby'] ) : apply_filters( 'quizmaker_default_catalog_orderby', get_option( 'quizmaker_default_catalog_orderby' ) );
		$show_default_orderby    = 'menu_order' === apply_filters( 'quizmaker_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
		$catalog_orderby_options = apply_filters( 'quizmaker_catalog_orderby', array(
			'menu_order' 	=> __( 'Default sorting', 'quizmaker' ),
			'date'			=> __( 'Sort by newness', 'quizmaker' ),
			'duration-asc'  => __( 'Sort by duration: low to high', 'quizmaker' ),
			'duration-desc' => __( 'Sort by duration: high to low', 'quizmaker' ),
			'played-asc'  => __( 'Sort by played: low to high', 'quizmaker' ),
			'played-desc' => __( 'Sort by played: high to low', 'quizmaker' )
		) );

		if ( ! $show_default_orderby ) {
			unset( $catalog_orderby_options['menu_order'] );
		}

		if ( 'no' === get_option( 'quizmaker_enable_review_rating' ) ) {
			unset( $catalog_orderby_options['rating'] );
		}

		qm_get_template( 'loop/orderby.php', array( 'catalog_orderby_options' => $catalog_orderby_options, 'orderby' => $orderby, 'show_default_orderby' => $show_default_orderby ) );
	}
}

if ( ! function_exists( 'quizmaker_pagination' ) ) {
	
	function quizmaker_pagination() {
		qm_get_template( 'loop/pagination.php' );
	}
}

if (  ! function_exists( 'quizmaker_template_loop_test_wrapper_desc_start' ) ) {
	
	function quizmaker_template_loop_test_wrapper_desc_start() {
		echo '<div class="col-xs-12 col-sm-8 item-desc">';
	}
}

if (  ! function_exists( 'quizmaker_template_loop_test_wrapper_desc_end' ) ) {

	function quizmaker_template_loop_test_wrapper_desc_end() {
		echo '</div>';
	}
}

if (  ! function_exists( 'quizmaker_template_loop_test_category' ) ) {
	
	function quizmaker_template_loop_test_category() {
		global $test;
		
		echo $test->get_category();
		
	}
}

if (  ! function_exists( 'quizmaker_template_loop_test_title' ) ) {
	
	function quizmaker_template_loop_test_title() {
		global $test;
		
		echo '<h3 class="item-title"><a href="' . esc_url( get_permalink( $test->id ) ) . '" title="' . esc_attr( $test->get_title() ) . '">' . get_the_title() . '</a></h3>';
	}
}

if (  ! function_exists( 'quizmaker_template_loop_test_excerpt' ) ) {
	
	function quizmaker_template_loop_test_excerpt() {
		
		echo '<div class="item-excerpt">' . get_the_excerpt() . '</div>';
		
	}
}

if (  ! function_exists( 'quizmaker_template_loop_test_action' ) ) {
	
	function quizmaker_template_loop_test_action() {
		global $test;

		$html = '<div class="item-action">'
				. '<a href="' . get_the_permalink() . '" class="btn btn-primary btn-sm qm-btn-start">' . __('Detail', 'quizmaker') . '</a>';

		if(get_option('quizmaker_is_rating') == 'yes') {

			$rating = $test->get_rating();

			$html .= aws_rating_html( $rating['star'], $rating['users'] );
			
		}
		
		$html .= '</div>';
		
		echo $html;	
	}
}

if ( ! function_exists( 'quizmaker_page_title' ) ) {
	
	function quizmaker_page_title( $echo = true ) {

		if ( is_search() ) {
			$page_title = sprintf( __( 'Search Results: &ldquo;%s&rdquo;', 'quizmaker' ), get_search_query() );

			if ( get_query_var( 'paged' ) )
				$page_title .= sprintf( __( '&nbsp;&ndash; Page %s', 'quizmaker' ), get_query_var( 'paged' ) );

		} elseif ( is_tax() ) {
			
			$page_title = single_term_title( "", false );
			
		} else {

			$test_page_id = qm_get_page_id( 'archive_test' );
			$page_title   = get_the_title( $test_page_id );

		}

		$page_title = apply_filters( 'quizmaker_page_title', $page_title );

		if ( $echo )
	    	echo $page_title;
	    else
	    	return $page_title;
	}
}

if ( ! function_exists( 'quizmaker_template_loop_test_info' ) ) {

	function quizmaker_template_loop_test_info() {
		
		global $test;
				
		$played		=	$test->get_played();
		$attempt	=	$test->get_attempt();
		$duration	=	$test->get_duration();
		$comments	=	$test->get_comments_number();
		$created	=	$test->get_date_created();
		
		echo '<div class="item-info">'.
				'<div class="duration"><i class="material-icons">query_builder</i>' . $duration . '</div>'.
				'<div class="doing"><i class="material-icons">play_arrow</i>' . $played . '</div>'.
				'<div class="comments"><i class="material-icons">comment</i>' . $comments . '</div>'.
				'<div class="doing"><i class="material-icons">query_builder</i>' . $created . '</div>'.
			'</div>';

	}
}

if ( ! function_exists( 'quizmaker_template_loop_test_thumbnail' ) ) {

	function quizmaker_template_loop_test_thumbnail() {
		$thumbnail	=	quizmaker_get_test_thumbnail();
		
		if($thumbnail){
			
			echo '<div class="col-xs-12 col-sm-4 item-thumbnail">' . $thumbnail . '</div>';
		}
	}
}

if ( ! function_exists( 'quizmaker_get_test_thumbnail' ) ) {
	
	function quizmaker_get_test_thumbnail( $size = 'shop_catalog', $deprecated1 = 0, $deprecated2 = 0 ) {
		global $post;
		
		if ( has_post_thumbnail() ) {
			return get_the_post_thumbnail( $post->ID, $size );
		}
		
		return false;
	}
}

if ( ! function_exists( 'quizmaker_template_single_test_start_title' ) ) {
	
	function quizmaker_template_single_test_start_title() {
		qm_get_template( 'single-test/start/title.php' );
	}
}

if ( ! function_exists( 'quizmaker_template_single_test_start_thumbnail' ) ) {
	
	function quizmaker_template_single_test_start_thumbnail() {
		qm_get_template( 'single-test/start/thumbnail.php' );
	}
}

if ( ! function_exists( 'quizmaker_template_single_test_result_head' ) ) {
	
	function quizmaker_template_single_test_result_head() {
		global $test;
		
		qm_get_template( 'single-test/result/head.php' );
	}
}

if ( ! function_exists( 'quizmaker_template_single_test_result_summary' ) ) {

	function quizmaker_template_single_test_result_summary() {
		
		global $test;

		$user_id 		=	get_current_user_id();
		$result_id		=	absInt(get_query_var('result'));

		$test_id	=	$test->get_id();
		$settings	=	$test->get_settings();
		
		$is_view = apply_filters( 'quizmaker_before_show_result', true, $result_id, $test_id );
			
			
		$can_store_result = apply_filters( 'quizmaker_can_store_result', false, 0, 0 );

		$args				=	$test->get_result_data($result_id, $can_store_result);
		
		$args['settings']	=	$settings;
		$args['test_id']	=	$test_id;
		$args['result_id']	=	$result_id;
		$args['test_link']	=	$test->get_permalink();
		
		qm_get_template( 'single-test/result/questions.php', $args );

		do_action( 'quizmaker_after_show_result', array( 'result_id' => $result_id, 'user_id' => $user_id ) );
			
	}
}

if( ! function_exists( 'quizmaker_template_before_show_result_is_guest' ) ) {

	function quizmaker_template_before_show_result_is_guest( $is_view = true, $result_id, $test_id ) {
		
		$user_id 		=	get_current_user_id();

		$user = wp_get_current_user();

		$is_admin = false;

		if ( in_array( apply_filters('quizmaker_user_roles_can_view_result', 'administrator', $user->roles), (array) $user->roles ) ) {
		    
		    $is_admin = true;
		}

		$tblResult      =	new QM_Table_Result();

		$result = $tblResult->load( $result_id );
		
		if( !isset($result['user_id']) || (($result['user_id'] != $user_id) && !$is_admin)  ) {

			wp_redirect( esc_url( get_permalink( $test_id ) ) );
			exit;
		}
		
	}

}

if( ! function_exists( 'quizmaker_template_remove_guest' ) ) {

	function quizmaker_template_remove_guest( $params = array() ) {

		$user_id 	=	get_current_user_id();

		$is_guest 	=	get_user_meta( $user_id, 'is_guest', true );

		if( $is_guest ) {

			require_once(ABSPATH . 'wp-admin/includes/user.php' );

			wp_logout();

			qm_remove_results( 'user', $user_id );

			wp_delete_user( $user_id );
		}
	}
}

if( ! function_exists( 'quizmaker_template_before_show_result_options_certificate' ) ) {

	function quizmaker_template_before_show_result_options_certificate( $result_id ) {
		
		
		return true;
	}
}

if( ! function_exists( 'quizmaker_template_single_test_title') ) {
	
	function quizmaker_template_single_test_title() {
		
		qm_get_template( 'single-test/title.php');
	}
}

if( ! function_exists( 'quizmaker_template_single_test_content') ) {
	
	function quizmaker_template_single_test_content() {
		
		qm_get_template( 'single-test/content.php' );
	}
}

if( ! function_exists( 'quizmaker_template_single_test_ranking' ) ) {
	function quizmaker_template_single_test_ranking() {
		global $test;
		
		$settings	=	$test->get_settings();
		
		if( isset($settings['is_ranking']) && $settings['is_ranking'] == 1) {
			
			$results	=	$test->get_lastest_results( array('order' => 'r.score DESC' ) );
			
			if($results){
				qm_get_template( 'single-test/ranking.php', array( 'results' => $results, 'settings' => $settings ));
			}
		}
	}
}

if( ! function_exists( 'quizmaker_template_single_test_review') ) {
	
	function quizmaker_template_single_test_review() {
		global $test;
		
		$settings	=	$test->get_settings();
				
		if(isset($settings['is_reviews']) && $settings['is_reviews'] == 1) {
			qm_get_template( 'single-test/review.php');
		}
	}
}

if( ! function_exists( 'quizmaker_template_single_test_start' ) ) {
	
	function quizmaker_template_single_test_start() {
		global $test;
		
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
					'value'		=>	count($test->get_questions())),

			), $test->id );

		if( $captcha = is_quizmaker_enable_captcha() ){

			array_push( $params['test_info'], array( 'type' => 'recaptcha', 'value' => $captcha['key'] ) );
		}

		if( get_option('quizmaker_is_rating') == 'yes' ) {

			array_push( $params['test_info'], array( 'type' => 'rating', 'label' =>	__('Rating', 'quizmaker'), 'value' => $test->get_rating() ) );
		}
		
		qm_get_template( 'single-test/start.php', $params );
	}
}

if( ! function_exists( 'quizmaker_template_single_test_start_data_info_adaptive' ) ) {
	
	function quizmaker_template_single_test_start_data_info_adaptive( $test_info, $test_id ) {
		
		$test	=	new QM_Test( $test_id );

		if( $test->get_type() == 1 ){

			$settings	=	$test->settings;

			$max_round 	=	$settings['adaptive_max_round'] ? $settings['adaptive_max_round'] : __('unlimited', 'quizmaker');

			array_push( $test_info, 
				array( 'type' => 'text', 'label' => __('Corrected Times', 'quizmaker'), 'value' => $settings['adaptive_times'] ),
				array( 'type' => 'text', 'label' => __('Max Round', 'quizmaker'), 'value' => $max_round )
			);
		}

		return $test_info;
	}
}

if( ! function_exists( 'quizmaker_template_single_test_start_data_info_infinite' ) ) {
	
	function quizmaker_template_single_test_start_data_info_infinite( $test_info, $test_id ) {
		
		$test	=	new QM_Test( $test_id );

		if( $test->get_type() == 2 ){

			array_push( $test_info, 
				array( 'type' => 'text', 'label' => __('Playing', 'quizmaker'), 'value' => __('Correcting all questions', 'quizmaker') )
			);

		}

		return $test_info;
	}
}

if( ! function_exists( 'quizmaker_template_single_test_start_test_info' ) ){
	
	function quizmaker_template_single_test_start_test_info( $test_info, $test_id ){
		
		if( is_array($test_info) && count($test_info) > 0 ){

			qm_get_template( 'single-test/start/info.php', array( 'test_info' => $test_info ) );

		}
	}
}

if( ! function_exists( 'quizmaker_template_test_start' ) ) {
	
	function quizmaker_template_test_start() {
		
		if(qm_can_do_test()){
			
			qm_get_template('content-single-test-start.php');
		}else{
			qm_get_template('single-test/start/error.php');
		}
	}
}

if( ! function_exists( 'quizmaker_template_test_doing' ) ) {
	
	function quizmaker_template_test_doing() {
		
		$session    = 	new QM_Test_Session();
				
		$session_data	=	$session->get('doing');
		$duration		=	$session_data['settings']['duration'];

		$type_testing	=	qm_get_type_testing( $session_data['tid'] );
		
		if($duration > 0 && qm_is_expired($session_data['time_start'], qm_get_date('now')->format('U'), $duration)){
			
			wp_redirect( qm_get_start_test_url($session_data['tid']) );
			exit;
		}
		
		qm_get_template('content-single-test-doing-' . $type_testing . '.php', array('doing_data' => $session_data));
	}
}

if ( ! function_exists( 'is_enable_review' ) ) {
	function is_enable_review() {
		global $wp_the_query;
		
		return isset($wp_the_query->query_vars['result']);
	}
}

if ( ! function_exists( 'quizmaker_login_form' ) ) {
	
	function quizmaker_login_form( $args = array() ) {

		$defaults = array(
			'message'  => '',
			'redirect' => '',
			'hidden'   => false
		);

		$args = wp_parse_args( $args, $defaults  );

		qm_get_template( 'myaccount/form-login.php', $args );
	}
}

if ( ! function_exists( 'quizmaker_template_before_my_account' ) ) {
	
	function quizmaker_template_before_my_account( $args = array() ) {
		
		global $wp_the_query;
		
		$defaults = apply_filters('quizmaker_template_my_account_link_tabs', array(
			'active'  => 'assigned_tests',
			'view_results_link'			=>	qm_get_endpoint_url( 'view-results', '', qm_get_page_permalink( 'myaccount' ) ),
			'view_assigned_tests_link'	=>	qm_get_endpoint_url( 'view-assigned-tests', '', qm_get_page_permalink( 'myaccount' ) ),
			// 'view_certificates_link'	=>	qm_get_endpoint_url( 'view-certificates', '', qm_get_page_permalink( 'myaccount' ) ),
			'view_edit_account_link'	=>	qm_get_endpoint_url( 'view-edit-account', '', qm_get_page_permalink( 'myaccount' ) ),
		));


		
		$tabs	=	apply_filters('quizmaker_template_my_account_tabs', array(
			'view-edit-account',
			'view-assigned-tests',
			'view-results' => array('view-result'),
			'view-certificates' => array('view-certificate'),
		));

		if(!is_array($args)) {

			$args = array();
		}

		
		foreach( $tabs as $link => $sub_link ) {
			
			if(!isset($args['active'])){
				
				if(is_array($sub_link)) {
				
					if(isset($wp_the_query->query_vars[$link])){
						$args['active']	=	$link;
					}
				
					if(!isset($args['active'])){
						foreach($sub_link as $value){
							if(isset($wp_the_query->query_vars[$value])){
								$args['active']	=	$link;
							}
						}
					}
				
				}else{
					
					if(isset($wp_the_query->query_vars[$sub_link])){
						$args['active']	=	$sub_link;
					}
				}
			}
			
		}
		
		$args = wp_parse_args( $args, $defaults  );
		
		qm_get_template( 'myaccount/tabs-start.php', $args );
	}
}

if ( ! function_exists( 'quizmaker_template_after_my_account' ) ) {
	
	function quizmaker_template_after_my_account( $args = array() ) {
		
		qm_get_template( 'myaccount/tabs-end.php' );
	}
}

if( ! function_exists( 'quizmaker_template_my_account_tabs_title' ) ) {

	function quizmaker_template_my_account_tabs_title( $args ) {

		if( $args ){
			
			foreach( $args as $tab_title => $tab_value ) {

				qm_get_template( 'myaccount/tabs_title/' . str_replace('_link', '', $tab_title) . '.php', $args );
			}
		}
	}
}

if( ! function_exists( 'quizmaker_template_my_account_user_info' ) ) {

	function quizmaker_template_my_account_user_info( $args ) {

		$user_id = get_current_user_id();


		$user_score = apply_filters( 'quizmaker_template_my_account_user_info_score', qm_get_user_score($user_id) );

		?>

	<div class="qm-user-info mb-3">
			
		<div class="user-info-avatar mb-3">
			<i class="material-icons">person</i>
		</div>

		<?php if(get_option('quizmaker_is_user_score') == 'yes'): ?>
		<div class="row">

			<div class="col-sm-12">
				
				<div class="user-info-intro">
					<span class="lbl"><?php _e('Score', 'quizmaker'); ?></span>
					<span class="vl"><?php echo apply_filters( 'quizmaker_template_my_account_user_info_score', qm_get_user_score($user_id) ); ?></span>
				</div>

			</div>

			
		</div>
		<?php endif; ?>
		

	</div>
			

		<?php
	}
}
	
if( ! function_exists( 'quizmaker_template_update_user_score' ) ) {
	
	function quizmaker_template_update_user_score( $result_id, $test_id ) {
		
		if( qm_is_user_score($test_id) ) {
			
			$result = qm_get_result( $result_id );
		
			qm_update_user_score( $result['score'], get_current_user_id() );

		}
	}
}

if( ! function_exists( 'quizmaker_template_update_user_cert_id' ) ) {
	
	function quizmaker_template_update_user_cert_id( $result_id, $test_id ) {
			
		$result = qm_get_result( $result_id );

		if( qm_is_ranking( $test_id, $result['score'], $result['total_score'] ) ) {

			qm_update_result_cert_id( $result_id );
		}
	}
}

if ( ! function_exists( 'get_test_search_form' ) ) {

	function get_test_search_form( $echo = true  ) {
		ob_start();

		do_action( 'pre_get_test_search_form'  );

		qm_get_template( 'test-searchform.php' );

		$form = apply_filters( 'get_test_search_form', ob_get_clean() );

		if ( $echo ) {
			echo $form;
		} else {
			return $form;
		}
	}
}

if( ! function_exists( 'quizmaker_template_single_result_rating' ) ) {

	function quizmaker_template_single_result_rating( $test_id, $result_id ) {

		if( get_option('quizmaker_is_rating') == 'yes' ) {

			$test = new QM_Test( $test_id );
			
			$star = $test->get_rating( get_current_user_id() );

			if( is_user_logged_in() ) {
				
				echo '<div class="item">'
					. '<div class="item-label">' . __('Rating', 'quizmaker') . ': </div>'
					. '<div class="item-value" id="qm-rating-wrapper"><star-rating value="' . $star . '" v-on:change="rateTest"></star-rating></div>'
				. '</div>';

			}

		}
	}
}

if( !function_exists('quizmaker_template_can_store_result') ) {

	function quizmaker_template_can_store_result( $value, $result, $test_id ) {

		$is_user_fillform = is_user_fillform_setting();

		$is_user_ranking_view_result_setting = is_user_ranking_view_result_setting();

		$is_play_test_as_guest = is_play_test_as_guest();

		$is_user = is_user_logged_in();

		// $is_enable = (!$is_user_fillform_setting && (!$is_user_ranking_view_result_setting && !$is_play_test_as_guest));
		
		$is_enable = !( (!$is_user && $is_play_test_as_guest) || $is_user_fillform );
		
		return $is_enable;
	}
}

if( !function_exists('quizmaker_template_show_user_fill_form') ) {

	function quizmaker_template_show_user_fill_form( $args ) {

		$is_user_fillform 		= is_user_fillform_setting();
		$is_play_test_as_guest  = is_play_test_as_guest();
		$is_user = is_user_logged_in();

		
		if( (!$is_user && $is_play_test_as_guest) || $is_user_fillform ) {

			return add_query_arg( 'doing-form', 1, qm_get_start_test_url(get_the_ID()) );			
		}

		return $args;
	}
}

if( !function_exists('quizmaker_template_header_doing') ) {

	function quizmaker_template_header_doing( $doing_data ) {

		qm_get_template( 'single-test/doing/header.php', array('doing_data' => $doing_data) );
	}
}

if( !function_exists('quizmaker_template_navigate_doing') ) {

	function quizmaker_template_navigate_doing( $doing_data ) {
			
		$session    	= 	new QM_Test_Session();
			
		$session_data	=	$session->get('doing');

		$time_passed = 0;

		if(isset($session_data['time_passed']) && $session_data['time_passed']){
				
			$time_passed	=	time() - $session_data['time_start'];
		}
			
		qm_get_template( 'single-test/doing/navigate.php', array('doing_data' => $doing_data, 'time_passed' => $time_passed) );
	}
}