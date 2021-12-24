<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

<?php 

if(isset($_GET['redirect'])) {

	$redirect = $_GET['redirect'];

}

 ?>

<div class="container container-qm-login">
	
	<?php qm_print_messages(); ?>
	
	<div class="inner">
		<h1 class="qm-myaccount-title-login"><?php _e('Login', 'quizmaker'); ?></h1>
	
		<form class="qm-myaccount-login" action="" method="post">
		
			<?php do_action( 'quizmaker_login_form_start' ); ?>
		
			<div class="form-row">
				<label for="username"><?php _e( 'Username or email', 'quizmaker' ); ?> <span class="required">*</span></label>
				<div class="ginput">
					<input type="text" class="input-text" name="username" id="username" />
				</div>
			</div>
			<div class="form-row">
				<label for="password"><?php _e( 'Password', 'quizmaker' ); ?> <span class="required">*</span></label>
				<div class="ginput">
					<input class="input-text" type="password" name="password" id="password" />
					<div class="info lost_password">
						<a href="<?php echo qm_get_endpoint_url( 'qm-lost-password', '', qm_get_page_permalink( 'myaccount' ) ); ?>"><?php _e( 'Lost your password?', 'quizmaker' ); ?></a>
					</div>
				</div>
			</div>
		
			<?php do_action( 'quizmaker_login_form' ); ?>
		
			<div class="form-action">
				<?php wp_nonce_field( 'quizmaker-login' ); ?>
				<input type="submit" class="button" name="login" value="<?php esc_attr_e( 'Login', 'quizmaker' ); ?>" />
				<input type="hidden" name="redirect" value="<?php echo esc_url( $redirect ) ?>" />
				<label for="rememberme" class="inline rememberme">
					<input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php _e( 'Remember me', 'quizmaker' ); ?>
				</label>
			</div>
		
			<?php do_action( 'quizmaker_login_form_end' ); ?>
		</form>
		<?php if(get_option( 'users_can_register' ) == 1): ?>
		<a href="<?php echo qm_get_page_permalink('register'); ?>?redirect=<?php echo urlencode($redirect); ?>" class="register-link"><?php _e('Register', 'quizmaker'); ?></a>
		<?php endif; ?>
	</div>
</div>