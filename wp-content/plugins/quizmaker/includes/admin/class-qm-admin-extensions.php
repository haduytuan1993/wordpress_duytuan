<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

class QM_Admin_Extensions {
	
	private static $errors   = array();

	private static $messages = array();
	
	public static function output() {

		$extensions 	= self::get_extensions();

		if( $extensions ) {
			
			foreach( $extensions as &$ex ) {

				$ex['status'] = 0;

				if( file_exists( ABSPATH . '/wp-content/plugins/' . $ex['slug'] ) ) {

					$ex['status'] = 1;

					if(is_plugin_active($ex['slug'] . '/' . $ex['slug'] . '.php')){

						$ex['status'] = 2;
					}
				}
			}
		}
		
		include 'views/html-admin-extensions.php';
	}


	public static function get_extensions() {

		$extensions = file_get_contents( 'http://rest.awstheme.com/quizmaker/extensions' );

		if( $extensions ) {

			$extensions = json_decode( $extensions, true );

			return $extensions;
			
		}else {

			return false;
		}
	}
}