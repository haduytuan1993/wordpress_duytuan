 
<div class="instant-answer">
	<div class="instant-answer-content">
		<div class="instant-status">
			<?php if($is_correct): ?>

			<div class="question-correct">

				<div><span class="material-icons question-correct-icon">check_circle</span> <?php echo __('Correct', 'quizmaker'); ?></div>
			</div>

			<?php else: ?>

			<div class="question-wrong">
				
				<div><span class="material-icons question-wrong-icon">cancel</span> <?php echo __('Wrong', 'quizmaker'); ?></div>
			</div>

			<?php endif; ?>
		</div>

		<?php if( isset($params['show_explanation']) && $params['show_explanation'] == 2 && $explanation): ?>
		<div class="margin-left instant-explanation">
			 <p>
			 	<?php echo $explanation; ?>
			 </p>
		 </div>
		<?php endif; ?>
	</div>

	<div class="instant-answer-action">
		<a href="#" class="qm-button question_next_page"><?php _e('Next Page', 'quizmaker'); ?></a>
	</div>
 </div>