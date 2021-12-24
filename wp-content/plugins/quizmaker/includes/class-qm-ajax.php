<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class QM_AJAX {
	
	const SECURITY =  'quizmaker_5Fv2x4YX';
	
	public static function init() {
		add_action( 'init', array( __CLASS__, 'define_ajax'), 0 );
		add_action( 'template_redirect', array( __CLASS__, 'do_qm_ajax' ), 0 );
		self::add_ajax_events();
	}
	
	public static function define_ajax() {
		if ( ! empty( $_GET['qm-ajax'] ) ) {
			if ( ! defined( 'DOING_AJAX' ) ) {
				define( 'DOING_AJAX', true );
			}
			if ( ! defined( 'QM_DOING_AJAX' ) ) {
				define( 'QM_DOING_AJAX', true );
			}
			
			if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
				@ini_set( 'display_errors', 0 );
			}
			
			$GLOBALS['wpdb']->hide_errors();
			
			nocache_headers();
		}
	}
	

	private static function qm_ajax_headers() {
		send_origin_headers();
		@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
		@header( 'X-Robots-Tag: noindex' );
		send_nosniff_header();
		nocache_headers();
		status_header( 200 );
	}

	public static function do_qm_ajax() {
		global $wp_query;

		if ( ! empty( $_GET['qm-ajax'] ) ) {
			$wp_query->set( 'qm-ajax', sanitize_text_field( $_GET['qm-ajax'] ) );
		}

		if ( $action = $wp_query->get( 'qm-ajax' ) ) {
			self::qm_ajax_headers();
			do_action( 'qm_ajax_' . sanitize_text_field( $action ) );
			die();
		}
	}

	public static function add_ajax_events() {
		$ajax_events = array(
			'check_verify' 						=>	true,
			'after_play_test_form_setting'		=>  true,
			'json_search_tests' 				=>	true,
			'json_search_questions'				=>	true,
			'json_search_users' 				=>	true,
			'get_questions'						=>	true,
			'get_users'							=>	true,
			'get_instant_answer'				=>	true,
			'get_doing_questions'				=>	true,
			'get_test_all_results'				=>	true,
			'remove_test_results'				=>	true,
			'save_ranking'						=>	true,
			'remove_ranking'					=>	true,
			'save_certificate'					=>	true,
			'get_text_boudingbox_certificate'	=>	true,
			'preview_certificate'				=>	true,
			'session_doing'						=>	true,
			'session_stop_doing'				=>	true,
			'load_html_lastest_general_results'	=>	true,
			'load_html_all_general_results'		=>	true,
			'load_html_fixed_questions'			=>	true,
			'load_html_assigned_users'			=>	true,
			'load_html_user_groups'				=>	true,
			'load_html_result'					=>	true,
			'question_report'					=>	true,
			'test_rating_from_result'			=>	true,
			'load_html_certificate_tests'		=>	true,
			'importing_questions'				=>	true,
			'question_ordering'					=>	true,
			'wpmedia'							=>	true,
			'quiz_resource'						=>	true,
			'quiz_player'						=>	true,
			'account_resource'					=>	true,
			'test_resource'						=>	true,

		);
		
		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_quizmaker_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			
			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_quizmaker_' . $ajax_event, array( __CLASS__, $ajax_event ) );
				
				// QM AJAX can be used for frontend ajax requests
				add_action( 'qm_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	public static function check_verify(){

		check_ajax_referer( 'admin_quizmaker', 'security' );

		// $verify_code 	 = get_option( 'quizmaker_verify_purchase_code' );
		// $verify_status 	 = get_option( 'quizmaker_verify_status' );

		// if( $verify_code ) {

		// 	$verify_time = get_option('quizmaker_verify_time');

		// 	if( $verify_time ) {

		// 		$verify_time  = time() - $verify_time;
		// 	}

		// 	if( !$verify_status || $verify_time >= 304800 ) {

		// 		$site = wp_parse_url(home_url());
				
		// 		$ch = curl_init();

		// 		curl_setopt($ch, CURLOPT_URL, "https://api.awstheme.com/envato/quizmaker_validate?code=$verify_code&site=" . urlencode($site['host']));
		// 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //      			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)');

		// 		$verify = json_decode(curl_exec($ch), true);
								
		// 		if( $verify['status'] == 1 ) {

		// 			$response['status'] = 1;

		// 			update_option('quizmaker_verify_time', time());
		// 			update_option('quizmaker_verify_status', 1);

		// 		}else{

		// 			$response['status'] = 0;
		// 			update_option('quizmaker_verify_status', 0);
		// 		}

		// 	}

		// }else{

		// 	$response['status'] = 0;
		// }
		
		echo wp_send_json( ['status' => 1] );
		// echo wp_send_json( $response );
	}

	public function after_play_test_form_setting() {

		check_ajax_referer( 'admin_quizmaker_after_play_test_form_setting', 'security' );

		$method =	isset($_POST['data']['method']) ? $_POST['data']['method'] : false;

		$response = array('status' => 0);

		switch ($method) {
			case 'save':
				
				$form_data =	isset($_POST['data']['data']) ? $_POST['data']['data'] : array();

				update_option('quizmaker_forms_after_play_test_settings', array(
					'form_data' => $form_data
				));

				break;
			
			case 'get':

				$data = get_option('quizmaker_forms_after_play_test_settings');

				$response['data'] = $data;
				
				break;
			default:
				# code...
				break;
		}

		echo wp_send_json( $response );
	}

	public static function json_search_tests()
	{
		check_ajax_referer( 'search_tests_nonce', 'security' );

		$search 	= 	$_GET['term'];
		$limit		=	absint( $_GET['limit'] );
		$exclude	=	$_GET['exclude'];

		$publish_for = 2;

		if( isset($_GET['others']['publish_for']) ) {

			$publish_for = absint($_GET['others']['publish_for']);
		}

		if( !is_array($exclude) ){
			$exclude = array( $exclude );
		}
		
		$tests 	=	qm_get_tests(array(
			'post_type'			=>	'test',
			'post_status'		=> 'publish',
			'posts_per_page'	=>	$limit,
			'meta_query'     	=> array(
				array( 'key' => '_publish_for', 'value' => $publish_for )
			),
			's' => $search,
			'exclude' => $exclude
		));

		$results = array();

		if( $tests ) {
			foreach( $tests as $test ){

				$results[$test->ID] = $test->post_title;
			}
		}

		echo wp_send_json( $results );
		
		wp_die();
	}

	public static function json_search_questions()
	{
		check_ajax_referer( 'search_questions_nonce', 'security' );

		$post_id	=	absInt( $_GET['others']['pid'] );
		$search		=	$_GET['term'];
		$limit		=	absint( $_GET['limit'] );
		$exclude	=	$_GET['exclude'];

		$params	=	array(
			'numberposts'	=>	-1,
			'post_type'		=>	'question',
			'orderby'   	=>	'post__in',
			's' 			=>	$search,
		);

		$fixed_questions	=	get_post_meta($post_id, '_fixed_questions', true);

		if(!$exclude || !is_array($exclude)){
			$exclude = array();
		}

		if(is_array($fixed_questions) && $fixed_questions){
			
			$exclude	=	array_merge( $fixed_questions, $exclude );
		}

		if($exclude){
			
			$params['post__not_in']	=	$exclude;
		}

		$questions	=	get_posts($params);

		$results = array();

		if( is_array($questions) && $questions ) {
			foreach( $questions as $question ){

				$results[$question->ID] = $question->post_title;
			}
		}

		echo wp_send_json( $results );
		
		wp_die();

	}

	public static function json_search_users()
	{
		check_ajax_referer( 'search_users_nonce', 'security' );

		$post_id	=	absInt( $_GET['others']['pid'] );
		$search		=	$_GET['term'];
		$limit		=	absint( $_GET['limit'] );
		$exclude	=	$_GET['exclude'];

		if(!$post_id || !$search) return false;
		
		$test		=	new QM_Test($post_id);
		
		$settings	=	$test->get_settings();
		
		$assigned	=	$settings['assigned_users'];
				
		$params		=	array('exclude' => array());

		if($exclude) {
			$params['exclude'] = $exclude;
		}
		
		if($assigned){
			
			$params['exclude']	=	array_merge($params['exclude'], $assigned);
		}
		
		$users 		=	array();
		$results	=	qm_get_users($search, $params);

		if( $results ) {
			foreach( $results as $user ){

				$users[$user['id']] = $user['name'];
			}
		}

		echo wp_send_json( $users );
		
		wp_die();
	}

	public static function get_questions()
	{
		global $wpdb;
		
		check_ajax_referer( 'admin_quizmaker', 'security' );
		
		$post_id	=	absInt($_GET['pid']);
		
		$params	=	array(
			'numberposts'	=>	-1,
			'post_type'		=>	'question',
			'orderby'   	=>	'post__in'
		);
		
		if(isset($_GET['s']) && $_GET['s'] && $post_id){
			$params['s']	=	$_GET['s'];
			
			$fixed_questions	=	get_post_meta($post_id, '_fixed_questions', true);
		}
		
		$fixed_questions	=	$fixed_questions ? $fixed_questions : array();
		
		if(is_array($_GET['nids']) && $_GET['nids']){
			
			$fixed_questions	=	array_merge($fixed_questions, $_GET['nids']);
		}
		
		if($fixed_questions){
			
			$params['post__not_in']	=	$fixed_questions;
		}
		
		$results	=	get_posts($params);
		
		echo wp_send_json( array('error' => 0, 'data' => $results) );
		
		wp_die();
	}
	
	public static function get_users()
	{
		global $wpdb;
		
		check_ajax_referer( 'admin_quizmaker', 'security' );
		
		
		$post_id	=	absInt($_GET['pid']);
		$search		=	$_GET['s'];
		
		if(!$post_id || !$search) return false;
		
		$test		=	new QM_Test($post_id);
		
		$settings	=	$test->get_settings();
		
		$assigned	=	$settings['assigned_users'];
		$exclude	=	$_GET['nids'];
		
		$params		=	array('exclude' => array());
		
		if($exclude){
			$params['exclude']	=	$exclude;
		}
		
		if($assigned){
			
			$params['exclude']	=	array_merge($params['exclude'], $assigned);
		}
		
		$results	=	qm_get_users($search, $params);
		
		echo wp_send_json( array('error' => 0, 'data' => $results) );
		
		wp_die();
	}
	
	public static function session_doing() {
		check_ajax_referer( 'quizmaker_doing_test', 'security' );
		
		echo wp_send_json( array('is_alive' => 1) );
		
		wp_die();
	}
	
	public static function session_stop_doing() {
		check_ajax_referer( 'admin_quizmaker', 'security' );
		
		$session_id	=	absInt($_GET['sid']);
		
		$result = qm_remove_session_doing( $session_id );
		
		$is_error	=	$result ? 0 : 1;
		
		echo wp_send_json( array('error' => $is_error) );
		
		wp_die();
	}
	
	public static function get_doing_questions(){

		check_ajax_referer( 'quizmaker_doing_test', 'security' );
		
		//if(qm_is_alive_session( get_current_user_id() )) {
			
			$doingTest	=	new QM_Doing();
		
			$doingTest->doing_questions($_GET['page']);
		//}
		
		wp_die();
	}

	public static function get_instant_answer(){

		check_ajax_referer( 'quizmaker_doing_test', 'security' );

		$test_id	=	absint($_GET['post_id']);

		if(!$test_id){

			return false;
		}

		$test 	=	new QM_Test( $test_id );

		if( $test->settings['instant_answer'] != 1 ) {

			return false;
		}
		
		if(isset($_GET['id']) && absint($_GET['id']) && qm_is_alive_session( get_current_user_id() )) {
			
			$session	=	QM()->session;
			
			$qid 		=	absint( $_GET['id'] );
			
			$question   =	new QM_Question( $qid );

			$answer_data = isset($_GET['answer']) ? $_GET['answer'] : array();
			
			$answer 	=	$question->get_instant_answer( $question->filter_answer_post_data( $answer_data ), $test->settings );
			
			$session->update_instant_answer( $qid, $answer['is_correct'] );
			
			echo wp_send_json( array( 'data' => $answer['html'] ) );
		}
		
		wp_die();
	}
	
	public static function get_test_all_results(){
		check_ajax_referer( 'admin_quizmaker', 'security' );
		
		$tid	=	absInt($_GET['tid']);
		$uid	=	absInt($_GET['uid']);
		
		if(!$tid || !$uid) return false;
		
		$test		=	QM()->test_factory->get_test($tid);
		
		$results	=	$test->get_user_results($uid, -1);
		
		$response	=	array('pagination' => $results['pagination'], 'status' => 'success');

		ob_start();

			include( 'admin/meta-boxes/views/html-admin-all-general-results.php' );
			
		$response['html'] = ob_get_clean();

		echo wp_send_json( $response );

		wp_die();
	}
	
	public static function remove_test_results() {
		check_ajax_referer( 'admin_quizmaker', 'security' );
		
		$tid	=	absInt($_GET['tid']);
		
		if(!$tid) return false;
		
		$test	=	QM()->test_factory->get_test($tid);
		
		$test->reset_played();
		
		$result	=	$test->remove_results();
		
		qm_reset_attempt( $tid );

		echo wp_send_json( $result );
	}
	
	public static function save_ranking() {
		check_ajax_referer( 'admin_quizmaker', 'security' );
		
		$tid	=	absInt($_GET['tid']);
		$data	=	$_GET['data'];
		
		if(!$data || !$tid) return false;
		
		$id = qm_add_ranking($tid, $data);
		
		if($id){
			
			$data['certificate']	=	qm_admin_ranking_link( $data['certificate'], false );

			$data = apply_filters( 'quizmaker_test_data_save_ranking', $data, $id );
			
			echo wp_send_json( array('id' => $id, 'data' => $data) );
		}else{
			
			echo wp_send_json( array('error' => __('Ranking is not validate')) );
		}
	}
	
	public static function remove_ranking() {
		check_ajax_referer( 'admin_quizmaker', 'security' );
		
		$tid	=	absInt($_GET['tid']);
		$id		=	$_GET['id'];
		
		if(!$id || !$tid) return false;
		
		qm_remove_ranking($tid, $id);
	}
	
	public static function save_certificate() {
		check_ajax_referer( 'admin_quizmaker', 'security' );
		
		$post_id	=	$_POST['id'] ? absint($_POST['id']) : false;
		
		if(!$post_id){
			return false;
		}
		
		$data		=	is_array($_POST['data']) ? $_POST['data'] : false;
		
		qm_update_post_meta( $post_id, array('certificate_data' => $data) );
		
		echo wp_send_json( array('result' => 1) );
		
		wp_die();
	}

	public static function get_text_boudingbox_certificate() {
		check_ajax_referer( 'admin_quizmaker', 'security' );

		if(!isset($_POST['data'])){
			return false;
		}

		$text 		=	$_POST['data']['content'];
		$color 		=	isset($_POST['data']['color']) ? $_POST['data']['color'] : '#000000';
		$font_name	= 	$_POST['data']['font_name'];
		$font_size	=	absInt( $_POST['data']['font_size'] );

		$image 		=	new QM_Image();

		$result 	=	$image->getTextBoundingBox( $text,
			array(
				'font_size' =>	$font_size,
				'font_name'	=>	$font_name, 
				'color' 	=>	$color
			)
		);

		echo wp_send_json( $result );

		wp_die();
	}

	public static function preview_certificate() {
		check_ajax_referer( 'admin_quizmaker', 'security' );

		$certificate_id	=	absInt($_GET['id']);

		if(!$certificate_id){
			return false;
		}

		$background_id	=	qm_get_post_meta( $certificate_id, 'certificate_background' );
		$data			=	qm_get_post_meta( $certificate_id, 'certificate_data');
		
		$background_src	=	wp_get_attachment_image_src( $background_id, 'full' );
		
		if( isset($background_src[0]) && $background_src[0] )
		{
			$certificate	=	new QM_Image( array('src' => $background_src[0]) );

			$data			=	qm_get_post_meta( $certificate_id, 'certificate_data');

			if($data && is_array($data)){
				foreach( $data as $d ) {
					switch( $d['type'] ){
						case 'test_name':
							
							$certificate->addText( array(
								'text' 			=> 'Dump Textname',
								'font_name' 	=> $d['font_name'],
								'font_size'		=> $d['font_size'],
								'text_align'	=> $d['text_align'],
								'color'			=> $d['color'],
								'angle'			=>	0,
								'x'				=> $d['x'],
								'y'				=> $d['y']
							 ) );
							
						break;
						case 'name':
					
							$certificate->addText( array(
								'text' 			=> 'Dump Name',
								'font_name' 	=> $d['font_name'],
								'font_size'		=> $d['font_size'],
								'text_align'	=> $d['text_align'],
								'color'			=> $d['color'],
								'angle'			=>	0,
								'x'				=> $d['x'],
								'y'				=> $d['y']
							 ) );
							 
						break;
						case 'date':
							
							$date	=	qm_get_date_formated( qm_get_date('now'), qm_date_format() );
							
							$certificate->addText( array(
								'text' 			=> $date, 
								'font_name' 	=> $d['font_name'],
								'font_size'		=> $d['font_size'],
								'text_align'	=> $d['text_align'],
								'color'			=> $d['color'],
								'angle'			=>	0,
								'x'				=> $d['x'],
								'y'				=> $d['y']
							 ) );
							
						break;
						case 'rank':
							
							$certificate->addText( array(
								'text' 			=> 'Ranking A', 
								'font_name' 	=> $d['font_name'],
								'font_size'		=> $d['font_size'],
								'text_align'	=> $d['text_align'],
								'color'			=> $d['color'],
								'angle'			=>	0,
								'x'				=> $d['x'],
								'y'				=> $d['y']
							 ) );
							
						break;
						case 'id':
							
							$certificate->addText( array(
								'text' 			=> 'Certificate ID', 
								'font_name' 	=> $d['font_name'],
								'font_size'		=> $d['font_size'],
								'text_align'	=> $d['text_align'],
								'color'			=> $d['color'],
								'angle'			=>	0,
								'x'				=> $d['x'],
								'y'				=> $d['y']
							 ) );
							
						break;
						case 'text':
							
							$certificate->addText( array(
								'text' 			=>	$d['content'], 
								'font_name' 	=> $d['font_name'],
								'font_size' 	=> $d['font_size'],
								'text_align'	=> $d['text_align'],
								'color'			=> $d['color'],
								'angle'			=>	0,
								'x'				=> $d['x'],
								'y'				=> $d['y']
							 ) );
							
						break;
					}
				}
			}
			
			echo wp_send_json( array('url' => $certificate->store( 'preview_certificate_' . $certificate_id ) . '?v='.rand()) );

		}
		
		wp_die();
	}

	public static function load_html_lastest_general_results()
	{
		check_ajax_referer( 'quizmaker_load_html_lastest_general_results', 'security' );

		if(!isset($_POST['data']['test_id']) || !absInt($_POST['data']['test_id'])) return false;

		$test_id 	= 	absInt($_POST['data']['test_id']);
		$page 		=	max(absInt($_POST['data']['page']), 1);
		
		$test		=	QM()->test_factory->get_test( $test_id );

		$results 	=	$test->get_lastest_pagination_results( array('page' => $page, 'perpage' => 20));

		$total_questions = count( $test->get_questions() );

		$response	=	array('pagination' => $results['pagination'], 'status' => 'success');

		ob_start();

			include( 'admin/meta-boxes/views/html-admin-lastest-general-results.php' );
			
		$response['html'] = ob_get_clean();

		echo wp_send_json( $response );

		wp_die();
	}

	public static function load_html_all_general_results()
	{
		check_ajax_referer( 'quizmaker_load_html_all_general_results', 'security' );

		if(!isset($_POST['data']['test_id']) || !absInt($_POST['data']['test_id'])) return false;

		$test_id 	= 	absInt($_POST['data']['test_id']);
		$page 		=	max(absInt($_POST['data']['page']), 1);

		$test		=	QM()->test_factory->get_test( $test_id );

		$results 	=	$test->get_pagination_results( array('page' => $page, 'perpage' => 20));
	}

	public static function load_html_fixed_questions()
	{
		check_ajax_referer( 'quizmaker_load_html_fixed_questions', 'security' );

		if(!isset($_POST['data']['test_id']) || !absInt($_POST['data']['test_id'])) return false;

		$test_id 		= 	absInt($_POST['data']['test_id']);

		$exclude 		=	isset($_POST['data']['exclude']) ? $_POST['data']['exclude'] : array();

		$page 			=	!isset($_POST['data']['is_reset_page']) ? max(absInt($_POST['data']['page']), 1) : 1;

		$method 		=	isset($_POST['data']['method']) ? $_POST['data']['method'] : false;

		$is_refresh		=	isset($_POST['data']['action_data']['is_refresh']) ? $_POST['data']['action_data']['is_refresh'] : 1;

		$test			=	new QM_Test($test_id);

		$response		=	array( 'status' => 'success' );

		switch( $method ){

			case 'add':

				$ids 	=	isset($_POST['data']['action_data']['ids']) ? $_POST['data']['action_data']['ids'] : false;

				if( $ids ){

					$results	=	$test->add_fixed_questions( $ids );

					if( $results ){

						$response['message'] = array('type' => 'success', 'content' => __('Success! add fixed questions', 'quizmaker'));

					}else{

						$response['message'] = array('type' => 'error', 'content' => __('Error! can not add fixed questions', 'quizmaker'));
					}
				}

				break;

			case 'remove':

				$ids 	=	isset($_POST['data']['action_data']['ids']) ? $_POST['data']['action_data']['ids'] : false;

				if( $ids ) {

					$results	=	$test->remove_fixed_questions( $ids );

					if( $results ){

						$response['message'] = array('type' => 'success', 'content' => __('Success! Remove fixed questions', 'quizmaker'));

					}else{

						$response['message'] = array('type' => 'error', 'content' => __('Error! Can not remove fixed questions', 'quizmaker'));
					}
				}

				break;

			case 'get_order':

				$results 	=	$test->get_fixed_questions( array('page' => $page, 'perpage' => -1));

				$response['pagination'] = $results['pagination'];

				ob_start();

				include( 'admin/meta-boxes/views/html-admin-order-fixed-questions.php' );
				
				$response['html'] = ob_get_clean();

				if( $results ){

					$response['message'] = array('type' => 'success', 'content' => __('Success! order fixed questions', 'quizmaker'));

				}else{

					$response['message'] = array('type' => 'error', 'content' => __('Error! can not order fixed questions', 'quizmaker'));
				}

				 break;

			case 'update_order':

				$ids 	=	isset($_POST['data']['action_data']['ids']) ? $_POST['data']['action_data']['ids'] : false;

				if( $ids ){

					$results	=	$test->order_fixed_questions( $ids );

					if( $results ){

						$response['message'] = array('type' => 'success', 'content' => __('Success! order fixed questions', 'quizmaker'));

					}else{

						$response['message'] = array('type' => 'error', 'content' => __('Error! can not order fixed questions', 'quizmaker'));
					}
				}

				break;
		}
		
		if( $is_refresh ){

			$results 	=	$test->get_fixed_questions( array('page' => $page, 'perpage' => 20, 'exclude' => $exclude));

			$response['pagination'] = $results['pagination'];

			ob_start();

				include( 'admin/meta-boxes/views/html-admin-fixed-questions.php' );
				
			$response['html'] = ob_get_clean();
		}

		echo wp_send_json( $response );

		wp_die();
	}

	public static function load_html_assigned_users(){

		check_ajax_referer( 'quizmaker_load_html_assigned_users', 'security' );
		
		if(!isset($_POST['data']['test_id']) || !absInt($_POST['data']['test_id'])) return false;

		$test_id 		= 	absInt($_POST['data']['test_id']);
		$page 			=	!isset($_POST['data']['is_reset_page']) ? max(absInt($_POST['data']['page']), 1) : 1;

		$method 		=	isset($_POST['data']['method']) ? $_POST['data']['method'] : false;

		$is_refresh		=	isset($_POST['data']['is_refresh']) ? $_POST['data']['is_refresh'] : true;

		$test			=	new QM_Test($test_id);

		$response		=	array( 'status' => 'success' );

		switch( $method ){

			case 'add':

				$ids 	=	isset($_POST['data']['action_data']['ids']) ? $_POST['data']['action_data']['ids'] : false;

				if( $ids ){

					$results	=	$test->assign_users( $ids );

					if( $results ){

						$response['message'] = array('type' => 'success', 'content' => __('Success! Assigning users', 'quizmaker'));

					}else{

						$response['message'] = array('type' => 'error', 'content' => __('Error! Can not assign users', 'quizmaker'));
					}
				}

				break;

			case 'remove':

				$ids 	=	isset($_POST['data']['action_data']['ids']) ? $_POST['data']['action_data']['ids'] : false;

				if( $ids ){

					$results	=	$test->remove_assigned_users( $ids );

					if( $results ){

						$response['message'] = array('type' => 'success', 'content' => __('Success! Remove assigned users', 'quizmaker'));

					}else{

						$response['message'] = array('type' => 'error', 'content' => __('Error! Can not remove assigned users', 'quizmaker'));
					}
				}

				break;

			case 'email':

				$ids 	=	isset($_POST['data']['action_data']['ids']) ? $_POST['data']['action_data']['ids'] : false;

				if( $ids ){

					$mailer	=	QM()->mailer();
		
					$results	=	$mailer->assign_users( $ids, $test_id );

					if( $results ){

						$response['message'] = array('type' => 'success', 'content' => __('Success! Email assigned users', 'quizmaker'));

					}else{

						$response['message'] = array('type' => 'error', 'content' => __('Error! Can not email assigned users', 'quizmaker'));
					}
				}

				break;
		}
		
		if( $is_refresh ){
			$results 	=	$test->get_assigned_users( array('page' => $page, 'perpage' => 20));
			
			$response['pagination'] = $results['pagination'];

			ob_start();

				include( 'admin/meta-boxes/views/html-admin-assigned-users.php' );
				
			$response['html'] = ob_get_clean();
		}

		echo wp_send_json( $response );

		wp_die();
	}

	public static function load_html_user_groups(){

		check_ajax_referer( 'quizmaker_load_html_user_groups', 'security' );
		
		if(!isset($_POST['data']['group_id']) || !absInt($_POST['data']['group_id'])) return false;

		$group_id 		= 	absInt($_POST['data']['group_id']);
		$page 			=	!isset($_POST['data']['is_reset_page']) ? max(absInt($_POST['data']['page']), 1) : 1;

		$method 		=	isset($_POST['data']['method']) ? $_POST['data']['method'] : false;

		$is_refresh		=	isset($_POST['data']['is_refresh']) ? $_POST['data']['is_refresh'] : true;

		$response		=	array( 'status' => 'success' );

		switch( $method ){

			case 'add':

				$ids 	=	isset($_POST['data']['action_data']['ids']) ? $_POST['data']['action_data']['ids'] : array();

				if( $ids ){

					$old_ids = get_post_meta( $group_id, '_users', true );

					if( !$old_ids ){ $old_ids = array(); }

					$new_ids 	 = array_unique(array_merge( $old_ids, $ids ));

					update_post_meta( $group_id, '_users', $new_ids );

					$response['message'] = array('type' => 'success', 'content' => __('Success! Adding User', 'quizmaker'));
				}

				break;

			case 'remove':

				$removed_ids 	=	isset($_POST['data']['action_data']['ids']) ? $_POST['data']['action_data']['ids'] : array();

				$old_ids = get_post_meta( $group_id, '_users', true );
				
				$new_ids = array();

				foreach( $old_ids as $id ) {

					if(!in_array( $id, $removed_ids )){

						$new_ids[] = $id;
					}
				}

				
				update_post_meta( $group_id, '_users', $new_ids );

				$response['message'] = array('type' => 'success', 'content' => __('Success!', 'quizmaker'));

				break;
		}
		
		if( $is_refresh ){

			$users = array();

			$ids = get_post_meta( $group_id, '_users', true );
			
			if( $ids ){
				$user_query = 	new WP_User_Query( array( 
					'include' => $ids, 
					'number'  => -1, 
				) );

				$users 	=	$user_query->results;

			}

			ob_start();

				include( 'admin/meta-boxes/views/html-admin-user-groups.php' );
				
			$response['html'] = ob_get_clean();
		}

		$response['pagination'] = array('page' => 1);

		echo wp_send_json( $response );

		wp_die();
	}


	public static function load_html_result() {

		check_ajax_referer( 'load_html_result', 'security' );

		if(!isset($_POST)) return false;

		$result = qm_questions_get_results($_POST['data']);

		ob_start();

		qm_get_template( 'content-shortcode-viral-result.php',  $result );

		$html = ob_get_clean();

		$response = array( 'html' => $html, 'questions' => $result['questions'] );
		
		wp_send_json( $response );
	}

	public static function question_report() {

		check_ajax_referer( 'quizmaker_submit_question_report', 'security' );

		$id 		= isset($_POST['data']['id']) ? absint( $_POST['data']['id'] ) : 0;
		$content 	= isset($_POST['data']['content']) ? sanitize_text_field( $_POST['data']['content'] ) : 0;

		if( !is_user_logged_in() || !$id || !$content ){ return false; exit; }

		$user = get_currentuserinfo();

		$time = current_time('mysql');

		$data = array(
		    'comment_post_ID' => $id,
		    'comment_author' => $current_user->user_login,
		    'comment_author_email' => $current_user->user_email,
		    'comment_content' => $content,
		    'comment_type' => '',
		    'comment_approved' => 0,
		    'user_id' => $user->ID
		);

		wp_new_comment($data);


		$response = array( 'html' => $_SERVER );
		
		wp_send_json( $response );
	}

	public static function test_rating_from_result() {

		check_ajax_referer( 'quizmaker_submit_test_rating_from_result', 'security' );

		$star 		= isset($_POST['data']['star']) ? absint( $_POST['data']['star'] ) : 0;
		$test_id 	= isset($_POST['data']['test_id']) ? absint( $_POST['data']['test_id'] ) : 0;
		$user_id 	= get_current_user_id();

		if( !$user_id || $star <= 0 || $star > 5 || $test_id <= 0 ){ return false; exit; }

		$test 		= new QM_Test( $test_id );

		$test->set_rating( $user_id, $star );

		$response = array( 'star' => $star );
		
		wp_send_json( $response );
	}

	public static function load_html_certificate_tests() {

		check_ajax_referer( 'quizmaker_load_html_certificate_tests', 'security' );
		
		if(!isset($_POST['data']['id']) || !absInt($_POST['data']['id'])) return false;

		$cert_id 		= 	absint( $_POST['data']['id'] );

		$page 			=	!isset($_POST['data']['is_reset_page']) ? max(absInt($_POST['data']['page']), 1) : 1;

		$method 		=	isset($_POST['data']['method']) ? $_POST['data']['method'] : false;

		$is_refresh		=	isset($_POST['data']['is_refresh']) ? $_POST['data']['is_refresh'] : true;

		$response		=	array( 'status' => 'success' );

		switch( $method ){

			case 'add':

				$ids 	=	isset($_POST['data']['action_data']['ids']) ? $_POST['data']['action_data']['ids'] : array();

				if( $ids ){

					$tests = get_post_meta( $cert_id, '_tests', true );

					if( !$tests ){ $tests = array(); }

					foreach( $ids as $id ) {

						$tests[] = array('id' => $id, 'pass' => 50);
					}

					update_post_meta( $cert_id, '_tests', $tests );

					$response['message'] = array('type' => 'success', 'content' => __('Success! Adding User', 'quizmaker'));
				}

				break;

			case 'remove':

				$removed_ids 	=	isset($_POST['data']['action_data']['ids']) ? $_POST['data']['action_data']['ids'] : array();

				$tests = get_post_meta( $cert_id, '_tests', true );
				

				foreach( $tests as $index => $test ) {

					if( in_array( $test['id'], $removed_ids ) ){

						unset($tests[$index]);
					}
				}

				
				update_post_meta( $cert_id, '_tests', $tests );

				$response['message'] = array('type' => 'success', 'content' => __('Success!', 'quizmaker'));

				break;

			case 'add_pass':

				$tid 	=	isset($_POST['data']['action_data']['id']) ? $_POST['data']['action_data']['id'] : 0;
				$pass 	=	isset($_POST['data']['action_data']['pass']) ? $_POST['data']['action_data']['pass'] : 0;

				$tests = get_post_meta( $cert_id, '_tests', true );

				if( $tests ){
					foreach($tests as &$test) {

						if( $test['id'] == $tid && $pass >= 0 && $pass <= 100 ) {

							$test['pass'] = absint( $pass );
						}
					}

					update_post_meta( $cert_id, '_tests', $tests );
				}
				
				break;
		}
		
		if( $is_refresh ){

			$cert_tests = get_post_meta( $cert_id, '_tests', true );
			
			if( $cert_tests ){

				$test_ids = array();

				foreach( $cert_tests as $t ) {

					$test_ids[] = $t['id'];
				}

				$tests = get_posts(array(
					'include' 	=> $test_ids,
					'post_type' => 'test'
				));
				
				foreach( $cert_tests as &$ct ) {

					foreach( $tests as $t ) {

						if( $ct['id'] == $t->ID ){

							$ct['title'] 	= $t->post_title;
							$ct['link'] 	= admin_url('post.php?post=' . $t->ID . '&action=edit');
						}
					}
				}
			}

			ob_start();

				include( 'admin/meta-boxes/views/html-admin-certificate-tests.php' );
				
			$response['html'] = ob_get_clean();
		}

		$response['pagination'] = array('page' => 1);

		echo wp_send_json( $response );

		wp_die();

	}

	public static function account_resource() {

		// check_ajax_referer( 'account_resource_quizmaker', 'security' );

		$method = isset($_GET['method']) ? $_GET['method'] : 'index';

		$qid = isset($_POST['data']['qid']) ? $_POST['data']['qid'] : 8;
		

		// if( !$qid ){ return false; }

		$response = array();

		switch ( $method ) {
			case 'results':

				$page		=	isset($_GET['data']['page']) ? absint($_GET['data']['page']) : 1;
		
				$userid     =	apply_filters('quizmaker_view_results_of_user', get_current_user_id());
				
				$results	=	qm_get_user_results( $userid, $page );
				
				$results['pagination']['link']	=	qm_get_endpoint_url( 'view-results', '', qm_get_page_permalink( 'myaccount' ) );

				$response = $results;

			break;
			case 'result':

				$test_id 	=	isset($_GET['tid']) ? absint($_GET['tid']) : 0;

				$test		=	QM()->test_factory->get_test($test_id);
				
				$response	=	$test->get_user_results(get_current_user_id(), -1);

				$response['test_link']  = $test->get_permalink();
				$response['test_title'] = $test->get_title();
				

			break;
			case 'assigned_tests':

				$page		=	1;
		
				$response	=	qm_get_user_tests(get_current_user_id(), $page);

			break;
			case 'update_user_profile':
				
				$errors       = new WP_Error();
				$user         = new stdClass();

				$user->ID     = (int) get_current_user_id();
				$current_user = get_user_by( 'id', $user->ID );

				if ( $user->ID <= 0 ) {
					return;
				}
					
				$account_first_name = ! empty( $_POST[ 'account_first_name' ] ) ? qm_clean( $_POST[ 'account_first_name' ] ) : '';
				$account_last_name  = ! empty( $_POST[ 'account_last_name' ] ) ? qm_clean( $_POST[ 'account_last_name' ] ) : '';
				$account_email      = ! empty( $_POST[ 'account_email' ] ) ? sanitize_email( $_POST[ 'account_email' ] ) : '';

				$username 			= ! empty( $_POST[ 'account_username' ] ) ? sanitize_user( $_POST[ 'account_username' ], 50 ) : $current_user->user_login;
				$pass1              = ! empty( $_POST[ 'password_1' ] ) ? $_POST[ 'password_1' ] : '';
				$pass2              = ! empty( $_POST[ 'password_2' ] ) ? $_POST[ 'password_2' ] : '';
				$save_pass          = true;

				$user->first_name   = $account_first_name;
				$user->last_name    = $account_last_name;

				// Prevent emails being displayed, or leave alone.
				$user->display_name = is_email( $current_user->display_name ) ? $user->first_name : $current_user->display_name;
				
				// Handle required fields
				$required_fields = apply_filters( 'quizmaker_save_account_details_required_fields', array(
					'account_first_name' => __( 'First Name', 'quizmaker' ),
					'account_last_name'  => __( 'Last Name', 'quizmaker' ),
					'account_email'      => __( 'Email address', 'quizmaker' ),
				) );
				
				foreach ( $required_fields as $field_key => $field_name ) {
					if ( empty( $_POST[ $field_key ] ) ) {
						qm_add_message( '<strong>' . esc_html( $field_name ) . '</strong> ' . __( 'is a required field.', 'quizmaker' ), 'error' );
					}
				}
				
				if ( $account_email ) {
					if ( ! is_email( $account_email ) ) {
						qm_add_message( __( 'Please provide a valid email address.', 'quizmaker' ), 'error' );
					} elseif ( email_exists( $account_email ) && $account_email !== $current_user->user_email ) {
						qm_add_message( __( 'This email address is already registered.', 'quizmaker' ), 'error' );
					}
					$user->user_email = $account_email;
				}

				if( $username != $current_user->user_login ) {
					
					if( username_exists( $username ) ) {

						qm_add_message( __( 'This username is already registered.', 'quizmaker' ), 'error' );

					}
							
				}

				if ( ! empty( $pass1 ) && empty( $pass2 ) ) {
					qm_add_message( __( 'Please re-enter your password.', 'quizmaker' ), 'error' );
					$save_pass = false;
				} elseif ( ( ! empty( $pass1 ) || ! empty( $pass2 ) ) && $pass1 !== $pass2 ) {
					qm_add_message( __( 'New passwords do not match.', 'quizmaker' ), 'error' );
					$save_pass = false;
				}
				
				if ( $pass1 && $save_pass ) {
					$user->user_pass = $pass1;
				}
				
				// Allow plugins to return their own errors.
				do_action_ref_array( 'quizmaker_save_account_details_errors', array( &$errors, &$user ) );
				
				if ( $errors->get_error_messages() ) {
					foreach ( $errors->get_error_messages() as $error ) {
						qm_add_message( $error, 'error' );
					}
				}

				
				
				if ( qm_message_count( 'error' ) === 0 ) {
					
					wp_update_user( $user ) ;
					delete_user_meta( $user->ID, 'is_guest' );

					if( $username != $current_user->user_login ) {

						$wpdb->update( $wpdb->prefix . 'users', array(
									'user_login'	=>	$username,
									'user_nicename' =>	$username,
									'display_name'	=>	$username
									), array('ID' => $user->ID), array('%s', '%s', '%s') );

					}
						
					do_action( 'quizmaker_save_account_details', $user->ID );
				}

				qm_clear_messages();

			break;
			case 'user_profile':

				$current_user = wp_get_current_user();

				$user_profile = array(
					'first_name' 	=> $current_user->user_firstname,
					'last_name' 	=> $current_user->user_lastname,
					'email' 		=> $current_user->user_email,
					'username' 		=> $current_user->user_login,
				);

				$response = $user_profile;

			break;
			case 'logout':

				wp_logout();

				$response = array('redirect' => home_url());

			break;
			default:



			break;
		}

		
		wp_send_json( $response );
		wp_die();
	}

	public static function importing_questions(){

		check_ajax_referer( 'admin_quizmaker_import_questions', 'security' );
		
		$fh = fopen($_FILES['question_csv']['tmp_name'], 'r+');

		$lines = array();

		while( ($row = fgetcsv($fh)) !== FALSE ) {

			$lines[] = $row;

		}

		if( $lines ) {

			array_shift( $lines );

			$lines = array_values($lines);

			foreach( $lines as &$line ) {

				$answer_type = $line[0];

				$params = array(
						'_score' => $line[5], 
						'_explanation' => $line[4], 
						'_order_type' => $line[6],
						'_answer-type' => $answer_type
					);

				$answers = array();
				$answer_start_index = 9;

				for( $i = $answer_start_index; $i < count($line); $i++ ){

					if( $line[$i] ) {
						
						$answers[] = array( 'content' => $line[$i], 'image' => '' );
					}
				}

				if( $answer_type == 'single' ) {

					$ans_id_correct	=	absInt($line[8]);

					foreach($answers as $id => &$value){

						if( $id == $ans_id_correct ){
							$value['is_correct']	=	1;
						}else{
							$value['is_correct']	=	-1;
						}
					}

					$params['_params_single'] =	array( 'columns' => absInt($line[7]) );

				}elseif ( $answer_type == 'multiple' ) {
					
					$ans_id_correct	=	explode(',', $line[8]);

					foreach($answers as $id => &$value){

						if(in_array($id, $ans_id_correct)){
							$value['is_correct']	=	1;
						
						}else{
							$value['is_correct']	=	-1;
						}
					}

					$params['_params_multiple'] =	array( 'columns' => absInt($line[7]) );
				}
					
				$params['_answers'] = $answers;

				$post_id = wp_insert_post( array(
					"post_type" 	=> 	'question',
					"post_status" 	=> 	'publish',
					"post_title" 	=> 	$line[2],
					"post_content" 	=> 	$line[3],
					"tax_input"		=>	array( 'question_cat' => explode(',', $line[1]) ),
					"meta_input"	=>	$params
				));

				$line['url'] =  admin_url( 'post.php?post=' . $post_id . '&action=edit' );
			}
		}

		echo wp_send_json( $lines );
		
		wp_die();
	}

	public static function question_ordering() {
		global $wpdb;

		$sorting_id  = absint( $_POST['id'] );
		$previd      = absint( isset( $_POST['previd'] ) ? $_POST['previd'] : 0 );
		$nextid      = absint( isset( $_POST['nextid'] ) ? $_POST['nextid'] : 0 );
		$menu_orders = wp_list_pluck( $wpdb->get_results( "SELECT ID, menu_order FROM {$wpdb->posts} WHERE post_type = 'question' ORDER BY menu_order ASC, post_title ASC" ), 'menu_order', 'ID' );
		$index       = 0;

		foreach ( $menu_orders as $id => $menu_order ) {
			$id = absint( $id );

			if ( $sorting_id === $id ) {
				continue;
			}
			if ( $nextid === $id ) {
				$index ++;
			}
			$index ++;
			$menu_orders[ $id ] = $index;
			$wpdb->update( $wpdb->posts, array( 'menu_order' => $index ), array( 'ID' => $id ) );

		}

		if ( isset( $menu_orders[ $previd ] ) ) {
			$menu_orders[ $sorting_id ] = $menu_orders[ $previd ] + 1;
		} elseif ( isset( $menu_orders[ $nextid ] ) ) {
			$menu_orders[ $sorting_id ] = $menu_orders[ $nextid ] - 1;
		} else {
			$menu_orders[ $sorting_id ] = 0;
		}

		$wpdb->update( $wpdb->posts, array( 'menu_order' => $menu_orders[ $sorting_id ] ), array( 'ID' => $sorting_id ) );

		wp_send_json( $menu_orders );
	}

	public static function wpmedia() {

		check_ajax_referer( 'admin_quizmaker', 'security' );

		$method = isset($_POST['data']['method']) ? $_POST['data']['method'] : 'get';

		$id 	= isset($_POST['data']['id']) ? absint($_POST['data']['id']) : 0;

		$size 	= isset($_POST['data']['size']) ? $_POST['data']['size'] : 'thumbnail';

		$response = array();

		$image = wp_get_attachment_image_src( $id, $size );
		
		if( $image ) {

			$response['data']['image'] = array(
				'url' => $image[0],
				'width' => $image[1],
				'height' => $image[2]
			);
		}
		
		wp_send_json( $response );
	}

	public static function quiz_resource() {

		check_ajax_referer( 'admin_quizmaker', 'security' );

		$method = isset($_POST['data']['method']) ? $_POST['data']['method'] : 'index';

		$qid = isset($_POST['data']['qid']) ? $_POST['data']['qid'] : false;
		

		if( !$qid ){ return false; }

		$response = array();

		switch ( $method ) {
			case 'save':
				
				$question = isset($_POST['data']['question']) ? $_POST['data']['question'] : false;

				$quiz = new QM_Quiz( $qid );

				$quiz->add_question( $question );

				$response['status'] = $question;

				break;

			case 'update':

				$data 	= isset($_POST['data']['question']) ? $_POST['data']['question'] : false;

				$quiz = new QM_Quiz( $qid );

				$quiz->update_question( $data );

				$response['status'] = 1;

				break;
			
			case 'remove':

				$questions 	= isset($_POST['data']['questions']) ? $_POST['data']['questions'] : false;

				if( $questions ){
				
					$quiz = new QM_Quiz( $qid );

					$quiz->remove_questions( $questions );

					$response['status'] = 1;
				}

				break;

			case 'double':

				$quiz = new QM_Quiz( $qid );

				$id 	= isset($_POST['data']['id']) ? $_POST['data']['id'] : false;

				$question = $quiz->double_question( $id );

				$response['status'] = 1;

				break;

			case 'sort':

				$sort_ids 	= isset($_POST['data']['sort_ids']) ? $_POST['data']['sort_ids'] : false;

				if( $sort_ids ){
				
					$quiz = new QM_Quiz( $qid );

					$quiz->sort_questions( $sort_ids );

					$response['status'] = 1;
				}

				break;

			case 'update_settings':

				$quiz = new QM_Quiz( $qid );

				$settings 	= isset($_POST['data']['settings']) ? $_POST['data']['settings'] : false;

				$quiz->update_settings($settings);

				$response['status'] = 1;

				break;

			case 'get':

				$quiz = new QM_Quiz( $qid );

				$id 	= isset($_POST['data']['id']) ? $_POST['data']['id'] : false;

				$response['data']['item'] 	  = $quiz->get_question( $id );

				break;	

			case 'play':

				$quiz = new QM_Quiz( $qid );

				$page 	= isset($_POST['data']['page']) ? $_POST['data']['page'] : false;

				$response['data']['item'] = $quiz->get_play_question( $page );

				break;

			case 'answer':

				$answer = isset($_POST['data']['answer']) ? $_POST['data']['answer'] : false;

				$quiz 	= new QM_Quiz( $qid );

				$response['data']['result'] = array(
					'result' => $quiz->is_correct( $answer )
				);
				
				break;

			case 'analytics':

				$qid = get_post_meta( $qid, '_quiz', true );

				$quiz = new QM_Quiz( $qid );

				$response['data'] = array(
					'questions' => $quiz->get_analytic_questions()
				);

				break;

			case 'load_marketing_app':

				$marketing_mailchimp   = new QM_Marketing_Mailchimp();

				$marketing_getresponse = new QM_Marketing_Getresponse();

    			$mailchimp_lists = $marketing_mailchimp->getLists();
    			
    			$getresponse_lists = $marketing_getresponse->getLists();

    			$data = array(
    				'data' => array(),
    				'value' => array(),
    			);

    			
    			if( $mailchimp_lists ) {

    				$data['data']['mailchimp'] = array(
						'lists' => $mailchimp_lists['lists']
					);
    			}

    			if( $getresponse_lists ) {

    				$data['data']['getresponse'] = array(
						'lists' => $getresponse_lists
					);
    			}

    			$value = get_post_meta( $qid, '_marketing_app', true );

    			$data['value']['mailchimp'] = isset($value['mailchimp']) ? $value['mailchimp'] : array('lists' => array());

    			$data['value']['getresponse'] = isset($value['getresponse']) ? $value['getresponse'] : array('lists' => array());

    			$response['data'] = $data;

				break;

			case 'save_marketing_app':

				$marketing_app = get_post_meta( $qid, '_marketing_app', true );

				if( !$marketing_app ) {

					$marketing_app = wp_parse_args( $marketing_app, array(
						'mailchimp' => array(),
						'getresponse' => array(),
					) );
				}

				$marketing_app['mailchimp'] = isset($_POST['data']['marketing']['mailchimp']) ? $_POST['data']['marketing']['mailchimp'] : array();
				
				$marketing_app['getresponse'] = isset($_POST['data']['marketing']['getresponse']) ? $_POST['data']['marketing']['getresponse'] : array();

				update_post_meta( $qid, '_marketing_app', $marketing_app );
				
				$response['data'] = $marketing_app;

				break;

			default:

				$quiz = new QM_Quiz( $qid );

				$questions = $quiz->questions;
				
				$response['data']['settings']  = $quiz->get_settings();
				
				if( $questions ){
					foreach( $questions as &$question ) {

						if( $question['type'] == 1 ) {

							$question['type_name'] = __('Text', 'quizmaker');

						}elseif( $question['type'] == 2 ) {

							$question['type_name'] = __('Image', 'quizmaker');

						}elseif( $question['type'] == 3 ) {

							$question['type_name'] = __('Scale', 'quizmaker');

						}elseif( $question['type'] == 4 ) {

							$question['type_name'] = __('Sorting', 'quizmaker');

						}elseif( $question['type'] == 5 ) {

							$question['type_name'] = __('Guess', 'quizmaker');

						}elseif( $question['type'] == 6 ) {

							$question['type_name'] = __('Keyword', 'quizmaker');

						}elseif( $question['type'] == 7 ) {

							$question['type_name'] = __('Item Match', 'quizmaker');

						}elseif( $question['type'] == 8 ) {

							$question['type_name'] = __('Group Match', 'quizmaker');
						}
					}
				}

				if( $questions ){

					$response['data']['items'] = $questions;

				}else{

					$response['data']['items'] = array();
				}

				break;
		}

	

		wp_send_json( $response );
	}

	public static function quiz_player() {

		check_ajax_referer( 'quiz-player', 'security' );

		$method = isset($_POST['data']['method']) ? $_POST['data']['method'] : 'index';

		$qid = isset($_POST['data']['qid']) ? $_POST['data']['qid'] : false;
		

		if( !$qid ){ return false; }

		$response = array();

		switch ( $method ) {

			case 'intro':

				$quiz = new QM_Quiz( $qid );

				$response['data']['settings'] 	  = $quiz->get_settings();

				break;

			case 'setup':

				$quiz 	 = new QM_Quiz( $qid );

				$session = new QM_Quiz_Session();

				$settings = $quiz->get_settings();

				$session->set('quiz', array(
					'id' => $qid,
					'result' => array(),
					'time_start' => qm_get_date('now')->format('U'),
					'ip' => $_SERVER['REMOTE_ADDR'],
					'questions' => $quiz->get_question_ids($settings['is_random'] == 'true')
				));

				$quiz->update_analytic_view();

				$response['data']['settings'] = $settings;

				break;

			case 'get':

				$quiz = new QM_Quiz( $qid );

				$id 	= isset($_POST['data']['id']) ? $_POST['data']['id'] : false;

				$response['data']['item'] 	  = $quiz->get_question( $id );

				break;

			case 'play':

				$quiz = new QM_Quiz( $qid );

				$page 	= isset($_POST['data']['page']) ? $_POST['data']['page'] : false;

				$response['data']['item'] = $quiz->get_play_question( $page );

				break;

			case 'answer':

				$answer = isset($_POST['data']['answer']) ? $_POST['data']['answer'] : false;

				$quiz 	= new QM_Quiz( $qid );

				$is_correct = $quiz->is_correct( $answer );

				$answer['is_correct'] = $is_correct;

				$session = new QM_Quiz_Session();

				$quiz_session = $session->get('quiz');

				if( !isset($quiz_session['result']) ) {

					$quiz_session['result'] = array();
				}

				$quiz_session['result'][] = $answer;

				$session->set('quiz', $quiz_session);
				
				$response['data']['result'] = array(
					'result' => $is_correct,
					's' => $quiz_session
				);

				break;
				
			case 'finish':

				$user 		 = isset($_POST['data']['user']) ? $_POST['data']['user'] : array();
				$plus_points = isset($_POST['data']['plus_points']) ? absint($_POST['data']['plus_points']) : 0;
				
				$session = new QM_Quiz_Session();

				$quiz_session = $session->get('quiz');
				
				$quiz_session['user'] = $user;
				$quiz_session['plus_points'] = $plus_points;

				$session->set('user', $user);

				$quiz 	= new QM_Quiz( $qid );
				
				$result  = $quiz->store_result( $quiz_session );

				$ranking =  $quiz->get_lastest_pagination_results(array(
					'order' => 'r.score DESC',
					'perpage' => 10
				));

				if( ($quiz->settings['is_share_for_view_result'] == 'true') || ( $plus_points > 0 ) ) {

					$quiz->update_analytic_total_share();
				}

				$response['data']['settings'] = $quiz->settings;
				
				$response['data']['result']   = $result;
				$response['data']['ranking']  = $ranking['data'];
				$response['data']['user_ranking']  = $quiz->get_user_ranking($user['your_email']);
				
				break;

			case 'rating':

				$rating 	= isset($_POST['data']['rating']) ? absint($_POST['data']['rating']) : 0;

				$quiz 	= new QM_Quiz( $qid );

				$response['data']['rating']   = $quiz->setRating( $rating );

				break;

			case 'analytic_share':



				break;

			default:


				break;
		}

	

		wp_send_json( $response );
	}

	public static function test_resource() {

		check_ajax_referer( 'quizmaker_test_resource', 'security' );

		$method = isset($_GET['method']) ? $_GET['method'] : 'index';

		$response = array();

		switch ( $method ) {
			case 'search_html':

				$categories 	= 	isset($_GET['data']['categories']) ? $_GET['data']['categories'] : '';
				$page 			= 	isset($_GET['data']['page']) ? absint($_GET['data']['page']) : 2;

				$perpage = 9;
				$offset = ($perpage * ($page - 1));

				$args = [
					'post_type' => 'test',
					'posts_per_page' => $perpage,
					'offset' => $offset
				];
				
				if( $categories ) {

					$args['tax_query'] = [array(
							'taxonomy' => 'test_cat',
							'field'    => 'id',
							'terms'    => $categories
						)];
				}

				$results = get_posts($args);

				$resultsData = [];

				if( $results ){

					foreach( $results as $rs ) {

						$categories = wp_get_post_terms( $rs->ID, 'test_cat' );

						ob_start();

						qm_get_template('content-widget-test-filters-results.php', array('data' => $rs, 'categories' => $categories));
						
						$resultsData[] = ob_get_clean();

					}

					$response['results'] = $resultsData;

				}

				if( !$results ) {

					$response['is_last'] = 1;

				}else{

					$response['is_last'] = 0;
				}


			break;

		}


		echo wp_send_json( $response );

		wp_die();
	}
}

QM_AJAX::init();