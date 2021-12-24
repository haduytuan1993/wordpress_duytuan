<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

class QM_Settings_Forms extends QM_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		
		$GLOBALS['hide_save_button'] = true;

		$this->id    = 'qm-settings-forms';
		$this->label = __( 'After Play Test Form', 'quizmaker' );

		add_filter( 'quizmaker_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'quizmaker_sections_' . $this->id, array( $this, 'output_sections' ) );
		add_action( 'quizmaker_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'quizmaker_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	public function get_sections() {

		$sections = array(
			'' => __('After Play Test', 'quizmaker'),
		);

		return apply_filters( 'quizmaker_get_sections_' . $this->id, $sections );
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings() {

		global $current_section;

		$settings = array();

		if ( '' == $current_section ) {

			$after_play_test_setting = get_option('quizmaker_forms_after_play_test_settings');


			$settings = apply_filters( 'quizmaker_forms_after_play_test_settings', array(			
				
				array( 'title' => __( 'After Play Test Form', 'quizmaker' ), 'type' => 'title', 'desc' => 'This form will show for user after compete a test', 'id' => 'after_play_test_options' ),
				
				array(
					'title'       => '',
					'id'          => 'quizmaker_after_play_test_form_builder',
					'type'        => 'form_builder',
					'ajax'		  => array(
						'url' => admin_url( 'admin-ajax.php' ),
						'security' => wp_create_nonce( "admin_quizmaker_after_play_test_form_setting" ),
						'action' => 'quizmaker_after_play_test_form_setting'
					),
					'disable_form_fields' => ['autocomplete', 'button', 'file', 'hidden', 'header', 'date'],
					'form_data' => $after_play_test_setting['form_data']
				),
				
				array( 'type' => 'sectionend', 'id' => 'after_play_test_options' ),
			) );

		}

		return apply_filters( 'quizmaker_get_settings_' . $this->id, $settings );
	}

	/**
	 * Save settings.
	 */
	public function save() {
		$settings = $this->get_settings();


		QM_Admin_Settings::save_fields( $settings );
	}

}

return new QM_Settings_Forms();
