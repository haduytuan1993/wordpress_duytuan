<?php
/**
 * Setup menus in WP admin.
 *
 * @author   AWSTheme
 * @category Admin
 * @package  QuizMaker/Admin
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'QM_Admin_Menus' ) ) :

/**
 * QM_Admin_Menus Class.
 */
class QM_Admin_Menus {
	
	public function __construct() {
		
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 10 );
		
		add_filter( 'menu_order', array( $this, 'menu_order' ) );
		add_filter( 'custom_menu_order', array( $this, 'custom_menu_order' ) );
		
	}
	
	public function admin_menu() {
		global $menu;

		if ( current_user_can( 'manage_quizmaker' ) ) {
			$menu[] = array( '', 'read', 'separator-quizmaker', '', 'wp-menu-separator quizmaker' );
		}

		add_menu_page(	__( 'QuizMaker', 'quizmaker' ), 
						__( 'QuizMaker', 'quizmaker' ), 
						'manage_quizmaker', 'quizmaker', 
						array( $this, 'dashboard_page' ), 'dashicons-quizmaker', 56
					);
					
		add_submenu_page(	'quizmaker', 
							__( 'Dashboard', 'quizmaker' ),
							__( 'Dashboard', 'quizmaker' ) , 
							'manage_quizmaker', 
							'qm-dashboard', array( $this, 'dashboard_page' ) );
		
		add_submenu_page(	'quizmaker', 
							__( 'User Certificates', 'quizmaker' ),
							__( 'User Certificates', 'quizmaker' ) , 
							'manage_quizmaker', 
							'qm-user-certificates', array( $this, 'user_certificates_page' ) );

		add_submenu_page(	'quizmaker', 
							__( 'Settings', 'quizmaker' ),
							__( 'Settings', 'quizmaker' ) , 
							'manage_quizmaker', 
							'qm-settings', array( $this, 'settings_page' ) );

		add_submenu_page(	'quizmaker', 
							__( 'Importing', 'quizmaker' ),
							__( 'Importing', 'quizmaker' ) , 
							'manage_quizmaker', 
							'qm-importing-questions', array( $this, 'importing_questions_page' ) );

		// add_submenu_page(	'quizmaker', 
		// 					__( 'Extensions', 'quizmaker' ),
		// 					__( 'Extensions', 'quizmaker' ) , 
		// 					'manage_quizmaker', 
		// 					'qm-extensions', array( $this, 'extensions_page' ) );
	}
	
	public function dashboard_page() {
		
		QM_Admin_Dashboard::output();
	}
	
	public function settings_page() {
		
		QM_Admin_Settings::output();
		
	}

	public function user_certificates_page() {

		QM_Admin_User_Certificates::output();
	}

	public function extensions_page() {
		
		QM_Admin_Extensions::output();
		
	}

	public function importing_questions_page() {

		QM_Admin_Importing_Questions::output();
	}
	
	public function menu_order( $menu_order ) {
		// Initialize our custom order array
		$quizmaker_menu_order = array();

		// Get the index of our custom separator
		$quizmaker_separator = array_search( 'separator-quizmaker', $menu_order );

		$quizmaker_test 	= array_search( 'edit.php?post_type=test', $menu_order );
		$quizmaker_question = array_search( 'edit.php?post_type=question', $menu_order );
		$quizmaker_quiz = array_search( 'edit.php?post_type=quiz', $menu_order );
		$quizmaker_qanalytics = array_search( 'edit.php?post_type=qanalytics', $menu_order );

		// Loop through menu order and do some rearranging
		foreach ( $menu_order as $index => $item ) {

			if ( ( ( 'quizmaker' ) == $item ) ) {
				$quizmaker_menu_order[] = 'separator-quizmaker';
				$quizmaker_menu_order[] = $item;
				$quizmaker_menu_order[] = 'edit.php?post_type=test';
				$quizmaker_menu_order[] = 'edit.php?post_type=question';
				$quizmaker_menu_order[] = 'edit.php?post_type=quiz';
				$quizmaker_menu_order[] = 'edit.php?post_type=qanalytics';
				unset( $menu_order[ $quizmaker_separator ] );
				unset( $menu_order[ $quizmaker_test ] );
				unset( $menu_order[ $quizmaker_question ] );
				unset( $menu_order[ $quizmaker_quiz ] );
				unset( $menu_order[ $quizmaker_qanalytics ] );
			} elseif ( !in_array( $item, array( 'separator-quizmaker' ) ) ) {
				$quizmaker_menu_order[] = $item;
			}

		}

		// Return order
		return $quizmaker_menu_order;
	}
	
	/**
	 * Custom menu order.
	 *
	 * @return bool
	 */
	public function custom_menu_order() {
		return current_user_can( 'manage_quizmaker' );
	}
	
	
}

endif;

return new QM_Admin_Menus();