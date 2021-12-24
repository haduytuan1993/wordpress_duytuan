<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

class QM_Admin_User_Certificates {
	
	private static $errors   = array();

	private static $messages = array();
	
	public static function output() {

		global $wpdb;

		$search = isset($_GET['s']) ? $_GET['s'] : '';
	
		$result_tbl	=	$wpdb->prefix . 'quizmaker_results rs';
		$test_tbl   =	$wpdb->prefix . 'posts t';
		$user_tbl   =	$wpdb->prefix . 'users u';
		
		$results = [];

		if( $search ) {

			$search_query = '%' . $search . '%';

			$results = $wpdb->get_results($wpdb->prepare('SELECT rs.*, u.user_nicename, u.ID AS user_id, t.post_title AS test_name FROM ' . $result_tbl . 
				' LEFT JOIN ' . $user_tbl . ' ON u.ID = rs.user_id' .
				' LEFT JOIN ' . $test_tbl . ' ON t.ID = rs.test_id' .
				' WHERE rs.cert_id != %s AND rs.cert_id LIKE %s' .
				' ORDER BY rs.date_start DESC LIMIT 100', 0, $search_query), ARRAY_A);
			
		}else{

			// $results = $wpdb->get_results($wpdb->prepare('SELECT rs.*, u.user_nicename, u.ID AS user_id, t.post_title AS test_name FROM ' . $result_tbl .
			// 	 ' LEFT JOIN ' . $user_tbl . ' ON u.ID = rs.user_id' .
			// 	 ' LEFT JOIN ' . $test_tbl . ' ON t.ID = rs.test_id' .
			// 	 ' WHERE rs.cert_id != %s' . 
			// 	 ' ORDER BY rs.date_start DESC LIMIT 100', 0), ARRAY_A);
		}

		include 'views/html-admin-user-certificates.php';
	}
}