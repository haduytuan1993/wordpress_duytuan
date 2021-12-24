<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

<div class="action">
	
	<form name="quizmaker_start_test" action="<?php echo esc_url(qm_get_start_test_url($test_id)); ?>" method="post" enctype="multipart/form-data">
		
		<div class="table-data">

			<?php apply_filters( 'quizmaker_single_test_start_test_info', $test_info, $test_id ); ?>

		</div>

		<?php if( $is_can_do_test ): ?>
		<button type="submit" name="quizmaker_start_test" value="1" class="qm-btn-single-start">
			<?php _e('START', 'quizmaker'); ?></button>
		<?php elseif( qm_can_do_test_as_guest( $test_id ) ): ?>
		<button type="submit" name="quizmaker_start_test" value="0" class="qm-btn-single-start">
			<?php _e('START', 'quizmaker'); ?></button>
		<?php elseif(!is_user_logged_in()): ?>
		<a href="<?php echo qm_get_page_permalink( 'myaccount' ); ?>?redirect=<?php echo urlencode(esc_url(qm_get_start_test_url($test_id))); ?>" class="qm-btn-single-start-login"><?php _e('LOGIN/REGISTER', 'quizmaker'); ?></a>
		<?php endif; ?>
		
		<input type="hidden" name="id" value="<?php echo $test_id; ?>"/>
		
	</form>
	
</div>