<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

?>

<?php do_action( 'quizmaker_before_single_test_start' ); ?>

<div id="test-<?php the_ID(); ?>-start" <?php post_class(); ?>>
	
	<?php
		/**
		 * quizmaker_single_test_start_summary hook.
		 *
		 */
		do_action( 'quizmaker_single_test_start_summary' );
	?>

</div>

<?php do_action( 'quizmaker_after_single_test_start' ); ?>