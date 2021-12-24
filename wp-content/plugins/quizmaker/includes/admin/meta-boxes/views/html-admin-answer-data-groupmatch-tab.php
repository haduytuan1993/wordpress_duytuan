<?php 
	
$params_group_match =	qm_get_post_meta($post->ID, 'params_group_match');

$style_columns_param	=	isset($params_group_match['style']) ? $params_group_match['style'] : 'style-left-to-right';
?>
<div class="answer-type-panel" id="answer-type-group_match">

	<div class="qm-groups-field">
		
		<div class="group-field">
			<label><?php _e('Style', 'quizmaker'); ?>:</label>
			<select name="answers_params_group_match[style]" value="<?php echo $style_columns_param; ?>">
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
		
		<?php if($answers && $answer_type == 'group_match'):
			
			foreach($answers as $index => $ans):
		?>

			<tr>
				<td>
					
					<div class="qm-group-input-label">
						<label class="center"><?php _e('Group', 'quizmaker'); ?></label>
						<div class="g-input">
							<input type="text" name="answers_group_match[<?php echo $index; ?>][group]" value="<?php echo $ans['group']; ?>"/>
						</div>
					</div>
					<div class="qm-group-input-label">
						<label class="center"><?php _e('Answer', 'quizmaker'); ?></label>
						<div class="g-input">
							<input type="text" name="answers_group_match[<?php echo $index; ?>][item]" value="<?php echo $ans['item']; ?>"/>
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
					<a class="button-new-table" id="add-new-group-match" href="#answers"><?php _e('Add answer', 'quizmaker'); ?></a>
				</td>
			</tr>
		</tfoot>
	</table>
</div>