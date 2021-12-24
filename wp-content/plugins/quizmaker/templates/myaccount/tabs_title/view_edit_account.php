<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

<a href="<?php echo $view_edit_account_link; ?>" class="list-group-item list-group-item-action<?php echo ($active == 'view-edit-account') ? ' active':''; ?>">
	<?php _e('Edit Account', 'quizmaker'); ?>
</a>