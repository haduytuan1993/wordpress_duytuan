<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="container container-myaccount-view-results">

<?php do_action( 'quizmaker_before_my_account' ); ?>

<h1 class="qm-myaccount-title"><?php _e('MEMBERSHIP', 'quizmaker'); ?></h1>

<div class="qm-myaccount-group-list">
	<div class="g">
		<div class="glabel"><?php _e('Name', 'quizmaker'); ?></div>
		<div class="gvalue"><?php echo $data['membership']->post_title; ?></div>
	</div>
	
	<div class="g">
		<div class="glabel"><?php _e('Price', 'quizmaker'); ?></div>
		<div class="gvalue">$<?php echo $data['membership']->price; ?></div>
	</div>
	
	<?php if($data['date_paid']): ?>
	<div class="g">
		<div class="glabel"><?php _e('Date', 'quizmaker'); ?></div>
		<div class="gvalue"><?php echo $data['date_paid']; ?></div>
	</div>
	<?php endif; ?>
	
	<?php if($data['expired']): ?>
	<div class="g">
		<div class="glabel"><?php _e('Expired', 'quizmaker'); ?></div>
		<div class="gvalue"><?php echo $data['expired']; ?></div>
	</div>
	<?php endif; ?>
	
	<div class="g">
		<div class="gvalue">
			<a href="<?php echo $payment_link; ?>" class="qm-myaccount-membership_submit"><?php _e( 'UPGRADE', 'quizmaker' ); ?></a>
		</div>
	</div>
</div>



<h2 class="qm-myaccount-title__group">HISTORY</h2>
<table cellpadding="0" cellspacing="0" class="qm-table qm-my-account-table qm-my-account-table_view_results">
	<thead>
		<tr>
			<th class="w-30">#</th>
			<th class="l"><?php _e('Name'); ?></th>
			<th class="w-150"><?php _e('Date'); ?></th>
			<th class="w-150"><?php _e('Expired'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if($data['history']): ?>
			<?php foreach($data['history'] as $index => $paid): ?>
		<tr>
			<td class="c"><?php echo $index + 1; ?></td>
			<td><?php echo $paid['membership']->post_title; ?></td>
			<td class="c"><?php echo $paid['date']; ?></td>
			<td class="c"><?php echo $paid['expired']; ?></td>
		</tr>
			<?php endforeach; ?>
		<?php else: ?>
		<tr>
			<td colspan="4" class="c"><?php _e('No Data', 'quizmaker'); ?></td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>

<?php do_action( 'quizmaker_after_my_account' ); ?>

</div>