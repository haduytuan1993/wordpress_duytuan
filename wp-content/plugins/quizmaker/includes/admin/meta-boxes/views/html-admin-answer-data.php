
<?php 

$answer_type_selector = apply_filters( 'answer_type_selector', qm_question_get_answer_type(), $answer_type );
		
$meta_type_box	=	'<label for="question-type"><select id="answer-type" name="answer-type"><optgroup label="' . esc_attr__( 'Answer Type', 'quizmaker' ) . '">';
		
foreach ( $answer_type_selector as $value => $label ) {
	$meta_type_box .= '<option value="' . esc_attr( $value ) . '" ' . selected( $answer_type, $value, false ) .'>' . esc_html( $label ) . '</option>';
}

$meta_type_box .= '</optgroup></select></label>';

 ?>
	
	
<?php include('html-admin-answer-data-single-tab.php'); ?>

<?php include('html-admin-answer-data-multiple-tab.php'); ?>

<?php include('html-admin-answer-data-fillblank-tab.php'); ?>

<?php include('html-admin-answer-data-dragmatch-tab.php'); ?>

<?php include('html-admin-answer-data-groupmatch-tab.php'); ?>

<?php include('html-admin-answer-data-order-tab.php'); ?>

<?php include('html-admin-answer-data-guess-word-tab.php'); ?>

<?php include('html-admin-answer-data-keywords-tab.php'); ?>

<?php apply_filters( 'html-admin-answer-data-tab', $thepostid, $answer_type, $answers ); ?>
			
<span class="meta-type-box"> &mdash;  <?php echo $meta_type_box; ?> </span>