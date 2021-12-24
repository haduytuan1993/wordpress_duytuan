<?php
/**
 * QuizMaker Meta Boxes
 *
 *
 * @author      AWSTheme
 * @category    Admin
 * @package     QuizMaker/Admin/Meta Boxes
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC_Admin_Meta_Boxes.
 */
class QM_Admin_Meta_Boxes {

	/**
	 * Is meta boxes saved once?
	 *
	 * @var boolean
	 */
	private static $saved_meta_boxes = false;

	/**
	 * Meta box error messages.
	 *
	 * @var array
	 */
	public static $meta_box_errors  = array();
	
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'rename_meta_boxes' ), 20 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );
		
		// Save Test Meta Boxes
		add_action( 'quizmaker_process_test_meta', 'QM_Meta_Box_Test_Data::save', 10, 2 );
		
		// Save Question Meta Boxes
		add_action( 'quizmaker_process_question_meta', 'QM_Meta_Box_Question_Data::save', 20, 2 );
		
		add_action( 'quizmaker_process_question_meta', 'QM_Meta_Box_Answer_Data::save', 20, 2 );
		
		add_action( 'quizmaker_process_certificate_meta', 'QM_Meta_Box_Certificate_Data::save', 10, 2 );

		add_action( 'quizmaker_process_usergroup_meta', 'QM_Meta_Box_Usergroup_Data::save', 10, 2 );
		
	}
	
	/**
	 * Add QM Meta boxes.
	 */
	public function add_meta_boxes() {
		$screen = get_current_screen();
		
		add_meta_box( 'quizmaker-question-data', __( 'Question Data', 'quizmaker' ), 'QM_Meta_Box_Question_Data::output', 'question', 'normal', 'high' );
		
		add_meta_box( 'quizmaker-answers-data', __( 'Answers', 'quizmaker' ), 'QM_Meta_Box_Answer_Data::output', 'question', 'normal', 'high' );
		
		add_meta_box( 'quizmaker-test-data', __( 'Test Data', 'quizmaker' ), 'QM_Meta_Box_Test_Data::output', 'test', 'normal', 'high' );
		
		add_meta_box( 'quizmaker-result-data', __( 'Results Data', 'quizmaker' ), 'QM_Meta_Box_Result_Data::output', 'test', 'normal', 'high' );
		
		add_meta_box( 'quizmaker-certificate-data', __( 'Composer', 'quizmaker' ), 'QM_Meta_Box_Certificate_Data::output', 'certificate', 'normal', 'high' );

		add_meta_box( 'quizmaker-usergroup-data', __( 'Users', 'quizmaker' ), 'QM_Meta_Box_Usergroup_Data::output', 'usergroup', 'normal', 'high' );

		add_meta_box( 'quizmaker-quiz-data', __( 'Quiz Composer', 'quizmaker' ), 'QM_Meta_Box_Quiz_Data::output', 'quiz', 'normal', 'high' );

		add_meta_box( 'quizmaker-quiz-analytics-data', __( 'Data', 'quizmaker' ), 'QM_Meta_Box_Quiz_Analytics_Data::output', 'qanalytics', 'normal', 'high' );	
	}
	
	/**
	 * Remove bloat.
	 */
	public function remove_meta_boxes() {
		
	}
	
	/**
	 * Rename core meta boxes.
	 */
	public function rename_meta_boxes() {
		
	}
	
	/**
	 * Check if we're saving, the trigger an action based on the post type.
	 *
	 * @param  int $post_id
	 * @param  object $post
	 */
	public function save_meta_boxes( $post_id, $post ) {
		// $post_id and $post are required
		if ( empty( $post_id ) || empty( $post ) || self::$saved_meta_boxes ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}
		
		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}
		
		// Check user has permission to edit
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		
		self::$saved_meta_boxes = true;
		
		do_action( 'quizmaker_process_' . $post->post_type . '_meta', $post_id, $post );
	}
}

new QM_Admin_Meta_Boxes();