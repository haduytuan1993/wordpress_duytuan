<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

<tr class="tr-view-all"><td colspan="9">
<table cellpadding="0" cellspacing="0" class="widefat wp-list-table">
	<thead>
				<tr>
					<th class="qm-a-c qm-s-3 percent"><?php _e('Percent', 'quizmaker'); ?></th>
					<th class="qm-a-c qm-s-1 score"><?php _e('Score', 'quizmaker'); ?></th>
					<th class="qm-a-c qm-s-4 duration"><?php _e('Duration', 'quizmaker'); ?></th>
					<!-- <th class="qm-a-c qm-s-4 ranking"><?php _e('Ranking', 'quizmaker'); ?></th> -->
					<th class="qm-a-l qm-s-4 certid"><?php _e('Cert ID', 'quizmaker'); ?></th>
					<th class="qm-a-l qm-s-5 date"><?php _e('Date', 'quizmaker'); ?></th>
					<th class="qm-a-c qm-s-1"></th>
				</tr>
			</thead>
	<tbody>
<?php if($results['data']): ?>
	<?php foreach($results['data'] as $result): 

		$percent 		= qm_checking_return($result, 'percent', 0);
		$score 			= qm_checking_return($result, 'score', 0);
		$total_score 	= qm_checking_return($result, 'total_score', 0);
		$duration 		= qm_checking_return($result, 'duration', 0);
		$date_start 	= qm_checking_return($result, 'date_start', 0);
		$ranking	    = qm_checking_return(qm_is_ranking($result['test_id'], $score, $total_score, false), 'name', '');
		$certid 		= $ranking && $result['cert_id'] ? $result['cert_id'] : '';
	?>

<tr>
	<td class="percent">
		<span class="lbl"><?php echo $percent; ?>%</span>
		<div class="chart">
			<div class="progress" style="width: <?php echo $percent; ?>%"></div>
		</div>
	</td>
	<td class="qm-a-c"><?php echo $score . '/' . $total_score; ?></td>
	<td class="qm-a-c"><?php echo $duration; ?></td>
	<!-- <td class="qm-a-c"><?php echo $ranking; ?></td> -->

	<?php if($certid): ?>
	<td class="qm-a-l"><a href="<?php echo admin_url('admin.php?s=' . $certid . '&page=qm-user-certificates') ?>"><?php echo $certid; ?></a></td>
	<?php else: ?>
	<td></td>
	<?php endif; ?>

	<td class="qm-a-l"><?php echo $date_start; ?></td>
	<td>
		<a class="qm-result-view-detail" href="<?php echo qm_get_result_test_url($result['id'], $result['test_id']); ?>" target="blank"><?php _e('Result', 'quizmaker'); ?></a>
	</td>
</tr>

	<?php endforeach; ?>
<?php else: ?>

	<tr class="nodata results_nodata"><td class="qm-a-c" colspan="7"><?php _e('No Data', 'quizmaker'); ?></td></tr>

<?php endif; ?>
	</tbody>
</table>
</td></tr>