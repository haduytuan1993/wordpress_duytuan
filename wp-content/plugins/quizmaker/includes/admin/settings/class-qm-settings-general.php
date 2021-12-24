<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * QM_Settings_General.
 */
class QM_Settings_General extends QM_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'general';
		$this->label = __( 'General', 'quizmaker' );

		add_filter( 'quizmaker_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'quizmaker_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'quizmaker_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings() {

		$GLOBALS['hide_save_button'] = false;

		$settings = apply_filters( 'quizmaker_general_settings', array(

			array( 'title' => __( 'General Options', 'quizmaker' ), 'type' => 'title', 'desc' => '', 'id' => 'general_options' ),
			
			array(
				'title'    => __( 'Archive Test Page', 'quizmaker' ),
				'desc'     => __( 'Page contents:', 'quizmaker' ),
				'id'       => 'quizmaker_archive_test_page_id',
				'type'     => 'single_select_page',
				'default'  => '',
				'class'    => 'qm-enhanced-select',
				'css'      => 'min-width:300px;',
				'desc_tip' => true,
			),
			
			array(
				'title'    => __( 'My Account Page', 'quizmaker' ),
				'desc'     => __( 'Page contents:', 'quizmaker' ) . ' [' . apply_filters( 'quizmaker_my_account_shortcode_tag', 'quizmaker_my_account' ) . ']',
				'id'       => 'quizmaker_myaccount_page_id',
				'type'     => 'single_select_page',
				'default'  => '',
				'class'    => 'qm-enhanced-select',
				'css'      => 'min-width:300px;',
				'desc_tip' => true,
			),
			
			array(
				'title'    => __( 'Register Page', 'quizmaker' ),
				'desc'     => __( 'Page contents:', 'quizmaker' ) . ' [' . apply_filters( 'quizmaker_register_shortcode_tag', 'quizmaker_register' ) . ']',
				'id'       => 'quizmaker_register_page_id',
				'type'     => 'single_select_page',
				'default'  => '',
				'class'    => 'qm-enhanced-select',
				'css'      => 'min-width:300px;',
				'desc_tip' => true,
			),
			
			array(
				'title'    => __( 'Login Page', 'quizmaker' ),
				'desc'     => __( 'Page contents:', 'quizmaker' ) . ' [' . apply_filters( 'quizmaker_login_shortcode_tag', 'quizmaker_login' ) . ']',
				'id'       => 'quizmaker_login_page_id',
				'type'     => 'single_select_page',
				'default'  => '',
				'class'    => 'qm-enhanced-select',
				'css'      => 'min-width:300px;',
				'desc_tip' => true,
			),
			
			array(
				'desc'     => __( 'Guest can play test not need to register', 'quizmaker' ),
				'id'       => 'quizmaker_is_play_test_as_guest',
				'title'    => __( 'Guest Play Test', 'quizmaker' ),
				'type'     => 'checkbox',
				'default'  => 'yes'
			),

			array(
				'desc'     => __( 'User must fill a form after complete a test', 'quizmaker' ),
				'id'       => 'quizmaker_is_user_fillform_setting',
				'title'    => __( 'Enable User Fill Form', 'quizmaker' ),
				'type'     => 'checkbox',
				'default'  => 'no'
			),

			array(
				'desc'     => __( 'User can view the result when having a ranking', 'quizmaker' ),
				'id'       => 'quizmaker_is_user_ranking_view_result_setting',
				'title'    => __( 'User Ranking View Result', 'quizmaker' ),
				'type'     => 'checkbox',
				'default'  => 'no'
			),

			array(
				'desc'     => __( 'Remove data when uninstall', 'quizmaker' ),
				'id'       => 'quizmaker_is_uninstall_remove_data',
				'title'    => __( 'Remove Data', 'quizmaker' ),
				'type'     => 'checkbox',
				'default'  => 'no'
			),
			
			array(
				'desc'     => __( 'Change plugin to debug status', 'quizmaker' ),
				'id'       => 'quizmaker_is_debug',
				'title'    => __( 'Debug', 'quizmaker' ),
				'type'     => 'checkbox',
				'default'  => 'no'
			),

			array( 'type' => 'sectionend', 'id' => 'general_options' ),
			
			array( 'title' => __( 'Display Options', 'quizmaker' ), 'type' => 'title', 'desc' => '', 'id' => 'display_options' ),
			
			array(
				'desc'     => __( 'Load default stylesheet', 'quizmaker' ),
				'id'       => 'quizmaker_is_default_stylesheet',
				'title'    => __( 'Default Stylesheet', 'quizmaker' ),
				'type'     => 'checkbox',
				'default'  => 'yes'
			),

			array(
				'desc'     => __( 'Load boostrap Library', 'quizmaker' ),
				'id'       => 'quizmaker_is_load_bootstrap_library',
				'title'    => __( 'Load Boostrap Library', 'quizmaker' ),
				'type'     => 'checkbox',
				'default'  => 'no'
			),

			array(
				'desc'     => __( 'User can rate a test after completing that test', 'quizmaker' ),
				'id'       => 'quizmaker_is_rating',
				'title'    => __( 'Rating', 'quizmaker' ),
				'type'     => 'checkbox',
				'default'  => 'no'
			),

			array(
				'desc'     => __( 'User can share the result to social', 'quizmaker' ),
				'id'       => 'quizmaker_is_share_result',
				'title'    => __( 'Share Result', 'quizmaker' ),
				'type'     => 'checkbox',
				'default'  => 'no'
			),

			array(
				'desc'     => __( 'Show user score in my account', 'quizmaker' ),
				'id'       => 'quizmaker_is_user_score',
				'title'    => __( 'Show User Score', 'quizmaker' ),
				'type'     => 'checkbox',
				'default'  => 'no'
			),
			
			array(
				'title'       => __( 'Posts per Page', 'quizmaker' ),
				'id'          => 'quizmaker_posts_per_page',
				'type'        => 'text',
				'css'         => 'min-width:300px;',
				'default'     => 10,
				'autoload'    => false,
				'desc_tip'    => true
			),
			
			array(
				'title'       => __( 'Default Test Order', 'quizmaker' ),
				'id'          => 'quizmaker_default_test_orderby',
				'type'        => 'select',
				'default'     => 'date',
				'options'	  => apply_filters( 'quizmaker_catalog_orderby', array(
												'menu_order' 	=> __( 'Default sorting', 'quizmaker' ),
												'date'			=> __( 'Sort by newness', 'quizmaker' ),
												'duration-asc'  => __( 'Sort by duration: low to high', 'quizmaker' ),
												'duration-desc' => __( 'Sort by duration: high to low', 'quizmaker' ),
												'played-asc'  => __( 'Sort by played: low to high', 'quizmaker' ),
												'played-desc' => __( 'Sort by played: high to low', 'quizmaker' )
											) )
			),

			array( 'type' => 'sectionend', 'id' => 'display_options' ),

			array( 'title' => __( 'Social Options', 'quizmaker' ), 'type' => 'title', 'desc' => '', 'id' => 'social_options' ),

			array(
				'title'       => __( 'Facebook App ID', 'quizmaker' ),
				'id'          => 'quizmaker_facebook_app_id',
				'type'        => 'text',
				'css'         => 'min-width:300px;',
				'default'     => '',
				'autoload'    => false,
				'desc_tip'    => true
			),

			array( 'type' => 'sectionend', 'id' => 'social_options' ),
			
			array( 'title' => __( 'Captcha Options', 'quizmaker' ), 'type' => 'title', 'desc' => 'Protect your website from spam and abuse while letting real people pass through with ease <a href="https://www.google.com/recaptcha" target="blank">Google reCAPTCHA</a>', 'id' => 'captcha_options' ),
			
			array(
				'title'       => __( 'Captcha Key', 'quizmaker' ),
				'id'          => 'quizmaker_is_captcha_key',
				'type'        => 'text',
				'css'         => 'min-width:300px;',
				'default'     => '',
				'autoload'    => false,
				'desc_tip'    => false
			),
			
			array(
				'title'       => __( 'Captcha Secret', 'quizmaker' ),
				'id'          => 'quizmaker_is_captcha_secret',
				'type'        => 'text',
				'css'         => 'min-width:300px;',
				'default'     => '',
				'autoload'    => false,
				'desc_tip'    => false
			),
			
			array(
				'desc'     => __( 'Enable reCaptcha', 'quizmaker' ),
				'id'       => 'quizmaker_is_captcha_on_test',
				'title'    => '',
				'type'     => 'checkbox',
				'default'  => 'no'
			),
			
			array( 'type' => 'sectionend', 'id' => 'captcha_options' ),
		) );

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

	/**
	 * Save settings.
	 */
	public function save() {
		$settings = $this->get_settings();

		QM_Admin_Settings::save_fields( $settings );
	}

}

return new QM_Settings_General();
