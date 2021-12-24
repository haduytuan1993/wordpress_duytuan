<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * QM_Settings_Verify.
 */
class QM_Settings_Verify extends QM_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'verify';
		$this->label = __( 'Verify Purchased', 'quizmaker' );

		add_filter( 'quizmaker_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'quizmaker_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'quizmaker_settings_save_' . $this->id, array( $this, 'save' ) );

		$this->verify_complete();
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings() {

		$GLOBALS['hide_save_button'] = false;

		$message	=	__('Inputing your purchase code', 'quizmaker');

		if( isset($_GET['status']) && $_GET['status'] != 'fail' ) {

			$message	=	__('<strong style="color: green;">Thanks you! Your purchase code is valid</strong>', 'quizmaker');
		}elseif( isset($_GET['status']) && $_GET['status'] == 'fail' ) {

			$message	=	__('<strong style="color: red;">Sorry! Your purchase code is not validated</strong>', 'quizmaker');
		}

		$image_purchase_code	=	'<img src="' . home_url() . '/wp-content/plugins/quizmaker/assets/images/purchase_code.jpg" />';

		$settings = apply_filters( 'quizmaker_verify_settings', array(

			array( 'title' => __( 'Get Quizmaker purchase code from your admin envato', 'quizmaker' ), 'type' => 'title', 'desc' => $image_purchase_code . '<p>With Regular License Quizmaker only install on one domain for each purchase</p>', 'id' => 'verify_general_options' ),
			
			array(
				'title'       => __( 'Purchase Code', 'quizmaker' ),
				'id'          => 'quizmaker_verify_purchase_code',
				'type'        => 'text',
				'css'         => 'min-width:300px;',
				'default'     => '',
				'autoload'    => false,
				'desc'    	  => $message
			),

			array( 'type' => 'sectionend', 'id' => 'verify_general_options' ),
			
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

	public function save() {

		$settings = $this->get_settings();

		if ( isset( $_POST['quizmaker_verify_purchase_code'] ) && $_POST['quizmaker_verify_purchase_code'] ) {

			$code = sanitize_title( $_POST['quizmaker_verify_purchase_code'] );

			$site = urlencode(admin_url('admin.php?page=qm-settings&tab=verify'));
			
			$url = "https://api.awstheme.com/envato/quizmaker_connect?code=$code&site=" . $site;

			wp_redirect( $url );

			wp_die();

		}else{

			QM_Admin_Settings::add_error(__('Please inputing your purchase code', 'quizmaker'));
		}
	}

	public function verify_complete() {
		
		if( isset($_GET['status']) && $_GET['status'] != 'fail' ) {

			update_option('quizmaker_verify_purchase_code', $_GET['status']);
			update_option('quizmaker_verify_time', time());

		}
	}

}

return new QM_Settings_Verify();
