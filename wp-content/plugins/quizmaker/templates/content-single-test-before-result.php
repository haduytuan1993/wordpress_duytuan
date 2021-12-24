<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="container-inner">

	<form method="POST" action="">
		<div class="form-row">
			<label><?php _e('First Name', 'quizmaker'); ?></label>
			<input type="text" name="account_first_name" class="input-text">
		</div>

		<div class="form-row">
			<label><?php _e('Last Name', 'quizmaker'); ?></label>
			<input type="text" name="account_last_name" class="input-text">
		</div>

		<div class="form-row">
			<label><?php _e('Email', 'quizmaker'); ?></label>
			<input type="email" name="account_email" class="input-text">
		</div>

		<div class="form-row">
			<label>
				<input type="checkbox" name="is_create_account">
				<?php _e('Create an account!', 'quizmaker'); ?></label>
		</div>

		<div class="form-group">
			
			<div class="form-row">
				<label><?php _e('Password', 'quizmaker'); ?></label>
				<input type="password" name="password_1" class="input-text">
			</div>

		</div>

		<button type="submit"><?php _e('Submit', 'quizmaker'); ?></button>
		<input type="hidden" name="quizmaker_guest_register" value="<?php echo get_current_user_id(); ?>"/>
	</form>

</div>