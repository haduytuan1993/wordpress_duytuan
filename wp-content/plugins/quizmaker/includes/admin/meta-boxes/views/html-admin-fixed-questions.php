
<?php if(isset($results['data']) && $results['data']): ?>
<?php foreach($results['data'] as $question): ?>
	<tr data-id="<?php echo $question->ID; ?>">

		<td class="qm-a-c"><input type="checkbox" value="<?php echo $question->ID; ?>" name="select_fixed[]" class="select_row select_fixed"/></td>

		<td><a href="<?php echo admin_url('post.php?post=' . $question->ID . '&action=edit'); ?>"><?php echo $question->post_title; ?></a></td>
		
	</tr>
<?php endforeach; ?>
<?php else: ?>
	<tr class="fixed-questions_nodata"><td class="qm-a-c" colspan="2"><?php _e('No Data', 'quizmaker'); ?></td></tr>
<?php endif; ?>