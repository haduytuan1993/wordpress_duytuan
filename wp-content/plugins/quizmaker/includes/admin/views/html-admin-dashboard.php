<?php

$members		=	qm_get_lastest_members();
$results		=	qm_get_lastest_results(array('limit' => 20));
$tests			=	qm_get_tests( array( 'numberposts' => 10, 'orderby' => 'date', 'order' => 'DESC') );
$tests_played	=	qm_get_tests( array( 'numberposts' => 10, 'orderby' => 'meta_value_num', 'order' => 'DESC', 'meta_key' => '_played' ) );


?>
<div class="wrap">
	
	<h1><?php _e('Dashboard', 'quizmaker'); ?></h1>

	<div class="qm-dashboard-section">
		
		<div class="aws-xs-last-12 aws-sm-6 aws-md-4">
			<div class="qm-info-box-1-3">
				<h2 class="title"><?php _e('Total Tests', 'quizmaker'); ?></h2>
				<div class="value">
					<?php echo qm_get_total_tests(); ?>
				</div>
			</div>
		</div>
	
		<div class="aws-xs-last-12 aws-sm-last-6 aws-md-4">
			<div class="qm-info-box-1-1">
				<h2 class="title"><?php _e('Total Questions', 'quizmaker'); ?></h2>
				<div class="value">
					<?php echo qm_get_total_questions(); ?>
				</div>
			</div>
		</div>
	
		<div class="aws-xs-last-12 aws-sm-last-12 aws-md-4 last">
			<div class="qm-info-box-1-2">
				<h2 class="title"><?php _e('Total Members', 'quizmaker'); ?></h2>
				<div class="value">
					<?php echo qm_get_total_members(); ?>
				</div>
			</div>
		</div>
	
	</div>

	<div class="qm-dashboard-section">
		
		<div class="aws-xs-last-12 aws-sm-last-12 aws-lg-4">
			
			<div class="qm-info-box-2">
				<h2 class="title"><?php _e('New Users', 'quizmaker'); ?></h2>
				<div class="body">
					<table cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<th class="qm-a-c qm-s-1">#</th>
								<th><?php _e('Name', 'quizmaker'); ?></th>
								<th><?php _e('Username', 'quizmaker'); ?></th>
								<th class="qm-s-5"><?php _e('Date', 'quizmaker'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php if($members): ?>
								<?php foreach($members as $key => $member): ?>
							<tr>
								<td class="qm-a-c"><?php echo $key + 1; ?></td>
								<td><?php echo $member->user_nicename; ?></td>
								<td><a href="<?php echo admin_url('user-edit.php?user_id=' . $member->ID); ?>"><?php echo $member->user_login; ?></a></td>
								<td><?php echo $member->user_registered; ?></td>
							</tr>
								<?php endforeach; ?>
							<?php else: ?>
							<tr>
								<td colspan="4" class="qm-a-c"><?php _e('NO DATA', 'quizmaker'); ?></td>
							</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
			
			<div class="qm-info-box-2">
				<h2 class="title"><?php _e('New Tests', 'quizmaker'); ?></h2>
				<div class="body">
					<table cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<th class="qm-a-c qm-s-1">#</th>
								<th><?php _e('Title', 'quizmaker'); ?></th>
								<th class="qm-s-5"><?php _e('Date', 'quizmaker'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php if($tests): ?>
								<?php foreach($tests as $key => $t): ?>
							<tr>
								<td class="qm-a-c"><?php echo $key + 1; ?></td>
								<td><a href="<?php echo admin_url('post.php?action=edit&post=' . $t->ID); ?>"><?php echo $t->post_title; ?></a></td>
								<td><?php echo $t->post_date; ?></td>
							</tr>
								<?php endforeach; ?>
							<?php else: ?>
							<tr>
								<td colspan="4" class="qm-a-c"><?php _e('NO DATA', 'quizmaker'); ?></td>
							</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
			
			<div class="qm-info-box-2">
				<h2 class="title"><?php _e('Top Played Tests', 'quizmaker'); ?></h2>
				<div class="body">
					<table cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<th class="qm-a-c qm-s-1">#</th>
								<th><?php _e('Title', 'quizmaker'); ?></th>
								<th class="qm-s-5"><?php _e('Count', 'quizmaker'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php if($tests_played): ?>
								<?php foreach($tests_played as $key => $t): ?>
							<tr>
								<td class="qm-a-c"><?php echo $key + 1; ?></td>
								<td><a href="<?php echo admin_url('post.php?action=edit&post=' . $t->ID); ?>"><?php echo $t->post_title; ?></a></td>
								<td><?php echo qm_get_post_meta($t->ID, 'played'); ?></td>
							</tr>
								<?php endforeach; ?>
							<?php else: ?>
							<tr>
								<td colspan="4" class="qm-a-c"><?php _e('NO DATA', 'quizmaker'); ?></td>
							</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
			
		</div>
		
		<div class="aws-xs-last-12 aws-sm-last-12 aws-lg-8 last">
			
			<div class="qm-info-box-2">
				<h2 class="title"><?php _e('Lastest Results', 'quizmaker'); ?></h2>
				<div class="body">
					<table cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<th class="qm-a-c qm-s-1">#</th>
								<th class="qm-s-4"><?php _e('User', 'quizmaker'); ?></th>
								<th class="qm-a-c qm-s-1"><?php _e('Percent', 'quizmaker'); ?></th>
								<th class="qm-s-1"><?php _e('Score', 'quizmaker'); ?></th>
								<th class="qm-s-4"><?php _e('Duration', 'quizmaker'); ?></th>
								<th><?php _e('Tests', 'quizmaker'); ?></th>
								<th class="qm-s-5"><?php _e('Date', 'quizmaker'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php if($results): ?>
								<?php foreach($results as $key => $result): ?>
							<tr>
								<td class="qm-a-c"><?php echo $key + 1; ?></td>
								<td><?php echo $result['user_login']; ?></td>
								<td class="qm-a-c"><?php echo $result['percent']; ?>%</td>
								<td><?php echo $result['score'] . '/' . $result['total_score']; ?></td>
								<td><?php echo $result['duration']; ?></td>
								<td><a href="<?php echo $result['test_admin_link']; ?>"><?php echo $result['test_title']; ?></a></td>
								<td><?php echo $result['date_start']; ?></td>
							</tr>
								<?php endforeach; ?>
								<?php else: ?>
							<tr>
								<td colspan="6" class="qm-a-c"><?php _e('NO DATA', 'quizmaker'); ?></td>
							</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
			
		</div>
		
	</div>
</div>