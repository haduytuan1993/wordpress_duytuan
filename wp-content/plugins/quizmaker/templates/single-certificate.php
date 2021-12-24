<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'quiz' ); ?>
	
	<?php do_action( 'quizmaker_before_main_content' ); ?>
	
	<div class="container">
	<?php while ( have_posts() ) : the_post(); ?>

		<?php qm_get_template_part( 'content', 'single-certificate' ); ?>

	<?php endwhile; // end of the loop. ?>
	</div>
	<?php do_action( 'quizmaker_after_main_content' ); ?>

<?php get_footer( 'quiz' ); ?>