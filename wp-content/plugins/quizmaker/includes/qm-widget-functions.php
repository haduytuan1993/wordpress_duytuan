<?php
/**
 * Quizmaker Widget Functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include widget classes.
include_once( 'abstracts/abstract-qm-widget.php' );
include_once( 'widgets/class-qm-widget-tests.php' );
include_once( 'widgets/class-qm-widget-test-search.php' );
include_once( 'widgets/class-qm-widget-test-categories.php' );
include_once( 'widgets/class-qm-widget-test-filters.php' );

function qm_register_widgets() {
	register_widget( 'QM_Widget_Tests' );
	register_widget( 'QM_Widget_Test_Search' );
	register_widget( 'QM_Widget_Test_Filters' );
	register_widget( 'QM_Widget_Test_Categories' );

	register_sidebar( array(
			'id'	=>	__(	'quizmaker-left-sidebar' ),
			'name'	=>	__( 'Quizmaker Left Sidebar', 'quizmaker'),
			'before_widget' => '<div id="%1$s" class="mb-3 box-sidebar qm-sidebar-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h5>',
			'after_title'  => '</h5>',
		) );
			
	register_sidebar( array(
		'id'	=>	__(	'quizmaker-right-sidebar' ),
		'name'	=>	__( 'Quizmaker Right Sidebar', 'quizmaker'),
		'before_widget' => '<div id="%1$s" class="box-sidebar qm-sidebar-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h5>',
		'after_title'  => '</h5>',
	) );
}
add_action( 'widgets_init', 'qm_register_widgets' );

