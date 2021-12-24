<?php
/**
 * QuizMaker Admin
 *
 * @class       QM_Admin
 * @author      AWSTheme
 * @category    Admin
 * @package     QuizMaker/Admin
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * QM_Admin class.
 */
class QM_Admin {
	
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'init', array( $this, 'download_csv_test_result' ) );
		add_action( 'current_screen', array( $this, 'conditional_includes' ) );
		add_action( 'delete_user', array( $this, 'delete_user' ) );
		add_action( 'before_delete_post', array( $this, 'delete_results' ) );
		add_action( 'user_register', array( $this, 'new_member' ));
		add_action( 'user_new_form', array( $this, 'referer_member' ) );

		add_action( 'save_post', array( $this, 'new_analytics' ) );
		add_action( 'before_delete_post', array( $this, 'delete_analytics' ) );

		add_filter( 'mce_buttons_2', array( $this, 'my_mce_buttons_2' ) );
		
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts'), 10 );
	}

	function my_mce_buttons_2($buttons) {	
		/**
		 * Add in a core button that's disabled by default
		 */
		$buttons[] = 'superscript';
		$buttons[] = 'subscript';

		return $buttons;
	}
	
	public function referer_member( $type ) {
		
		if(isset($_GET['qm_admin_new_member'])){
			echo '<input type="hidden" name="qm_admin_new_member" value="1">';
		}
		
	}
	
	public function new_member( $user_id ) {
		
		if( $user_id ){
			
			$member_id	=	qm_new_member($user_id);
			
			if(is_admin() && isset($_POST['qm_admin_new_member'])){
				wp_redirect(admin_url('post.php?post=' . $member_id . '&action=edit'));
				exit;
			}
			
		}
	}

	public function new_analytics( $quiz_id ) {

		$post_type = get_post_type($quiz_id);

		if( $post_type == 'quiz' && (get_post_status($quiz_id) == 'publish') ) {

			$analytic_id = get_post_meta( $quiz_id, '_analytic', true );
			
			$analytic = get_post( $analytic_id );

			if( !$analytic_id || !$analytic ) {

				$quiz = get_post( $quiz_id );

				$analytic_id = wp_insert_post(array(
					'post_type' => 'qanalytics',
					'post_title' => $quiz->post_title,
					'post_status' => 'publish',
					'meta_input' => array(
						'_quiz' => $quiz_id,
						'_view' => 0,
						'_player' => 0,
						'_total_share' => 0
					)
				));

				update_post_meta( $quiz_id, '_analytic', $analytic_id );

			}
		}
	}

	public function delete_analytics( $quiz_id ) {

		$post_type = get_post_type($quiz_id);

		if( $post_type == 'quiz' ) {

			$analytic_id = get_post_meta( $quiz_id, '_analytic', true );

			wp_delete_post( $analytic_id, true );
		}
	}

	

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {

		include_once( 'class-qm-admin-notices.php' );
		include_once( 'class-qm-admin-menus.php' );
		include_once( 'class-qm-admin-post-types.php' );
		include_once( 'qm-admin-functions.php' );


	}
	
	public function conditional_includes() {
		if ( ! $screen = get_current_screen() ) {
			return;
		}
		
		
	}

	public function remove_admin_enqueue_scripts() {

		wp_dequeue_script( 'select2');
		wp_deregister_script('select2');
		wp_dequeue_script( 'wc-admin-meta-boxes');
		wp_deregister_script('wc-admin-meta-boxes');
	}

	public function admin_enqueue_scripts() {
		global $post;
		
		$screen       = get_current_screen();
		$screen_id    = $screen ? $screen->id : '';
		
		if ( in_array( $screen_id, array(
			'question', 'edit-question', 'test', 'edit-test', 'certificate', 'edit-certificate', 'usergroup', 'edit-usergroup', 'quizmaker', 'quizmaker_page_qm-settings', 'quizmaker_page_qm-dashboard', 'quizmaker_page_qm-extensions', 'quizmaker_page_qm-importing-questions', 'quiz', 'edit-quiz', 'qanalytics', 'edit-qanalytics', 'quizmaker_page_qm-user-certificates'
		) ) ) {
			
			$ajax_nonce 		=	wp_create_nonce( "admin_quizmaker" );
			
			$lib_js_path		=	QUIZMAKER_URI . 'assets/js/libs/';
			$admin_js_path		=	QUIZMAKER_URI . 'assets/js/admin/';
			$admin_js_params 	=	array(
				'plugin_url'	=>	QUIZMAKER_URI,
				'ajax_url'		=>	admin_url( 'admin-ajax.php' ),
				'security'		=>	$ajax_nonce,
				'verify_link'	=>	admin_url( 'admin.php?page=qm-settings&tab=verify' ),
			);
			
			$min_js				=	qm_is_debug() ? '' : '.min';
			
			wp_register_script( 'axios', $lib_js_path . 'axios.min.js' );
			wp_register_script( 'vue', $lib_js_path . 'vue.min.js' );
			
			wp_register_script( 'aws-ui', $lib_js_path . 'aws-ui' . $min_js . '.js', array('jquery') );
			
			wp_register_script( 'imagesloaded', $lib_js_path . 'imagesloaded.pkgd' . $min_js . '.js', array('jquery') );

			wp_register_script( 'fitvids', QUIZMAKER_URI . 'assets/js/vendor/jquery.fitvids.js', array('jquery') );

			$this->_enqueue_select2_scripts( $min_js );
						
			wp_enqueue_script( 'quizmaker_script', $admin_js_path . 'quizmaker' . $min_js . '.js', array('jquery', 'backbone', 'underscore', 'imagesloaded', 'aws-ui', 'select2'), '2.0.2');
			
			wp_enqueue_script( 'quizmaker_admin_script', QUIZMAKER_URI . 'assets/js/admin' . $min_js . '.js', array( 'jquery', 'fitvids', 'jquery-ui-slider', 'jquery-ui-sortable' ), '1.2.2');

			wp_localize_script( 'quizmaker_script', 'quizmaker', $admin_js_params );
			
			
			switch($screen->id){
				case 'question':
					wp_enqueue_script( 'meta_boxes_answer_script', $admin_js_path . 'meta-boxes-answer' . $min_js . '.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'quizmaker_script'), '1.0.1');					
				break;
				case 'edit-question':
				
					wp_enqueue_script('jquery-ui-sortable');

					wp_enqueue_script('quizmaker_edit_question', $admin_js_path . 'question-ordering' . $min_js . '.js', array('jquery-ui-sortable'));
				break;
				case 'test':

					wp_enqueue_script( 'meta_boxes_test_script', $admin_js_path . 'meta-boxes-test' . $min_js . '.js', array('quizmaker_script', 'jquery-ui-core', 'jquery-ui-sortable' ), '1.0.3');
					
					wp_localize_script( 'meta_boxes_test_script', 'meta_boxes_test_script', array(
						'post_id'	=>	isset( $post->ID ) ? $post->ID : ''
					));
				break;
				case 'quizmaker_page_qm-dashboard':

					wp_enqueue_script( 'meta_boxes_dashboard_script', $admin_js_path . 'dashboard' . $min_js . '.js', array('quizmaker_script'));
					
				break;
				case 'certificate':

					wp_enqueue_style( 'wp-color-picker' );
					
					wp_enqueue_script( 'mousewheel_script', $lib_js_path . 'jquery.mousewheel.js');
					wp_enqueue_script( 'jscrollpane_script', $lib_js_path . 'jquery.jscrollpane.min.js', array('mousewheel_script'));

					wp_enqueue_script( 'meta_boxes_certificate_script', $admin_js_path . 'meta-boxes-certificate' . $min_js . '.js', array('jquery', 'jquery-ui-core', 'jquery-ui-draggable', 'jscrollpane_script', 'wp-color-picker', 'quizmaker_script'), '2.0.0');
					
					$post_id	=	isset( $post->ID ) ? $post->ID : '';
					$data		=	qm_get_post_meta($post_id, 'certificate_data');
					
					$data		=	$data ? $data : array();
					
					wp_localize_script( 'meta_boxes_certificate_script', 'meta_boxes_certificate', array(
						'post_id'		=>	$post_id,
						'data'			=>	$data
					));

				break;
				case 'usergroup':

					wp_enqueue_script( 'meta_boxes_usergroup_script', $admin_js_path . 'meta-boxes-usergroup' . $min_js . '.js', array('quizmaker_script', 'jquery-ui-core', 'jquery-ui-sortable' ), '1.0.2');

				break;
				case 'quizmaker_page_qm-importing-questions':

					wp_enqueue_script( 'axios' );
					wp_enqueue_script( 'vue' );
					
					wp_enqueue_script( 'importing_questions_script', $admin_js_path . 'importing_questions' . $min_js . '.js', array('vue'));

					wp_localize_script( 'importing_questions_script', 'importing_questions', array(
						'security'		=>	wp_create_nonce( "admin_quizmaker_import_questions" ),
					));

				break;
				case 'quiz':

					$admin_js_params['postid'] = $post->ID;

					wp_localize_script( 'quizmaker_admin_script', 'qm_admin', $admin_js_params );

				break;
				case 'qanalytics':

					$admin_js_params['postid'] = $post->ID;

					wp_localize_script( 'quizmaker_admin_script', 'qm_admin', $admin_js_params );

				break;

				case 'edit-qanalytics':

					$admin_js_params['postid'] = $post->ID;

					wp_localize_script( 'quizmaker_admin_script', 'qm_admin', $admin_js_params );

				break;

				case 'quizmaker_page_qm-settings':
						
					wp_localize_script( 'quizmaker_admin_script', 'qm_admin', $admin_js_params );

				break;
			}
			
		}
		
		wp_enqueue_style( 'quizmaker-ionicons', QUIZMAKER_URI . '/assets/css/ionicons/css/ionicons.min.css', array(), '1.0.2' );

		wp_enqueue_style( 'quizmaker-font', 'https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' );

		wp_enqueue_style( 'quizmaker-admin', QUIZMAKER_URI . '/assets/css/admin.css', array('quizmaker-font'), '1.0.7' );
	}

	private function _enqueue_select2_scripts( $min_js ) {

		wp_enqueue_script( 'select2', QUIZMAKER_URI . 'assets/js/libs/select2/js/select2.full' . $min_js . '.js', array( 'jquery' ));
		wp_enqueue_style( 'select2', QUIZMAKER_URI . 'assets/js/libs/select2/css/select2' . $min_js . '.css' );
	}
	
	// Before user deleted
	public function delete_user( $user_id ) {
		
		if(!$user_id) return false;
				
		$user_tests	=	qm_get_user_meta( $user_id, 'tests' );
		$member_id	=	qm_get_user_meta( $user_id, 'member_id' );
					
		$user	=	new WP_User( $user_id );
			
		if( $user_tests && is_array($user_tests) ){
		
			foreach( $user_tests as $tid ){
			
				$test_object	=	QM()->test_factory->get_test($tid);
				
				if(isset($test_object) && $test_object){
					$test_object->remove_assigned_users( $user_id );
				}
			}
		}
		
		qm_remove_results('user', $user_id);
	}
	
	public function delete_results( $test_id ) {
		
		$post_type = get_post_type($test_id);
		
		if( $post_type == 'test' ){

			$test = new QM_Test( $test_id );

			$test->remove_results();
			
		}
		
	}

	public function download_csv_test_result() {

		if(isset($_POST['download_csv_test_result'])) {

			$test		=	QM()->test_factory->get_test( absint( $_POST['download_csv_test_result'] ) );

			$results 	=	$test->get_lastest_pagination_results( array('page' => 1, 'perpage' => 2000000, 'user_meta' => true));

			if( isset($results['data']) && $results['data'] ) {

				if( !is_dir(QM_LOG_DIR) ) {

					mkdir(QM_LOG_DIR, 0755, true);
				}

				$csv_file 	= 	QM_LOG_DIR .'/result.csv';

				$out = fopen( $csv_file, 'w+');

				$headers = array(
					'#',
					'Percent',
					'Score',
					'Duration',
					'Date',
					'Name',
					'Email',
					'Other Infomation'
				);

				fputcsv($out, $headers);

				foreach( $results['data'] as $index => $row ) {

					$row_value = array(
						$index + 1,
						$row['percent'],
						$row['score'] . '/' . $row['total_score'],
						$row['duration'],
						$row['date_start'],
						$row['user_name'],
						$row['user_email'],
					);

					if( isset($row['user_meta']) && $row['user_meta'] ) {

						$um_str = '';

						foreach( $row['user_meta'] as $um_label => $um_value ) {

							 $um_str .= $um_label . ': ' . $um_value . "\r\n";
						}

						$row_value[] = trim($um_str);
					}

					// if( isset($row['user_meta']) && $row['user_meta'] ) {

					// 	foreach( $row['user_meta'] as $um_label => $um_value ) {

					// 		$row_value[] = $um_value;
					// 	}
					// }

					fputcsv($out, $row_value);
				}
				 
				fclose($out);

				header('Content-Type: application/csv');
				header('Content-Disposition: attachment; filename=result.csv');
				header('Pragma: no-cache');

				 $fp = fopen($csv_file, "r");
			    fpassthru($fp);
			    fclose($fp);
			    
			    exit;
			}

		}	
	}
}

return new QM_Admin();
