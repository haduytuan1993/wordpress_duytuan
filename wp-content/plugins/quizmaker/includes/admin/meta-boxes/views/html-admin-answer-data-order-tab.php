<?php 
	
$params_order =	qm_get_post_meta($post->ID, 'params_order');


$style_param	=	isset($params_order['style']) ? $params_order['style'] : 'style-line';

 ?>

<div class="answer-type-panel" id="answer-type-order">

	<div class="qm-groups-field">

		<div class="group-field">
			<label><?php _e('Style', 'quizmaker'); ?>:</label>
			<select name="answers_params_order[style]" value="<?php echo $style_param; ?>">
				<option value="style-line" <?php selected($style_param, 'style-line'); ?>><?php _e('Line', 'quizmaker'); ?></option>
				<option value="style-block" <?php selected($style_param, 'style-block'); ?>><?php _e('Block', 'quizmaker'); ?></option>
			</select>
		</div>

	</div>

	<table class="widefat wp-list-table answers-box order" cellspacing="0">
		<thead>
			<tr>
				<th class="is-correct"><?php _e('Correct', 'quizmaker'); ?></th>
				<th><?php _e('Content', 'quizmaker'); ?></th>
				<th class="qm-a-c qm-s-2"></th>
			</tr>
		</thead>
		<tbody>
		
		<?php 
			if($answers && $answer_type == 'order'):
				foreach($answers as $index => $ans):
					
					$image_tag	=	qm_image_tag($ans['image'], 'thumbnail', false);
		?>
			<tr>
				<td class="qm-td-c-a"><span class="answers_order-drag">Drag</span></td>
				<td>
					<div class="qm-answer-info">
						<?php echo $image_tag ? '<span class="qm-answer-remove-image"><i class="material-icons">cancel</i></span>' : ''; ?>
						<span class="qm-answer-image" data-name="answers_order[<?php echo $index; ?>][image]">
							<?php echo $image_tag; ?>
							<input type="hidden" name="answers_order[<?php echo $index; ?>][image]" value="<?php echo $ans['image']; ?>"/>
						</span>
					</div>
					<div class="qm-answer-desc">
						<textarea name="answers_order[<?php echo $index; ?>][content]" class="qm-s-wide"><?php echo $ans['content']; ?></textarea>
					</div>
				</td>
				<td class="qm-td-c-a"><button class="qm-remove"><i class="material-icons">cancel</i></button></td>
			</tr>
				<?php endforeach; ?>
		<?php endif; //End answers  ?>
		
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4"><a class="button-new-table" id="add-new-order" href="#answers"><?php _e('Add answer', 'quizmaker'); ?></a></td>
			</tr>
		</tfoot>
	</table>
</div>