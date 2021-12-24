<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $test;

$comments = get_comments(array(
			'post_id' => $test->get_id(),
			'status' => 'approve'
		));
?>
<div class="qm-comments">
	<h2 class="part-title"><?php _e('Comments', 'quizmaker'); ?></h2>
	<?php if($comments): ?>
	<div class="comment list">
		
	    <?php echo wp_list_comments( array( 'style' => 'div' ), $comments ); ?>
	</div>
	<?php endif; ?>
	
	<?php comment_form(); ?>
</div>