<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'quizmaker' ); ?>

	<?php do_action( 'quizmaker_before_main_content' ); ?>
	
		<div class="pt-4 pb-4 mb-4 page-header-wrapper">

			<div class="container">
				<h3 class="page-title mb-0"><?php quizmaker_page_title(); ?></h3>
			</div>

		</div>
		
		<div class="container">

			<div class="row">
		<?php do_action( 'quizmaker_left_sidebar' ); ?>
		
		<?php if ( have_posts() ) : ?>
			<div <?php quizmaker_container_class(); ?>>

				<?php echo apply_filters( 'quizmaker_wrap_before_nav', '' ); ?>

				<?php do_action( 'quizmaker_before_test_loop' ); ?>

				<?php echo apply_filters( 'quizmaker_wrap_after_nav', '' ); ?>
			
			<?php quizmaker_test_loop_start(); ?>
				
				<?php while ( have_posts() ) : the_post(); ?>

					<?php qm_get_template_part( 'content', 'test' ); ?>
					
				<?php endwhile; ?>
				
			<?php quizmaker_test_loop_end(); ?>
				
			<?php do_action( 'quizmaker_after_test_loop' ); ?>
			</div>
		<?php endif; ?>
		
		<?php do_action( 'quizmaker_right_sidebar' ); ?>

			</div>
		</div>
		
	<?php do_action( 'quizmaker_after_main_content' ); ?>

<?php get_footer( 'quizmaker' ); ?>