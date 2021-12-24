<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }


$settings	=	$test->get_settings();

?>

<div id="qm-my-account-table__view-results" class="container-myaccount-view-result">

<?php do_action( 'quizmaker_before_my_account' ); ?>

	<h5 class="mb-3"><?php echo $test->get_title(); ?></h5>
			
	<?php do_action( 'quizmaker_before_my_account_view_result' ); ?>
	
	<table cellpadding="0" cellspacing="0" class="table table-light qm-my-account-table_view_result">
		<thead class="thead-light">
			<tr>
				<th>#</th>
				<th><?php _e('Rank', 'quizmaker'); ?></th>
				<?php if($settings['show_score']): ?>
				<th><?php _e('Score', 'quizmaker'); ?></th>
				<th><?php _e('Percent', 'quizmaker') ?></th>
				<?php endif; ?>
				<th><?php _e('Duration', 'quizmaker') ?></th>
				<th><?php _e('Date', 'quizmaker') ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
	<?php if(isset($results['data']) && $results['data']): ?>
		<?php foreach($results['data'] as $index => $result): 
			?>
			<tr>
				<td class="c"><?php echo $index + 1 ?></td>
				<?php if(isset($result['ranking'])): ?>
				<td class="c"><?php echo $result['ranking']; ?></td>
				<?php else: ?>
				<td class="c"></td>
				<?php endif; ?>
				<?php if($settings['show_score']): ?>
				<td class="c"><?php echo $result['score'] . '/' . $result['total_score']; ?></td>
				<td class="c"><?php echo $result['percent']; ?>%</td>
				<?php endif; ?>
				<td class="c"><?php echo $result['duration']; ?></td>
				<td class="c"><?php echo $result['date_start']; ?></td>
				<td class="c"><a href="<?php echo qm_get_endpoint_url( 'result', $result['id'], get_permalink( $result['test_id'] )); ?>" target="blank"><?php _e('Detail', 'quizmaker'); ?></a></td>
			</tr>
		<?php endforeach; ?>
	<?php endif; ?>
		</tbody>
	</table>
	
	<?php if(isset($results['data']) && $results['data']): ?>
	<?php qm_get_template('global/pagination.php', $results['pagination']); ?>
	<?php endif; ?>
	
	<?php do_action( 'quizmaker_after_my_account_view_result' ); ?>
	<?php do_action( 'quizmaker_after_my_account' ); ?>
</div>