<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="qm-activation" class="container">
	
<?php if( $status == 2 ): ?>
	
	<div class="mt-5 alert alert-success" role="alert">
	  <h4 class="alert-heading"><?php _e('Your account is activated!', 'quizmaker'); ?></h4>
	  <p><?php _e('You could click this link for Login', 'quizmaker'); ?></p>
	  <hr>
	  <p class="mb-0"><a href="<?php echo qm_get_endpoint_url( 'view-results', '', qm_get_page_permalink( 'myaccount' ) ); ?>" class="btn btn-primary"><?php _e('Login', 'quizmaker'); ?></a></p>
	</div>
	
<?php elseif( $status == 3 ): ?>
	
	<div class="mt-5 alert alert-danger" role="alert">
	  <h4 class="alert-heading"><?php _e('Your activation key is not valid!', 'quizmaker'); ?></h4>
	  <p><?php _e('You need to access your email and click to the valid activate link', 'quizmaker'); ?></p>
	  <hr>
	  <p class="mb-0"><a href="<?php echo site_url('/'); ?>" class="btn btn-danger"><?php _e('Home', 'quizmaker'); ?></a></p>
	</div>
	
<?php elseif( $status == 1 ): ?>

	<div class="mt-5 alert alert-danger" role="alert">
	  <h4 class="alert-heading"><?php _e('Your account is not activated!', 'quizmaker'); ?></h4>
	  <p><?php _e('You need to access your email and click to the activate link', 'quizmaker'); ?></p>
	  <hr>
	  <p class="mb-0"><a href="<?php echo site_url('/'); ?>" class="btn btn-danger"><?php _e('Home', 'quizmaker'); ?></a></p>
	</div>

<?php else: ?>

	<div class="mt-5 alert alert-primary" role="alert">
	  <h4 class="alert-heading"><?php _e('Account registered successfully!', 'quizmaker'); ?></h4>
	  <p><?php _e('Check your email for activate your account!', 'quizmaker'); ?></p>
	  <hr>
	  <p class="mb-0"><a href="<?php echo site_url('/'); ?>" class="btn btn-primary"><?php _e('Home', 'quizmaker'); ?></a></p>
	</div>

<?php endif; ?>

</div>