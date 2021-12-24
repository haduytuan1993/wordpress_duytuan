<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>


<?php if(isset($results['data']) && $results['data']): ?>
	<?php foreach($results['data'] as $result): 

		$percent 			= qm_checking_return($result, 'percent', 0);
		$score 				= qm_checking_return($result, 'score', 0);
		$total_score 		= qm_checking_return($result, 'total_score', 0);
		$duration 			= qm_checking_return($result, 'duration', 0);
		$date_start 		= qm_checking_return($result, 'date_start', 0);
		$user_id 			= qm_checking_return($result, 'user_id', 0);
		$user_nicename 		= qm_checking_return($result, 'user_nicename', '');

		$attend_question	= qm_checking_return($result, 'attend_question', 0);
		
		$ranking	    	= qm_checking_return(qm_is_ranking($result['test_id'], $score, $total_score, false), 'name', '');
		
		$user_email			=	isset($result['user_email']) && $result['user_email'] ? $result['user_email'] : $result['login_email'];
	?>

<tr>
	<td class="percent">
		<span class="lbl"><?php echo $percent; ?>%</span>
		<div class="chart">
			<div class="progress" style="width: <?php echo $percent; ?>%"></div>
		</div>
	</td>
	<td class="qm-a-c"><?php echo $score . '/' . $total_score; ?></td>
	<!-- <td class="qm-a-c"><?php echo $attend_question . '/' . $total_questions; ?></td> -->
	<!-- <td class="qm-a-c"><?php echo $duration; ?></td> -->
	<!-- <td class="qm-a-c"><?php echo $ranking; ?></td> -->
	<td class="qm-a-l"><?php echo $date_start; ?></td>
	<td><a href="<?php echo admin_url('user-edit.php?user_id=' . $user_id); ?>"><?php echo $user_email; ?></a></td>
	<td><a class="qm-result-view-all" data-uid="<?php echo $user_id; ?>"><?php _e('View All', 'quizmaker'); ?></a></td>
	<td>
		<a class="qm-result-view-detail" href="<?php echo qm_get_result_test_url($result['id'], $result['test_id']); ?>" target="blank"><?php _e('Result', 'quizmaker'); ?></a>
	</td>
</tr>

	<?php endforeach; ?>
<?php else: ?>

	<tr class="nodata results_nodata"><td class="qm-a-c" colspan="7"><?php _e('No Data', 'quizmaker'); ?></td></tr>

<?php endif; ?>