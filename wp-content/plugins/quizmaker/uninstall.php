<?php
/**
 * QuizMaker Uninstall
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$is_remove_data = get_option( 'quizmaker_is_uninstall_remove_data', 'no' );

if ( $is_remove_data == 'yes' ) {
	
	include_once( 'includes/class-qm-install.php' );
	QM_Install::remove_roles();
	
	wp_trash_post( get_option( 'quizmaker_archive_test_page_id' ) );
	wp_trash_post( get_option( 'quizmaker_myaccount_page_id' ) );
	wp_trash_post( get_option( 'quizmaker_register_page_id' ) );
	wp_trash_post( get_option( 'quizmaker_login_page_id' ) );
	wp_trash_post( get_option( 'quizmaker_memberships_page_id' ) );
	
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}quizmaker_results" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}quizmaker_sessions" );
	
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'quizmaker\_%';");
	
	// Delete posts + data.
	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type IN ( 'test', 'question', 'membership', 'member' );" );
	$wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;" );
	
	foreach ( array( 'test_cat', 'question_cat' ) as $taxonomy ) {
		$wpdb->delete(
			$wpdb->term_taxonomy,
			array(
				'taxonomy' => $taxonomy,
			)
		);
	}
	
	wp_cache_flush();
}
