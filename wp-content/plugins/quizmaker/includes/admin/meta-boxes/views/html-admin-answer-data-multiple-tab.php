<?php 
	
$params_multiple =	qm_get_post_meta($post->ID, 'params_multiple');

$multiple_columns_param	=	isset($params_multiple['columns']) && $params_multiple['columns'] > 0 ? absint($params_multiple['columns']) : 2;

$style_columns_param	=	isset($params_multiple['style']) ? $params_multiple['style'] : 'style-line';

 ?>

<div class="answer-type-panel" id="answer-type-multiple">
	
	<div class="qm-groups-field">

		<div class="group-field">
			<label><?php _e('Columns', 'quizmaker'); ?>:</label>
			
			<select name="answers_params_multiple[columns]" value="<?php echo $multiple_columns_param; ?>">
				<?php foreach( array(1, 2, 3, 4, 6, 12) as $col ): ?>
				<option value="<?php echo $col; ?>" <?php selected($multiple_columns_param, $col); ?>><?php echo $col; ?></option>
				<?php endforeach; ?>
			</select>

		</div>

		<div class="group-field">
			<label><?php _e('Style', 'quizmaker'); ?>:</label>
			<select name="answers_params_multiple[style]" value="<?php echo $style_columns_param; ?>">
				<option value="style-line" <?php selected($style_columns_param, 'style-line'); ?>><?php _e('Line', 'quizmaker'); ?></option>
				<option value="style-block" <?php selected($style_columns_param, 'style-block'); ?>><?php _e('Block', 'quizmaker'); ?></option>
			</select>
		</div>

	</div>

	<table class="widefat wp-list-table answers-box" cellspacing="0">
		<thead>
			<tr>
				<th class="is-correct"><?php _e('Correct', 'quizmaker'); ?></th>
				<th><?php _e('Content', 'quizmaker'); ?></th>
				<th class="qm-a-c qm-s-2"></th>
			</tr>
		</thead>
		<tbody>
					
		<?php if( $answers && $answer_type == 'multiple'):
			foreach($answers as $index => $ans):
				
				$selected	=	isset($ans['is_correct']) && $ans['is_correct'] == 1 ? ' checked':'';

				$image_tag	=	isset($ans['image']) ? qm_image_tag($ans['image'], 'thumbnail', false) : false;
				
				$image_val 	=	isset($ans['image']) ? $ans['image'] : 0;
		?>
	<tr>
		<td class="qm-td-c-a">
			<input type="checkbox" value="<?php echo $index; ?>" class="ir-is-correct" name="answers_multiple_is-correct[]" <?php echo $selected; ?> /></td>
		<td>
			<div class="qm-answer-info">
			<?php echo $image_tag ? '<span class="qm-answer-remove-image"><i class="material-icons">cancel</i></span>' : ''; ?>
			<span class="qm-answer-image" data-name="answers_multiple[<?php echo $index; ?>][image]">
				<?php echo $image_tag; ?>
				<input type="hidden" name="answers_multiple[<?php echo $index; ?>][image]" value="<?php echo $image_val; ?>"/>
			</span>
			</div>
			<div class="qm-answer-desc"><textarea name="answers_multiple[<?php echo $index; ?>][content]" class="qm-s-wide qm-answer-desc__editor"><?php echo $ans['content']; ?></textarea></div>
		</td>
		<td class="qm-td-c-a"><button class="qm-remove"><i class="material-icons">cancel</i></button></td>
	</tr>

			<?php endforeach; ?>
		<?php endif; //End answers  ?>

		</tbody>
		<tfoot>
			<tr>
				<td colspan="4">
					<a class="button-new-table" id="add-new-multiple-answer" href="#answers"><?php _e('Add answer', 'quizmaker'); ?></a>
				</td>
			</tr>
		</tfoot>
	</table>
</div>