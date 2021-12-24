<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'quiz' ); ?>

	<?php if ( apply_filters( 'quizmaker_show_page_title', true ) ) : ?>

	<div class="pt-4 pb-4 mb-4 page-header-wrapper">

		<div class="container">

			<h3 class="page-title mb-0"><?php quizmaker_page_title(); ?></h3>
		</div>

	</div>

	<?php endif; ?>
	
	<?php do_action( 'quizmaker_before_main_content' ); ?>
	
	<div class="container">
		<div class="row">
	<?php while ( have_posts() ) : the_post(); ?>

		<?php qm_get_template_part( 'content', 'single-test' ); ?>

	<?php endwhile; // end of the loop. ?>

			<?php do_action( 'quizmaker_after_main_content' ); ?>

		</div>
	</div>
	

<?php get_footer( 'quiz' ); ?>