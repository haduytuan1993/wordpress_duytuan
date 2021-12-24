<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div id="qm-myaccount-form-edit-account" class="container-myaccount-edit-account">
	
<?php do_action( 'quizmaker_before_my_account' ); ?>

<form class="edit-account" action="" method="post">

	<?php do_action( 'quizmaker_edit_account_form_start' ); ?>

	<div class="form-group row">
		<label for="account_first_name" class="col-sm-3 col-form-label"><?php _e( 'First name', 'quizmaker' ); ?> <span class="required">*</span></label>
		<div class="col-sm-9">

		  <input type="text" class="form-control" name="account_first_name" id="account_first_name" value="<?php echo esc_attr( $user->first_name ); ?>">

		</div>
	</div>

	<div class="form-group row">
		<label for="account_last_name" class="col-sm-3 col-form-label"><?php _e( 'Last name', 'quizmaker' ); ?> <span class="required">*</span></label>
		<div class="col-sm-9">

		  <input type="text" class="form-control" name="account_last_name" id="account_last_name" value="<?php echo esc_attr( $user->last_name ); ?>">

		</div>
	</div>

	<div class="form-group row">
		<label for="account_email" class="col-sm-3 col-form-label"><?php _e( 'Email', 'quizmaker' ); ?> <span class="required">*</span></label>
		<div class="col-sm-9">

		  <input type="email" class="form-control" name="account_email" id="account_email" value="<?php echo esc_attr( $user->user_email ); ?>">

		</div>
	</div>

	<div class="form-group row">
		<label for="account_username" class="col-sm-3 col-form-label"><?php _e( 'Username', 'quizmaker' ); ?> <span class="required">*</span></label>
		<div class="col-sm-9">

		  <input type="text" class="form-control" name="account_username" id="account_username" value="<?php echo esc_attr( $user->user_login ); ?>">

		</div>
	</div>

	<div class="form-group row">
		<label for="qm_password_1" class="col-sm-3 col-form-label"><?php _e( 'New Password', 'quizmaker' ); ?></label>
		<div class="col-sm-9">

		  <input type="password" class="form-control" name="password_1" id="qm_password_1">
		  <small id="password_1Help" class="form-text text-muted"><?php _e('Leave blank to leave unchanged', 'quizmaker'); ?></small>
		</div>
	</div>

	<div class="form-group row">
		<label for="qm_password_2" class="col-sm-3 col-form-label"><?php _e( 'Confirm New Password', 'quizmaker' ); ?></label>
		<div class="col-sm-9">

		  <input type="password" class="form-control" name="password_2" id="qm_password_2">

		</div>
	</div>

	<?php do_action( 'quizmaker_edit_account_form_end' ); ?>

	<div class="qm-form-sepa-head"></div>
	
	<?php wp_nonce_field( 'qm_save_account_details' ); ?>
	<button type="submit" class="btn mb-2"><?php esc_attr_e( 'Save changes', 'quizmaker' ); ?></button>
	<input type="hidden" name="action" value="qm_save_account_details" />
	<?php if(isset($_GET['redirect'])): ?>
	<input type="hidden" name="redirect" value="<?php echo $_GET['redirect']; ?>"/>
	<?php endif; ?>

</form>

	<?php do_action( 'quizmaker_after_my_account' ); ?>
</div>