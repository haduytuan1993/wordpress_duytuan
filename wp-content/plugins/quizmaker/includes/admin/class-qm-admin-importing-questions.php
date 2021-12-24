<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

class QM_Admin_Importing_Questions {
	
	private static $errors   = array();

	private static $messages = array();
	
	public static function output() {

		
		include 'views/html-admin-importing-questions.php';
	}

}