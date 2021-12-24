<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

class QM_Admin_Dashboard {
	
	private static $errors   = array();

	private static $messages = array();
	
	public static function output() {
		
		include 'views/html-admin-dashboard.php';
	}
}