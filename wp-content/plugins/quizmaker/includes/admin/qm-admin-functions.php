<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function qm_create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0 ) {

	global $wpdb;

	$option_value     = get_option( $option );

	if ( $option_value > 0 ) {
		$page_object = get_post( $option_value );

		if ( 'page' === $page_object->post_type && ! in_array( $page_object->post_status, array( 'pending', 'trash', 'future', 'auto-draft' ) ) ) {
			// Valid page is already in place
			return $page_object->ID;
		}
	}

	if ( strlen( $page_content ) > 0 ) {
		// Search for an existing page with the specified page content (typically a shortcode)
		$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
	} else {
		// Search for an existing page with the specified page slug
		$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", $slug ) );
	}

	$valid_page_found = apply_filters( 'quizmaker_create_page_id', $valid_page_found, $slug, $page_content );

	if ( $valid_page_found ) {
		if ( $option ) {
			update_option( $option, $valid_page_found );
		}
		return $valid_page_found;
	}

	// Search for a matching valid trashed page
	if ( strlen( $page_content ) > 0 ) {
		// Search for an existing page with the specified page content (typically a shortcode)
		$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
	} else {
		// Search for an existing page with the specified page slug
		$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_name = %s LIMIT 1;", $slug ) );
	}

	if ( $trashed_page_found ) {
		$page_id   = $trashed_page_found;
		$page_data = array(
			'ID'             => $page_id,
			'post_status'    => 'publish',
		);
	 	wp_update_post( $page_data );
	} else {
		$page_data = array(
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_author'    => 1,
			'post_name'      => $slug,
			'post_title'     => $page_title,
			'post_content'   => $page_content,
			'post_parent'    => $post_parent,
			'comment_status' => 'closed'
		);
		$page_id = wp_insert_post( $page_data );
	}

	if ( $option ) {
		update_option( $option, $page_id );
	}

	return $page_id;
}

function qm_get_total_tests() {
	
	$count_tests	=	wp_count_posts('test');
	
	$total			=	$count_tests->publish ? $count_tests->publish : 0;
	
	return $total;
}

function qm_get_total_questions() {
	
	$count_questions	=	wp_count_posts('question');
	
	$total				=	$count_questions->publish ? $count_questions->publish : 0;
	
	return $total;
}

function qm_get_total_members() {
	
	$count_users = count_users();
	
	if(isset($count_users['avail_roles']['quizmaker_member'])){
		$members_role	=	$count_users['avail_roles']['quizmaker_member'];
	}else{	
		$members_role	=	0;
	}
	
	return $members_role;
}

function qm_get_lastest_members( $order_by = 'registered' ) {
	
	$members	=	get_users(array('role' => 'quizmaker_member', 'order_by' => $order_by, 'order' => 'DESC', 'number' => 10, 'paged' => 1));
	
	return $members;
}

function qm_get_lastest_results( $params = array() ) {
	
	global $wpdb;
	
	$params	=	wp_parse_args( $params, array('limit' => 10) );
	
	$result_tbl	=	$wpdb->prefix . 'quizmaker_results';
	$user_tbl	=	$wpdb->prefix . 'users';
	
	$query	=	$wpdb->prepare('SELECT r.id, r.test_id, r.user_id, r.score, r.total_score, r.percent, r.duration, r.date_start, r.date_end, u.user_nicename, u.user_login '.
			'FROM ' . $result_tbl . ' r LEFT JOIN ' . $user_tbl . ' u ON u.id = r.user_id '.
			'WHERE r.date_start IN(SELECT max(dr.date_start) FROM ' . $result_tbl . ' dr GROUP BY dr.user_id) '.
			'GROUP BY r.user_id ORDER BY date_start DESC LIMIT %d', $params['limit']);
	
	$results	= $wpdb->get_results($query, ARRAY_A);
	
	if($results){
		
		$results	=	qm_format_results( $results );
		
	}
	
	return $results;
}

function qm_get_sessions( $args = array() ) {
	
	global $wpdb;

	$params	=	wp_parse_args( $args, array('limit' => 10) );
	
	$sessions	=	$wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'quizmaker_sessions ORDER BY session_expiry DESC LIMIT ' . absint($params['limit']), 'ARRAY_A' );
	
	if($sessions) {
		$data				=	array();
		$expiry_sessions	=	array();
		
		foreach($sessions as $session){
			
			if(isset($session['session_value'])){
				
				$session_data	=	unserialize( $session['session_value'] );
		
				$sdata			=	array();
				
				if(isset($session_data['doing']) && $session_data['doing']) {
					
					$doing_data		=	unserialize($session_data['doing']);
					
					$is_session_expiry	=	qm_is_expired($session['session_expiry'], qm_time(), 169200);
					$is_test_expiry		=	$doing_data['duration'] > 0 && qm_is_expired($doing_data['time_start'], qm_time(), $doing_data['duration']);
					
					if( !$is_session_expiry && !$is_test_expiry){
					
						$user			=	new WP_User( $doing_data['uid'] );
						$test			=	new QM_Test( $doing_data['tid'] );
					
						$user_admin_link	=	'<a href="' . admin_url('user-edit.php?user_id=' . $user->ID) . '">' . $user->user_login . '</a>';
						$test_admin_link	=	'<a href="' . admin_url('post.php?action=edit&post=' . $test->get_id()) . '">' . $test->get_title() . '</a>';
					
						array_push($data, array(
							'user'			=>	$user_admin_link,
							'test_link'		=>	$test_admin_link,
							'test_duration'	=>	$test->get_duration()
						));
					}else{
						
						array_push($expiry_sessions, $session['session_id']);
					}
				}
				
			}
		}
		
		if($expiry_sessions) {
			qm_destroy_session( $expiry_sessions );
		}
		
		return $data;
	}
		
	return array();
}

function qm_destroy_session( $session_id ) {
	global $wpdb;
	
	if(!isset($session_id)) return false;
	
	$ids	=	absint($session_id);
		
	if(is_array($session_id)) {
		$ids	=	implode(',', array_map('absint', $session_id));
	}
	
	$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . 'quizmaker_sessions' . ' WHERE session_id IN(' . $ids . ')' );
}

function qm_dropdown_posts( $args, $attributes = '', $selected = 0, $echo = true ) {
	
	$args	=	wp_parse_args( $args, array( 'posts_per_page' => -1 ) );
	
	$posts	=	get_posts( $args );
	
	$output	=	'<select ' . $attributes . '>';

	$output .= '<option value="0"' . selected($selected, 0, false) . '>' . __('None', 'quizmaker') . '</option>';
	
	if($posts){
		foreach($posts as $post){
			$output .= '<option value="' . $post->ID . '" ' . selected($selected, $post->ID, false) . '>' . $post->post_title . '</option>';
		}
	}
	
	$output .= '</select>';
	
	if($output){
		echo $output;
	}else{
		return $output;
	}
}

function qm_admin_ranking_link( $post_id, $echo = true ){
	
	if(!$post_id) return false;
	
	$link	= '<a href="' . admin_url('post.php?post=' . $post_id . '&action=edit') . '">' . get_the_title($post_id) . '</a>';
	
	if( $echo ) {
		echo $link;
	}else{
		
		return $link;
	}
}



function qm_curl_post( $url, $fields = array() ) {

	if( !isset($fields) || !$fields ) return false;

	$fields_string = '';
	
	foreach( $fields as $key => $value ) { 

		$fields_string .= $key.'='.$value.'&'; 
	}

	rtrim( $fields_string, '&' );

	$ch = curl_init();

	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_POST, count($fields) );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields_string );

	$result = curl_exec($ch);

	curl_close($ch);

	return $result;
}

function qm_curl_get( $url ) {
	
	$curl = curl_init();

	curl_setopt_array($curl, array(
	    CURLOPT_RETURNTRANSFER => 1,
	    CURLOPT_URL => $url
	));

	$result = curl_exec($ch);

	curl_close($ch);

	return $result;
}