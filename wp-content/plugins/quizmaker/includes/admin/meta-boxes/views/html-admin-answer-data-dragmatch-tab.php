<?php 
	
$params_drag_match =	qm_get_post_meta($post->ID, 'params_drag_match');

$style_columns_param	=	isset($params_drag_match['style']) ? $params_drag_match['style'] : 'style-right-to-left';
?>
<div class="answer-type-panel" id="answer-type-drag_match">
	
	<div class="qm-groups-field">
		
		<div class="group-field">
			<label><?php _e('Style', 'quizmaker'); ?>:</label>
			<select name="answers_params_drag_match[style]" value="<?php echo $style_columns_param; ?>">
				<option value="style-left-to-right" <?php selected($style_columns_param, 'style-left-to-right'); ?>><?php _e('Drag Left to Right', 'quizmaker'); ?></option>
				<option value="style-right-to-left" <?php selected($style_columns_param, 'style-right-to-left'); ?>><?php _e('Drag Right to Left', 'quizmaker'); ?></option>
			</select>
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
		
		<?php if($answers && $answer_type == 'drag_match'):
			foreach($answers as $index => $ans):
		?>

			<tr>
				<td>
					<textarea name="answers_drag_match[<?php echo $index; ?>][content]" class="qm-s-wide"><?php echo $ans['content']; ?></textarea>
					<div class="qm-group-input-label">
						<label class="center"><?php _e('Answer', 'quizmaker'); ?></label>
						<div class="g-input">
							<input type="text" name="answers_drag_match[<?php echo $index; ?>][answer]" value="<?php echo $ans['answer']; ?>"/>
						</div>
					</div>
				</td>
				<td class="qm-td-c-a"><button class="qm-remove"><i class="material-icons">cancel</i></button></td>
			</tr>
			
			<?php endforeach; ?>
		<?php endif; //End answers  ?>
		
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4">
					<a class="button-new-table" id="add-new-drag-match" href="#answers"><?php _e('Add answer', 'quizmaker'); ?></a>
				</td>
			</tr>
		</tfoot>
	</table>
</div>