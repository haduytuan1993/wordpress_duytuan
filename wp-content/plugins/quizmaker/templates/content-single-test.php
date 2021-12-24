
<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

<?php if(is_doing()): 
	
	do_action( 'quizmaker_test_doing' ); ?>

<?php elseif(is_doing_form()):
	
	qm_get_template('content-fillform.php'); ?>
	
<?php elseif(is_result()):

	qm_get_template('content-single-test-result.php'); ?>
	
<?php else: ?>
	
	<?php do_action( 'quizmaker_left_sidebar' ); ?>
		
<?php

	 do_action( 'quizmaker_before_single_test' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>

<div id="test-<?php the_ID(); ?>" <?php quizmaker_container_class('single-test'); ?>>
	<div class="qm-container-inner">
		<?php do_action( 'quizmaker_single_test_summary' ); ?>
	</div>
</div>

		<?php do_action( 'quizmaker_after_single_test' ); ?>

		<?php do_action( 'quizmaker_right_sidebar' ); ?>

<?php endif; ?>