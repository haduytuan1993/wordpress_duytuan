<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

$settings	=	isset($doing_data['settings']) && $doing_data['settings'] ? $doing_data['settings'] : array();

?>


<div id="qm-page-doing" class="reset-vuetify doing-stage" data-pages="<?php echo count($doing_data['questions']); ?>">
	

		<?php do_action('quizmaker_before_doing', $doing_data ); ?>

		<?php if(isset($settings['is_sidebar_tracking']) && !$settings['is_sidebar_tracking']){ ?>
		<div class="stage-timer"></div>
		<?php } ?>
	
		<form name="quizmaker_doing_test"  action="<?php echo esc_url(qm_get_result_test_url()); ?>" method="post" enctype="multipart/form-data">
			
			<div class="stage-questions"></div>

			<?php if(isset($doing_data['questions']) && $doing_data['questions']): ?>
			<div v-show="false" class="quizmaker-direction">
				<div class="stage-navigation">
					
					<?php if($settings['is_backward']): ?>
					<a href="#" class="qm_prev_page"><span class="material-icons">chevron_left</span>Prev</a>
					<?php endif; ?>
					
					<a href="#" class="qm_next_page"><span class="material-icons">chevron_right</span>Next</a>
					
				</div>
				
				<div class="stage-pagination" style="<?php echo !$settings['is_pagination'] ? 'display:none;' : ''; ?>">
					<ul>
					<?php foreach($doing_data['questions'] as $index => $q): ?>
						<li data-index="<?php echo $index; ?>"><span><?php echo $index + 1; ?></span></li>
					<?php endforeach; ?>
					</ul>
				</div>
			</div>
			<?php endif; ?>
			
			<button v-show="false" dark primary :style="{margin: '0 auto'}" type="submit" name="quizmaker_submit_test" value="1" class="qm-btn-submit-test"><?php _e('SUBMIT', 'quizmaker'); ?></button>
			
		</form>

	
		<?php do_action('quizmaker_after_doing', $doing_data ); ?>
</div>

