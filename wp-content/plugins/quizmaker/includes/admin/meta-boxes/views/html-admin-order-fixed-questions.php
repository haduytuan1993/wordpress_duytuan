
<?php if(isset($results['data']) && $results['data']): ?>

<ul id="list-order-questions">
<?php foreach($results['data'] as $question): ?>
	<li data-id="<?php echo $question->ID; ?>" id="q_<?php echo $question->ID; ?>">

		<a href="<?php echo admin_url('post.php?post=' . $question->ID . '&action=edit'); ?>"><?php echo $question->post_title; ?></a>
		
	</li>
<?php endforeach; ?>
</ul>

<?php else: ?>
<div class="fixed-questions_nodata"><?php _e('No Data', 'quizmaker'); ?></div>
<?php endif; ?>