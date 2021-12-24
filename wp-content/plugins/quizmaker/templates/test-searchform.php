<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

<form role="search" method="get" class="quizmaker-test-search-form" action="<?php echo esc_url( qm_get_page_permalink('archive_test') ); ?>">
	
	<div class="form">
									
		<input type="search" id="quizmaker-test-search-field" class="search-field qm-search-tests" placeholder="<?php echo esc_attr_x( 'Search Tests&hellip;', 'placeholder', 'quizmaker' ); ?>" value="<?php echo get_search_query(); ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label', 'quizmaker' ); ?>" />
			
		<input type="hidden" name="post_type" value="test" />
		
	</div>
	
</form>