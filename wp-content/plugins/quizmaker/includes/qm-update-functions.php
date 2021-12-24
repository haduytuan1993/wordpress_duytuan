<?php
/**
 * Quizmaker Updates
 *
 * Functions for updating data, used by the background updater.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function qm_update_120_edit_database(){

	global $wpdb;

	if ( !$wpdb->get_var( "SHOW COLUMNS FROM `{$wpdb->prefix}quizmaker_results` LIKE 'others';" ) ) {
		$wpdb->query( "ALTER TABLE {$wpdb->prefix}quizmaker_results ADD `others` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;" );
		
	}
}

function qm_update_120_db_version() {

	QM_Install::update_db_version( '1.2.0' );
}

function qm_update_130_edit_database(){

	global $wpdb;

	$collate = '';
	
	if ( $wpdb->has_cap( 'collation' ) ) {
		$collate = $wpdb->get_charset_collate();
	}
			
			$tables = "CREATE TABLE {$wpdb->prefix}quizmaker_test_sessions ( id varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	  session_value longtext COLLATE utf8mb4_unicode_520_ci NOT NULL, session_expiry bigint(20) NOT NULL, UNIQUE KEY id (id), PRIMARY KEY  (id)) $collate;";
	 
	$wpdb->query($tables);
}

function qm_update_130_db_version() {

	QM_Install::update_db_version( '1.3.0' );
}

function qm_update_149_edit_database(){

	global $wpdb;

	if ( !$wpdb->get_var( "SHOW COLUMNS FROM `{$wpdb->prefix}quizmaker_results` LIKE 'user_name';" ) ) {

		$wpdb->query( "ALTER TABLE {$wpdb->prefix}quizmaker_results ADD `user_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL;" );
	}

	if ( !$wpdb->get_var( "SHOW COLUMNS FROM `{$wpdb->prefix}quizmaker_results` LIKE 'user_email';" ) ) {

		$wpdb->query( "ALTER TABLE {$wpdb->prefix}quizmaker_results ADD `user_email` varchar(100) COLLATE utf8_unicode_ci NOT NULL;" );
	}

	if ( !$wpdb->get_var( "SHOW COLUMNS FROM `{$wpdb->prefix}quizmaker_results` LIKE 'user_meta';" ) ) {

		$wpdb->query( "ALTER TABLE {$wpdb->prefix}quizmaker_results ADD `user_meta` longtext COLLATE utf8_unicode_ci NOT NULL;" );
	}

	if ( !$wpdb->get_var( "SHOW COLUMNS FROM `{$wpdb->prefix}quizmaker_results` LIKE 'user_ip';" ) ) {
		
		$wpdb->query( "ALTER TABLE {$wpdb->prefix}quizmaker_results ADD `user_ip` varchar(100) COLLATE utf8_unicode_ci NOT NULL;" );
	}

	$user_ids = $wpdb->get_col("SELECT user_id FROM `{$wpdb->prefix}quizmaker_results` GROUP BY user_id");

	if( $user_ids ) {

		foreach( $user_ids as $user_id ) {

			$user = get_user_by( 'id', $user_id );

			if( $user ) {

				$wpdb->update( $wpdb->prefix . 'quizmaker_results', array(
					'user_name'  => qm_get_formated_user_name($user),
					'user_email' => $user->user_email
				), array('user_id' => $user_id), array( '%s', '%s' ), array( '%d' ));
			}

		}
	}

}

function qm_update_149_db_version() {

	QM_Install::update_db_version( '1.4.9' );
}

function qm_update_170_db_version() {

	QM_Install::update_db_version( '1.7.0' );
}

function qm_update_170_edit_database(){
	
	global $wpdb;
	
	if ( !$wpdb->get_var( "SHOW COLUMNS FROM `{$wpdb->prefix}quizmaker_results` LIKE 'cert_id';" ) ) {
		
		$wpdb->query( "ALTER TABLE {$wpdb->prefix}quizmaker_results ADD `cert_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL AFTER `test_id`;" );
		
		$wpdb->query( "UPDATE {$wpdb->prefix}quizmaker_results SET cert_id = UPPER(SUBSTRING(MD5(UUID()), -10))" );
	}

}