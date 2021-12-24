<div class="answer-type-panel" id="answer-type-guess_word">

	<table class="widefat wp-list-table answers-box" cellspacing="0">
		<thead>
			<tr>
				<th class="is-correct"><?php _e('Show', 'quizmaker'); ?></th>
				<th><?php _e('Content', 'quizmaker'); ?></th>
				<th class="qm-a-c qm-s-2"></th>
			</tr>
		</thead>
		<tbody>
		
		<?php if( $answers && $answer_type == 'guess_word'):
			foreach($answers as $index => $ans):
				
				$selected	=	isset($ans['is_correct']) && $ans['is_correct'] == 1 ? ' checked':'';
		?>
	<tr>
		<td class="qm-td-c-a">

			<input type="checkbox" value="<?php echo $index; ?>" class="ir-is-correct" name="answers_guess_word_is-correct[]" <?php echo $selected; ?> /></td>

		<td>
			
			<div class="qm-answer-desc no-pad"><input name="answers_guess_word[<?php echo $index; ?>][content]" class="qm-s-wide" value="<?php echo $ans['content']; ?>"/></div>

		</td>
		<td class="qm-td-c-a"><button class="qm-remove"><i class="material-icons">cancel</i></button></td>
	</tr>

			<?php endforeach; ?>
		<?php endif; //End answers  ?>

		</tbody>
		<tfoot>
			<tr>
				<td colspan="4">
					<a class="button-new-table" id="add-new-guess-word-answer" href="#answers"><?php _e('Add answer', 'quizmaker'); ?></a>
				</td>
			</tr>
		</tfoot>
	</table>
</div>