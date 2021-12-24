<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * QuizMaker Autoloader.
 *
 * @class 		QM_Autoloader
 * @version		1.0.0
 * @package		QuizMaker/Classes/
 * @category	Class
 * @author 		AWSTheme
 */
class QM_Autoloader {

	private $include_path = '';
	
	public function __construct() {
		if ( function_exists( "__autoload" ) ) {
			spl_autoload_register( "__autoload" );
		}

		spl_autoload_register( array( $this, 'autoload' ) );

		$this->include_path = untrailingslashit( plugin_dir_path( QM_PLUGIN_FILE ) ) . '/includes/';
	}

	private function get_file_name_from_class( $class ) {
		return 'class-' . str_replace( '_', '-', $class ) . '.php';
	}

	private function load_file( $path ) {
		if ( $path && is_readable( $path ) ) {
			include_once( $path );
			return true;
		}
		return false;
	}

	public function autoload( $class ) {
		$class = strtolower( $class );
		$file  = $this->get_file_name_from_class( $class );
		$path  = '';

		if ( strpos( $class, 'qm_shortcode_' ) === 0 ) {
			$path = $this->include_path . 'shortcodes/';
		} elseif ( strpos( $class, 'qm_meta_box' ) === 0 ) {
			$path = $this->include_path . 'admin/meta-boxes/';
		} elseif ( strpos( $class, 'qm_admin' ) === 0 ) {
			$path = $this->include_path . 'admin/';
		} elseif ( strpos( $class, 'qm_marketing' ) === 0 ) {
			$path = $this->include_path . 'marketing/';
		} elseif ( strpos( $class, 'qm_table' ) === 0 ) {
			$path = $this->include_path . 'tables/';
		} elseif ( strpos( $class, 'qm_addon_' ) === 0 ) {
			$path = $this->include_path . 'addons/';
		}
		
		if ( empty( $path ) || ( ! $this->load_file( $path . $file ) && strpos( $class, 'qm_' ) === 0 ) ) {
			
			$this->load_file( $this->include_path . $file );
		}
	}
}

new QM_Autoloader();
