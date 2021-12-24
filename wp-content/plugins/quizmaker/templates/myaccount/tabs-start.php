<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

<div class="pt-4 pb-4 mb-4 page-header-wrapper">

	<div class="container">
		<h3 class="page-title mb-0"><?php _e('My Account', 'quizmaker'); ?></h3>
	</div>

</div>

<div id="qm-myaccount">
	
		<div class="container">
			<div class="row">

				<div class="col-sm-3 qm-tabs-title-container">

					<?php do_action( 'quizmaker_my_account_user_info', $args ); ?>

					<ul class="list-group mb-3">
						
						<?php apply_filters( 'quizmaker_my_account_tabs_title', $args ); ?>

			        </ul>

			        <a href="<?php echo wp_logout_url( apply_filters('quizmaker_logout_redirect', site_url()) ); ?>" class="btn btn-outline-danger btn-block"><?php _e('Logout', 'quizmaker'); ?></a>

				</div>
				<div class="col-sm-9 qm-tabs-panel-container">
					<?php qm_print_messages(); ?>