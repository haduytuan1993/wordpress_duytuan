<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function qm_disable_admin_bar( $show_admin_bar ) {
	if ( apply_filters( 'quizmaker_disable_admin_bar', get_option( 'quizmaker_lock_down_admin', 'yes' ) === 'yes' ) && ! ( current_user_can( 'edit_posts' ) || current_user_can( 'manage_quizmaker' ) ) ) {
		$show_admin_bar = false;
	}

	return $show_admin_bar;
}
add_filter( 'show_admin_bar', 'qm_disable_admin_bar', 10, 1 );

function qm_update_user_meta( $user_id, $data = array() ) {
	
	if(!$user_id || !is_array($data) || !$data) return false;
	
	foreach( $data as $key => $d ) {
				
		update_user_meta( $user_id, $key , $d );
	}	
}

function qm_get_user_meta( $user_id, $key = '' ) {
	
	if(!$user_id) return false;
	
	if( !$key ){
		
		$membership_default	=	qm_get_membership_default();
	
		$data	=	array(
			'member_id'				=>	0,
			'tests'					=>	array(),
			'did_tests'				=>	array(),
			'paids'					=>	array(),
			'is_payment'			=>	'no',
			'date_paid'				=>	false,
			'member_token'			=>	'',
			'membership_expired'	=>	-1,
			'membership_date_paid'	=>	'0000-00-00 00:00:00',
			'pre_membership'		=>	$membership_default,
			'membership'			=>	$membership_default
		);
		
		foreach( $data as $key => &$value ){
			
			$_value	= get_user_meta( $user_id, $key, true );
			
			if( $_value ) {
				$value	=	$_value;
			}
		}
		
		return $data;
		
	}else{
		
		return get_user_meta( $user_id, $key, true );
		
	}
}

function qm_update_member_meta( $member_id, $data = array() ) {
	
	if(!$member_id || !is_array($data) || !$data) return false;
	
	foreach( $data as $key => $d ) {
				
		update_post_meta( $member_id, '_' . $key , $d );
	}	
}

function qm_get_member_meta( $member_id, $key = '' ) {
	
	if(!$member_id) return false;
	
	$member_data 	=	get_post_meta( $member_id, '_' . $key, true);
	
	return $member_data;
}

function qm_update_user_paid( $user_id, $data = array() ) {
	
	if( !$user_id || !$data ) return false;
	
	$user_meta	=	qm_get_user_meta( $user_id );
	
	if(isset($data['is_payment'])){
		$user_meta['is_payment']	=	$data['is_payment'];
	}
	
	if(isset($data['paid'])){
		
		$data['paid']['expired']			=	qm_date_nextmonth( $data['paid']['date'] );
		
		$user_meta['membership']			=	$data['paid']['membership_id'];
		$user_meta['membership_date_paid']	=	$data['paid']['date'];
		$user_meta['membership_expired']	=	$data['paid']['expired'];
		
		$user_meta['pre_membership']		=	$data['pre_membership'];
		
		array_push( $user_meta['paids'], $data['paid'] );
		
		qm_add_member_to_membership( $user_id, $data['paid']['membership_id'] );
	}
	
	qm_update_user_meta( $user_id, $user_meta );
}

function qm_add_member_to_membership( $user_id, $membership_id ) {
	
	$user_id		=	absInt($user_id);
	$membership_id	=	absInt($membership_id);
	
	$old_membership_id	=	qm_get_user_meta( $user_id, 'membership' );
	
	qm_update_user_meta( $user_id, array('membership' => $membership_id) );
		
	$old_membership	=	new QM_Membership( $old_membership_id );
	$old_membership->remove_member( $user_id );
	
	if( $membership_id ){
		$new_membership	=	new QM_Membership( $membership_id );
		$new_membership->add_member( $user_id );
	}
}

function qm_add_test_to_memberships( $test_id, $memberships = array() ) {
	
	$test_id			=	absInt($test_id);
	$all_memberships	=	qm_get_memberships();
	$memberships		=	$memberships ? $memberships : array();
	
	if(!$all_memberships) return false;
	
	$all_memberships	=	qm_get_values_by_key( $all_memberships, 'ID' );
	
		
	foreach( $all_memberships as $m ){
		
		$membership	=	new QM_Membership( $m );
		
		if(in_array($m, $memberships)){
			
			$membership->add_test( $test_id );
			
		}else{
			
			$membership->remove_test( $test_id );
			
		}
		
	}
	
}

function qm_remove_membership( $membership_id = '' ) {
	
	$membership	=	new QM_Membership( $membership_id );
	
	$membership->remove();
}

function qm_is_membership_expired( $user_id ) {
	
	$membership_id	=	qm_get_user_meta( $user_id, 'membership' );
	
	if($membership_id) {
		$membership	=	qm_get_memberships( array('ID' => $membership_id), true );
		
		if( $membership->price > 0 ) {
			
			$is_payment	=	qm_get_user_meta( $user_id, 'is_payment' );
			
			if( $is_payment == 'no' ) return false;
			
			$expired	=	strtotime( qm_get_user_meta( $user_id, 'expired' ) );
			
			if( !$expired ) return true;
			
			if(time() > $expired ){
				
				return true;
			}
			
		}
	}
	
	return false;
}

function qm_get_user_paids( $user_id ) {
	
	$user_meta	=	qm_get_user_meta( $user_id );
	$data		=	array();
	
	if($user_meta['paids']) {
		foreach( $user_meta['paids'] as &$paid ) {
			
			if($paid['membership_id']){
				$paid['membership']	=	qm_get_memberships( array( 'ID' => $paid['membership_id'] ), true);
			}
		}
		
		$data['history']	=	$user_meta['paids'];
	}
	
	if($user_meta['membership']){
		
		$data['membership']		=	qm_get_memberships( array( 'ID' => $user_meta['membership'] ), true);
		$data['is_payment']		=	$user_meta['is_payment'];
		$data['date_paid']		=	$user_meta['membership_date_paid'] != '0000-00-00 00:00:00' ? $user_meta['membership_date_paid'] : false;
		$data['expired']		=	$user_meta['membership_expired'] != -1 ? $user_meta['membership_expired'] : false;
		
	}
	
	return $data;
}

function qm_update_did_tests( $user_id, $test_id ) {
	
	if(!$user_id) return false;
	
	$did_tests	=	qm_get_user_meta( $user_id, 'did_tests' );
		
	if(!in_array($test_id, $did_tests)){
		
		array_push( $did_tests, $test_id );
	}
	
	qm_update_user_meta( $user_id, array('did_tests' => $did_tests));
}

function qm_get_current_member_id() {
	
	if(!get_current_user_id()) return false;
	
	$member_id	=	qm_get_user_meta( get_current_user_id(), 'member_id' );
		
	if( $member_id ) return $member_id;
	
	return false;
}

function qm_get_user_tests( $user_id, $page = 1, $perpage = 20 ) {
	global $wpdb;
	
	$test_ids	=	apply_filters( 'quizmaker_before_get_user_tests', qm_get_user_meta( $user_id, 'tests' ) );
	
	if( $test_ids )
	{		
		$tests = get_posts( apply_filters( 'quizmaker_my_account_my_tests_query', array(
			'numberposts' 	=>	-1,
			'post_type'   	=>	'test',
			'include'		=>	$test_ids
		) ) );
		

		$data = array();

		if($tests){ 
			foreach($tests as &$t){ 

				$t	=	QM()->test_factory->get_test($t->ID); 

				$data[] = array(
					'id' => $t->id,
					'test_title' => $t->get_title(),
					'test_link' => $t->get_permalink(),
					'duration' => $t->get_duration(),
					'attempt' => $t->get_attempt(),
				);

			} 
		}
		
		return qm_pagination_format( $data, $page, $perpage );
	}
	
	return array('data' => array());
}

function qm_get_user_results( $user_id, $page = 1, $perpage	= 10 ) {
	
	global $wpdb;
	
	$result_tbl	=	$wpdb->prefix . 'quizmaker_results';
	$test_tbl	=	$wpdb->prefix . 'posts';
	$page		=	$page ? $page : 1;
	
	$query	=	$wpdb->prepare(
		'SELECT r.id, r.test_id, r.score, r.total_score, ROUND((r.score * 100)/r.total_score) AS percent, r.duration, r.date_start, r.date_end '.
			'FROM ' . $result_tbl . ' r ' .
			'WHERE r.user_id = %d AND r.date_start IN(SELECT max(dr.date_start) FROM ' . $result_tbl . ' dr WHERE dr.user_id = %d GROUP BY dr.test_id) '.
			'GROUP BY r.test_id ORDER BY r.date_start DESC'
		, $user_id, $user_id);
	
	$results	=	$wpdb->get_results($query, ARRAY_A);
	
	$results	=	qm_format_results( $results );
	
	return qm_pagination_format( $results, $page, $perpage );
}

function qm_get_new_user( $data, $params = array() ) {
	
	$username	=	! empty( $data[ 'username' ] ) ? sanitize_user( $data[ 'username' ], 50 ) : '';
	$email      =	! empty( $data[ 'email' ] ) ? sanitize_email( $data[ 'email' ] ) : '';
	$password   =	! empty( $data[ 'password' ] ) ? $data[ 'password' ] : '';
	$membership	=	! empty( $data[ 'membership' ] ) ? absInt($data[ 'membership' ]) : false;
	
	$user         = new stdClass();
	
	if ( $email ) {
		
		if ( ! is_email( $email ) ) {
			qm_add_message( __( 'Please provide a valid email address.', 'quizmaker' ), 'error' );
		} elseif ( email_exists( $email ) ) {
			qm_add_message( __( 'This email address is already registered.', 'quizmaker' ), 'error' );
		}
		
		$user->user_email = $email;
	} else {
		
		qm_add_message( __( 'This email is empty.', 'quizmaker' ), 'error' );
	}

	if( isset($params['is_email_for_username']) && $params['is_email_for_username'] == 'yes') {

		$username = $email;
	}
	
	if( $username ) {
		
		if( username_exists( $username ) ) {
			qm_add_message( __( 'This username is already registered.', 'quizmaker' ), 'error' );
		}
		
		$user->user_login	=	$username;
	}else{
		
		qm_add_message( __( 'This username is empty.', 'quizmaker' ), 'error' );
	}
	
	$user->user_pass 	=	$password;
	$user->role			=	'quizmaker_member';
	
	return $user;
}

function qm_new_guest( $is_login = true ) {
	
	$id					=	uniqid();
	$username			=	'guest_' . $id;
	
	$user         		=	new stdClass();
	
	$user->user_login	=	$username;
	$user->user_pass 	=	'';
	$user->role			=	'quizmaker_member';
	
	$user_id = wp_insert_user( $user ) ;
	
	update_user_meta($user_id, 'is_guest', 1);
	
	if( $is_login ){
		wp_set_current_user($user_id);
		wp_set_auth_cookie($user_id);
	}
	
	return $user_id;
}

function qm_new_member( $user_id, $args = array() ) {
	
	$user_id	=	absInt($user_id);
	
	if(!$user_id) return false;
	
	$membership_default	=	qm_get_membership_default();
	
	$args	=	wp_parse_args( $args, array(
		'pre_membership'	=>	$membership_default,
		'membership'		=>	$membership_default,
		'is_payment'		=>	'no'
	));
	
	$is_payment	=	$args['is_payment'] == 'yes' ? 'yes' : 'no';
	
	$user = new WP_User( $user_id );
	
	if(!empty( $user->roles ) && is_array( $user->roles ) && in_array('quizmaker_member', $user->roles)) {
		
		$member_id	=	wp_insert_post(array(
			'post_type'		=>	'member',
			'post_title'	=>	$user->nickname,
			'post_status'	=>	'publish',
			'meta_input'	=>	array(
				'_user_id'	=>	$user_id
			)
		));
		
		qm_update_user_meta( $user_id, array(
			'pre_membership'	=> absInt($args['pre_membership']), 
			'membership' 		=> absInt($args['membership']), 
			'is_payment' 		=> 'no', 
			'member_id' 		=> $member_id
		) );
		
		return $member_id;
	}
	
	return false;
}

function qm_update_new_membership( $user_id ) {
	
	$pre_membership	=	qm_get_user_meta( $user_id, 'pre_membership' );
	
	$data			=	array();
	
	$data['membership']		=	$pre_membership;
	$data['pre_membership']	=	qm_get_membership_default();
	$data['member_token']	=	'';
	
	qm_update_user_meta( $user_id, $data );
}

function qm_get_users( $search = '', $params	=	array() ) {
	
	$params	=	wp_parse_args($params, array('search_columns' => array( 'user_login', 'user_email', 'user_nicename' ) ) );
	
	if($search){
		$params['search']	=	'*'.esc_attr( $search ).'*';
	}
	
	$user_query = new WP_User_Query( $params );
	
	$results	=	array();
	
	if ( ! empty( $user_query->results ) ) {
		foreach($user_query->results as $u){
			
			array_push($results, array('id' => $u->ID, 'name' => ($u->display_name . ' - ' . $u->user_email)));
		}
	}
	
	return $results;
}

function qm_get_memberships( $args	=	array(), $single = false ) {
	
	$args	=	wp_parse_args( $args, array('numberposts' => -1, 'post_type' =>	'membership') );
	
	if(isset($args['ID'])){
		$args['include']	=	array(absInt($args['ID']));
	}
	
	$memberships = get_posts( apply_filters( 'quizmaker_admin_get_memberships_query', $args ) );
	
	if( $memberships ) {
		foreach( $memberships as $membership ) {
			
			$price	=	qm_get_post_meta( $membership->ID, 'price' );
			
			if(isset($price)){ $membership->price	=	absInt( $price ); }
			
			$membership->link	=	qm_get_page_permalink('archive_test') . '?mid=' . $membership->ID;
		}
		
		return $single ? $memberships[0] : $memberships;
	}
	
	return false;
}

function qm_dropdown_memberships( $name, $selected = 0, $attrs = '', $echo = true ) {
	
	$memberships	=	qm_get_memberships();
	
	if(!$memberships) return false;
	
	$options		=	'<option value="0"' . selected(0, $selected, false) . '>' . __('Select All') . '</option>';
	
	foreach($memberships as $m) {
		$options	.=	sprintf('<option value="%d" ' . selected($m->ID, $selected, false) . '>%s</option>', $m->ID, $m->post_title);
	}
	
	$output	= sprintf('<select name="%s" %s>%s</select>', $name, $attrs, $options);
	
	if( $echo ) {
		
		echo $output;
		
	}else{
		
		return $output;
	}
}

function qm_get_membership_default() {
	
	return get_option('quizmaker_membership_default', 0);
}

function qm_set_membership_default( $id ) {
	
	update_option('quizmaker_membership_default', absInt($id));
}

function qm_get_user_by_member( $member_id ) {
	
	$user_id	=	qm_get_member_meta( $member_id, 'user_id' );
	
	if( $user_id ) {
		
		$user	=	new WP_User( $user_id );
		$user_meta	=	qm_get_user_meta( $user_id );
		
		if( $user ){
			return array( 'user' => $user, 'meta' => $user_meta );
		}
		
		return false;
	}
	
	return false;
}

function qm_is_alive_session( $session_key = 0 ) {
	
	return true;
	global $wpdb;
	
	if(!$session_key) return false;
	
	$session_key	=	absint($session_key);
	
	$results	=	$wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'quizmaker_sessions WHERE session_key = ' . $session_key, ARRAY_A );
	
	if( !$results ) return false;
	
	if(count($results) > 1) {
		
		for( $i = 0; $i < count($results); $i++ ) {
			qm_remove_session_doing( $results[$i]['session_id'] );
		}
		
		return false;
	}
	
	return true;
}

function qm_remove_session_doing( $session_id = 0 ) {

	global $wpdb;
	
	if(!$session_id) return false;
	
	$result	= $wpdb->delete( $wpdb->prefix . 'quizmaker_sessions', array('session_id' => $session_id), array('%d') );
	
	$wpdb->flush();
	
	return $result;
}

function qm_get_user_groups() {
	
	return get_posts(array(
			'post_type' 	 => 'usergroup',
			'posts_per_page' => -1
		));
}

function qm_can_doing_form() {

	$is_doing_form = is_doing_form();

	if( $is_doing_form ){

		$session    = 	new QM_Test_Session();

		$result_id = $session->get('result_id');
		$test_id   = $session->get('test_id');
		
		$is_redirect = true;

		if( $result_id ) {

			

			if( $test_id == get_the_ID() ) {

				$is_redirect = false;
			}
		}
		
		if( $is_redirect ) {
			
			wp_redirect( get_permalink( $test_id ) );
			exit;
		}

		return true;
	}

	return false;
}

function qm_get_user_score( $user_id ) {

	$score_1 = get_user_meta( $user_id, '_score', true );

	return $score_1 ? absint( $score_1 ) : 0;
}

function qm_update_user_score( $score_2, $user_id ) {

	$score_1 = qm_get_user_score( $user_id );

	$score_2 = absint($score_2) + $score_1;

	update_user_meta( $user_id, '_score', $score_2 );
}

function qm_new_user_score( $score_2, $user_id ) {

	update_user_meta( $user_id, '_score', $score_2 );
}

function qm_reset_user_score( $user_id ) {

	update_user_meta( $user_id, '_score', 0 );
}

function qm_user_generate_activation_key( $user_id, $user_email ) {

	global $wpdb;

	$salt = wp_generate_password(20);
	$key = sha1($salt . $user_email . uniqid(time(), true));

	$wpdb->update( $wpdb->prefix . 'users', 
		['user_activation_key' => $key], ['ID' => $user_id], ['%s'], ['%d'] );

	return $key;
}

function qm_user_validate_activation_key( $key ) {

	global $wpdb;

	$user = $wpdb->get_row( $wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'users WHERE user_activation_key = %s', $key), ARRAY_A );

	if( $user ) {

		$wpdb->update( $wpdb->prefix . 'users', 
		['user_activation_key' => ''], ['ID' => $user['ID']], ['%s'], ['%d'] );

		return $user;
	}

	return false;
}

function qm_user_is_activation( $user_login ) {

	global $wpdb;

	$user = $wpdb->get_row( $wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . "users WHERE user_activation_key = '' AND user_login = %s", $user_login), ARRAY_A );

	if( $user ) {

		return true;
		
	}else{

		return false;
	}
}

function qm_user_get_attempt( $user_id, $test_id ) {

	$attempt = get_user_meta( $user_id, 'qm_attempt_' . $test_id, true );

	return $attempt ? absint($attempt) : 0;
}

function qm_user_increase_attempt( $user_id, $test_id ) {

	$attempt = qm_user_get_attempt( $user_id, $test_id );

	$attempt = $attempt ? absint($attempt) + 1 : 1;

	update_user_meta( $user_id, 'qm_attempt_' . $test_id , $attempt );

	return $attempt;
}

function qm_reset_attempt( $test_id ) {

	global $wpdb;

	$wpdb->update( $wpdb->prefix. 'usermeta', ['meta_value' => 0], ['meta_key' => 'qm_attempt_' . $test_id], ['%d'], ['%s'] );

}