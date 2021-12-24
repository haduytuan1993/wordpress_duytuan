<h1 itemprop="name" class="entry-title"><?php the_title(); ?></h1>

<form name="quizmaker_start_test" action="<?php echo esc_url(qm_get_start_test_url()); ?>" method="post" enctype="multipart/form-data">
	
	<button type="submit" name="quizmaker_start_test" value="1">START</button>
	<input type="hidden" name="id" value="<?php the_ID(); ?>"/>
</form>