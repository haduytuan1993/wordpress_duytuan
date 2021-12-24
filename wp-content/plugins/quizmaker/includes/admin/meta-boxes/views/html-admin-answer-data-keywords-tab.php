<?php 
	
$params_keywords =	qm_get_post_meta($post->ID, 'params_keywords');


$min_corrects	=	isset($params_keywords['min_corrects']) ? absint($params_keywords['min_corrects']) : 1;

 ?>

<div class="answer-type-panel" id="answer-type-keywords">
	
	<div class="qm-groups-field">

		<div class="group-field">
			<label><?php _e('Min Corrects', 'quizmaker'); ?>:</label>
			<input type="text" name="answers_params_keywords[min_corrects]" value="<?php echo $min_corrects; ?>">
		</div>

	</div>

	<table class="widefat wp-list-table answers-box" cellspacing="0">
		<thead>
			<tr>
				<th><?php _e('Content', 'quizmaker'); ?></th>
				<th class="qm-a-c qm-s-2"></th>
			</tr>
		</thead>
		<tbody>
		
		<?php if( $answers && $answer_type == 'keywords'):
			foreach($answers as $index => $ans):	
		?>
	<tr>

		<td>
			
			<div class="qm-answer-desc no-pad"><input name="answers_keywords[<?php echo $index; ?>][content]" class="qm-s-wide" value="<?php echo $ans['content']; ?>"/></div>

		</td>
		<td class="qm-td-c-a"><button class="qm-remove"><i class="material-icons">cancel</i></button></td>

	</tr>

			<?php endforeach; ?>
		<?php endif; //End answers  ?>

		</tbody>
		<tfoot>
			<tr>
				<td colspan="4">
					<a class="button-new-table" id="add-new-keywords-answer" href="#answers"><?php _e('Add answer', 'quizmaker'); ?></a>
				</td>
			</tr>
		</tfoot>
	</table>
</div>