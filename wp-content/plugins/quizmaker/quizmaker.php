<?php
/*
Plugin Name: QuizMaker
Plugin URI: http://www.awstheme.com/
Description: Create Custom Tests & Exams Online
Version: 2.1.0
Author: AWSTheme
Author URI: http://www.awstheme.com

Text Domain: quizmaker
Domain Path: /languages
*/

defined('ABSPATH') or die("No script kiddies please!");

$plugin_name	=	'quizmaker';

define( 'QUIZMAKER_SLUG' , 'quizmaker' );
define( 'QUIZMAKER_DIR' , plugin_dir_path( __FILE__ ) );
define( 'QUIZMAKER_URI' , plugin_dir_url( __FILE__ ) );
define( 'QUIZMAKER_CACHE_DIR' , plugin_dir_path( __FILE__ ).'/cache' );
define( 'QUIZMAKER_LIBRARY_DIR' , plugin_dir_path( __FILE__ ).'/includes/library' );
define( 'QUIZMAKER_URL', get_site_url().'/wp-admin/admin.php?page=quizmaker');

final class Quizmaker {
	
	public $version = '2.1.0';
	
	public $test_factory = null;
	
	protected static $_instance = null;
	
	private $settings;
	
	public static function instance(){
		
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	private function __construct(){
		
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
		
		do_action( 'quizmaker_loaded' );
	}
	
	private function init_hooks() {
		
		register_activation_hook( __FILE__, array( 'QM_Install', 'install' ) );
		register_deactivation_hook( __FILE__, array( $this, 'uninstall' ) );
		
		add_action( 'after_setup_theme', array( $this, 'setup_environment' ) );
		add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'init', array( 'QM_Shortcodes', 'init' ) );
		add_action( 'init', array( 'QM_Emails', 'init_transactional_emails' ) );

	}
	
	public function uninstall() {
		
		uninstall_plugin( __FILE__ );
	}
	
	public function init(){
		// Before init action.
		do_action( 'before_quizmaker_init' );
		
		// Set up localisation.
		$this->load_plugin_textdomain();
		
		$this->test_factory = new QM_Test_Factory();
		
		if ( $this->is_request( 'frontend' ) || $this->is_request( 'cron' ) ) {

			$session_class  = apply_filters( 'quizmaker_session_handler', 'QM_Session_Handler' );
			$this->session  = new $session_class();
		}
		
		add_action( 'init', array($this, 'initPlugin') );
				
		// Init action.
		do_action( 'quizmaker_init' );
	}
	
	
	public function define_constants() {
		
		$upload_dir = wp_upload_dir();

		$this->define( 'QM_PLUGIN_FILE', __FILE__ );
		$this->define( 'QM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		$this->define( 'QM_PLUGIN_DIR' , plugin_dir_path( __FILE__ ) );
		$this->define( 'QM_VERSION', $this->version );
		$this->define( 'QM_LOG_DIR', $upload_dir['basedir'] . '/qm-logs/' );
		$this->define( 'QM_SESSION_CACHE_GROUP', 'qm_session_id' );
	}
	
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}
	
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}
	
	public function includes() {

		// include_once( 'vendor/autoload.php' );
		
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
		include_once( 'includes/class-qm-autoloader.php' );
		include_once( 'includes/qm-core-functions.php' );
		include_once( 'includes/qm-widget-functions.php' );
		include_once( 'includes/class-qm-install.php' );
		include_once( 'includes/class-qm-image.php' );
		include_once( 'includes/tables/class-qm-table.php' );
		include_once( 'includes/class-qm-error.php' );
		include_once( 'includes/class-qm-ajax.php' );

		if ( $this->is_request( 'admin' ) ) {
			include_once( 'includes/admin/class-qm-admin.php' );
		}
		
		if ( $this->is_request( 'frontend' ) ) {
			$this->frontend_includes();
		}
		
		if ( $this->is_request( 'frontend' ) || $this->is_request( 'cron' ) ) {
			include_once( 'includes/abstracts/abstract-qm-session.php' );
			include_once( 'includes/class-qm-session-handler.php' );

			include_once( 'includes/class-qm-test-session.php' );
			
			include_once( 'includes/class-qm-quiz-session.php' );
		}
		
		$this->query = include( 'includes/class-qm-query.php' );
		
		include_once( 'includes/qm-question-functions.php' );
		include_once( 'includes/qm-test-functions.php' );
		
		include_once( 'includes/class-qm-post-type-test.php' );
		include_once( 'includes/class-qm-post-type-question.php' );
		include_once( 'includes/class-qm-post-type-certificate.php' );
		include_once( 'includes/class-qm-post-type-usergroup.php' );
		include_once( 'includes/class-qm-test.php' );
		include_once( 'includes/class-qm-question.php' );
		include_once( 'includes/class-qm-quiz.php' );
		
		include_once( 'includes/class-qm-test-factory.php' );

		if(is_plugin_active('woocommerce/woocommerce.php')){
			include_once( 'includes/class-qm-woocommerce.php' );
		}

		if(is_plugin_active('mycred/mycred.php')){
			// include_once( 'includes/class-qm-mycred.php' );
		}

		if(is_plugin_active('contact-form-7/wp-contact-form-7.php')){
			//include_once( 'includes/class-qm-wpcf7.php' );
		}
	}
	
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'quizmaker' );

		load_textdomain( 'quizmaker', WP_LANG_DIR . '/quizmaker/quizmaker-' . $locale . '.mo' );
		load_plugin_textdomain( 'quizmaker', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}
	
	public function frontend_includes() {
		
		include_once( 'includes/qm-message-functions.php' );
		include_once( 'includes/qm-template-hooks.php' );
		include_once( 'includes/class-qm-template-loader.php' );
		include_once( 'includes/class-qm-frontend-scripts.php' );
		include_once( 'includes/class-qm-shortcodes.php' );
		include_once( 'includes/class-qm-doing.php' );
		include_once( 'includes/class-qm-form-handler.php' );
	}
	
	public function initPlugin(){
		ob_start();
	}
	
	public function include_template_functions() {
		include_once( 'includes/qm-template-functions.php' );
	}
	
	public function setup_environment() {
		
		$this->define( 'QM_TEMPLATE_PATH', $this->template_path() );
	}
	
	public function template_path() {
		return apply_filters( 'quizmaker_template_path', 'quizmaker/' );
	}
	
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
	
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}
	
	public function doing() {
		
		return new QM_Doing();
	}
	
	public function mailer() {
		return QM_Emails::instance();
	}
}

function QM(){
	
	return Quizmaker::instance();
}

$GLOBALS['quizmaker'] = QM();