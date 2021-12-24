<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

<div class="container">
	
	<?php qm_print_messages(); ?>
	
	<h1 class="qm-myaccount-title-register"><?php _e('Register', 'quizmaker'); ?></h1>
	
	<form class="qm-myaccount-register" action="" method="post">
	
		<?php do_action( 'quizmaker_register_form_start' ); ?>
		
		<div class="form-row">
			<label for="username"><?php _e( 'Username', 'quizmaker' ); ?> <span class="required">*</span></label>
			<input type="text" class="input-text" name="username" id="username" />
		</div>
		
		<div class="form-row">
			<label for="email"><?php _e( 'Email address', 'quizmaker' ); ?> <span class="required">*</span></label>
			<input type="email" class="input-text" name="email" id="email" />
		</div>
		
		<div class="form-row">
			<label for="password"><?php _e( 'Password', 'quizmaker' ); ?> <span class="required">*</span></label>
			<input type="password" class="input-text" name="password" id="password" />
		</div>
		
		<?php do_action( 'quizmaker_register_form' ); ?>
		
		<div class="form-action">
			<?php wp_nonce_field( 'qm_save_register' ); ?>
			<input type="submit" class="button" name="qm_save_register" value="<?php esc_attr_e( 'Register', 'quizmaker' ); ?>" />
			<input type="hidden" name="action" value="qm_save_register" />

			<?php if(isset($_GET['redirect'])): ?>
			<input type="hidden" name="redirect" value="<?php echo esc_url(urldecode($_GET['redirect'])); ?>" />
			<?php endif; ?>
		</div>
	
		<?php do_action( 'quizmaker_register_form_end' ); ?>
	</form>
	
</div>