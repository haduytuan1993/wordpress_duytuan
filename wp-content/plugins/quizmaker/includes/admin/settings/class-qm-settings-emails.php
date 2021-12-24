<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * QM_Settings_Emails.
 */
class QM_Settings_Emails extends QM_Settings_Page {
	
	public function __construct() {

		$this->id    = 'emails';
		$this->label = __( 'Emails', 'quizmaker' );

		add_filter( 'quizmaker_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'quizmaker_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'quizmaker_settings_save_' . $this->id, array( $this, 'save' ) );
	}
	
	public function get_settings() {

		$GLOBALS['hide_save_button'] = false;
		
		$settings = apply_filters( 'quizmaker_emails_settings', array(

			array( 'title' => __( 'Sender Options', 'quizmaker' ), 'type' => 'title', 'desc' => '', 'id' => 'email_sender_options' ),
			
			array(
				'title'       => __( '"From" Name', 'quizmaker' ),
				'id'          => 'quizmaker_email_from_name',
				'type'        => 'text',
				'css'         => 'min-width:300px;',
				'default'     => 'Quizmaker',
				'autoload'    => false,
				'desc_tip'    => true
			),
			
			array(
				'title'       => __( '"From" Email', 'quizmaker' ),
				'id'          => 'quizmaker_email_from_address',
				'type'        => 'text',
				'css'         => 'min-width:300px;',
				'default'     => '',
				'autoload'    => false,
				'desc_tip'    => true
			),
			
			array( 'type' => 'sectionend', 'id' => 'email_sender_options' ),
			
			array( 'title' => __( 'Template', 'quizmaker' ), 'type' => 'title', 'desc' => '', 'id' => 'email_template_options' ),
			
			array(
				'title'       => __( 'Header Image', 'quizmaker' ),
				'id'          => 'quizmaker_email_header_image',
				'type'        => 'text',
				'css'         => 'min-width:300px;',
				'default'     => '',
				'autoload'    => false,
				'desc_tip'    => true
			),
			
			array(
				'title'       => __( 'Footer Text', 'quizmaker' ),
				'id'          => 'quizmaker_email_footer_text',
				'type'        => 'textarea',
				'css'         => 'min-width:300px;',
				'default'     => 'Quizmaker - AWSTheme',
				'autoload'    => false,
				'desc_tip'    => true
			),
			
			array( 'type' => 'sectionend', 'id' => 'email_template_options' ),
			
			array( 'title' => __( 'New Member', 'quizmaker' ), 'type' => 'title', 'desc' => '', 'id' => 'email_template_new_member_options' ),
			
			array(
				'title'       => __( 'Subject', 'quizmaker' ),
				'id'          => 'quizmaker_email_new_member_subject',
				'type'        => 'text',
				'css'         => 'min-width:300px;',
				'default'     => 'Welcome to Quizmaker',
				'autoload'    => false,
				'desc_tip'    => true
			),
			
			array(
				'title'       => __( 'Email Heading', 'quizmaker' ),
				'id'          => 'quizmaker_email_new_member_heading',
				'type'        => 'text',
				'css'         => 'min-width:300px;',
				'default'     => 'hello new member',
				'autoload'    => false,
				'desc_tip'    => true
			),
			
			array( 'type' => 'sectionend', 'id' => 'email_template_new_member_options' ),
			
			array( 'title' => __( 'New Result', 'quizmaker' ), 'type' => 'title', 'desc' => '', 'id' => 'email_template_new_result_options' ),
			
			array(
				'title'       => __( 'Subject', 'quizmaker' ),
				'id'          => 'quizmaker_email_new_result_subject',
				'type'        => 'text',
				'css'         => 'min-width:300px;',
				'default'     => 'New Result: [{test_title}]',
				'autoload'    => false,
				'desc_tip'    => true
			),
			
			array(
				'title'       => __( 'Email Heading', 'quizmaker' ),
				'id'          => 'quizmaker_email_new_result_heading',
				'type'        => 'text',
				'css'         => 'min-width:300px;',
				'default'     => '[{test_title}]',
				'autoload'    => false,
				'desc_tip'    => true
			),

			array(
				'title'       => __( 'Email Result To Admin', 'quizmaker' ),
				'id'          => 'quizmaker_email_notification_new_result_recipient',
				'type'        => 'text',
				'css'         => 'min-width:300px;',
				'default'     => '',
				'autoload'    => false,
				'desc_tip'    => true
			),
			
			array( 'type' => 'sectionend', 'id' => 'email_template_new_result_options' ),
			
			array( 'title' => __( 'Assign Test', 'quizmaker' ), 'type' => 'title', 'desc' => '', 'id' => 'email_template_assign_test_options' ),
			
			array(
				'title'       => __( 'Subject', 'quizmaker' ),
				'id'          => 'quizmaker_email_assign_test_subject',
				'type'        => 'text',
				'css'         => 'min-width:300px;',
				'default'     => 'New assign test: [{name}]',
				'autoload'    => false,
				'desc_tip'    => true
			),
			
			array(
				'title'       => __( 'Email Heading', 'quizmaker' ),
				'id'          => 'quizmaker_email_assign_test_heading',
				'type'        => 'text',
				'css'         => 'min-width:300px;',
				'default'     => '[{name}]',
				'autoload'    => false,
				'desc_tip'    => true
			),
			
			array( 'type' => 'sectionend', 'id' => 'email_template_new_result_options' ),
			
			array( 'title' => __( 'Reset Password', 'quizmaker' ), 'type' => 'title', 'desc' => '', 'id' => 'email_template_reset_password_options' ),
			
			array(
				'title'       => __( 'Subject', 'quizmaker' ),
				'id'          => 'quizmaker_email_reset_password_subject',
				'type'        => 'text',
				'css'         => 'min-width:300px;',
				'default'     => 'Password Reset',
				'autoload'    => false,
				'desc_tip'    => true
			),
			
			array(
				'title'       => __( 'Email Heading', 'quizmaker' ),
				'id'          => 'quizmaker_email_reset_password_heading',
				'type'        => 'text',
				'css'         => 'min-width:300px;',
				'default'     => 'Password Reset Instructions',
				'autoload'    => false,
				'desc_tip'    => true
			),
			
			array( 'type' => 'sectionend', 'id' => 'email_template_reset_password_options' )
			
		) );

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

return new QM_Settings_Emails();