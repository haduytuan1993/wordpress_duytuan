<?php
/**
 * Admin View: Notice - Updated
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated quizmaker-message qm-connect">
	<a class="quizmaker-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'qm-hide-notice', 'update', remove_query_arg( 'do_update_quizmaker' ) ), 'quizmaker_hide_notices_nonce', '_qm_notice_nonce' ) ); ?>"><?php _e( 'Dismiss', 'quizmaker' ); ?></a>

	<p><?php _e( 'Quizmaker data update complete. Thank you for updating to the latest version!', 'quizmaker' ); ?></p>
</div>
