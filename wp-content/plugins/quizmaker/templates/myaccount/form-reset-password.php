<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

<div class="container container-qm-myaccount-reset-password">
	
	<?php qm_print_messages(); ?>
	
	<h1 class="qm-myaccount-title-login"><?php _e('Reset Password'); ?></h1>
	
	<form class="qm-myaccount-reset-password" action="" method="post">
		
		<?php do_action( 'quizmaker_login_form_start' ); ?>
		
		<div class="form-row">
			<label for="password_1"><?php _e( 'Password', 'quizmaker' ); ?></label>
			<div class="ginput">
				<input type="text" class="input-text" name="password_1" id="password_1" />
			</div>
		</div>
		
		<div class="form-row">
			<label for="password_2"><?php _e( 'Password Again', 'quizmaker' ); ?></label>
			<div class="ginput">
				<input type="text" class="input-text" name="password_2" id="password_2" />
			</div>
		</div>
		
		<?php do_action( 'quizmaker_reset_password_form' ); ?>
		
		<div class="form-action">
			<?php wp_nonce_field( 'quizmaker-reset-password' ); ?>
			<input type="submit" class="button" name="reset_password" value="<?php esc_attr_e( 'Reset', 'quizmaker' ); ?>" />
			<input type="hidden" name="redirect" value="<?php echo esc_url( $redirect ) ?>" />
		</div>
		
		<?php do_action( 'quizmaker_login_form_end' ); ?>
	</form>
</div>