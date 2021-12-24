<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="container">
	
	<?php qm_print_messages(); ?>
	
	<p class="myaccount_user">
		<?php
		printf(
			__( 'Hello <strong>%1$s</strong> (not %1$s? <a href="%2$s">Sign out</a>).', 'quizmaker' ) . ' ',
			$current_user->display_name,
			$logout_url
		);

		printf( __( 'From your account dashboard you can view your recent orders, manage your shipping and billing addresses and <a href="%s">edit your password and account details</a>.', 'quizmaker' ), $edit_url);
		?>
	</p>
	
	<?php do_action( 'quizmaker_before_my_account' ); ?>
	
	<?php qm_get_template( 'myaccount/my-tests.php' ); ?>
	
	<?php do_action( 'quizmaker_after_my_account' ); ?>
	
</div>