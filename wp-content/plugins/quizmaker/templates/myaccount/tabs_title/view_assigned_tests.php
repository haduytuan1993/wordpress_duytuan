<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

<a href="<?php echo $view_assigned_tests_link; ?>" class="list-group-item list-group-item-action<?php echo ($active == 'view-assigned-tests') ? ' active':''; ?>">
	<?php _e('Assigned Tests', 'quizmaker'); ?>
</a>