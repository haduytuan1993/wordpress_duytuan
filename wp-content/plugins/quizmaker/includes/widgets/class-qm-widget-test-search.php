<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Test Search Widget.
 *
 * @author   AWSTheme
 * @category Widgets
 * @package  Quizmaker/Widgets
 * @version  1.0.0
 * @extends  QM_Widget
 */

class QM_Widget_Test_Search extends QM_Widget {
	
	public function __construct() {
		$this->widget_cssclass    = 'quizmaker widget_test_search';
		$this->widget_description = __( 'Display a test search on your site.', 'quizmaker' );
		$this->widget_id          = 'quizmaker_test_search';
		$this->widget_name        = __( 'Quizmaker Test Search', 'quizmaker' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title', 'quizmaker' )
			)
		);

		parent::__construct();
	}
	
	function widget( $args, $instance ) {
		$this->widget_start( $args, $instance );

		get_test_search_form();

		$this->widget_end( $args );
	}
}
