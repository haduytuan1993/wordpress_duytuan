<?php 

if ( ! defined( 'ABSPATH' ) ) { exit; }

include( 'qm-formatting-functions.php' );
include( 'qm-conditional-functions.php' );
include( 'qm-page-functions.php' );
include( 'qm-user-functions.php' );

function qm_update_or_new_array_meta( $post_id, $data, $meta_key, $id = false, $key = 'id' ) {

	if( !$post_id ) {

		return false;
	}

	$stored_data = get_post_meta( $post_id, $meta_key, true );

	if( $id && $stored_data && is_array($stored_data) ) {

		foreach( $stored_data as &$item ) {

			if( $item[$key] == $id ) {

				$item = $data;
			}
		}

	}elseif( !$id && $stored_data && is_array($stored_data) ){

		array_push( $stored_data, $data );

	}else{

		$stored_data = array( $data );
	}

	update_post_meta( $post_id, $meta_key, $stored_data );

	return $stored_data;
}

function qm_remove_array_meta( $post_id, $remove_ids, $meta_key, $key = 'id' ) {

	if( !$post_id || !$remove_ids ) {

		return false;
	}

	if( !is_array( $remove_ids ) ) {

		$remove_ids = array($remove_ids);
	}

	$stored_data = get_post_meta( $post_id, $meta_key, true );

	if( $stored_data && is_array($stored_data) ) {

		foreach( $stored_data as $index => &$question ) {

			if( in_array($question[$key], $remove_ids) ) {

				unset( $stored_data[$index] );
			}
		}
	}

	update_post_meta( $post_id, $meta_key, $stored_data );

	return $stored_data;
}

function qm_update_element_to_array_post_meta( $post_id, $value, $meta_key ) {

	if( !$post_id ) {

		return false;
	}

	$stored_data = get_post_meta( $post_id, $meta_key, true );

	if( $stored_data && is_array($stored_data) ) {
		
		array_push( $stored_data, $value );
		
		if( !is_array( $value ) ){
			
			$stored_data = array_unique($stored_data);
			
		}
		
	}else{

		$stored_data = array( $value );
	}

	update_post_meta( $post_id, $meta_key, $stored_data );

	return $stored_data;
}

function qm_update_elements_to_array_post_meta( $post_id, $values, $meta_key ) {

	if( !$post_id || !is_array($values) ) {

		return false;
	}
	
	$stored_data = get_post_meta( $post_id, $meta_key, true );

	if( $stored_data ) {

		$stored_data = array_unique(array_merge( $stored_data, $values ));

	}else{

		$stored_data 	= array_unique($values);
	}

	update_post_meta( $post_id, $meta_key, $stored_data );

	return $stored_data;
}

function qm_remove_elements_from_array_post_meta( $post_id, $values, $meta_key ) {

	if( !$post_id ) {

		return false;
	}

	if( !is_array( $values ) ) {

		$values = array( $values );
	}

	$stored_data = get_post_meta( $post_id, $meta_key, true );

	if( $stored_data ) {

		$stored_data = array_diff( $stored_data, $values );

	}else{

		$stored_data 	= array();
	}

	update_post_meta( $post_id, $meta_key, $stored_data );

	return $stored_data;
}

function qm_checking_return( $instance, $key, $default = false ) {

	if(isset($instance) && isset($instance[$key])){
		return $instance[$key];
	}else{
		return $default;
	}
}

function qm_update_post_meta ( $post_id, $data = array() ) {
	
	if(!$post_id || !is_array($data) || !$data) return false;
	
	foreach( $data as $key => $d ) {
				
		update_post_meta( $post_id, '_' . $key , $d );
	}

	return true;
}

function qm_get_post_meta( $post_id, $key = '', $default = false ) {
	
	if(!$post_id) return $default;
	
	$data 	=	get_post_meta( $post_id, '_' . $key, true);
	
	if( !isset($data) ) return $default;
	
	return $data;
}

function qm_update_post_meta_array( $post_id, $key, $value, $is_unique = true ) {
	
	if( !isset($post_id) || !isset($key) || !isset($value) ) return false;
	
	$data	=	qm_get_post_meta( $post_id, $key );
	
	$data	=	is_array( $data ) ? $data : array();
	
	array_push( $data, $value );
	
	if( $is_unique ) $data	=	array_unique( $data );
	
	
	qm_update_post_meta( $post_id, array( $key => $data ) );
}

function qm_remove_post_meta_array( $post_id, $key, $value ) {
	
	if( !isset($post_id) || !isset($key) || !isset($value) ) return false;
	
	$data	=	qm_get_post_meta( $post_id, $key );
	
	qm_update_post_meta( $post_id, array( $key => qm_array_remove_by_value( $data, $value ) ) );
}

function qm_array_remove_by_value( $data = array(), $value = '', $is_reset_index = true ) {
	
	if( !is_array($data) || !$data || !isset($value) ) return array();
	
	if( !in_array( $value, $data ) ) return $data;
	
	foreach( $data as $index => $v ){
		
		if( $v == $value ){
			
			unset($data[$index]);
		}
		
	}
	
	if( $is_reset_index ) {
		
		return array_values( $data );
		
	}else{
		
		return $data;
	}
}

function qm_get_values_by_key( $data = array(), $key ) {

	if(!$data || !$key) return false;
	
	$results	=	array();
	
	foreach($data as $d){
		
		if(is_object($d))
			$d = get_object_vars($d);
		
		if(isset($d) && isset($d[$key]))
		{
			array_push($results, $d[$key]);
		}
	}
	
	return $results;
}

function qm_setcookie( $name, $value, $expire = 0, $secure = false ) {
	if ( ! headers_sent() ) {
		setcookie( $name, $value, $expire, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, $secure );
	} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		headers_sent( $file, $line );
		trigger_error( "{$name} cookie cannot be set - headers already sent by {$file} on line {$line}", E_USER_NOTICE );
	}
}

function qm_crypto_rand_secure($min, $max)
{
    $range = $max - $min;
    if ($range < 1) return $min; // not so random...
    $log = ceil(log($range, 2));
    $bytes = (int) ($log / 8) + 1; // length in bytes
    $bits = (int) $log + 1; // length in bits
    $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
    do {
        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        $rnd = $rnd & $filter; // discard irrelevant bits
    } while ($rnd >= $range);
    return $min + $rnd;
}

function qm_get_token($length)
{
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789-";
    $max = strlen($codeAlphabet) - 1;
    for ($i=0; $i < $length; $i++) {
        $token .= $codeAlphabet[qm_crypto_rand_secure(0, $max)];
    }
    return $token;
}

function qm_get_setting( $name ) {
	
	return get_option( 'quizmaker_' . $name );
}

function qm_get_timezone_offset() {
	
	return get_option('gmt_offset');
}

function qm_get_timezone(){
	
	if ( $timezone = get_option( 'timezone_string' ) )
		return $timezone;

	if ( 0 === ( $utc_offset = get_option( 'gmt_offset', 0 ) ) )
		return 'UTC';
	
	// adjust UTC offset from hours to seconds
	$utc_offset *= 3600;

	// attempt to guess the timezone string from the UTC offset
	if ( $timezone = timezone_name_from_abbr( '', $utc_offset, 0 ) ) {
	    return $timezone;
	}

	// last try, guess timezone string manually
	$is_dst = date( 'I' );

	foreach ( timezone_abbreviations_list() as $abbr ) {
	    foreach ( $abbr as $city ) {
	        if ( $city['dst'] == $is_dst && $city['offset'] == $utc_offset )
	            return $city['timezone_id'];
	    }
	}

	// fallback to UTC
	return 'UTC';
}

function qm_time( $timezone = '' ) {
	if( !$timezone ) {
		$timezone	=	qm_get_timezone();
	}
	
	date_default_timezone_set($timezone);
	
	return time();
}

function qm_get_date( $value = '', $format = '', $timezone = '' )
{
	if( !$timezone ) {
		$timezone	=	qm_get_timezone();
	}
	
	date_default_timezone_set($timezone);
	
	if( !$format ) {
		$format	=	'Y-m-d H:i:s';
	}
	
	if( !$value || $value == 'now' ) {
		
		return new DateTime( 'now', new DateTimeZone( $timezone ) );
	}
	
	if( is_string($value) ){
		$value = strtotime( $value );
		
		return new DateTime( date( $format, $value ), new DateTimeZone( $timezone ) );
	}else{
		
		return new DateTime( date($format, $value), new DateTimeZone( $timezone ) );
	}
}

function qm_get_date_formated( $value, $format = '', $timezone = '' ) {
	
	$date	=	qm_get_date( $value, $format, $timezone );
	
	if(!$format) {
		$format	=	get_option('date_format') . ' ' . get_option('time_format');
	}
	
	return date_i18n( $format, strtotime($date->format( $format )) );
}

function qm_get_duration( $date_start, $date_end, $format = 'Y-m-d H:i:s', $timezone = '' )
{
	
	$date_s 	= 	qm_get_date( $date_start, $format, $timezone );
	$date_e 	= 	qm_get_date( $date_end, $format, $timezone );
	
	$interval	=	$date_e->diff($date_s);
	
	return $interval->format('%H:%I:%S');
}

function qm_get_seconds( $date_start, $date_end, $format = 'Y-m-d H:i:s', $timezone = '' )
{
	
	$date_s 	= 	qm_get_date( $date_start, $format, $timezone );
	$date_e 	= 	qm_get_date( $date_end, $format, $timezone );
	
	$interval	=	$date_e->diff($date_s);
	
	return $interval->format('%S');
}

function qm_is_expired( $time_start, $time_end, $duration ) {
	
	$current_duration	=	$time_end - $time_start;
	$duration			=	$duration * 60;

	if($current_duration > $duration){

		return true;
	}

	return false;
}

function qm_get_start_test_url( $test_id = 0 )
{
	global $test;
	
	if(!$test_id) {
		$test_id = get_the_ID();
	}
	
	return get_permalink( $test_id );
}

function qm_get_doing_test_url()
{
	global $test;

	return add_query_arg( 'doing', 1, get_permalink($test->ID) );
	
}

function qm_get_ranking_test_url()
{
	global $test;

	return add_query_arg( 'ranking', 1, get_permalink($test->ID) );
}

function qm_get_result_test_url( $result_id = false, $test_id = false )
{
	global $test;

	if(isset($test) && (!isset($test_id) || !$test_id))
	{
		$test_id = $test->id;
	}
	
	if($result_id){

		return add_query_arg( 'result', $result_id, get_permalink($test_id) );

	}else{

		if($test){
			return get_permalink($test_id);
		}else{

			return '';
		}
	}
	
}

function qm_view_certificate_test_url( $test_id, $result_id = false )
{	
	if( !$result_id || !$test_id ) return false;

	return add_query_arg( 'view-certificate', $result_id, get_permalink($test_id) );
}

function is_owner_test( $test_id, $user_id = 0 ) {
	
	if(!$user_id){ $user_id	=	get_current_user_id(); }
	
	$test = QM()->test_factory->get_test($test_id);
	
	if($test && ($test->get_author_id() == $user_id)) {
		return true;
	}
	
	return false;
}

function is_owner_question( $question_id, $user_id = 0 ) {
	
	if(!$user_id){ $user_id	=	get_current_user_id(); }
	
	$question = new QM_Question($question_id);
	
	if($question && ($question->get_author_id() == $user_id)) {
		return true;
	}
	
	return false;
}

function is_owner_result( $result_id, $user_id = 0 ) {
	
	if(!$user_id){ $user_id	=	get_current_user_id(); }
	
	$result	=	QM_Test::get_result( $result_id );
	
	if($result){
		
		return $result['user_id'] == $user_id ? $result : false;
	}
	
	return false;
}

function is_assigned_to_test( $test_id, $user_id ) {
	
	if(!$user_id){ $user_id	=	get_current_user_id(); }
	
	$user_tests	=	qm_get_user_meta( $user_id, 'tests' );
	
	if( in_array($test_id, $user_tests) ){
		
		return true;
	}
	
	return false;
}

function qm_can_edit_test( $test_id, $user_id = 0 ) {
		
	if(is_owner_test( $test_id, $user_id )){
		
		return true;
	}
	
	return false;
}

function qm_can_edit_question( $question_id, $user_id = 0 ) {
	
	if(!$user_id){ $user_id	=	get_current_user_id(); }
	
	if(is_owner_question( $question_id, $user_id )){
		
		return true;
	}
	
	return false;
}

function qm_can_view_result( $result_id, $test_id, $user_id = 0 ) {
	
	if(!$user_id){ $user_id	=	get_current_user_id(); }
	
	$user = wp_get_current_user();

	if( is_owner_result( $result_id, $user_id ) || is_owner_test( $test_id, $user_id ) || in_array( 'administrator', (array) $user->roles ) ){
		
		return true;
	}
	
	return false;
}

function qm_can_edit() {
	
	if(!is_user_logged_in()) return false;
	
	if(current_user_can('edit_test')){
		return true;
	}
}

function qm_can_do_test( $test_id = 0, $user_id = 0 ){
	
	global $test;
	
	if(!is_user_logged_in()) return false;
	
	if($test_id) {
		$test	=	QM()->test_factory->get_test($test_id);
	}
	
	$settings	=	$test->get_settings();
	
	$attempt		=	qm_get_post_meta( $test_id, 'attempt' );
	$publish_for	=	qm_get_post_meta( $test_id, 'publish_for' );
	
	if(current_user_can('edit_posts')){
		return true;
	}
	
	// is private
	if($publish_for == 1){
		
		return current_user_can('edit_posts');
		
	// is assigned users
	}elseif($publish_for == 2){
		
		$current_user	=	wp_get_current_user();
		$attempts		=	absInt($attempt);
		
		if($attempts > 0){
			
			$results	=	$test->get_total_user_results( $current_user->ID );
			
			if(is_array($results) && count($results) >= $attempts){
				return false;
			}
		}
		
		$assigned_users	=	$settings['assigned_users'];

		$can_do = apply_filters( 'quizmaker_can_do_test_assigned_users', $assigned_users && is_array($assigned_users) && in_array($current_user->ID, $assigned_users), $test_id );
		
		if( $can_do ){
			
			return true;
		}else{
			
			return false;
		}
	
	}elseif($publish_for == 3){

		$current_user	=	wp_get_current_user();
		$attempts		=	absInt($attempt);
		
		if($attempts > 0){
			
			$user_attempts	=	qm_user_get_attempt( $current_user->ID, $test_id );
			
			if($user_attempts >= $attempts){
				return false;
			}
		}
		
		$assigned_groups	=	$settings['assigned_groups'];

		if( $assigned_groups ) {

			foreach( $assigned_groups as $group_id ) {

				$users_group = get_post_meta( $group_id, '_users', true );

				if( in_array( $current_user->ID, $users_group ) ) {

					return true;
				}
			}

		}

		return false;
		
	}elseif($publish_for == 0){
		
		$current_user	=	wp_get_current_user();
		$attempts		=	absInt($attempt);
		
		if($attempts > 0){
			
			$user_attempts	=	qm_user_get_attempt( $current_user->ID, $test_id );
			
			if( $user_attempts >= $attempts ){
				return false;
			}
		}
		
		return true;
	}

	return apply_filters( 'quizmaker_can_do_test', false, $test_id );
}

function qm_is_guest() {
	
	if( !is_user_logged_in() && get_option('quizmaker_is_play_test_as_guest') == 'yes' ) return true;
	
	return false;
}

function qm_can_do_test_as_guest( $test_id = 0 ) {
	global $test;
	
	if( is_user_logged_in() || get_option('quizmaker_is_play_test_as_guest') == 'no' ) return false;
	
	if( $test_id ) {

		$test	=	new QM_Test($test_id);
	}
	
	$publish	=	$test->get_publish();
	
	// for every user
	if($publish == 0){
		
		return true;
	}
	
	return false;
}

function qm_help_tip( $tip, $allow_html = false ) {
	if ( $allow_html ) {
		$tip = qm_sanitize_tooltip( $tip );
	} else {
		$tip = esc_attr( $tip );
	}

	return '<span class="quizmaker-help-tip" data-tip="' . $tip . '"></span>';
}

function check_recaptcha( $params = array() ) {
	
	$captcha	=	is_quizmaker_enable_captcha();
	
	if(!$captcha){
		return true;
	}
	
	$args	=	array(
		'secret'	=>	$captcha['secret'],
		'response'	=>	$params['response'],
		'remoteip'	=>	$params['remoteip']
	);
	
	$results	=	qm_http_post('https://www.google.com/recaptcha/api/siteverify', $args);
	
	$results	=	$results ? json_decode( $results ) : false;
	
	if($results) {
			
		return $results->success;
		
	}else{
		
		return false;
	}
}

function qm_http_post($url, $data) {
	
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

function qm_image_tag( $attachment_id, $size = 'thumbnail', $attr = true ) {
	
	if( !$attachment_id ) return false;
	
	$background_attr	=	'';
	
	$background_src		=	wp_get_attachment_image_src( $attachment_id, $size );
	
	if( !$background_src ) return false;
	
	if( $attr ) {
		$background_attr	=	'width="' . $background_src[1] . '" height="' . $background_src[2] . '"';
	}
	
	return sprintf('<img src="%s" %s/>', $background_src[0], $background_attr);
}

function qm_is_debug() {
	if(get_option('quizmaker_is_debug') == 'yes'){
		return true;
	}else{
		return false;
	}
}

function qm_get_categories( $cat_ids )
{
	if(!$cat_ids) { return false; }

	$categories = array();

	foreach( $cat_ids as $cat_id )
	{
		$term = get_term( $cat_id );

		$categories[$cat_id] = $term->name;
	}

	return $categories;
}

function qm_get_params_question( $question_id, $all_params = array() )
{
		
	if( isset($question_id) && isset($all_params) && $all_params )
	{ 
		foreach( $all_params as $params ){
			if( $params['id'] == $question_id )
			{
				return $params;
			}
		}
	}

	return array();
}

function aws_check_code() {

	$code 	=	get_option( 'aws_verify_code' );

	if( !$code ) {

		return false;
	}

	if( isset($_COOKIE[$code]) && $_COOKIE[$code] == 1 ) {

		return true;
	}

	$response = file_get_contents( 'http://verify.awstheme.com/envato/' . $code );

	$contents = utf8_encode($response); 

	$results  = json_decode($contents, true);
	
	if( $results['status'] == 'OK' ) {

		setcookie( $code, 1, time()+60*60*24*30 );

		return true;
	}else {

		return false;
	}
}

function aws_validate_code() {

	$result 	=	aws_check_code();

	if( $result ) {

		return true;
	}else{

		wp_redirect( admin_url( 'admin.php?page=qm-settings&tab=verify' ) );
	}
}

function qm_get_log_file_path( $handle ) {
	return trailingslashit( QM_LOG_DIR ) . $handle . '-' . sanitize_file_name( wp_hash( $handle ) ) . '.log';
}

function aws_rating_html( $star = 0, $users = 0, $show_count = false ) {


	$html = '<div class="qm-star-rating">';
	
	for( $i = 1; $i <= 5; $i++ ){

		$html .= '<label class="star-rating__star' . ($i <= $star ? ' is-selected' : '') . '"><i class="material-icons">grade</i></label>';
	}
		

		if( $show_count && count($users) > 0 ){
			$html .= '<span class="count">(' . count($users) . ')</span>';
		}
		
	$html .= '</div>';

	return $html;
}

function qm_get_all_publish_for() {

	return apply_filters( 'quizmaker_publish_for', [
		['id' => 0, 'name' => __('Every Users', 'quizmaker')],
		['id' => 1, 'name' => __('Private', 'quizmaker')],
		['id' => 2, 'name' => __('Assigned Users', 'quizmaker')],
		['id' => 3, 'name' => __('Assigned Groups', 'quizmaker')],
	] );
}