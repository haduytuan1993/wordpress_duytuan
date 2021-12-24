<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="qm-my-account-table__view-results" class="container-myaccount-view-results">
		
<?php do_action( 'quizmaker_before_my_account' ); ?>

<table cellpadding="0" cellspacing="0" class="table table-light qm-my-account-table_view_results">
	<thead class="thead-light">
		<tr>
			<th scope="col">#</th>
			<th scope="col"><?php _e('Title', 'quizmaker'); ?></th>
			<th scope="col"><?php _e('Score', 'quizmaker'); ?></th>
			<th scope="col"><?php _e('Percent', 'quizmaker'); ?></th>
			<th scope="col"><?php _e('Duration', 'quizmaker'); ?></th>
			<th scope="col"><?php _e('Date', 'quizmaker'); ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php if(isset($data) && $data): ?>
		<?php foreach($data as $index => $result): 

			$test	=	new QM_Test($result['test_id']); 
			$settings	=	$test->get_settings();
			?>
		<tr>
			<th scope="row"><?php echo $result['index']; ?></th>
			<td class="l"><a href="<?php echo get_permalink($test->get_id()); ?>" target="blank"><?php echo $test->get_title(); ?></a></td>
			<td class="c">
				<?php if($settings['show_score']): ?>
				<?php echo ($result['score'] > 0 ? $result['score'] : 0) . '/' . $result['total_score']; ?>	
				<?php endif; ?>
			</td>
			<td class="c">
				<?php if($settings['show_score']): ?>
				<?php echo ($result['percent'] > 0 ? $result['percent'] : 0); ?>%
				<?php endif; ?>
			</td>
			<td class="l"><?php echo $result['duration']; ?></td>
			<td class="c"><?php echo $result['date_start']; ?></td>
			<td class="c">
				<a href="<?php echo qm_get_endpoint_url( 'view-result', $test->get_id(), qm_get_page_permalink( 'myaccount' ) ); ?>"><?php _e('View All', 'quizmaker'); ?></a>
			</td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>

<?php if(isset($data) && $data): ?>
	<?php qm_get_template('global/pagination.php', $pagination); ?>
<?php endif; ?>

<?php do_action( 'quizmaker_after_my_account' ); ?>
	
	
</div>