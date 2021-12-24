<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

<div class="container container-qm-myaccount-lost-password">
	
	<?php qm_print_messages(); ?>
	
	<h1 class="qm-myaccount-title-login"><?php _e('Lost Password', 'quizmaker'); ?></h1>
	
	<form class="qm-myaccount-lost-password" action="" method="post">
		
		<?php if( 'lost_password' === $args['form'] ) : ?>
		
		<div class="form-row">
			<label for="user_login"><?php _e( 'Username or email', 'quizmaker' ); ?> <span class="required">*</span></label>
			<div class="ginput">
				<input type="text" class="input-text" name="user_login" id="user_login" />
			</div>
		</div>
		
		<?php else : ?>
		
		<div class="form-row">
			<label for="password_1"><?php _e( 'Password', 'quizmaker' ); ?></label>
			<div class="ginput">
				<input type="password" class="input-text" name="password_1" id="password_1" />
			</div>
		</div>
		
		<div class="form-row">
			<label for="password_2"><?php _e( 'Password Again', 'quizmaker' ); ?></label>
			<div class="ginput">
				<input type="password" class="input-text" name="password_2" id="password_2" />
			</div>
		</div>
		
		<input type="hidden" name="reset_key" value="<?php echo isset( $args['key'] ) ? $args['key'] : ''; ?>" />
		<input type="hidden" name="reset_login" value="<?php echo isset( $args['login'] ) ? $args['login'] : ''; ?>" />
		
		<?php endif; ?>
		
		<?php do_action( 'quizmaker_lostpassword_form' ); ?>
		
		<div class="form-action">
			<?php wp_nonce_field( 'quizmaker-reset-password' ); ?>
			<input type="submit" class="button" name="lost_password" value="<?php esc_attr_e( 'Reset Password', 'quizmaker' ); ?>" />
			<input type="hidden" name="redirect" value="<?php echo esc_url( $redirect ) ?>" />
		</div>
		
	</form>
</div>