<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="container-inner container-myaccount-edit-account">
	
<?php qm_print_messages(); ?>

<h1 class="qm-myaccount-title"><?php _e('CREATE AN ACCOUNT', 'quizmaker'); ?></h1>

<form class="qm-myaccount-form edit-account" action="" method="post">
	
	<?php do_action( 'quizmaker_edit_account_form_start' ); ?>
	
	<div class="form-row">
		<label for="account_first_name"><?php _e( 'First name', 'quizmaker' ); ?> <span class="required">*</span></label>
		<div class="ginput">
			<input type="text" class="input-text" name="account_first_name" id="account_first_name" />
		</div>
	</div>
	
	<div class="form-row">
		<label for="account_last_name"><?php _e( 'Last name', 'quizmaker' ); ?> <span class="required">*</span></label>
		<div class="ginput">
			<input type="text" class="input-text" name="account_last_name" id="account_last_name" />
		</div>
	</div>
	
	<div class="form-row form-row-wide">
		<label for="account_email"><?php _e( 'Email address', 'quizmaker' ); ?> <span class="required">*</span></label>
		<input type="email" class="input-text" name="account_email" id="account_email"/>
	</div>
	
	<div id="is_account_group">
		<div class="qm-form-sepa-head"></div>
		
		<div class="form-row">
			<label for="account_username"><?php _e( 'Username', 'quizmaker' ); ?> <span class="required">*</span></label>
			<div class="ginput">
				<input type="text" class="input-text" name="account_username" id="account_username" />
			</div>
		</div>

		<div class="form-row form-row-wide">
			<label for="password_1"><?php _e( 'New Password', 'quizmaker' ); ?></label>
			<div class="ginput">
				<input type="password" class="input-text" name="password_1" id="qm_password_1" />
			</div>
		</div>
		<div class="form-row form-row-wide">
			<label for="password_2"><?php _e( 'Confirm New Password', 'quizmaker' ); ?></label>
			<input type="password" class="input-text" name="password_2" id="qm_password_2" />
		</div>
	</div>
	
	<?php do_action( 'quizmaker_edit_account_form' ); ?>
	
	<div class="qm-form-sepa-head"></div>
	
	<div class="form-row-action">
		<?php wp_nonce_field( 'qm_save_account_details' ); ?>
		<input type="submit" class="button" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'quizmaker' ); ?>" />
		<input type="hidden" name="is_account" id="is_account" checked value="1"/>
		<input type="hidden" name="quizmaker_guest_register" value="<?php echo $result_id; ?>" />
		
	</div>
	
	<?php do_action( 'quizmaker_edit_account_form_end' ); ?>
</form>

</div>