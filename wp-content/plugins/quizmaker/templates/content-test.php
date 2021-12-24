<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

$has_thumbnail	=	quizmaker_get_test_thumbnail() ? true : false;

?>

<li class="item<?php echo $has_thumbnail ? ' is-thumbnail' : '' ?>">
	<div class="row">
	<?php
	/**
	 * quizmaker_before_quiz_loop_item hook.
	 *
	 */
	do_action( 'quizmaker_before_quiz_loop_item' );

	/**
	 * quizmaker_quiz_loop_item_summary hook.
	 *
	 */
	do_action( 'quizmaker_quiz_loop_item_summary' );

	/**
	 * quizmaker_after_quiz_loop_item hook.
	 *
	 */
	do_action( 'quizmaker_after_quiz_loop_item' );
	?>
	</div>
</li>