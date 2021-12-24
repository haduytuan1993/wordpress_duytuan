<?php
/**
 * Admin View: Notice - Updating
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated quizmaker-message qm-connect">
	<p><strong><?php _e( 'Quizmaker Data Update', 'quizmaker' ); ?></strong> &#8211; <?php _e( 'Your database is being updated in the background.', 'quizmaker' ); ?> <a href="<?php echo esc_url( add_query_arg( 'force_update_quizmaker', 'true', admin_url( 'admin.php?page=qm-settings' ) ) ); ?>"><?php _e( 'Taking a while? Click here to run it now.', 'quizmaker' ); ?></a></p>
</div>
