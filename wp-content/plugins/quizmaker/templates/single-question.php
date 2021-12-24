<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'quiz' ); 


$questions = qm_get_doing_questions( ['ids' => [get_the_ID()], 'question_params' => [['id' => get_the_ID()]]] );

$settings = ['is_timeout_answer' => 0, 'show_explanation' => 1];

?>

	<?php if ( apply_filters( 'quizmaker_show_page_title', true ) ) : ?>

	<div class="pt-4 pb-4 mb-4 page-header-wrapper">

		<div class="container">

			<h3 class="page-title mb-0"><?php _e('Question', 'quizmaker'); ?></h3>
		</div>

	</div>

	<?php endif; ?>
	
	<?php do_action( 'quizmaker_before_main_content' ); ?>
	
	<div class="container">
		
		<?php qm_get_template('content-questions.php', [
			'questions' => $questions, 
			'question_params' => [['id' => get_the_ID(), 'answer' => 0]],
			'settings' => $settings,
			'q_key' => 0,
			'order' => 1
		]) ?>

	</div>

<?php get_footer( 'quiz' ); ?>