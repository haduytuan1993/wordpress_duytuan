<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }

$settings	=	isset($doing_data['settings']) && $doing_data['settings'] ? $doing_data['settings'] : array();

 ?>

</div>

<div id="qm-navigate-container">

	<div v-if="is_submited" class="overlay-loading">
		
		<div class="progress-icon">
			<v-progress-circular v-if="is_submited" class="mt-2 ml-2 mr-2" indeterminate v-bind:size="70" color="white"></v-progress-circular>
		</div>

	</div>

<?php if(isset($settings['is_sidebar_tracking']) && $settings['is_sidebar_tracking']){ ?>
	
	<div v-show="is_show_sidebar" class="sidebar-stage-timer">

		<div class="sidebar-content">

			<?php the_post_thumbnail(); ?>

			<?php if($doing_data['questions']): ?>
			
			<div class="stage-timer"></div>

			
			<div class="questions-minilist">
				<div class="container-scroll">
				<?php $page_order = 1; foreach($doing_data['questions'] as $page => $questions): ?>
				<ul class="questions-minilist_page page-<?php echo $page + 1; ?>" data-page="<?php echo $page + 1; ?>">
					<?php foreach($questions as $index => $q): ?>
					<li class="sidebar-question" data-id="<?php echo $q; ?>" data-page="<?php echo $page + 1; ?>">
						<?php echo $page_order++; ?>
					</li>
					<?php endforeach; ?>				
				</ul>
				<?php endforeach; ?>
				
				</div>
			</div>
			

			<?php endif; ?>
		</div>

		<div class="sidebar-bottom-toolbar"></div>

	</div>

<?php } ?>

<div class="doing-toolbar">

	<v-progress-circular indeterminate color="white" v-if="is_loading"></v-progress-circular>


	<button v-if="!is_submited" class="doing-toolbar_button right" id="doing-submit" @click="submitTest(false)"><?php _e('SUBMIT', 'quizmaker'); ?></button>

	<?php if(isset($settings['save_for_later']) && $settings['save_for_later']): ?>
	<button class="doing-toolbar_button right page" @click="saveForLater()"><?php _e('Save', 'quizmaker'); ?></button>
	<?php endif; ?>

	<div v-if="<?php echo $settings['duration']; ?> > 0" class="stopwatch-wrap">
		<stopwatch v-if="!is_submited" :seconds="<?php echo $settings['duration'] * 60; ?>" :passed="<?php echo absint($time_passed); ?>" v-on:complete="submitTest(true)"></stopwatch>
	</div>

	<?php if(isset($settings['is_sidebar_tracking']) && $settings['is_sidebar_tracking']): ?>
	<button v-if="!is_submited" class="doing-toolbar_button right" id="doing-show-sidebar" @click="toggleSidebar"><?php _e('Sidebar', 'quizmaker'); ?></button>
	<?php endif; ?>

	<div v-if="!is_submited" class="right"><span class="doing-pages">{{page}}/{{pages}}</span></div>
	<button v-if="!is_submited" class="doing-toolbar_button right page" @click="nextPage" v-bind:class="{is_last: is_last_page}"><?php _e('Next', 'quizmaker'); ?></button>
	<button v-if="!is_submited" class="doing-toolbar_button right page" @click="prevPage" v-bind:class="{is_first: (page == 1)}"><?php _e('Prev', 'quizmaker'); ?></button>
	
	<?php if(isset($settings['auto_save']) && $settings['auto_save']): ?>
	<div class="doing-toolbar_button right page" id="qm-auto-saving" data-progress="0">0</div>
	<?php endif; ?>

</div>

</div>