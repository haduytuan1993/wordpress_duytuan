<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

?>

<?php do_action( 'quizmaker_before_single_test_ranking' ); ?>

<div id="test-<?php the_ID(); ?>-ranking" <?php post_class(); ?>>
	<table>
		<thead>
			<tr>
				<th><?php _e('Ranking', 'quizmaker'); ?></th>
				<th><?php _e('User', 'quizmaker'); ?></th>
				<th><?php _e('Score', 'quizmaker'); ?></th>
			</tr>
		</thead>
		<tbody>
	<?php foreach( $results as $index => $result ): ?>
			<tr>
				<td><?php echo $index + 1; ?></td>
				<td><?php echo $result['user_nicename']; ?></td>
				<td><?php echo $result['score']; ?></td>
			</tr>
	<?php endforeach; ?>
		</tbody>
	</table>
</div>

<?php do_action( 'quizmaker_after_single_test_ranking' ); ?>