<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="qm-my-account-table__view-assigned-tests" class="container-myaccount-assigned-tests">

<?php do_action( 'quizmaker_before_my_account' ); ?>

<table cellpadding="0" cellspacing="0" class="table table-light qm-my-account-table__view-assigned-tests">
	<thead class="thead-light">
		<tr>
			<th scope="col">#</th>
			<th scope="col"><?php _e('Title', 'quizmaker'); ?></th>
			<th scope="col"><?php _e('Duration', 'quizmaker'); ?></th>
			<th scope="col"><?php _e('Attempt', 'quizmaker'); ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php if(isset($data) && $data): ?>
		<?php foreach($data as $index => $t): 
			
			$mtest = QM()->test_factory->get_test($t['id']);  ?>

		<tr>
			<th><?php echo $index + 1; ?></th>
			<td><a href="<?php echo get_permalink($mtest->get_id()); ?>" target="blank"><?php echo $mtest->get_title(); ?></a></td>
			<td scope="col"><?php echo $mtest->get_duration(); ?></td>
			<td scope="col"><?php echo $mtest->get_attempt(); ?></td>
			<td scope="col">
				<a href="<?php echo qm_get_endpoint_url( 'view-result', $mtest->get_id(), qm_get_page_permalink( 'myaccount' ) ); ?>"><?php _e('Results', 'quizmaker'); ?></a>
			</td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>

<?php qm_get_template('global/pagination.php', $pagination); ?>

<?php do_action( 'quizmaker_after_my_account' ); ?>

</div>