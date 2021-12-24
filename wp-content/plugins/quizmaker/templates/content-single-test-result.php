<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

?>

<?php do_action( 'quizmaker_before_single_test_result' ); ?>

<div id="test-<?php the_ID(); ?>-result" <?php post_class('col-sm-12'); ?>>
	
	<?php
		/**
		 * quizmaker_single_test_result_summary hook.
		 *
		 */
		do_action( 'quizmaker_single_test_result_summary' );
	?>

</div>

<?php do_action( 'quizmaker_after_single_test_result' ); ?>