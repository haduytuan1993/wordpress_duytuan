<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

?>

<?php do_action( 'quizmaker_before_single_test_ranking' ); ?>

<div class="qm-ranking">
	<h2 class="part-title"><?php _e('Ranking', 'quizmaker'); ?></h2>
	<table class="qm-table" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th class="c w-50">#</th>
				<th class="l"><?php _e('Name', 'quizmaker'); ?></th>
				<th class="c"><?php _e('Rank', 'quizmaker'); ?></th>
				<th class="c"><?php _e('Percent', 'quizmaker'); ?></th>
				<th class="c"><?php _e('Score', 'quizmaker'); ?></th>
			</tr>
		</thead>
		<tbody>
	<?php foreach( $results as $index => $result ): ?>
			<tr>
				<td class="c"><?php echo $index + 1; ?></td>
				<td class="l"><?php echo $result['user_name']; ?></td>
				
				<td class="c">
					<?php if(isset($result['ranking'])): ?>
					<?php echo $result['ranking']; ?>
					<?php endif; ?>
				</td>
				
				<td class="c"><?php echo $result['percent']; ?>%</td>
				<td class="c"><?php echo $result['score']; ?></td>
			</tr>
	<?php endforeach; ?>
		</tbody>
	</table>
</div>

<?php do_action( 'quizmaker_after_single_test_ranking' ); ?>