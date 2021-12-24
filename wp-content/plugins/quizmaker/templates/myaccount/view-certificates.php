<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="qm-my-account-table__view-certificates" class="container-myaccount-view-certificates">

<?php do_action( 'quizmaker_before_my_account' ); ?>

<table cellpadding="0" cellspacing="0" class="table table-light qm-my-account-table_view_results">
	<thead class="thead-light">
		<tr>
			<th>#</th>
			<th class="c"><?php _e('Title', 'quizmaker'); ?></th>
			<th class="c"><?php _e('Score', 'quizmaker'); ?></th>
			<th class="c"><?php _e('Percent', 'quizmaker'); ?></th>
			<th class="c"><?php _e('Duration', 'quizmaker'); ?></th>
			<th class="c"><?php _e('Date', 'quizmaker'); ?></th>
			<th class="c"><?php _e('Attempt', 'quizmaker'); ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>

		
		
	</tbody>
</table>

<?php do_action( 'quizmaker_after_my_account' ); ?>

</div>