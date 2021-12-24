<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

class QM_Settings_Marketing_App extends QM_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'marketing-app';
		$this->label = __( 'Marketing App', 'quizmaker' );

		add_filter( 'quizmaker_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'quizmaker_sections_' . $this->id, array( $this, 'output_sections' ) );
		add_action( 'quizmaker_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'quizmaker_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	public function get_sections() {

		$sections = array(
			'' => __('General options', 'quizmaker'),
			'mailchimp' => __( 'Mailchimp', 'quizmaker' ),
			'getresponse' => __( 'GetResponse', 'quizmaker' ),
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

		if ( 'mailchimp' == $current_section ) {

			$settings = apply_filters( 'quizmaker_mailchimp_settings', array(			
				
				array( 'title' => __( 'MailChimp API keys', 'quizmaker' ), 'type' => 'title', 'desc' => 'API keys provide full access to your MailChimp account', 'id' => 'mailchimp_options' ),
				
				array(
					'title'       => __( 'API key', 'quizmaker' ),
					'id'          => 'quizmaker_marketing_app_mailchimp_api_key',
					'type'        => 'text',
					'css'         => 'min-width:300px;',
					'default'     => '',
					'autoload'    => false,
					'desc_tip'    => false
				),
				
				array( 'type' => 'sectionend', 'id' => 'mailchimp_options' ),
			) );

		}elseif( 'getresponse' == $current_section ){

			$settings = apply_filters( 'quizmaker_getresponse_settings', array(

				array( 'title' => __( 'GetResponse API keys', 'quizmaker' ), 'type' => 'title', 'desc' => 'API keys provide full access to your GetResponse account', 'id' => 'getresponse_options' ),
				
				array(
					'title'       => __( 'API key', 'quizmaker' ),
					'id'          => 'quizmaker_marketing_app_getresponse_api_key',
					'type'        => 'text',
					'css'         => 'min-width:300px;',
					'default'     => '',
					'autoload'    => false,
					'desc_tip'    => false
				),
				
				array( 'type' => 'sectionend', 'id' => 'getresponse_options' ),
			));

		}else{

			$settings = apply_filters( 'quizmaker_maketing_app_general_settings', array(
					array( 'title' => __( 'General Options', 'quizmaker' ), 'type' => 'title', 'desc' => '', 'id' => 'general_options' ),
					array(
						'desc'     => __( 'All user data will push to Marketing App', 'quizmaker' ),
						'id'       => 'quizmaker_marketing_app_enabled',
						'title'    => __( 'Enable Marketing App', 'quizmaker' ),
						'type'     => 'checkbox',
						'default'  => 'no'
					),

					array( 'type' => 'sectionend', 'id' => 'general_options' ),
			));

		}

		return apply_filters( 'quizmaker_get_settings_' . $this->id, $settings );
	}

	/**
	 * Output a colour picker input box.
	 *
	 * @param mixed $name
	 * @param string $id
	 * @param mixed $value
	 * @param string $desc (default: '')
	 */
	public function color_picker( $name, $id, $value, $desc = '' ) {
		echo '<div class="color_box">' . wc_help_tip( $desc ) . '
			<input name="' . esc_attr( $id ). '" id="' . esc_attr( $id ) . '" type="text" value="' . esc_attr( $value ) . '" class="colorpick" /> <div id="colorPickerDiv_' . esc_attr( $id ) . '" class="colorpickdiv"></div>
		</div>';
	}

	// public function output() {
	// 	global $current_section;

	// 	var_dump($current_section); exit;
	// }

	/**
	 * Save settings.
	 */
	public function save() {
		$settings = $this->get_settings();


		QM_Admin_Settings::save_fields( $settings );
	}

}

return new QM_Settings_Marketing_App();
