<?php 

if ( ! defined( 'ABSPATH' ) ) { exit; }

function qm_test_get_settings( $post_id )
{
	$settings 	=	get_post_meta($post_id, '_test_settings', true);
	
	$settings	=	$settings ? $settings : array();
	
	$settings	=	wp_parse_args($settings, array(
		'display_perpage'		=>	10,
		'duration'				=>	20,
		'publish_for'			=>	0,
		'assigned_users'		=>	array(),
		'assigned_groups'		=>	array(),
		'is_ranking'			=>	1,
		'is_reviews'			=>	1,
		'is_email_result'		=>	1,
		'is_pagination'			=>	1,
		'is_backward'			=>	1,
		'is_sidebar_tracking'	=>	1,
		'is_timeout_answer'		=>	0,
		'is_shuffle_answers'	=>	1,
		'attempt'				=>	0,
		'result_is_answers'		=>	1,
		'memberships'			=>	array(),
		'played'				=>	0,
		'ranking'				=>	array(),
		'adaptive_times'		=>	3,
		'adaptive_max_round'	=>	3,
		'is_question_report'	=>	0,
		'save_for_later'		=>	0,
		'auto_save'				=>	0,
		'play_all'				=>	0,
		'show_result'			=>	1,
	));
	
	return $settings;
}

function qm_test_set_settings( $post_id, $key, $value )
{
	$settings 	=	get_post_meta( $post_id, '_test_settings', true );
	
	$settings	=	$settings ? $settings : array();
	
	$settings	=	wp_parse_args($settings, array(
		'display_perpage'		=>	10,
		'duration'				=>	20,
		'publish_for'			=>	0,
		'attempt'				=>	0,
		'assigned_users'		=>	array(),
		'assigned_groups'		=>	array(),
		'memberships'			=>	array(),
		'played'				=>	0,
		'is_reviews'			=>	1,
		'is_email_result'		=>	1,
		'is_pagination'			=>	1,
		'is_backward'			=>	1,
		'is_sidebar_tracking'	=>	1,
		'is_timeout_answer'		=>	0,
		'ranking'				=>	array(),
		'adaptive_times'		=>	3,
		'adaptive_max_round'	=>	3,
		'is_question_report'	=>	0,
		'save_for_later'		=>	0,
		'auto_save'				=>	0,
		'play_all'				=>	0,
		'show_result'			=>	1,
	));
	
	$settings[$key]	=	$value;
	
	return update_post_meta( $post_id, '_test_settings', $settings );
}

function qm_get_test( $the_test, $args = array() ) {
	
	return QM()->test_factory->get_test( $the_test, $args );
}

function qm_get_tests( $args = array() ) {
	
	$args	=	wp_parse_args( $args, array(
		'post_type'			=>	'test',
		'posts_per_page'	=>	-1,
		'meta_query'     => array(
			array( 'key' => '_publish_for', 'value' => 0 )
		)
	));
	
	return get_posts( $args );
}

function qm_get_doing_questions( $session_data = array(), $is_shuffle_answers = false )
{

	$question_ids 	 = $session_data['ids'];
	$question_params = isset($session_data['question_params']) ? $session_data['question_params'] : array();
	
	$questions	=	get_posts(array(
		'post_type'			=>	'question',
		'post_status'		=>	'publish',
		'suppress_filters' 	=>	true,
		'orderby' 			=> 'post__in',
		'numberposts'		=>	-1,
		'include'			=>	$question_ids,
	));

	if($questions){

		foreach($questions as &$question)
		{	
			$question->score		=	qm_get_post_meta( $question->ID, 'score' );
			$question->answer_type	=	qm_get_post_meta( $question->ID, 'answer-type' );
			$question->answers		=	qm_get_post_meta( $question->ID, 'answers' );
			$question->order_type	=	qm_get_post_meta( $question->ID, 'order_type' );
			$question->explanation	=	qm_get_post_meta( $question->ID, 'explanation' );
			$question->timeout		=	qm_get_post_meta( $question->ID, 'timeout' );
			
			$question->params 		=	$question_params;

			$params_answer 			= 'params_' . $question->answer_type;

			$question->$params_answer 	=	qm_get_post_meta( $question->ID, $params_answer );

			if( $question->answers && $is_shuffle_answers ) {
				
				if( is_array($question->answers) ) {
					
					if( $question->answer_type != 'order' && $question->answer_type != 'guess_word'  ){
						uksort( $question->answers, function() { return rand() > getrandmax() / 2; } );
					}
				}
			}
		}
	}
	
	return $questions;
}

function qm_get_rankings( $test_id, $args = array() )
{
	global $wpdb;
	
	$args	=	wp_parse_args($args, array( 'count' => 10 ));
	
	$result_tbl	=	$wpdb->prefix . 'quizmaker_results';
	
	$rankings	=	 $wpdb->get_results($wpdb->prepare(
						'SELECT MAX(score) AS score, total_score, user_id FROM ' .
						$result_tbl . ' WHERE test_id = %d GROUP BY user_id ORDER BY score DESC LIMIT %d',
						absInt($test_id), $args['count']),
					ARRAY_A);
	
	if($rankings){
		
		foreach($rankings as &$r){
			if($r['user_id']){
				$user	=	new WP_User($r['user_id']);
				
				$r['user']	=	$user->user_nicename;
			}
		}
	}
	
	return $rankings;
}

function qm_test_remove_assigned_users( $post_id, $ids_remove = array() ) {
	
	if( !$ids_remove ) {
		return false;
	}
	
	if( !is_array($ids_remove) ) {
		$ids_remove	=	array($ids_remove);
	}
	
	$settings 	=	get_post_meta($post_id, '_test_settings', true);
	
	$settings	=	$settings ? $settings : array();
	
	if( isset($settings['assigned_users']) && $settings['assigned_users'] ){
		
		$ids_save	=	array_diff($settings['assigned_users'], $ids_remove);
		
	}
	
	qm_test_set_settings($post_id, 'assigned_users', $ids_save);
	
}

function qm_get_result( $result_id ) {
	global $wpdb;
	
	if(!$result_id || !absInt($result_id)) return false;
	
	$result_id	=	absInt($result_id);
	
	$result_tbl	=	$wpdb->prefix . 'quizmaker_results';
	
	$result = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $result_tbl . ' WHERE id = %d', $result_id), ARRAY_A);
	
	if( $result ) {
		
		$result['test']	=	new QM_Test($result['test_id']);
		
		$ranking	=	qm_is_ranking($result['test_id'], $result['score'], $result['total_score']);
		
		if($ranking){
			$result['ranking']	=	$ranking;
		}
	}
	
	return $result;
}

function qm_get_results( $test_id ){

	global $wpdb;
	
	$result_tbl	=	$wpdb->prefix . 'quizmaker_results';
	
	$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . $result_tbl . ' WHERE test_id = %d', $test_id), ARRAY_A);
	
	return $results;
}

function qm_get_result_url( $result_id ) {

	$result = QM_Test::get_result( $result_id, false );

	if( isset($result) && isset($result['test_id']) ){

		$test = new QM_Test( $result['test_id'] );

		return add_query_arg( 'result', $result_id, $test->get_permalink() );

	}else{

		return false;
	}
}

function qm_remove_results( $by = 'user', $value ) {
	global $wpdb;
	
	$result_tbl	=	$wpdb->prefix . 'quizmaker_results';
	
	$where		=	array();
	
	switch( $by ) {
		case 'test':
			$where['test_id']	=	$value;
		break;
		default: 
			$where['user_id']	=	$value;
		break;
	}
	
	return $wpdb->delete( $result_tbl, $where, array('%d') );
}

function qm_test_remove_user_score( $test_id ) {

	$results = qm_get_results( $test_id );

	if( $results ) {

		foreach( $results as $result ) {

			if( $result['user_id'] ) {

				$user_score = qm_get_user_score( $result['user_id'] );

				if( $user_score > 0 ) {

					$new_user_score = absint($user_score) - absint($result['score']);

					$new_user_score = $new_user_score > 0 ? $new_user_score : 0;

					qm_new_user_score( $new_user_score, $result['user_id'] );
				}
			}
		}
	}
		
}

function qm_format_results( $data ) {
	
	if(!$data) return false;
	
	foreach($data as &$result){
		
		if(isset($result['score']) && isset($result['test_id'])){
			$ranking	=	qm_is_ranking( $result['test_id'], $result['score'], $result['total_score']);
			$test		=	new QM_Test( $result['test_id'] );
			
			if($ranking){
				$result['ranking']	=	$ranking;
			}
			
			if($test) {
				$result['test_title']		=	$test->get_title();
				$result['test_admin_link']	=	admin_url('post.php?post=' . $result['test_id'] . '&action=edit');
			}
			
			if(isset($result['user_id']) && $result['user_id']) {
				$user_info	=	get_userdata( $result['user_id'] );
				
				if($user_info) {

				    $first_name = $user_info->first_name;
				    $last_name	= $user_info->last_name;
					
					if( $first_name || $last_name ) {
						$result['user_nicename']	=	$first_name . ' ' . $last_name;
					}

				}else{

					$result['user_nicename'] = 'Guest';
				}
			}
		}
		
		if(isset($result['date_start'])){
			
			$result['date_start']	=	qm_get_date_formated( $result['date_start'] );
		}

		if(isset($result['answers'])) {

			$answers = json_decode( $result['answers'], true );

			if( $answers ) {

				$result['attend_question'] = count( $answers );
			}
			
		}
		
		$result	=	apply_filters( 'quizmaker_filters_format_results', $result );
	}
	
	return $data;
}

function qm_add_ranking( $test_id, $data ){
	
	if(!$test_id || !$data ) return false;
	
	if (is_object($data))
	{
		$data = get_object_vars( $data );
	}
	
	if (!isset($data['min']) || !isset($data['max']) || !$data['name'] ) return false;
	
	if($data['min'] > $data['max']) return false;
	
	if($data['certificate']) {
		$data['certificate']	=	absint($data['certificate']);
	}
	
	$test_settings	=	qm_test_get_settings( $test_id );
	
	$ranking		=	$test_settings['ranking'];
	
	$is_validated	=	true;
	
	if(!isset($data['id'])){
		
		$data['id']	=	md5(($data['min'] + $data['max']));
		array_push($ranking, $data);
		
	}else{
		
		foreach( $ranking as $index => $r ){
			if($r['id'] == $data['id']){
				
				$ranking[$index] =	$data;
				
			}
		}
	}
	
	foreach($ranking as $r){
	
		$range_ranking	=	range($r['min'], $r['max']);
	
		if( ($r['id'] != $data['id']) && (in_array($data['min'], $range_ranking) || in_array($data['max'], $range_ranking)) ) {
			$is_validated	=	false;
		}
	
	}
	
	if(!$is_validated) {
	
		return false;
	}
	
	qm_test_set_settings($test_id, 'ranking', $ranking);
	
	return $data['id'];
}

function qm_remove_ranking( $test_id = '', $id = '') {
	if(!$test_id || !$id) return false;
	
	$test_settings	=	qm_test_get_settings( $test_id );
	
	$ranking		=	$test_settings['ranking'];
	$is_remove		=	false;
	
	foreach( $ranking as $index => $r ){
		if($r['id'] == $id){
			$is_remove	=	true;
			unset($ranking[$index]);
		}
	}
	
	if($is_remove){
		qm_test_set_settings($test_id, 'ranking', $ranking);
	}
}

function qm_get_ranking( $test_id = '', $id = '' ) {
	
	if(!$test_id) return false;
	
	$test_settings	=	qm_test_get_settings( $test_id );
	
	$ranking		=	$test_settings['ranking'];
	
	if($ranking){
		
		if($id) {
			foreach( $ranking as $r ){
				if($r['id'] == $id){
					
					return $r;
				}
			}
		}else{
			
			return $ranking;
		}
	}
	
	return false;
}

function qm_is_ranking( $test_id = '', $score = 0, $total_score = 0, $only_name = true ) {
	
	if($total_score < 0) return false;
	
	if($score > 0){

		$percent	=	round(( absint($score) * 100 ) / absint($total_score));
	}else{

		$percent 	=	0;
	}
	
	$test		=	new QM_Test( $test_id );
	
	$ranking	=	qm_get_ranking( $test_id );
	
	if(!$ranking) return false;
	
	foreach($ranking as $r){
		
		if(($percent >= $r['min']) && ($percent <= $r['max'])){
			
			if($only_name){
				return $r['name'];
			}else{
				return $r;
			}
			
		}
	}

	return false;
}

function qm_is_user_score( $test_id ) {

	$is_user_score = get_post_meta( $test_id, '_user_score', true );

	return $is_user_score;
}

function qm_test_get_publish_for( $id ) {
	
	$publish_for = array(
		0 => __('Every Users', 'quizmaker'),
		1 => __('Private', 'quizmaker'),
		2 => __('Assigned Users', 'quizmaker'),
		3 => __('Assigned Groups', 'quizmaker'),
	);
	
	if(isset($id)){
		
		return $publish_for[$id];
		
	}else{
		
		return $publish_for;
	}
}

function qm_get_type_testing( $test_id ) {

	if(!$test_id) return 0;

	$type_testing	=	qm_get_post_meta( $test_id, 'type_testing' );

	if( !$type_testing ) {

		return 0;
	}

	return $type_testing;
}

function qm_test_get_scores( $ids = array() ){

	global $wpdb;

	if( !$ids || !is_array( $ids ) ){
		return 0;
	}

	$query = "SELECT meta_value FROM $wpdb->postmeta WHERE post_id IN (" . implode(',', $ids) . ") AND meta_key LIKE '_score'";
	
    $scores = $wpdb->get_col( $query );

    if( !empty( $scores ) ) {
    	
    	return array_sum( $scores );

    }else{

    	return 0;
    }

}

function qm_get_result_meta( $result_id, $name ) {

	global $wpdb;
	
	$result = QM_Test::get_result( $result_id, false );

	if( isset($result['others']) ) {

		$result['others'] = json_decode( $result['others'], true );

		if( isset($result['others'][$name]) ) {

			return $result['others'][$name];
		}

	}

	return false;
}

function qm_update_result_meta( $result_id, $name, $value ) {

	global $wpdb;

	$result = QM_Test::get_result( $result_id, false );

	if( isset($result['others']) ) {

		$result['others'] = json_decode( $result['others'], true );

		$result['others'][$name] = $value;

	}else{

		$result['others'] = array( $name => $value );
	}

	$others_query = json_encode( $result['others'] );

	$wpdb->update( $wpdb->prefix . 'quizmaker_results', 
		array( 'others' => $others_query ), 
		array( 'id' => $result_id ), array( '%s' ), array( '%d' ) );
}

function qm_update_result_cert_id( $result_id, $cert_id = false ) {

	global $wpdb;

	if( !$cert_id ) {

		$cert_id = qm_get_random_user_cert_id( $result_id );
		
		$wpdb->update( $wpdb->prefix . 'quizmaker_results', 
			array( 'cert_id' => strtoupper($cert_id) ), 
			array( 'id' => $result_id ), array( '%s' ), array( '%d' ) );
	}


}

function qm_get_random_user_cert_id( $id ) {

	$count = strlen($id);

	$certid = substr(uniqid(), 0, (10 - $count)) . $id;

	return $certid;
}