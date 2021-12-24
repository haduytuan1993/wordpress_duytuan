<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

<?php if(is_active_sidebar('quizmaker-right-sidebar')): ?>
<div class="col-md-3 qm-right-sidebar">
	<?php dynamic_sidebar( 'quizmaker-right-sidebar' ); ?>
</div>
<?php endif; ?>