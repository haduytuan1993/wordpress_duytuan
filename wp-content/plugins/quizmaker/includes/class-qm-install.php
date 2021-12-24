<?php
/**
 * Installation related functions and actions
 *
 * @author   AWSTheme
 * @category Admin
 * @package  QuizMaker/Classes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * QM_Install Class.
 */
class QM_Install {

	private static $db_updates = array(
		'1.2.0' => array(
			'qm_update_120_edit_database',
			'qm_update_120_db_version'
		),
		'1.3.0' => array(
			'qm_update_130_edit_database',
			'qm_update_130_db_version'
		),
		'1.4.9' => array(
			'qm_update_149_edit_database',
			'qm_update_149_db_version'
		),
		'1.7.0' => array(
			'qm_update_170_edit_database',
			'qm_update_170_db_version'
		)
	);

	private static $background_updater;
	
	public static function init() {
		add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
		add_action( 'init', array( __CLASS__, 'init_background_updater' ), 5 );
		add_action( 'admin_init', array( __CLASS__, 'install_actions' ) );
	}

	/**
	 * Init background updates
	 */
	public static function init_background_updater() {
		
		include_once( dirname( __FILE__ ) . '/class-qm-background-updater.php' );
		self::$background_updater = new QM_Background_Updater();
		
	}
	
	public static function check_version() {
	

		if ( ! defined( 'IFRAME_REQUEST' ) && get_option( 'quizmaker_version' ) !== QM()->version ) {
			
			self::install();
			do_action( 'quizmaker_updated' );
		}
	}

	public static function install_actions() {
		
		if ( ! empty( $_GET['do_update_quizmaker'] ) ) {
			self::update();
			QM_Admin_Notices::add_notice( 'update' );
		}
		
	}
	
	/**
	 * Install QM.
	 */
	public static function install() {
		global $wpdb;
		
		if ( ! defined( 'QM_INSTALLING' ) ) {
			define( 'QM_INSTALLING', true );
		}

		// Ensure needed classes are loaded
		include_once( 'admin/class-qm-admin-notices.php' );
		
		self::create_options();
		self::create_tables();
		self::create_roles();
		self::create_pages();
		
		// Queue upgrades/setup wizard
		$current_qm_version    = get_option( 'quizmaker_version', null );
		$current_db_version    = get_option( 'quizmaker_db_version', null );
		
		QM_Admin_Notices::remove_all_notices();

		// Register post types
		QM_Post_Type_Test::register_post_types();
		QM_Post_Type_Test::register_taxonomies();

		QM_Post_Type_Question::register_post_types();
		QM_Post_Type_Question::register_taxonomies();
		
		QM_Post_Type_Certificate::register_post_types();

		QM_Post_Type_Usergroup::register_post_types();

		if ( ! is_null( $current_db_version ) && version_compare( $current_db_version, max( array_keys( self::$db_updates ) ), '<' ) ) {

			QM_Admin_Notices::add_notice( 'update' );

		} else {

			self::update_db_version();
		}

		// self::update_db_version();
		self::update_qm_version();
		
		flush_rewrite_rules();
		
		$sql = "DELETE a, b FROM $wpdb->options a, $wpdb->options b
			WHERE a.option_name LIKE %s
			AND a.option_name NOT LIKE %s
			AND b.option_name = CONCAT( '_transient_timeout_', SUBSTRING( a.option_name, 12 ) )
			AND b.option_value < %d";
		$wpdb->query( $wpdb->prepare( $sql, $wpdb->esc_like( '_transient_' ) . '%', $wpdb->esc_like( '_transient_timeout_' ) . '%', time() ) );
		
		// Trigger action
		do_action( 'quizmaker_installed' );
	}
	
	private static function update_qm_version() {
		delete_option( 'quizmaker_version' );
		add_option( 'quizmaker_version', QM()->version );
	}
	
	public static function update_db_version( $version = null ) {
		delete_option( 'quizmaker_db_version' );
		add_option( 'quizmaker_db_version', is_null( $version ) ? QM()->version : $version );
	}

	public static function cron_schedules( $schedules ) {
		$schedules['monthly'] = array(
			'interval' => 2635200,
			'display'  => __( 'Monthly', 'quizmaker' )
		);
		return $schedules;
	}

	/**
	 * Push all needed DB updates to the queue for processing.
	 */
	private static function update() {
		
		$current_db_version = get_option( 'quizmaker_db_version' );
		//$logger             = new QM_Logger();
		$update_queued      = false;

		include_once( dirname( __FILE__ ) . '/class-qm-background-updater.php' );
		self::$background_updater = new QM_Background_Updater();
		
		foreach ( self::$db_updates as $version => $update_callbacks ) {

			if ( version_compare( $current_db_version, $version, '<' ) ) {
				foreach ( $update_callbacks as $update_callback ) {
					//$logger->add( 'qm_db_updates', sprintf( 'Queuing %s - %s', $version, $update_callback ) );

					self::$background_updater->push_to_queue( $update_callback );
					$update_queued = true;
				}
			}
		}

		if ( $update_queued ) {

			self::$background_updater->save()->dispatch();
		}
	}
	
	/**
	 * Create roles and capabilities.
	 */
	public static function create_roles() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		// Member role
		add_role( 'quizmaker_member', __( 'Member', 'quizmaker' ), array(
			'read' 					=> true
		) );

		// Manager role
		add_role( 'quizmaker_manager', __( 'QuizMaker Manager', 'quizmaker' ), array(
			'level_9'                => true,
			'level_8'                => true,
			'level_7'                => true,
			'level_6'                => true,
			'level_5'                => true,
			'level_4'                => true,
			'level_3'                => true,
			'level_2'                => true,
			'level_1'                => true,
			'level_0'                => true,
			'read'                   => true,
			'read_private_pages'     => true,
			'read_private_posts'     => true,
			'edit_users'             => true,
			'edit_posts'             => true,
			'edit_pages'             => true,
			'edit_published_posts'   => true,
			'edit_published_pages'   => true,
			'edit_private_pages'     => true,
			'edit_private_posts'     => true,
			'edit_others_posts'      => true,
			'edit_others_pages'      => true,
			'publish_posts'          => true,
			'publish_pages'          => true,
			'delete_posts'           => true,
			'delete_pages'           => true,
			'delete_private_pages'   => true,
			'delete_private_posts'   => true,
			'delete_published_pages' => true,
			'delete_published_posts' => true,
			'delete_others_posts'    => true,
			'delete_others_pages'    => true,
			'manage_categories'      => true,
			'manage_links'           => true,
			'moderate_comments'      => true,
			'unfiltered_html'        => true,
			'upload_files'           => true,
			'export'                 => true,
			'import'                 => true,
			'list_users'             => true
		) );

		$capabilities = self::get_core_capabilities();

		foreach ( $capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->add_cap( 'quizmaker_manager', $cap );
				$wp_roles->add_cap( 'administrator', $cap );
			}
		}
	}

	private static function get_core_capabilities() {
		$capabilities = array();

		$capabilities['core'] = array(
			'manage_quizmaker'
		);
		
		$capability_types = array( 'test', 'question', 'result', 'certificate' );
		
		foreach ( $capability_types as $capability_type ) {
			
			$capabilities[ $capability_type ] = array(
				// Post type
				"edit_{$capability_type}",
				"read_{$capability_type}",
				"delete_{$capability_type}",
				"edit_{$capability_type}s",
				"edit_others_{$capability_type}s",
				"publish_{$capability_type}s",
				"read_private_{$capability_type}s",
				"delete_{$capability_type}s",
				"delete_private_{$capability_type}s",
				"delete_published_{$capability_type}s",
				"delete_others_{$capability_type}s",
				"edit_private_{$capability_type}s",
				"edit_published_{$capability_type}s",

				// Terms
				"manage_{$capability_type}_terms",
				"edit_{$capability_type}_terms",
				"delete_{$capability_type}_terms",
				"assign_{$capability_type}_terms"
			);
		}

		return $capabilities;
	}

	public static function remove_roles() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		$capabilities = self::get_core_capabilities();

		foreach ( $capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->remove_cap( 'quizmaker_manager', $cap );
				$wp_roles->remove_cap( 'administrator', $cap );
			}
		}

		remove_role( 'quizmaker_member' );
		remove_role( 'quizmaker_manager' );
	}
	
	private static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( self::get_schema() );
	}
	
	private static function get_schema() {
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}
		
		$tables = "
CREATE TABLE {$wpdb->prefix}quizmaker_sessions (
  session_id bigint(20) NOT NULL AUTO_INCREMENT,
  session_orders mediumtext NOT NULL,
  session_key char(32) NOT NULL,
  session_value longtext NOT NULL,
  session_expiry bigint(20) NOT NULL,
  UNIQUE KEY session_id (session_id),
  PRIMARY KEY  (session_key)
) $collate;
CREATE TABLE {$wpdb->prefix}quizmaker_results (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  user_id bigint(20) NOT NULL,
  user_name varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  user_email varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  user_meta longtext COLLATE utf8_unicode_ci NOT NULL,
  user_ip varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  test_id bigint(20) NOT NULL,
  cert_id varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  percent smallint(6) NOT NULL DEFAULT 0,
  score int(10) NOT NULL DEFAULT 0,
  total_score int(10) NOT NULL DEFAULT 0,
  date_start datetime NOT NULL,
  date_end datetime NOT NULL,
  duration time NOT NULL,
  answers longtext NOT NULL,
  others longtext NOT NULL,
  PRIMARY KEY  (id)
) $collate;
CREATE TABLE {$wpdb->prefix}quizmaker_test_sessions ( id varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	  session_value longtext COLLATE utf8_unicode_ci NOT NULL, session_expiry bigint(20) NOT NULL, UNIQUE KEY id (id), PRIMARY KEY  (id)) $collate;
";
		return $tables;
	}
	
	public static function create_pages() {
		include_once( 'admin/qm-admin-functions.php' );
		
		$pages = apply_filters( 'quizmaker_create_pages', array(
			'archive_test' => array(
				'name'    => _x( 'quiz', 'Page slug', 'quizmaker' ),
				'title'   => _x( 'Quizmaker Archive', 'Page title', 'quizmaker' ),
				'content' => ''
			),
			'myaccount' => array(
				'name'    => _x( 'qm-account', 'Page slug', 'quizmaker' ),
				'title'   => _x( 'Quizmaker My Account', 'Page title', 'quizmaker' ),
				'content' => '[' . apply_filters( 'quizmaker_my_account_shortcode_tag', 'quizmaker_my_account' ) . ']'
			),
			'register' => array(
				'name'    => _x( 'qm-register', 'Page slug', 'quizmaker' ),
				'title'   => _x( 'Quizmaker Register', 'Page title', 'quizmaker' ),
				'content' => '[' . apply_filters( 'quizmaker_register_shortcode_tag', 'quizmaker_register' ) . ']'
			),
			'login' => array(
				'name'    => _x( 'qm-login', 'Page slug', 'quizmaker' ),
				'title'   => _x( 'Quizmaker Login', 'Page title', 'quizmaker' ),
				'content' => '[' . apply_filters( 'quizmaker_login_shortcode_tag', 'quizmaker_login' ) . ']'
			)
		) );
		
		foreach ( $pages as $key => $page ) {
			qm_create_page( esc_sql( $page['name'] ), 'quizmaker_' . $key . '_page_id', $page['title'], $page['content'], ! empty( $page['parent'] ) ? qm_get_page_id( $page['parent'] ) : '' );
		}
		
		delete_transient( 'quizmaker_cache_excluded_uris' );
		
	}
	
	private static function create_options() {
		// Include settings so that we can run through defaults
		include_once( 'admin/class-qm-admin-settings.php' );

		$settings = QM_Admin_Settings::get_settings_pages();
		
		foreach ( $settings as $section ) {
			if ( ! method_exists( $section, 'get_settings' ) ) {
				continue;
			}
			$subsections = array_unique( array_merge( array( '' ), array_keys( $section->get_sections() ) ) );

			foreach ( $subsections as $subsection ) {
				foreach ( $section->get_settings( $subsection ) as $value ) {
					if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
						$autoload = isset( $value['autoload'] ) ? (bool) $value['autoload'] : true;
						add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
					}
				}
			}
		}
	}
}

QM_Install::init();