<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QM_Frontend_Scripts {
	
	public static function init() {

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_styles' ) );
		
	}

	public static function load_styles()
	{
		// if( is_quizmaker() ) {

			$min_js	=	qm_is_debug() ? '' : '.min';

			$isLoadBootstrapLibrary = (get_option('quizmaker_is_load_bootstrap_library') == 'no') ? false : true;

			
		
			wp_enqueue_style( 'quizmaker-google-fonts', 'https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' );

			if(get_option('quizmaker_is_default_stylesheet') == 'yes') {
				
				wp_enqueue_style( 'quizmaker-frontend', QUIZMAKER_URI . 'assets/css/frontend.css', array('quizmaker-google-fonts'), '1.6.4' );

				wp_enqueue_style( 'quizmaker-frontend-extend', QUIZMAKER_URI . 'assets/css/frontend-extend.css', array('quizmaker-frontend'), '1.0.7' );
			}

			if( $isLoadBootstrapLibrary ) {
				
				wp_enqueue_style('quizmaker-bootstrap-library', QUIZMAKER_URI . 'assets/vendor/bootstrap/css/bootstrap' . $min_js . '.css', array(), '4.0.2');
			}
		// }
	}
	
	public static function load_scripts()
	{
		
		global $post;
		
		if ( ! did_action( 'before_quizmaker_init' ) ) {
			return;
		}
		
		$min_js	=	qm_is_debug() ? '' : '.min';

		wp_register_script( 'quizmaker_vendor', QUIZMAKER_URI . 'assets/js/vendor.js', array('jquery'), '1.0.2' );
		wp_register_script( 'quizmaker_vue', QUIZMAKER_URI . 'assets/js/vue.js', array('jquery'), '1.0.1' );

		wp_register_script( 'frontend-quizmaker', QUIZMAKER_URI . 'assets/js/frontend/quizmaker' . $min_js . '.js', array('jquery', 'jquery-ui-core', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable', 'underscore' ), '1.0.2', true );
		
		wp_register_script( 'vue-frontend-quizmaker', QUIZMAKER_URI . 'assets/js/front.min.js', array('jquery', 'jquery-ui-slider', 'jquery-ui-core', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable', 'underscore', 'quizmaker_vue' ), '1.3.3', true );

		wp_register_script( 'vue-myaccount-quizmaker', QUIZMAKER_URI . 'assets/js/myaccount.min.js', array('jquery', 'jquery-ui-slider', 'jquery-ui-core', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable', 'underscore', 'quizmaker_vue' ), '1.0.4', true );
		
		wp_register_script( 'quizmaker-viral', QUIZMAKER_URI . 'assets/js/frontend/viral' . $min_js . '.js', array('frontend-quizmaker'), '1.0.0', true );

		$isLoadBootstrapLibrary = (get_option('quizmaker_is_load_bootstrap_library') == 'no') ? false : true;
		

		

		if( $isLoadBootstrapLibrary ) {
			
			wp_enqueue_script('quizmaker-bootstrap-library', QUIZMAKER_URI . 'assets/vendor/bootstrap/js/bootstrap' . $min_js . '.js', array('jquery'), '4.0.1');
		}

		wp_enqueue_script( 'vue-frontend-quizmaker' );

		wp_localize_script( 'vue-frontend-quizmaker', 'frontendQuizmaker', [
			'ajax_url' => site_url() . '?qm-ajax='
		] );
		
		if( is_quiz() || is_test_taxonomy() || is_test_category() ){
			
			wp_enqueue_script('quizmaker_archive_script', QUIZMAKER_URI . 'assets/js/frontend/archive' . $min_js . '.js', array('jquery', 'underscore'));
		}
		
		if( is_test() ) {
			wp_enqueue_script( 'quizmaker_vendor' );
			wp_enqueue_script( 'frontend-quizmaker' );
			
			wp_enqueue_script( 'recaptcha', 'https://www.google.com/recaptcha/api.js' );
		}

		if( is_question() ) {

			wp_enqueue_script( 'quizmaker_preview_question_script', 
					QUIZMAKER_URI . 'assets/js/frontend/preview_question' . $min_js . '.js', 
					array( 'jquery', 'jquery-ui-core', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable', 'underscore', 'quizmaker_vendor'), '1.0.7' );
		}
		
		if( is_doing() ){
			
			$session    	= 	new QM_Test_Session();
			
			$session_data	=	$session->get('doing');
			$test			=	QM()->test_factory->get_test($post->ID);

			$type_testing 	=	$test->get_type();
			$settings		=	$test->get_settings();
			$user_id		=	get_current_user_id();
			
			wp_enqueue_script( 'quizmaker_doing_script', 
					QUIZMAKER_URI . 'assets/js/frontend/doing' . $min_js . '.js', 
					array( 'jquery', 'jquery-ui-core', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable', 'underscore', 'quizmaker_vendor'), '1.1.3' );

			$ajax_nonce =	wp_create_nonce( "quizmaker_doing_test" );
			
			$home_url = is_ssl() ? home_url('/', 'https') : home_url();
			
			$frontend_js_params 	=	array(
				'plugin_url'	=>	QUIZMAKER_URI,
				'site_url'		=>	$home_url,
				'ajax_url'		=>	admin_url( 'admin-ajax.php' ),
				'security'		=>	$ajax_nonce,
				'post_id'		=>	$post->ID,
				'post_link'		=>	get_permalink( $post->ID ),
				'uid'			=>	$user_id,
				'type_testing'	=>	$type_testing,
				'trans'			=>	['confirm_submit' => __('Do you want to submit this test ?', 'quizmaker')]
			);

			if( $type_testing == 1 ){

				$frontend_js_params['adaptive_max_round']	=	$settings['adaptive_max_round'];
				$frontend_js_params['adaptive_times']		=	$settings['adaptive_times'];

				$frontend_js_params['adaptive_round']		=	$session_data['adaptive_round'];
				$frontend_js_params['adaptive_percent']		=	$session_data['adaptive_percent'];
			}
			
			if($settings['duration']){
				
				$frontend_js_params['duration']	=	$settings['duration'];
			}
						
			if($settings['duration'] > 0 && isset($session_data['time_passed']) && $session_data['time_passed']){
				
				$frontend_js_params['time_passed']	=	time() - $session_data['time_start'];
			}else{
				
				$frontend_js_params['time_passed']	=	0;
			}
		
			wp_localize_script( 'quizmaker_doing_script', 'quizmaker', $frontend_js_params );
			
		}
		
		if( is_result() ){

			wp_enqueue_script('quizmaker_result_script', QUIZMAKER_URI . 'assets/js/frontend/result' . $min_js . '.js', array('frontend-quizmaker'), '1.0.7');

			$result_params = array(
				'ajax_url'					=>	admin_url( 'admin-ajax.php' ),
				'test_id'					=>	$post->ID,
				'security_question_report'	=>	wp_create_nonce( "quizmaker_submit_question_report" ));

			if( get_option('quizmaker_is_rating') == 'yes' ) {

				$result_params['security_test_rating_from_result'] = wp_create_nonce( "quizmaker_submit_test_rating_from_result" );
			}
			
			wp_localize_script( 'quizmaker_result_script', 'quizmaker', $result_params );
			
		}

		
		if( is_myaccount() ) {

			wp_enqueue_script( 'quizmaker_vendor' );
			wp_enqueue_script( 'vue-frontend-quizmaker' );

			wp_add_inline_script( 'vue-frontend-quizmaker', "qmMyaccount = null;");
		}

		
	}
}
QM_Frontend_Scripts::init();