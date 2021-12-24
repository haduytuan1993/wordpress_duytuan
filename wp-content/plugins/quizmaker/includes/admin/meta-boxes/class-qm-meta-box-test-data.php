<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class QM_Meta_Box_Test_Data {
	
	public static function output( $post ) {
		global $post, $thepostid;

		$fixed_question_ids 	=	get_post_meta( $post->ID, '_fixed_questions', true);
		
		$fixed_questions		=	qm_question_get_fixed_items($post->ID);
		$random_questions		=	qm_question_get_random_items($post->ID);
		
		$settings				=	qm_test_get_settings($post->ID);

		$publish_for_options	=	qm_get_all_publish_for();

		$attempt				=	qm_get_post_meta($post->ID, 'attempt');
		$duration				=	qm_get_post_meta($post->ID, 'duration');
		$publish_for 			=	qm_get_post_meta($post->ID, 'publish_for');
		$type_testing 			=	qm_get_post_meta($post->ID, 'type_testing');
		$user_score 			=	qm_get_post_meta($post->ID, 'user_score');
		
		$attempt				=	$attempt ? $attempt : 0;
		$duration				=	$duration ? $duration : 0;
		$publish_for			=	$publish_for ? $publish_for : 0;
		$user_score				=	$user_score ? $user_score : 0;

		$adaptive_times			=	$settings['adaptive_times'] ? $settings['adaptive_times'] : 1;
		$adaptive_max_round		=	$settings['adaptive_max_round'] > 0 ? $settings['adaptive_max_round'] : 0;
		
		$ranking				=	$settings['ranking'];

		$show_explanation		=	isset($settings['show_explanation']) ? $settings['show_explanation'] : 0;
		
		$assign_users			=	$settings['assigned_users'];
		$assign_groups			=	$settings['assigned_groups'];
		
		$category_questions		=	qm_question_get_categories($post->ID);
		
		$group_assign_users		=	array();
		$group_fixed_questions	=	array();
		
		if($assign_users){
			$group_assign_users = array_chunk($assign_users, 10);
		}
		
		if($fixed_questions){
			$group_fixed_questions = array_chunk($fixed_questions, 10);
		}
		
?>
	
	<div class="qm-panel-tabs">
		<ul class="qm-tabs">
			<li data-tab="fixed_question" class="default"><a href="#"><span class="qm-tab-icon ion-pin"><?php _e('Fixed Questions', 'quizmaker'); ?></span></a></li>
			<li data-tab="random_question"><a href="#"><span class="qm-tab-icon ion-shuffle"><?php _e('Random Questions', 'quizmaker'); ?></span></a></li>
			<li data-tab="ranking"><a href="#"><span class="qm-tab-icon ion-trophy"><?php _e('Ranking', 'quizmaker'); ?></span></a></li>
			<li data-tab="assign_users"><a href="#"><span class="qm-tab-icon ion-person"><?php _e('Users', 'quizmaker'); ?></span></a></li>
			<li data-tab="assign_groups"><a href="#"><span class="qm-tab-icon ion-android-people"><?php _e('Groups', 'quizmaker'); ?></span></a></li>
			<li data-tab="settings"><a href="#"><span class="qm-tab-icon ion-ios-gear"><?php _e('Settings', 'quizmaker'); ?></span></a></li>
		</ul>
		
		<!-- Fixed question tab content -->
		<div class="qm-panel-tabs fixed_question_panel_tab fixed_question_data">

			<div class="aws-ui-table-data qm-list-fixed-questions" 
			data-ajax-url="<?php echo admin_url('admin-ajax.php'); ?>" 
		data-ajax-action="quizmaker_load_html_fixed_questions" 
		data-ajax-security="<?php echo wp_create_nonce( "quizmaker_load_html_fixed_questions" ); ?>" 
		data-ajax-params="<?php echo htmlspecialchars(json_encode( array('test_id' => $post->ID, 'exclude' => $fixed_question_ids ), JSON_FORCE_OBJECT )); ?>" 
		data-ajax-page="1">

			<table cellpadding="0" cellspacing="0" class="qm-table-meta-box">
				<thead>
					<tr>
						<th class="qm-a-c qm-s-2 checkall"><input type="checkbox" class="aws-ui-table-data-checkall"/></th>
						<th class="qm-a-l name"><?php _e('Name', 'quizmaker'); ?></th>
					</tr>
				</thead>
				<tbody></tbody>
				<tfoot>
					<tr>
						<th colspan="9">
							<div class="pull-right">
								<div class="aws-ui-pagination">
									<div class="ion-ios-arrow-left page-prev "></div>
									<div class="pages"></div>
									<div class="ion-ios-arrow-right page-next"></div>
								</div>
							</div>
						</th>
					</tr>
				</tfoot>
			</table>

			</div>
			<div class="clear form-add-fixed_questions" style="padding: 10px 10px;">
				<select class="qm-select2" id="input-add-fixed_questions" style="width: 100%;" data-action="quizmaker_json_search_questions" data-security="<?php echo wp_create_nonce( "search_questions_nonce" ); ?>" data-others="<?php echo htmlspecialchars(json_encode(array('pid' => $post->ID)), ENT_QUOTES, 'UTF-8'); ?>" multiple="multiple" data-placeholder="<?php _e('Search Questions', 'quizmaker'); ?>" data-limit="10"></select>
			</div>
			
			<div class="clear add-question-action add-question-action__fixed">
				<div class="qm-pull-left">
					<button class="button button-primary" id="add-fixed-question"><?php _e('Save questions', 'quizmaker'); ?></button>
				</div>
				<div class="qm-pull-right">
					<button class="button" id="order-fixed-question"><?php _e('Order Questions', 'quizmaker'); ?></button>
					<button class="button" id="remove-fixed-question"><?php _e('Remove Question', 'quizmaker'); ?></button>
				</div>
				<span class="spinner spinner-remove-fixed-question"></span>
			</div>
			
		</div>
		
		<!-- Random question tab content -->
		<div class="qm-panel-tabs random_question_panel_tab random_question_data">
			
			<div class="random-questions-options">
				<div class="groups-field">
					<div class="group-field">
						<label><?php _e('Type Random', 'quizmaker'); ?></label>
						
						<select name="random_question[type]" id="random_question_type">
							<optgroup label="Type of Random">
								<option value="nonce" <?php selected($random_questions['type'], 'nonce'); ?>><?php _e('No Random', 'quizmaker'); ?></option>
								<option value="selected" <?php selected($random_questions['type'], 'selected'); ?>><?php _e('Selected Category', 'quizmaker'); ?></option>
								<option value="per" <?php selected($random_questions['type'], 'per'); ?>><?php _e('Per Category', 'quizmaker'); ?></option>
							</optgroup>
						</select>
					</div>
				</div>
			</div>
			
			<div id="random-questions-1">
				<div class="random-questions-options">
					<div class="groups-field">
						<p class="group-field">
							<label><?php _e('Order', 'quizmaker'); ?></label>
							<select name="random_question[selected][order]">
								<optgroup label="Orders">
									<option value="mixed" <?php selected($random_questions['selected']['order'], 'mixed'); ?>>
										<?php _e('Mixed', 'quizmaker'); ?>
									</option>
									<option value="custom_order" <?php selected($random_questions['selected']['order'], 'custom_order'); ?>>
										<?php _e('Custom Order', 'quizmaker'); ?>
									</option>
								</optgroup>
							</select>
						</p>
						<p class="group-field">
							<label><?php _e('Position', 'quizmaker'); ?></label>
							<select name="random_question[selected][position]">
								<optgroup label="Position">
									<option value="mixed_fquestions" <?php selected($random_questions['selected']['position'], 'mixed_fquestions'); ?>><?php _e('Mixed with fixed questions', 'quizmaker'); ?></option>
									<option value="top_fquestions" <?php selected($random_questions['selected']['position'], 'top_fquestions'); ?>><?php _e('Top fixed questions', 'quizmaker'); ?></option>
									<option value="bottom_fquestions" <?php selected($random_questions['selected']['position'], 'bottom_fquestions'); ?>><?php _e('Bottom fixed questions', 'quizmaker'); ?></option>
								</optgroup>
							</select>
						</p>
					</div>
				</div>
				
				<table cellpadding="0" cellspacing="0" class="qm-list-random-questions">
					<thead>
						<tr>
							<th class="qm-a-c qm-s-2 checkall"><input type="checkbox" class="qm-checkall" value="random_question_selected_categories"/></th>
							<th class="qm-a-l category"><?php _e('Category', 'quizmaker'); ?></th>
							<th class="qm-a-c qm-s-1 total"><?php _e('Total', 'quizmaker'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($category_questions as $cat): ?>
						<tr>
							<td class="qm-a-c"><input type="checkbox" name="random_question[selected][categories][<?php echo $cat->term_id; ?>]" value="<?php echo $cat->term_id; ?>" <?php isset($random_questions['selected']['categories'][$cat->term_id]) ? checked($random_questions['selected']['categories'][$cat->term_id], $cat->term_id) : ''; ?> class="random_question_selected_categories"/></td>
							<td><a href="<?php echo admin_url('term.php?taxonomy=question_cat&tag_ID=' . $cat->term_id . '&post_type=question'); ?>"><?php echo $cat->name; ?></a></td>
							<td class="qm-a-c"><?php echo $cat->total_questions; ?></td>
						</tr>
						<?php endforeach; ?>
						<tr>
							<td colspan="3">
								<div class="qm-pull-right">
									<div class="total-question">
										<label><?php _e('Total Question', 'quizmaker'); ?></label>
										<input class="input qm-s-1 qm-a-c" type="text" name="random_question[selected][total]" value="<?php echo $random_questions['selected']['total'] ?>"/>
									</div>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
				
			</div>
			
			<div id="random-questions-2">
				<div class="random-questions-options">
					<div class="groups-field">
						<p class="group-field">
							<label><?php _e('Order', 'quizmaker'); ?></label>
							<select name="random_question[per][order]">
								<optgroup label="Order">
									<option value="mixed" <?php selected($random_questions['per']['order'], 'mixed'); ?>><?php _e('Mixed', 'quizmaker'); ?></option>
									<option value="by_category" <?php selected($random_questions['per']['order'], 'by_category'); ?>><?php _e('Group by Category', 'quizmaker'); ?></option>
								</optgroup>
							</select>
						</p>
						<p class="group-field">
							<label><?php _e('Position', 'quizmaker'); ?></label>
							<select name="random_question[per][position]">
								<optgroup label="Position">
									<option value="mixed_fquestions" <?php selected($random_questions['per']['position'], 'mixed_fquestions'); ?>><?php _e('Mixed with fixed questions', 'quizmaker'); ?></option>
									<option value="top_fquestions" <?php selected($random_questions['per']['position'], 'top_fquestions'); ?>><?php _e('Top fixed questions', 'quizmaker'); ?></option>
									<option value="bottom_fquestions" <?php selected($random_questions['per']['position'], 'bottom_fquestions'); ?>><?php _e('Bottom fixed questions', 'quizmaker'); ?></option>
								</optgroup>
							</select>
						</p>
					</div>
				</div>
				
				<table cellpadding="0" cellspacing="0" class="qm-list-random-questions">
					<thead>
						<tr>
							<th class="qm-a-c qm-s-1 total"><?php _e('Total', 'quizmaker'); ?></th>
							<th class="qm-a-l category"><?php _e('Category', 'quizmaker'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($category_questions as $cat): ?>
						<tr>
							<td class="qm-a-c"><input type="text" name="random_question[per][categories][<?php echo $cat->term_id; ?>]" value="<?php echo isset($random_questions['per']['categories'][$cat->term_id]) ? $random_questions['per']['categories'][$cat->term_id] : 10; ?>" class="qm-a-c qm-s-1"/></td>
							<td><a href="<?php echo admin_url('term.php?taxonomy=question_cat&tag_ID=' . $cat->term_id . '&post_type=question'); ?>"><?php echo $cat->name; ?></a></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			
		</div>
		
		<!-- Ranking tab content -->
		<div class="qm-panel-tabs ranking_panel_tab ranking_data">
			<table cellpadding="0" cellspacing="0" class="qm-table-meta-box" id="qm-table-ranking_data">
				<thead>
					<tr>
						<th class="qm-a-c qm-s-3"><?php _e('Min Score', 'quizmaker'); ?> (%)</th>
						<th class="qm-a-c qm-s-3"><?php _e('Max Score', 'quizmaker'); ?> (%)</th>
						<th class="qm-a-l qm-s-3"><?php _e('Certificate', 'quizmaker'); ?></th>
						<th class="qm-a-l name"><?php _e('Name', 'quizmaker'); ?></th>
						<th class="qm-s-3"></th>
					</tr>
				</thead>
				<tbody class="tbody_data">
					<?php if($ranking): ?>
					
					<?php foreach( $ranking as $index => $rank ): ?>
							
						<tr class="row_data" data-id="<?php echo $rank['id']; ?>">
							<td class="qm-a-c min_data"><?php echo $rank['min']; ?></td>
							<td class="qm-a-c max_data"><?php echo $rank['max']; ?></td>
							<td class="qm-a-l certificate_data"><?php qm_admin_ranking_link($rank['certificate']); ?></td>
							<td class="name_data"><?php echo $rank['name']; ?></td>
							<td class="qm-a-c">
								<span class="edit_ranking" data-id="<?php echo $rank['id']; ?>"><?php _e('Edit', 'quizmaker'); ?></span>
								<span class="remove_ranking" data-id="<?php echo $rank['id']; ?>"><?php _e('Remove', 'quizmaker'); ?></span>
							</td>
						</tr>
						<tr class="qm-table-group-input">
							<td colspan="5">
								<div class="qm-form-groups">
									<div class="qm-group-input-label">
										<label><?php _e('Min Score', 'quizmaker'); ?> (%)</label>
									
										<div class="g-input">
											<input type="text" name="qm_ranking_<?php echo $rank['id']; ?>_min" value="<?php echo $rank['min']; ?>"/>
										</div>
									</div>
									
									<div class="qm-group-input-label">
										<label><?php _e('Max Score', 'quizmaker'); ?> (%)</label>
									
										<div class="g-input">
											<input type="text" name="qm_ranking_<?php echo $rank['id']; ?>_max" value="<?php echo $rank['max']; ?>"/>
										</div>
									</div>
									
									<div class="qm-group-input-label">
										<label><?php _e('Name', 'quizmaker'); ?></label>
									
										<div class="g-input">
											<input type="text" name="qm_ranking_<?php echo $rank['id']; ?>_name" value="<?php echo $rank['name']; ?>"/>
										</div>
									</div>
								
									<div class="qm-group-input-label">
										<label><?php _e('Certificate', 'quizmaker') ?></label>
										<div class="g-input">
										<?php qm_dropdown_posts(array('post_type' => 'certificate'), 'name = "qm_ranking_' . $rank['id'] . '_certificate"', $rank['certificate']); ?>
										</div>
									</div>

									<div class="qm-group-input-label">
										<label><?php _e('Message', 'quizmaker'); ?></label>
									
										<div class="g-input">
											<input type="text" name="qm_ranking_<?php echo $rank['id']; ?>_message" value="<?php echo isset($rank['message']) ? $rank['message'] : ''; ?>"/>
										</div>
									</div>

									<?php do_action( 'quizmaker_test_data_form_edit_ranking', $rank ); ?>
									
									<div class="qm-group-input-action">
										<button class="button close_ranking" data-id="<?php echo $rank['id']; ?>">
											<?php _e('Cancel', 'quizmaker'); ?></button>
										<button class="button save_ranking" data-id="<?php echo $rank['id']; ?>">
											<?php _e('Update Ranking', 'quizmaker'); ?></button>
									</div>
								</div>
							</td>
						</tr>
						
					<?php endforeach; ?>
						
					<?php else: ?>
						<tr class="ranking_nodata"><td class="qm-a-c" colspan="4"><?php _e('No Data', 'quizmaker'); ?></td></tr>
					<?php endif; ?>
				</tbody>
				
				<tbody class="tbody_add">
					
					<tr class="row_data dump">
						<td class="qm-a-c min_data"></td>
						<td class="qm-a-c max_data"></td>
						<td class="qm-a-l certificate_data"></td>
						<td class="name_data"></td>
						<td class="qm-a-c">
							<span class="edit_ranking"><?php _e('Edit', 'quizmaker'); ?></span>
							<span class="remove_ranking"><?php _e('Remove', 'quizmaker'); ?></span>
						</td>
					</tr>
					
					<tr class="qm-table-group-input-dump">
						
						<td colspan="5">
							
							<div class="qm-form-groups">
								<div class="qm-group-input-label">
									<label><?php _e('Min Score', 'quizmaker'); ?> (%)</label>
								
									<div class="g-input">
										<input type="text" name="qm_ranking_min" value=""/>
									</div>
								</div>
								
								<div class="qm-group-input-label">
									<label><?php _e('Max Score', 'quizmaker'); ?> (%)</label>
								
									<div class="g-input">
										<input type="text" name="qm_ranking_max" value=""/>
									</div>
								</div>
								
								<div class="qm-group-input-label">
									<label><?php _e('Name', 'quizmaker'); ?></label>
								
									<div class="g-input">
										<input type="text" name="qm_ranking_name" value=""/>
									</div>
								</div>
								
								<div class="qm-group-input-label">
									<label><?php _e('Certificate', 'quizmaker'); ?></label>
								
									<div class="g-input">
										
										<?php qm_dropdown_posts(array('post_type' => 'certificate'), 'name="qm_ranking_certificate"'); ?>
										
									</div>
								</div>

								<div class="qm-group-input-label">
										<label><?php _e('Message', 'quizmaker'); ?></label>
									
										<div class="g-input">
											<input type="text" name="qm_ranking_message" value=""/>
										</div>
								</div>

								<?php do_action( 'quizmaker_test_data_form_add_ranking' ); ?>
								
								<div class="qm-group-input-action">
									<button class="button add_ranking"><?php _e('Save', 'quizmaker'); ?></button>
									<button class="button close_add_ranking"><?php _e('Cancel', 'quizmaker'); ?></button>
							
									<button class="button button-primary save_ranking hide"><?php _e('Save', 'quizmaker'); ?></button>
									<span class="edit_ranking hide" data-id="<?php echo isset($rank['id']) ? $rank['id'] : 0; ?>"><?php _e('Edit', 'quizmaker'); ?></span>
									<span class="remove_ranking hide" data-id="<?php echo isset($rank['id']) ? $rank['id'] : 0; ?>"><?php _e('Remove', 'quizmaker'); ?></span>
								</div>
								
							</div>
						</td>
						
					</tr>
				</tbody>
			</table>
			
			<div class="clear actions ranking-action">
				<div class="qm-pull-left">
					
					<button class="button button-primary" id="add-ranking"><?php _e('Add Ranking', 'quizmaker'); ?></button>
					<span class="spinner spinner-ranking"></span>
				</div>
			</div>
		</div>
		
		<!-- Assign users tab content -->
		<div class="qm-panel-tabs assign_users_panel_tab assign_users_data">
		
			<div class="aws-ui-table-data qm-list-assign-users" 
			data-ajax-url="<?php echo admin_url('admin-ajax.php'); ?>" 
		data-ajax-action="quizmaker_load_html_assigned_users" 
		data-ajax-security="<?php echo wp_create_nonce( "quizmaker_load_html_assigned_users" ); ?>" 
		data-ajax-params="<?php echo htmlspecialchars(json_encode( array('test_id' => $post->ID ), JSON_FORCE_OBJECT )); ?>" data-ajax-page="1">

				<table cellpadding="0" cellspacing="0" class="qm-table-meta-box">

					<thead>
						<tr>
							<th class="qm-a-c qm-s-2 checkall"><input type="checkbox" class="aws-ui-table-data-checkall"/></th>
							<th class="qm-a-l name"><?php _e('Name', 'quizmaker'); ?></th>
						</tr>
					</thead>

					<tbody></tbody>
					
					<tfoot>
						<tr>
							<th colspan="9">
								<div class="pull-right">
									<div class="aws-ui-pagination">
										<div class="ion-ios-arrow-left page-prev "></div>
										<div class="pages"></div>
										<div class="ion-ios-arrow-right page-next"></div>
									</div>
								</div>
							</th>
						</tr>
					</tfoot>
					
				</table>
				
			</div>

			<div class="clear form-assign-users" style="padding: 10px 10px;">
				<select class="qm-select2" id="input-assign-users" style="width: 100%;" data-action="quizmaker_json_search_users" data-security="<?php echo wp_create_nonce( "search_users_nonce" ); ?>" data-others="<?php echo htmlspecialchars(json_encode(array('pid' => $post->ID)), ENT_QUOTES, 'UTF-8'); ?>" multiple="multiple" data-placeholder="<?php _e('Search Users', 'quizmaker'); ?>" data-limit="10"></select>
			</div>

			<div class="clear actions assign-users-action">
				<div class="qm-pull-left">
					<button class="button button-primary" id="lightbox-assign-users"><?php _e('Save users', 'quizmaker'); ?></button>
					<button class="button" id="email-assign-users"><?php _e('Email Inform', 'quizmaker'); ?></button>
					<span class="spinner spinner-assign-users"></span>
				</div>
				<div class="qm-pull-right">
					<button class="button" id="remove-assign_users"><?php _e('Remove', 'quizmaker'); ?></button>
				</div>
				<span class="spinner spinner-remove-assign-users"></span>
			</div>
		</div>
		
		<!-- Assign groups tab content -->
		<div class="qm-panel-tabs assign_groups_panel_tab assign_groups_data">
			
			<?php $usergroups = qm_get_user_groups(); ?>

			<table cellpadding="0" cellspacing="0" class="qm-table-meta-box">

				<thead>
					<tr>
						<th class="qm-a-c qm-s-2 checkall"></th>
						<th class="qm-a-l name"><?php _e('Group Name', 'quizmaker'); ?></th>
					</tr>
				</thead>

				<tbody>
					
					<?php if($usergroups): ?>
					<?php foreach( $usergroups as $usergroup ): ?>
						
						<tr>
							<td class="qm-a-c"><input type="checkbox" name="test_settings[usergroups][]" value="<?php echo $usergroup->ID; ?>" <?php echo in_array($usergroup->ID, $assign_groups) ? 'checked' : ''; ?>></td>
							<td><a href="<?php echo admin_url('post.php?post=' . $usergroup->ID . '&action=edit'); ?>"><?php echo $usergroup->post_title; ?></a></td>
						</tr>

					<?php endforeach; ?>
					<?php else: ?>
						
						<tr>
							<td class="qm-a-c" colspan="2"><?php _e('No Data', 'quizmaker'); ?></td>
						</tr>

					<?php endif; ?>

				</tbody>
				
			</table>

		</div>
		
		<!-- Settings tab content -->
		<div class="qm-panel-tabs settings_panel_tab settings_data">

			<div class="groups-field">
				<div class="group-field">
					<label><?php _e('Publish for', 'quizmaker'); ?></label>
					<div class="field-input checkbox">
						<select name="test_settings[publish_for]">
							<?php foreach($publish_for_options as $p): ?>
							<option value="<?php echo $p['id']; ?>" <?php selected($publish_for, $p['id']); ?>><?php echo $p['name']; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</div>

			<div class="groups-field">
				<div class="group-field">
					<label><?php _e('Type Testing', 'quizmaker'); ?></label>
					<div class="field-input checkbox">
						<select name="test_settings[type_testing]">
							<option value="0" <?php selected($type_testing, 0); ?>><?php _e('Normal', 'quizmaker'); ?></option>
							<option value="2" <?php selected($type_testing, 2); ?>><?php _e('Infinite', 'quizmaker'); ?></option>
							<option value="1" <?php selected($type_testing, 1); ?>><?php _e('Adaptive', 'quizmaker'); ?></option>
						</select>
					</div>
				</div>
			</div>

			<div class="groups-field">
				<div class="group-field">
					<label><?php _e('Adaptive Times', 'quizmaker'); ?></label>
					<div class="field-input checkbox">
						<input type="text" name="test_settings[adaptive_times]" value="<?php echo $adaptive_times; ?>" class="qm-s-1"/>
					</div>
				</div>
			</div>

			<div class="groups-field">
				<div class="group-field">
					<label><?php _e('Adaptive Max Round', 'quizmaker'); ?></label>
					<div class="field-input checkbox">
						<input type="text" name="test_settings[adaptive_max_round]" value="<?php echo $adaptive_max_round; ?>" class="qm-s-1"/>
					</div>
				</div>
			</div>

			<div class="groups-field">
				<div class="group-field">
					<label><?php _e('Duration', 'quizmaker'); ?></label>
					<div class="field-input">
						
						<div class="qm-input-switch-number">
							<div class="qm-input-switch-number-input">
				
								<div class="qm-input-switch-number-input-slide">
									<div class="on-block"><?php _e('ON', 'quizmaker'); ?></div>
									<div class="off-block"><?php _e('OFF', 'quizmaker'); ?></div>
								</div>
				
								<div class="qm-input-switch-number-input-off"><?php _e('OFF', 'quizmaker'); ?></div>
				
								<div class="qm-input-switch-number-input-on">
									<input type="text" name="test_settings[duration]" data-off="0" value="<?php echo $duration; ?>"/>
								</div>
				
							</div>
							
							<div class="qm-input-switch-number-info">
								<span class="qm-input-switch-number-info-on"><?php _e('Minutes', 'quizmaker'); ?></span>
								<span class="qm-input-switch-number-info-off"><?php _e('Unlimited', 'quizmaker'); ?></span>
							</div>
						</div>
						
					</div>
				</div>
			</div>

			<div class="groups-field">
				<div class="group-field">
					<label><?php _e('Display', 'quizmaker'); ?></label>
					<div class="field-input">
						
						<div class="qm-input-switch-number">
			
							<div class="qm-input-switch-number-input">
				
								<div class="qm-input-switch-number-input-slide">
									<div class="on-block"><?php _e('ON', 'quizmaker'); ?></div>
									<div class="off-block"><?php _e('OFF', 'quizmaker'); ?></div>
								</div>
				
								<div class="qm-input-switch-number-input-off"><?php _e('OFF', 'quizmaker'); ?></div>
				
								<div class="qm-input-switch-number-input-on">
									<input type="text" name="test_settings[display_perpage]" data-off="0" value="<?php echo $settings['display_perpage'] ?>"/>
								</div>
				
							</div>
							
							<div class="qm-input-switch-number-info">
								<span class="qm-input-switch-number-info-on"><?php _e('Question perpage', 'quizmaker'); ?></span>
								<span class="qm-input-switch-number-info-off"><?php _e('All questions in a single page', 'quizmaker'); ?></span>
							</div>
			
						</div>
						
					</div>
				</div>
			</div>
			<div class="groups-field">
				<div class="group-field">
					<label><?php _e('Attempt', 'quizmaker'); ?></label>
					<div class="field-input">
							
						<div class="qm-input-switch-number">
			
							<div class="qm-input-switch-number-input">
				
								<div class="qm-input-switch-number-input-slide">
									<div class="on-block"><?php _e('ON', 'quizmaker'); ?></div>
									<div class="off-block"><?php _e('OFF', 'quizmaker'); ?></div>
								</div>
				
								<div class="qm-input-switch-number-input-off"><?php _e('OFF', 'quizmaker'); ?></div>
				
								<div class="qm-input-switch-number-input-on">
									<input type="text" name="test_settings[attempt]" data-off="0" value="<?php echo $attempt; ?>"/>
								</div>
							
							</div>
							
							<div class="qm-input-switch-number-info">
								<span class="qm-input-switch-number-info-on"><?php _e('Times for each member', 'quizmaker'); ?></span>
								<span class="qm-input-switch-number-info-off"><?php _e('Unlimited', 'quizmaker'); ?></span>
							</div>
							
						</div>
						
					</div>
				</div>
			</div>

			<div class="groups-field">
				<div class="group-field">
					<label><?php _e('Instant Answer', 'quizmaker'); ?></label>
					<div class="field-input checkbox">
						
						<div class="qm-input-switch-select">
							<div class="qm-input-switch-select-input">
				
								<div class="qm-input-switch-select-input-slide">
									<div class="on-block"><?php _e('ON', 'quizmaker'); ?></div>
									<div class="off-block"><?php _e('OFF', 'quizmaker'); ?></div>
								</div>
				
								<div class="qm-input-switch-select-input-off"><?php _e('OFF', 'quizmaker'); ?></div>
				
								<div class="qm-input-switch-select-input-on">
									<span><?php _e('ON', 'quizmaker'); ?></span>
									
									<input type="hidden" name="test_settings[instant_answer]" data-off="0" data-on="1" value="<?php echo isset($settings['instant_answer']) && $settings['instant_answer'] ? 1 : 0; ?>"/>
								</div>
								
							</div>
							
						</div>
						
					</div>
				</div>
			</div>

			<div class="groups-field">
				<div class="group-field">
					<label><?php _e('Show Result', 'quizmaker'); ?></label>
					<div class="field-input checkbox">
						
						<div class="qm-input-switch-select">
							<div class="qm-input-switch-select-input">
				
								<div class="qm-input-switch-select-input-slide">
									<div class="on-block"><?php _e('ON', 'quizmaker'); ?></div>
									<div class="off-block"><?php _e('OFF', 'quizmaker'); ?></div>
								</div>
				
								<div class="qm-input-switch-select-input-off"><?php _e('OFF', 'quizmaker'); ?></div>
				
								<div class="qm-input-switch-select-input-on">
									<span><?php _e('ON', 'quizmaker'); ?></span>
									<input type="hidden" name="test_settings[show_result]" data-off="0" data-on="1" value="<?php echo $settings['show_result'] ?>"/>
								</div>
								
							</div>
							
						</div>
						
					</div>
				</div>
			</div>

			<div class="groups-field">
				<div class="group-field">
					<label><?php _e('Show Answers', 'quizmaker'); ?></label>
					<div class="field-input checkbox">
						
						<div class="qm-input-switch-select">
							<div class="qm-input-switch-select-input">
				
								<div class="qm-input-switch-select-input-slide">
									<div class="on-block"><?php _e('ON', 'quizmaker'); ?></div>
									<div class="off-block"><?php _e('OFF', 'quizmaker'); ?></div>
								</div>
				
								<div class="qm-input-switch-select-input-off"><?php _e('OFF', 'quizmaker'); ?></div>
				
								<div class="qm-input-switch-select-input-on">
									<span><?php _e('ON', 'quizmaker'); ?></span>
									<input type="hidden" name="test_settings[result_is_answers]" data-off="0" data-on="1" value="<?php echo $settings['result_is_answers'] ?>"/>
								</div>
								
							</div>
							
							<div class="qm-input-switch-select-info">
								<span class="qm-input-switch-select-info-on"><?php _e('Show answers in results', 'quizmaker'); ?></span>
								<span class="qm-input-switch-select-info-off"><?php _e('Hide answers in results', 'quizmaker'); ?></span>
							</div>
						</div>
						
					</div>
				</div>
			</div>

			<div class="groups-field">
				<div class="group-field">
					<label><?php _e('Shuffle Answers', 'quizmaker'); ?></label>
					<div class="field-input checkbox">
						
						<div class="qm-input-switch-select">
							<div class="qm-input-switch-select-input">
				
								<div class="qm-input-switch-select-input-slide">
									<div class="on-block"><?php _e('ON', 'quizmaker'); ?></div>
									<div class="off-block"><?php _e('OFF', 'quizmaker'); ?></div>
								</div>
				
								<div class="qm-input-switch-select-input-off"><?php _e('OFF', 'quizmaker'); ?></div>
				
								<div class="qm-input-switch-select-input-on">
									<span><?php _e('ON', 'quizmaker'); ?></span>
									<input type="hidden" name="test_settings[is_shuffle_answers]" data-off="0" data-on="1" value="<?php echo isset($settings['is_shuffle_answers']) ? $settings['is_shuffle_answers'] : 1; ?>"/>
								</div>
								
							</div>
							
							<div class="qm-input-switch-select-info">
								<span class="qm-input-switch-select-info-on"><?php _e('Shuffle answers when doing', 'quizmaker'); ?></span>
								<span class="qm-input-switch-select-info-off"><?php _e('No shuffle answers when doing', 'quizmaker'); ?></span>
							</div>
						</div>
						
					</div>
				</div>
			</div>

			<div class="groups-field">
				<div class="group-field">
					<label><?php _e('Show Score', 'quizmaker'); ?></label>
					<div class="field-input checkbox">
						
						<div class="qm-input-switch-select">
							<div class="qm-input-switch-select-input">
				
								<div class="qm-input-switch-select-input-slide">
									<div class="on-block"><?php _e('ON', 'quizmaker'); ?></div>
									<div class="off-block"><?php _e('OFF', 'quizmaker'); ?></div>
								</div>
				
								<div class="qm-input-switch-select-input-off"><?php _e('OFF', 'quizmaker'); ?></div>
				
								<div class="qm-input-switch-select-input-on">
									<span><?php _e('ON', 'quizmaker'); ?></span>
									<input type="hidden" name="test_settings[show_score]" data-off="0" data-on="1" value="<?php echo isset($settings['show_score']) ? $settings['show_score'] : 0; ?>"/>
								</div>
								
							</div>
						
						</div>
						
					</div>
				</div>
			</div>

			<div class="groups-field">
				<div class="group-field">
					<label><?php _e('User Score', 'quizmaker'); ?></label>
					<div class="field-input checkbox">
						
						<div class="qm-input-switch-select">
							<div class="qm-input-switch-select-input">
				
								<div class="qm-input-switch-select-input-slide">
									<div class="on-block"><?php _e('ON', 'quizmaker'); ?></div>
									<div class="off-block"><?php _e('OFF', 'quizmaker'); ?></div>
								</div>
				
								<div class="qm-input-switch-select-input-off"><?php _e('OFF', 'quizmaker'); ?></div>
				
								<div class="qm-input-switch-select-input-on">
									<span><?php _e('ON', 'quizmaker'); ?></span>
									<input type="hidden" name="test_settings[user_score]" data-off="0" data-on="1" value="<?php echo $user_score; ?>"/>
								</div>
								
							</div>

							<div class="qm-input-switch-select-info">
								<span class="qm-input-switch-select-info-on"><?php _e('Adding score to user', 'quizmaker'); ?></span>
								<span class="qm-input-switch-select-info-off"><?php _e('No adding score to user', 'quizmaker'); ?></span>
							</div>
						
						</div>
						
					</div>
				</div>
			</div>

			<div class="groups-field">
				<div class="group-field">
					<label><?php _e('Show Explanation', 'quizmaker'); ?></label>
					<div class="field-input checkbox">
						<select name="test_settings[show_explanation]">
							<option value="0" <?php selected($show_explanation, 0); ?>><?php _e('None', 'quizmaker'); ?></option>
							<option value="1" <?php selected($show_explanation, 1); ?>><?php _e('On Doing', 'quizmaker'); ?></option>
							<option value="2" <?php selected($show_explanation, 2); ?>><?php _e('Only On Result', 'quizmaker'); ?></option>
						</select>
					</div>
				</div>
			</div>

			<div class="groups-field">
				<div class="group-field">
					<label><?php _e('Show Pagination', 'quizmaker'); ?></label>
					<div class="field-input checkbox">
						
						<div class="qm-input-switch-select">
							<div class="qm-input-switch-select-input">
				
								<div class="qm-input-switch-select-input-slide">
									<div class="on-block"><?php _e('ON', 'quizmaker'); ?></div>
									<div class="off-block"><?php _e('OFF', 'quizmaker'); ?></div>
								</div>
				
								<div class="qm-input-switch-select-input-off"><?php _e('OFF', 'quizmaker'); ?></div>
				
								<div class="qm-input-switch-select-input-on">
									<span><?php _e('ON', 'quizmaker'); ?></span>
									<input type="hidden" name="test_settings[is_pagination]" data-off="0" data-on="1" value="<?php echo $settings['is_pagination'] ?>"/>
								</div>
								
							</div>
							
						</div>
						
					</div>
				</div>
			</div>

			<div class="groups-field">
				<div class="group-field">
					<label><?php _e('Backward', 'quizmaker'); ?></label>
					<div class="field-input checkbox">
						
						<div class="qm-input-switch-select">
							<div class="qm-input-switch-select-input">
								
								<div class="qm-input-switch-select-input-slide">
									<div class="on-block"><?php _e('ON', 'quizmaker'); ?></div>
									<div class="off-block"><?php _e('OFF', 'quizmaker'); ?></div>
								</div>
								
								<div class="qm-input-switch-select-input-off"><?php _e('OFF', 'quizmaker'); ?></div>
								
								<div class="qm-input-switch-select-input-on">
									<span><?php _e('ON', 'quizmaker'); ?></span>
									<input type="hidden" name="test_settings[is_backward]" data-off="0" data-on="1" value="<?php echo $settings['is_backward'] ?>"/>
								</div>
								
							</div>
							
						</div>
						
					</div>
				</div>
			</div>

			<div class="groups-field">
				<div class="group-field">
					<label><?php _e('Show Ranking', 'quizmaker'); ?></label>
					<div class="field-input checkbox">
						
						<div class="qm-input-switch-select">
							<div class="qm-input-switch-select-input">
				
								<div class="qm-input-switch-select-input-slide">
									<div class="on-block"><?php _e('ON', 'quizmaker'); ?></div>
									<div class="off-block"><?php _e('OFF', 'quizmaker'); ?></div>
								</div>
				
								<div class="qm-input-switch-select-input-off"><?php _e('OFF', 'quizmaker'); ?></div>
				
								<div class="qm-input-switch-select-input-on">
									<span><?php _e('ON', 'quizmaker'); ?></span>
									<input type="hidden" name="test_settings[is_ranking]" data-off="0" data-on="1" value="<?php echo $settings['is_ranking'] ?>"/>
								</div>
								
							</div>
							
							<div class="qm-input-switch-select-info">
								<span class="qm-input-switch-select-info-on"><?php _e('Show ranking in single test', 'quizmaker'); ?></span>
								<span class="qm-input-switch-select-info-off"><?php _e('Hide ranking in single test', 'quizmaker'); ?></span>
							</div>
						</div>
						
					</div>
				</div>
			</div>

			<div class="groups-field">
				<div class="group-field">
					<label><?php _e('Show Sidebar Tracking', 'quizmaker'); ?></label>
					<div class="field-input checkbox">
						
						<div class="qm-input-switch-select">
							<div class="qm-input-switch-select-input">
				
								<div class="qm-input-switch-select-input-slide">
									<div class="on-block"><?php _e('ON', 'quizmaker'); ?></div>
									<div class="off-block"><?php _e('OFF', 'quizmaker'); ?></div>
								</div>
				
								<div class="qm-input-switch-select-input-off"><?php _e('OFF', 'quizmaker'); ?></div>
				
								<div class="qm-input-switch-select-input-on">
									<span><?php _e('ON', 'quizmaker'); ?></span>
									<input type="hidden" name="test_settings[is_sidebar_tracking]" data-off="0" data-on="1" value="<?php echo $settings['is_sidebar_tracking'] ?>"/>
								</div>
								
							</div>
							
						</div>
						
					</div>
				</div>
			</div>

			<div class="groups-field">
				<div class="group-field">
					<label><?php _e('Question Title', 'quizmaker'); ?></label>
					<div class="field-input checkbox">
						
						<div class="qm-input-switch-select">
							<div class="qm-input-switch-select-input">
				
								<div class="qm-input-switch-select-input-slide">
									<div class="on-block"><?php _e('ON', 'quizmaker'); ?></div>
									<div class="off-block"><?php _e('OFF', 'quizmaker'); ?></div>
								</div>
				
								<div class="qm-input-switch-select-input-off"><?php _e('OFF', 'quizmaker'); ?></div>
				
								<div class="qm-input-switch-select-input-on">
									<span><?php _e('ON', 'quizmaker'); ?></span>
									<input type="hidden" name="test_settings[show_question_title]" data-off="0" data-on="1" value="<?php echo isset($settings['show_question_title']) ? $settings['show_question_title'] : 1; ?>"/>
								</div>
								
							</div>
							
							<div class="qm-input-switch-select-info">
								<span class="qm-input-switch-select-info-on"><?php _e('Show question title', 'quizmaker'); ?></span>
								<span class="qm-input-switch-select-info-off"><?php _e('Hide question title', 'quizmaker'); ?></span>
							</div>
						</div>
						
					</div>
				</div>
			</div>
			<div class="groups-field">
				<div class="group-field">
					<label><?php _e('Enable reviews', 'quizmaker'); ?></label>
					<div class="field-input checkbox">
							
						<div class="qm-input-switch-select">
							<div class="qm-input-switch-select-input">
				
								<div class="qm-input-switch-select-input-slide">
									<div class="on-block"><?php _e('ON', 'quizmaker'); ?></div>
									<div class="off-block"><?php _e('OFF', 'quizmaker'); ?></div>
								</div>
				
								<div class="qm-input-switch-select-input-off"><?php _e('OFF', 'quizmaker'); ?></div>
				
								<div class="qm-input-switch-select-input-on">
									<span><?php _e('ON', 'quizmaker'); ?></span>
									<input type="hidden" name="test_settings[is_reviews]" data-off="0" data-on="1" value="<?php echo $settings['is_reviews'] ?>"/>
								</div>
								
							</div>
							
							<div class="qm-input-switch-select-info">
								<span class="qm-input-switch-select-info-on"><?php _e('Show reviews', 'quizmaker'); ?></span>
								<span class="qm-input-switch-select-info-off"><?php _e('Hide reviews', 'quizmaker'); ?></span>
							</div>
						</div>
						
					</div>
				</div>
			</div>
			<div class="groups-field">
				<div class="group-field">
					<label><?php _e('Question Report', 'quizmaker'); ?></label>
					<div class="field-input checkbox">
							
						<div class="qm-input-switch-select">
							<div class="qm-input-switch-select-input">
				
								<div class="qm-input-switch-select-input-slide">
									<div class="on-block"><?php _e('ON', 'quizmaker'); ?></div>
									<div class="off-block"><?php _e('OFF', 'quizmaker'); ?></div>
								</div>
				
								<div class="qm-input-switch-select-input-off"><?php _e('OFF', 'quizmaker'); ?></div>
				
								<div class="qm-input-switch-select-input-on">
									<span><?php _e('ON', 'quizmaker'); ?></span>
									<input type="hidden" name="test_settings[is_question_report]" data-off="0" data-on="1" value="<?php echo $settings['is_question_report'] ?>"/>
								</div>
								
							</div>
							
							<div class="qm-input-switch-select-info">
								<span class="qm-input-switch-select-info-on"><?php _e('User can make a question report after completing the test', 'quizmaker'); ?></span>
							</div>
						</div>
						
					</div>
				</div>
			</div>
			<div class="groups-field">
				<div class="group-field">
					<label><?php _e('Email result', 'quizmaker'); ?></label>
					<div class="field-input checkbox">
							
						<div class="qm-input-switch-select">
							<div class="qm-input-switch-select-input">
				
								<div class="qm-input-switch-select-input-slide">
									<div class="on-block"><?php _e('ON', 'quizmaker'); ?></div>
									<div class="off-block"><?php _e('OFF', 'quizmaker'); ?></div>
								</div>
				
								<div class="qm-input-switch-select-input-off"><?php _e('OFF', 'quizmaker'); ?></div>
				
								<div class="qm-input-switch-select-input-on">
									<span><?php _e('ON', 'quizmaker'); ?></span>
									<input type="hidden" name="test_settings[is_email_result]" data-off="0" data-on="1" value="<?php echo $settings['is_email_result'] ?>"/>
								</div>
								
							</div>
							
							<div class="qm-input-switch-select-info">
								<span class="qm-input-switch-select-info-on"><?php _e('Email result to member when complete testing', 'quizmaker'); ?></span>
								<span class="qm-input-switch-select-info-off"><?php _e('No email result to member when complete testing', 'quizmaker'); ?></span>
							</div>
						</div>
						
					</div>
				</div>
			</div>
			
			<div class="groups-field">
				<div class="group-field">
					<label><?php _e('Reset Rating', 'quizmaker'); ?></label>
					<div class="field-input checkbox">
							
						<div class="qm-input-switch-select">
							<div class="qm-input-switch-select-input">
				
								<div class="qm-input-switch-select-input-slide">
									<div class="on-block"><?php _e('ON', 'quizmaker'); ?></div>
									<div class="off-block"><?php _e('OFF', 'quizmaker'); ?></div>
								</div>
				
								<div class="qm-input-switch-select-input-off"><?php _e('OFF', 'quizmaker'); ?></div>
				
								<div class="qm-input-switch-select-input-on">
									<span><?php _e('ON', 'quizmaker'); ?></span>
									<input type="hidden" name="test_settings[is_reset_rating]" data-off="0" data-on="1" value="0"/>
								</div>
								
							</div>
							
							<div class="qm-input-switch-select-info">
								<span class="qm-input-switch-select-info-on"><?php _e('The rating of test will be reset', 'quizmaker'); ?></span>
							</div>
						</div>
						
					</div>
				</div>
			</div>


		</div>
	</div>
	<div class="clear"></div>
<?php
	}
	
	public static function save( $post_id, $post ) {
		
		if(isset($_POST['random_question']))
		{

			$random_question	=	$_POST['random_question'];

			update_post_meta( $post_id, '_randoms_questions', $random_question );
		}
		
		if(isset($_POST['test_settings']))
		{
			
			$test_settings		=	$_POST['test_settings'];
			
			$pre_test_settings	=	qm_test_get_settings($post_id);
			
			$data		=	wp_parse_args( $test_settings, $pre_test_settings );
			
			if(isset($test_settings['is_reviews']) && is_numeric($test_settings['is_reviews']) && $test_settings['is_reviews'] >= 0){
				$data['is_reviews'] = absInt($test_settings['is_reviews']);
			}else{
				$data['is_reviews'] = 0;
			}
			
			if(isset($test_settings['is_email_result']) && is_numeric($test_settings['is_email_result']) && $test_settings['is_email_result'] >= 0){
				$data['is_email_result'] = absInt($test_settings['is_email_result']);
			}else{
				$data['is_email_result'] = 0;
			}
			
			if(isset($test_settings['duration']) && is_numeric($test_settings['duration']) && $test_settings['duration'] >= 0){
				$duration = absInt($test_settings['duration']);
			}else{
				$duration	=	0;
			}
			
			if(isset($test_settings['attempt']) && is_numeric($test_settings['attempt']) && $test_settings['attempt'] >= 0){
				$attempt	=	absInt($test_settings['attempt']);
			}else{
				$attempt	=	0;
			}
			
			if(isset($test_settings['result_is_answers']) && is_numeric($test_settings['result_is_answers']) && $test_settings['result_is_answers'] >= 0){
				$data['result_is_answers'] = absInt($test_settings['result_is_answers']);
			}else{
				$data['result_is_answers'] = 0;
			}
			
			if(isset($test_settings['is_timeout_answer']) && is_numeric($test_settings['is_timeout_answer']) && $test_settings['is_timeout_answer'] >= 0){
				$data['is_timeout_answer'] = absInt($test_settings['is_timeout_answer']);
			}else{
				$data['is_timeout_answer'] = 0;
			}

			if(isset($test_settings['save_for_later']) && is_numeric($test_settings['save_for_later']) && $test_settings['save_for_later'] >= 0){
				$data['save_for_later'] = absInt($test_settings['save_for_later']);
			}else{
				$data['save_for_later'] = 0;
			}

			if(isset($test_settings['auto_save']) && is_numeric($test_settings['auto_save']) && $test_settings['auto_save'] >= 0){
				$data['auto_save'] = absInt($test_settings['auto_save']);
			}else{
				$data['auto_save'] = 0;
			}
			
			if(isset($test_settings['play_all']) && is_numeric($test_settings['play_all']) && $test_settings['play_all'] >= 0){
				$data['play_all'] = absInt($test_settings['play_all']);
			}else{
				$data['play_all'] = 0;
			}

			if(isset($test_settings['is_ranking']) && is_numeric($test_settings['is_ranking']) && $test_settings['is_ranking'] >= 0){
				$data['is_ranking'] = absInt($test_settings['is_ranking']);
			}else{
				$data['is_ranking'] = 0;
			}

			if(isset($test_settings['is_sidebar_tracking']) && is_numeric($test_settings['is_sidebar_tracking']) && $test_settings['is_sidebar_tracking'] >= 0){
				$data['is_sidebar_tracking'] = absInt($test_settings['is_sidebar_tracking']);
			}else{
				$data['is_sidebar_tracking'] = 0;
			}

			if(isset($test_settings['show_explanation']) && is_numeric($test_settings['show_explanation']) && $test_settings['show_explanation'] >= 0){
				$data['show_explanation'] = absInt($test_settings['show_explanation']);
			}else{
				$data['show_explanation'] = 0;
			}

			if(isset($test_settings['is_pagination']) && is_numeric($test_settings['is_pagination']) && $test_settings['is_pagination'] >= 0){
				$data['is_pagination'] = absInt($test_settings['is_pagination']);
			}else{
				$data['is_pagination'] = 1;
			}
			
			if(isset($test_settings['display_perpage']) && is_numeric($test_settings['display_perpage']) && $test_settings['display_perpage'] > 0){
				$data['display_perpage'] = absInt($test_settings['display_perpage']);
			}else{
				$data['display_perpage'] = 0;
			}

			if(isset($test_settings['show_score']) && is_numeric($test_settings['show_score']) && $test_settings['show_score'] > 0){
				$data['show_score'] = absInt($test_settings['show_score']);
			}else{
				$data['show_score'] = 0;
			}

			if(isset($test_settings['show_result']) && is_numeric($test_settings['show_result']) && $test_settings['show_result'] > 0){
				$data['show_result'] = absInt($test_settings['show_result']);
			}else{
				$data['show_result'] = 0;
			}

			if(isset($test_settings['user_score']) && is_numeric($test_settings['user_score']) && in_array($test_settings['user_score'], array(0, 1, 2, 3))){
				$user_score = absInt($test_settings['user_score']);
			}else{
				$user_score = 0;
			}
			
			if(isset($test_settings['publish_for']) && is_numeric($test_settings['publish_for']) ){
				$publish_for = absInt($test_settings['publish_for']);
			}else{
				$publish_for = 0;
			}

			if(isset($test_settings['type_testing']) && is_numeric($test_settings['type_testing']) && in_array($test_settings['type_testing'], array(0, 1, 2))){
				$type_testing = absInt($test_settings['type_testing']);
			}else{
				$type_testing = 0;
			}
			
			if(isset($test_settings['show_question_title']) && is_numeric($test_settings['show_question_title']) && $test_settings['show_question_title'] >= 0){
				$data['show_question_title'] = absInt($test_settings['show_question_title']);
			}else{
				$data['show_question_title'] = 0;
			}

			if(isset($test_settings['is_ranking']) && is_numeric($test_settings['is_ranking']) && $test_settings['is_ranking'] >= 0){
				$data['is_ranking'] = absInt($test_settings['is_ranking']);
			}else{
				$data['is_ranking'] = 0;
			}

			// Instant Answer
			if(isset($test_settings['instant_answer']) && is_numeric($test_settings['instant_answer']) && $test_settings['instant_answer'] > 0){
				$data['instant_answer'] = absInt($test_settings['instant_answer']);
			}else{
				$data['instant_answer'] = 0;
			}

			// Shuffle Answers
			if(isset($test_settings['is_shuffle_answers']) && is_numeric($test_settings['is_shuffle_answers']) && $test_settings['is_shuffle_answers'] > 0){
				$data['is_shuffle_answers'] = absInt($test_settings['is_shuffle_answers']);
			}else{
				$data['is_shuffle_answers'] = 0;
			}

			// Adaptive times
			if(isset($test_settings['adaptive_times']) && is_numeric($test_settings['adaptive_times']) && $test_settings['adaptive_times'] > 0){
				$data['adaptive_times'] = absInt($test_settings['adaptive_times']);
			}else{
				$data['adaptive_times'] = 1;
			}

			// Adaptive max round
			if(isset($test_settings['adaptive_max_round']) && is_numeric($test_settings['adaptive_max_round']) && $test_settings['adaptive_max_round'] > 0){
				$data['adaptive_max_round'] = absInt($test_settings['adaptive_max_round']);
			}else{
				$data['adaptive_max_round'] = 0;
			}

			if(isset($test_settings['is_reset_rating']) && $test_settings['is_reset_rating']) {

				$test = new QM_Test( $post_id );

				$test->reset_rating();
			}

			// Assign User Groups
			$data['assigned_groups'] = isset($test_settings['usergroups']) ? $test_settings['usergroups'] : [];
			
			qm_update_post_meta( $post_id, array( 
				'test_settings' => $data,
				'attempt'		=>	$attempt,
				'duration' 		=>	$duration,
				'publish_for'	=>	$publish_for,
				'type_testing'	=>	$type_testing,
				'user_score'	=>	$user_score,
			) );
		}

		qm_add_question_categories( $post_id );
	}
}