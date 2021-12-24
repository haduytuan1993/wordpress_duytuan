<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

<?php if(is_active_sidebar('quizmaker-left-sidebar')): ?>
<div class="col-md-3 qm-left-sidebar">
	<?php dynamic_sidebar( 'quizmaker-left-sidebar' ); ?>
</div>
<?php endif; ?>