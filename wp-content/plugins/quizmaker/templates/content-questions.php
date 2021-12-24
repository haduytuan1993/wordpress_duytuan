<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php foreach($questions as $q_key => $q): 

	global $post;

	$post = $q;

	$ans_type 			=	$q->answer_type;

	$params 			=	qm_get_params_question( $q->ID, $question_params );

	$answer_params 		=	qm_get_post_meta( $q->ID, 'params_' . $ans_type );
	
	$timeout 			=   $q->timeout ? absint($q->timeout) : 0;

	$is_start			= 	($settings['is_timeout_answer'] == 1) && $timeout ? true : false;

?>
	
	<div class="question<?php echo $is_start ? ' is-starting': ''; ?>" data-type="<?php echo $ans_type; ?>" data-id="<?php echo $q->ID; ?>" id="<?php echo $q->ID; ?>">

		<div class="question_body_wrap">
			<div class="question_wrap_info">
				
				<?php if(isset($q_key) && isset($order)): ?>
				<div class="info_index"><span class="number"><?php echo $q_key + $order; ?></span></div>
				<?php endif; ?>

				<?php if( isset($settings['show_score']) && isset($q->score) && $settings['show_score'] == 1 ): ?>
				<div class="info_score">
					<span class="number"><?php echo qm_score($q->score); ?></span>
				</div>
				<?php endif; ?>
				
				<?php if( isset($settings['show_explanation']) && $settings['show_explanation'] == 1 && $q->explanation ): ?>
				<div class="info_explanation"><i class="material-icons">visibility</i></div>				
				<?php endif; ?>

				<?php if($is_start): ?>

				<div class="qm-timer">
					
					<div class="qm-timer-value" data-timeout="<?php echo $timeout; ?>"><?php echo $timeout; ?></div>

				</div>

				<?php endif; ?>

			</div>

			<div class="question_wrap_content">
				<div class="question_main_content">

					<?php if(isset($params['cat_name'])): ?>
					<div class="question_category"><?php echo $params['cat_name']; ?></div>
					<?php endif; ?>
					
					<?php if( isset($settings['show_question_title']) && $settings['show_question_title'] == 1 ): ?>
					<h5 class="question_title"><?php echo $q->post_title; ?></h5>
					<?php endif; ?>
					
					<div class="question_content">
						<?php echo apply_filters('the_content', $q->post_content); ?>

					</div>

					<div class="answers">
						<?php if($ans_type == 'single'): 
								$index =  0;
								$style =  isset($q->params_single['style']) ? $q->params_single['style'] : 'style-line';
								$columns =  isset($q->params_single['columns']) ? $q->params_single['columns'] : 1;
						?>
														
							<div class="<?php echo $style; ?>">
								<ul class="row">
								<?php foreach($q->answers as $skey => $ans): ?>
									<li class="col-xs-12 col-sm-12 col-md-12 col-lg-<?php echo 12/$columns; ?> group-box-radio" data-value="<?php echo $skey; ?>">
										<div class="inner">
											<div class="answers_info">
												<?php if($q->order_type): ?>
												<span class="order">
													<?php echo qm_order_content( $index + 1, $q->order_type ); ?>
												</span>
												<?php endif; ?>
										
												<div class="answers_input radio">
													<input type="radio" name="q[<?php echo $q->ID; ?>]" value="<?php echo $skey; ?>"/>
													<span class="box"></span>
												</div>
										
											</div>
										
											<div class="answers_content">

												<?php echo qm_image_tag($ans['image'], 'medium'); ?>

												<div class="txt">
													<?php echo apply_filters('the_content', $ans['content']); ?>
												</div>

											</div>
										</div>
									</li>
								<?php $index++; endforeach; ?>
								</ul>
							</div>

						<?php elseif($ans_type == 'multiple'): 
								$index =  0;
								$style =  isset($q->params_multiple['style']) ? $q->params_multiple['style'] : 'style-line';
								$columns =  isset($q->params_multiple['columns']) ? $q->params_multiple['columns'] : 1;
						?>
							<div class="<?php echo $style; ?>">
								<ul class="row">
								<?php foreach($q->answers as $mkey => $ans): ?>
									<li class="col-xs-12 col-sm-12 col-md-12 col-lg-<?php echo 12/$columns; ?> group-box-checkbox" data-value="<?php echo $mkey; ?>">
										<div class="inner">
											<div class="answers_info">
												<?php if($q->order_type): ?>
												<span class="order">
													<?php echo qm_order_content( $index + 1, $q->order_type ); ?>
												</span>
												<?php endif; ?>
											
												<div class="answers_input checkbox">
													<input type="checkbox" name="q[<?php echo $q->ID; ?>][]" value="<?php echo $mkey; ?>" class="checkbox_input"/>
													<span class="box"></span>
												</div>
											</div>
											<div class="answers_content">
											
												<?php echo qm_image_tag($ans['image'], 'medium'); ?>

												<div class="txt">
													<?php echo apply_filters('the_content', $ans['content']); ?>
												</div>
												
											</div>
										</div>
									</li>
								<?php $index++; endforeach; ?>
								</ul>
							</div>
							
						<?php elseif($ans_type == 'fill_blank'): ?>

							<div class="group-box-fill-blank">
								<textarea name="q[<?php echo $q->ID; ?>]"></textarea>
							</div>	

						<?php elseif($ans_type == 'drag_match'): ?>

							<?php $style =  isset($q->params_drag_match['style']) ? $q->params_drag_match['style'] : ''; ?>

							<div class="group-drag-match <?php echo $style; ?>">
								<div class="match-answers">
									<?php shuffle($q->answers); foreach($q->answers as $key => $ans): ?>
									<div class="match-answer" data-id="<?php echo $ans['id']; ?>">
										<div class="label">
											<?php echo $ans['answer']; ?>
										</div>
										<div class="value"></div>
										
										<input type="hidden" class="q" name="q[<?php echo $q->ID; ?>][<?php echo $ans['id']; ?>]"/>
									</div>
									<?php endforeach; ?>
								</div>
								
								<div class="match-questions">
								<?php shuffle($q->answers); foreach($q->answers as $key => $ans): ?>
									<div class="match-question-container">
										<div class="match-question" data-value="<?php echo $ans['value']; ?>"><?php echo $ans['content']; ?></div>
									</div>
								<?php endforeach; ?>
								</div>
							</div>
						
						<?php elseif($ans_type == 'group_match'): ?>

							<?php $style =  isset($q->params_group_match['style']) ? $q->params_group_match['style'] : 'style-left-to-right'; ?>
							
							<?php $groups = qm_questions_group_match_get_groups($q->answers); ?>

							
							<div class="group-group-match <?php echo $style; ?>">
								<?php if( $style == 'style-left-to-right' ): ?>
								<div class="connectedSortable match-items">
									<?php shuffle($q->answers); foreach($q->answers as $key => $ans): ?>
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

								<?php else: ?>

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

								<div class="connectedSortable match-items">
									<?php shuffle($q->answers); foreach($q->answers as $key => $ans): ?>
									<div class="match-answer" data-id="<?php echo $ans['id']; ?>">
										<div class="label">
											<?php echo $ans['item']; ?>
										</div>
										<div class="value"></div>
									</div>
									<?php endforeach; ?>

								</div>
								<?php endif; ?>
							</div>


						<?php elseif($ans_type == 'order'): 

							$style =  isset($q->params_order['style']) ? $q->params_order['style'] : 'style-line';
						?>
							<ul class="group-order <?php echo $style; ?>">
							<?php shuffle($q->answers); foreach($q->answers as $skey => $ans): ?>
								<li class="group-box-order" data-id="<?php echo $ans['id']; ?>">
									
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
									
									<input type="hidden" name="q[<?php echo $q->ID; ?>][]" value="<?php echo $ans['id']; ?>"/>
								</li>
							<?php endforeach; ?>
							</ul>

						<?php elseif($ans_type == 'guess_word'): ?>
							
							<div class="group-box-guess-word">
								<ul>
								<?php foreach($q->answers as $mkey => $ans): ?>
									<li class="word" data-value="<?php echo $mkey; ?>">
										<div class="inner">
											
											<?php if( $ans['is_correct'] == 1 ): ?>

											<input type="text" class="word-input show" name="q[<?php echo $q->ID; ?>][<?php echo $mkey; ?>]" value="<?php echo $ans['content']; ?>" readonly>

											<?php else: ?>

											<input type="text" class="word-input" name="q[<?php echo $q->ID; ?>][<?php echo $mkey; ?>]"/>

											<?php endif; ?>
										</div>
									</li>
								<?php endforeach; ?>
								</ul>
							</div>

						<?php elseif($ans_type == 'keywords'): ?>
							
							<div class="group-box-keywords">
								<textarea name="q[<?php echo $q->ID; ?>]"></textarea>
							</div>

						<?php endif; ?>
					</div>
					
					<?php if( $settings['show_explanation'] == 1 &&  $q->explanation): ?>
						<div class="explanation">
							<div class="explanation_content">
								<?php echo wpautop($q->explanation); ?>
							</div>
						</div>
					<?php endif; ?>

					<?php if(isset($settings['instant_answer']) && $settings['instant_answer'] == 1): ?>
					<a class="qm-button qm_btn_instant_answer"><?php _e('Instant Answer', 'quizmaker'); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<?php if($is_start): ?>

		<div class="start-intro">
			<i class="material-icons qm-icon">play_circle_filled</i>
		</div>

		<div class="timeout-intro">
			<i class="material-icons qm-icon">playlist_add_check</i>
		</div>

		<?php endif; ?>

</div>

<?php endforeach; ?>