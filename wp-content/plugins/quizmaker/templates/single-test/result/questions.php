<?php 

if ( ! defined( 'ABSPATH' ) ) { exit; }

$show_title_question	=	$settings['show_question_title'];

$is_share_result = get_option('quizmaker_is_share_result');

$face_app_id = get_option('quizmaker_facebook_app_id');

?>

<div id="quizmaker-result" class="result-test-stage">
	
	<h1><?php the_title(); ?></h1>
	
	<div class="result-panel">
		
		<?php if(isset($result['ranking']) && isset($ranking['message']) && $ranking['message']): ?>
			<h4><?php echo $ranking['message']; ?></h4>
		<?php endif; ?>
		

		<div>

			<?php if($settings['show_score']): ?>

			<div class="item">
				<div class="item-label"><?php _e('Corrects', 'quizmaker'); ?>: </div>
				<div class="item-value"><?php echo $num_corrects; ?>/<?php echo $total_questions; ?></div>
			</div>

			<div class="item">
				<div class="item-label"><?php _e('Percent', 'quizmaker'); ?>: </div>
				<div class="item-value"><?php echo $result['percent']; ?>%</div>
			</div>
			
			<div class="item">
				<div class="item-label"><?php _e('Score', 'quizmaker'); ?>: </div>
				<div class="item-value"><?php echo $result['score']; ?>/<?php echo $result['total_score']; ?></div>
			</div>
			
			<?php endif; ?>

			<div class="item">
				<div class="item-label"><?php _e('Duration', 'quizmaker'); ?>: </div>
				<div class="item-value"><?php echo $result['duration']; ?></div>
			</div>

			<div class="item">
				<div class="item-label"><?php _e('Date', 'quizmaker'); ?>: </div>
				<div class="item-value"><?php echo date_i18n( 'H:i:s d F Y', strtotime( $result['date_start'] ) ); ?></div>
			</div>
			
			<?php if(isset($result['ranking'])): ?>
			<div class="item">
				<div class="item-label"><?php _e('Rank', 'quizmaker'); ?>: </div>
				<div class="item-value"><?php echo $result['ranking']; ?></div>
			</div>

			<?php if(isset($ranking['certificate']) && $ranking['certificate']): ?>
			<div class="item item__certificate">
				<div class="item-label"><?php _e('Certificate', 'quizmaker'); ?>: </div>
				<div class="item-value"><a href="<?php echo qm_view_certificate_test_url($test_id, $result_id); ?>" class="item__certificate_link"><?php _e('Download', 'quizmaker'); ?></a></div>
			</div>
			<?php endif; ?>

			<?php endif; ?>
			
			<?php do_action( 'quizmaker_single_result_info', $test_id, $result_id ); ?>
		</div>

		<?php if($is_share_result == 'yes'): ?>
		<p style="margin-top: 24px;">
			<a href="#" data-title="<?php the_title(); ?>" data-link="<?php echo $test_link; ?>" data-image="<?php echo get_facebook_image_result( $result_id ); ?>" id="qm-share-result"><img src="<?php echo home_url('wp-content/plugins/quizmaker/assets/images/share-on-facebook.png'); ?>" width="150" height="auto"/></a>
		</p>
		<?php endif; ?>
	</div>

	<?php do_action( 'quizmaker_single_result_after_info', $test_id, $result_id ); ?>

<?php if($settings['show_result']): ?>

	<div class="stage-questions">
<?php if($page_question): ?>

	<?php foreach($page_question as $page => $questions_1): ?>
	
	<div class="questions qm-page qm-page-<?php echo $page + 1; ?> show_answers_<?php echo $settings['result_is_answers']; ?>" v-bind:class="{active: page == <?php echo $page; ?>}">
		<?php foreach($questions_1 as $q_key => $q): 
			$ans_type = $q->answer_type; 

			$q_is_correct = $q->is_correct;

			if(!$settings['result_is_answers']){

				$q_is_correct = 2;
			}
		?>
			
			<div class="question q_correct_<?php echo $q_is_correct; ?>" data-id="<?php echo $q->ID; ?>">
				<div class="question_wrap_info">
					<div class="info_index"><span class="number"><?php echo $q->order; ?></span></div>

					<?php if( isset($settings['show_score']) && isset($q->score) && $settings['show_score'] == 1 ): ?>
					<div class="info_score <?php echo $q->is_correct ? 'right' : 'wrong'; ?>">
						<span class="number">
							<?php echo apply_filters( 'quizmaker_show_result_loop_score', $q->score, $q ); ?>	
						</span>
					</div>
					<?php endif; ?>
					
					<?php if( isset($settings['show_explanation']) && $settings['show_explanation'] > 1 && $q->explanation ): ?>
					<div class="info_explanation"><i class="material-icons">visibility</i></div>
					<?php endif; ?>
					
					<?php if( isset($settings['is_question_report']) && $settings['is_question_report'] ): ?>
					<div class="report" @click="showReport(<?php echo $q->ID; ?>)"><i class="material-icons">report_problem</i></div>
					<?php endif; ?>

				</div>
				<div class="question_wrap_content">

					<div class="category">
						<?php echo qm_get_question_params( $q->ID, $question_params, 'cat_name'); ?>
					</div>

					<?php if($show_title_question): ?>
					<h5 class="question_title"><?php echo $q->post_title; ?></h5>
					<?php endif; ?>
			
					<div class="question_content">

						<?php echo apply_filters('the_content', $q->post_content); ?>

						<?php if( isset($settings['show_explanation']) && $settings['show_explanation'] > 0 && $q->explanation): ?>
						<div class="explanation show">
							<div class="explanation_content">
								<?php echo wpautop($q->explanation); ?>
							</div>
						</div>
						<?php endif; ?>

					</div>
					<div class="answers">
						
						<?php if($ans_type == 'single'): 

							$style =  isset($q->params_single['style']) ? $q->params_single['style'] : 'style-line';
							$columns =  isset($q->params_single['columns']) ? $q->params_single['columns'] : 1;

							$index = 0; ?>

							<div class="<?php echo $style; ?>">
							
							<ul class="row">

							<?php foreach($q->answers as $key => $ans): 

								$ans['user_check'] = isset($ans['user_check']) ? $ans['user_check'] : 0;

								$ans_is_correct = isset($ans['is_correct']) && $ans['is_correct'] > 0 ? 1 : 0;

								$class_ans_result = '';

								if( $settings['result_is_answers'] && $ans_is_correct ) {

									$class_ans_result = 'ans_right';
								}

								if( $settings['result_is_answers'] && !$ans_is_correct && $ans['user_check'] ) {

									$class_ans_result = 'ans_wrong';
								}


								if( $settings['result_is_answers'] && $ans['user_check'] && !$ans_is_correct ) {

									$class_ans_result = 'ans_wrong';
								}

								if( $settings['result_is_answers'] && $ans['user_check'] && $ans_is_correct ) {

									$class_ans_result = 'ans_right';

								}

							?>
								<li class="col-xs-12 col-sm-12 col-md-12 col-lg-<?php echo 12/$columns; ?> group-box-radio <?php echo $class_ans_result; ?>">
									<div class="inner">
									<div class="answers_info">
										<span class="order">
										<?php if($q->order_type): ?>
											<?php echo qm_order_content( $index + 1, $q->order_type ); ?>
										<?php endif; ?>
										</span>
									
										<div class="answers_input radio a_correct_<?php echo isset($ans['is_correct']) && $ans['is_correct'] > 0 ? 1 : 0; ?>">
											<input type="radio" name="q[<?php echo $q->ID; ?>]" value="<?php echo $key; ?>" <?php checked(1, $ans['user_check'], true); ?> disabled/>
											<span class="box"></span>
										</div>
									</div>
								
									<div class="answers_content">
									
										<?php if( isset($ans['order_content']) && $ans['order_content']): ?>

											<?php echo $ans['order_content']; ?>
										
										<?php endif; ?>
										
										<?php echo qm_image_tag($ans['image'], 'medium'); ?>
										
										<div class="txt"><?php echo apply_filters('the_content', $ans['content']); ?></div>
									</div>
									</div>
								</li>
							<?php $index++; endforeach; ?>
							</ul>
							</div>
						<?php elseif($ans_type == 'multiple'): 

							$style =  isset($q->params_multiple['style']) ? $q->params_multiple['style'] : 'style-line';
							$columns =  isset($q->params_multiple['columns']) ? $q->params_multiple['columns'] : 1;

						?>
							<div class="<?php echo $style; ?>">

							<ul class="row">
							<?php foreach($q->answers as $key => $ans): 

								$ans['user_check'] = isset($ans['user_check']) ? $ans['user_check'] : 0;

								$ans_is_correct = isset($ans['is_correct']) && $ans['is_correct'] > 0 ? 1 : 0;

								$class_ans_result = '';

								if( $ans_is_correct && $ans['user_check'] ) {

									$class_ans_result = 'ans_right';
								}

								if( !$ans_is_correct && $ans['user_check'] ) {

									$class_ans_result = 'ans_wrong';
								}


								if( $settings['result_is_answers'] && $ans['user_check'] && !$ans_is_correct ) {

									$class_ans_result = 'ans_wrong';
								}

								if( $settings['result_is_answers'] && $ans['user_check'] && $ans_is_correct ) {

									$class_ans_result = 'ans_right';

								}

							?>
								<li class="col-xs-12 col-sm-12 col-md-12 col-lg-<?php echo 12/$columns; ?> group-box-checkbox <?php echo $class_ans_result; ?>">
									<div class="inner">
									<div class="answers_info">
										<span class="order">
										<?php if($q->order_type): ?>
											<?php echo qm_order_content( $key + 1, $q->order_type ); ?>
										<?php endif; ?>
										</span>
										<div class="answers_input checkbox a_correct_<?php echo isset($ans['is_correct']) && $ans['is_correct'] > 0 ? 1 : 0; ?>">
											<input type="checkbox" name="q[<?php echo $q->ID; ?>][]" value="<?php echo $key; ?>" <?php checked(1, $ans['user_check'], true); ?> disabled/>
											<span class="box"></span>
										</div>
									</div>
								
									<div class="answers_content">
										<?php if( isset($ans['order_content']) && $ans['order_content']): ?>
											<?php echo $ans['order_content']; ?>	
										<?php endif; ?>
										
										<?php if(isset($ans['image'])): ?>
										<?php echo qm_image_tag($ans['image'], 'medium'); ?>
										<?php endif; ?>
										
										<div class="txt"><?php echo apply_filters('the_content', $ans['content']); ?></div>
									</div>
									</div>
								</li>
							<?php endforeach; ?>
							</ul>
							</div>

						<?php elseif($ans_type == 'fill_blank'): ?>
						
							<div class="group-box-fill-blank">
								<div class="alert alert-secondary group-box-fill-blank-result"><?php echo $q->u_answers; ?></div>
							</div>
							
							<?php if($settings['result_is_answers']): ?>
							<div class="group-box-fill-blank">
								<div class="alert alert-success group-box-fill-blank-result"><?php echo $q->answers; ?></div>
							</div>
							<?php endif; ?>

						<?php elseif($ans_type == 'drag_match'): ?>

							<?php $style =  isset($q->params_drag_match['style']) ? $q->params_drag_match['style'] : ''; ?>
							
							<div class="group-drag-match <?php echo $style; ?>">
								<div class="match-answers match-answer__result match-answer__result__show-anwsers">
								<?php foreach($q->answers as $key => $ans): ?>
									<div class="match-answer">
										
										<?php if($settings['result_is_answers']): ?>
										<div class="value value__correct-answers">
											<div class="match-question" data-value="<?php echo $ans['value']; ?>"><?php echo $ans['content']; ?></div>
										</div>
										<?php endif; ?>
										
										<div class="label">
											<?php echo $ans['answer']; ?>
										</div>
										
										<?php foreach($q->answers as $u_key => $u_ans): ?>
											<?php if(isset($q->u_answers[$key]) && ($q->u_answers[$key] == $u_ans['value'])): ?>
										<div class="value value__user-answers">
											<div class="match-question"><?php echo $u_ans['content']; ?></div>
										</div>
											<?php endif; ?>
										<?php endforeach; ?>
										
									</div>
								<?php endforeach; ?>
								</div>
							</div>

						<?php elseif($ans_type == 'group_match'): ?>
							<?php $groups = qm_questions_group_match_get_groups($q->answers); ?>

							<div class="group-group-match">
								
								<?php if(qm_questions_group_match_is_play($q->user_answer)): ?>
								<div class="match-groups user-match">

									<?php if($groups): ?>
									<?php foreach($groups as $group_name => $group_label): ?>
									<h5><?php echo $group_label ?></h5>
									<div class="match-group">
										
										<?php foreach($q->user_answer as $u_group => $u_answer): ?>

											<?php if( $group_name == sanitize_title($u_group) ): ?>

												<?php $u_match = explode(',', $u_answer); ?>

												<?php foreach( $u_match as $um ): ?>
													
													<?php foreach($q->answers as $answer): ?>
														<?php if( $um == $answer['id']): ?>
												<div class="match-answer"><?php echo $answer['item']; ?></div>
														<?php endif; ?>
													<?php endforeach; ?>
												<?php endforeach; ?>
											<?php endif ?>

										<?php endforeach; ?>

									</div>
									<?php endforeach; ?>
									<?php endif; ?>

								</div>
								<?php else: ?>

									<?php if(!$settings['result_is_answers']): ?>
									
									<div class="group-group-match">

										<div class="match-items">
											<?php foreach($q->answers as $key => $ans): ?>
											<div class="match-answer" data-id="<?php echo $ans['id']; ?>">
												<div class="label">
													<?php echo $ans['item']; ?>
												</div>
												<div class="value"></div>
											</div>
											<?php endforeach; ?>
										</div>

										<div class="match-groups">

											<?php if($groups): ?>
											<?php foreach($groups as $group_name => $group_label): ?>
											<h5><?php echo $group_label ?></h5>
											<div class="connectedSortable match-group">
												
												<input type="hidden" class="q" name="q[<?php echo $q->ID; ?>][<?php echo $group_name; ?>]"/>

											</div>
											<?php endforeach; ?>
											<?php endif; ?>

										</div>
										
									</div>

									<?php endif; ?>

								<?php endif; ?>
								
								<?php if($settings['result_is_answers']): ?>

								<div class="match-groups system-match">

									<?php if($groups): ?>
									<?php foreach($groups as $group_name => $group_label): ?>
									<h5><?php echo $group_label ?></h5>
									<div class="match-group">
										
										<?php foreach($q->answers as $answer): ?>
											<?php if( $group_name == sanitize_title($answer['group']) ): ?>
												<div class="match-answer"><?php echo $answer['item']; ?></div>
											<?php endif ?>
										<?php endforeach; ?>

									</div>
									<?php endforeach; ?>
									<?php endif; ?>

								</div>

								<?php else: ?>

								<?php endif; ?>
			

							</div>
							
							

						<?php elseif($ans_type == 'order'): 

							$style =  isset($q->params_order['style']) ? $q->params_order['style'] : 'style-line';
						?>
															
							<ul class="group-order <?php echo $style; ?>">
								
							<?php foreach($q->answers as $skey => $ans): ?>
								<li class="group-box-order">
									<i class="material-icons">swap_vert</i>
									<?php if($q->order_type): ?>
									<div class="qm-pull-left">
										<span class="order">
										
											<?php echo qm_order_content( $skey + 1, $q->order_type ); ?>
										
										</span>
									</div>
									<?php endif; ?>
									
									<div class="answers_content">
										<?php echo qm_image_tag($ans['image'], 'medium'); ?>
										<?php echo $ans['content']; ?>
									</div>
									
								</li>
							<?php endforeach; ?>
							</ul>
						
						<?php elseif($ans_type == 'guess_word'): ?>
							
							<div class="group-box-guess-word">
								<ul>
								<?php foreach($q->answers as $mkey => $ans): ?>
									<li class="word" data-value="<?php echo $mkey; ?>">
										<div class="inner">
											
											<input type="text" class="word-input show" name="q[<?php echo $q->ID; ?>][<?php echo $mkey; ?>]" value="<?php echo $ans; ?>" readonly>

										</div>
									</li>
								<?php endforeach; ?>
								</ul>
							</div>

						<?php elseif($ans_type == 'keywords'): ?>
							
							<div class="group-box-keywords">
							<div class="user-answered"><?php echo $q->user_answer; ?></div>
							<?php if( $settings['result_is_answers'] ): ?>
							
								<div class="group-box-keywords-result">
									<?php foreach($q->answers as $kans):?>
										<?php echo '<span>' . $kans['content'] . '</span>'; ?>
									<?php endforeach ?>
								</div>
							
							<?php endif; ?>
							</div>

						<?php endif; ?>
					</div>
					
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	
	<?php endforeach; ?>
<?php endif; ?>
	</div>

<?php if($page_question): ?>
<div class="stage-pagination">
	<ul>
	<?php foreach($page_question as $index => $q): ?>
		<li @click="page = <?php echo $index; ?>" v-bind:class="{active:page==<?php echo $index; ?>}"><span><?php echo $index + 1; ?></span></li>
	<?php endforeach; ?>
	</ul>
</div>
<?php endif; ?>

<div class="result-actions">
		<a href="<?php echo $test_link; ?>" class="qm-button"><?php _e('Back to the test', 'quizmaker'); ?></a>
</div>

<?php if( isset($settings['is_question_report']) && $settings['is_question_report'] ): ?>
<div class="result-report" v-if="id_report > 0">
	<div class="container">
		
		<div class="inner">
			
			<h3 class="heading"><?php _e('Question Report', 'quizmaker'); ?></h3>

			<div class="content">

				<form action="" method="POST" v-show="is_report_status == 0">

					<div class="input-item">
						<textarea id="report-question-email" v-model.trim="question_report_message" placeholder="<?php _e('Message', 'quizmaker'); ?>"></textarea>
					</div>

					<div class="input-actions">
						
						<button class="qm-button primary" id="submit-question-report" v-on:click.stop.prevent="submitReport"><?php _e('Submit', 'quizmaker'); ?></button>

						<button class="qm-button" id="cancel-question-report" v-on:click.stop.prevent="closeReport"><?php _e('Cancel', 'quizmaker'); ?></button>

					</div>

				</form>

			</div>

		</div>

	</div>
</div>
<?php endif; ?>

<?php endif; ?>

</div>

<?php if($is_share_result == 'yes' && $face_app_id): ?>
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId            : '<?php echo $face_app_id; ?>',
      autoLogAppEvents : true,
      xfbml            : true,
      version          : 'v2.10'
    });
    FB.AppEvents.logPageView();
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "https://connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>
<?php endif; ?>