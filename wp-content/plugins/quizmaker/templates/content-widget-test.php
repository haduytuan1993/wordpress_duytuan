<?php 

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $test; 

$thumbnail	=	quizmaker_get_test_thumbnail();

?>

<a href="<?php echo esc_url( get_permalink( $test->id ) ); ?>" title="<?php echo esc_attr( $test->get_title() ); ?>"  class="list-group-item list-group-item-action flex-column align-items-start <?php echo $thumbnail ? 'is-thumbnail' : ''; ?>">
	
	<div class="d-flex w-100 justify-content-between">

		<?php if($display_date): ?>
		<small><?php echo $test->get_date_created(); ?></small>
		<?php endif; ?>

		

	</div>

<?php if($display_thumbnail): ?>
		<?php
			$thumbnail	=	quizmaker_get_test_thumbnail();
			
			if($thumbnail){
				
				echo '<div class="mt-2 mb-2 item-thumbnail-wide">' . $thumbnail . '</div>';
			}
			
			?>
		<?php endif; ?>	

		<?php if($display_title): ?>
		<h5 class="mt-2 mb-1">
				<?php echo $test->get_title(); ?>
		</h5>
		<?php endif; ?>

		<?php if($display_description): ?>
		<?php quizmaker_template_loop_test_excerpt(); ?>
		<?php endif; ?>
</a>