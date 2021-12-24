<?php

global $post;

$show_question_title	=	$settings['show_question_title'];

?>

<div class="questions qm-page qm-page-<?php echo $page + 1; ?>" data-index="<?php echo $page + 1; ?>">
	
	<?php do_action('quizmaker_before_doing_questions'); ?>

	<?php qm_get_template('content-questions.php', array(
		'questions' => $questions, 
		'question_params' => $question_params, 
		'page' => $page, 
		'order' => $order, 
		'settings' => $settings)); ?>

	<?php do_action('quizmaker_after_doing_questions'); ?>

</div>