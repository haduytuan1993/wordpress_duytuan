<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class QM_Meta_Box_Answer_Data {
	
	public static function output( $post ) {
		global $post, $thepostid;
		
		wp_nonce_field( 'quizmaker_save_data', 'quizmaker_meta_nonce' );
		
		$thepostid = $post->ID;

		$answer_type	=	qm_get_post_meta($post->ID, 'answer-type');
		$answers 		=	qm_get_post_meta($post->ID, 'answers');
		
		$answer_type_selector = apply_filters( 'answer_type_selector', qm_question_get_answer_type(), $answer_type );
		
		
		include('views/html-admin-answer-data.php');
	}
	
	/**
	 * Save meta box data.
	 */
	public static function save( $post_id, $post ) {
		
		$answer_type    = empty( $_POST['answer-type'] ) ? 'nonce' : sanitize_title( stripslashes( $_POST['answer-type'] ) );
		
		qm_update_post_meta( $post_id, array('answer-type' => $answer_type) );
		
		switch($answer_type){
			case 'single':
				if(isset($_POST['answers_single'])){
					
					$answers		=	$_POST['answers_single'];
					$ans_id_correct	=	absInt($_POST['answers_single_is-correct']);

					$params 		=	isset($_POST['answers_params_single']) ? $_POST['answers_params_single'] : array('columns' => 2);
					
					// assign correct answer
					if(isset($ans_id_correct)){
						foreach($answers as $id => &$value){
							if($id == $ans_id_correct){
								$value['is_correct']	=	1;
							}else{
								$value['is_correct']	=	-1;
							}
						}
					}
					
					qm_update_post_meta( $post_id, array('answers' => $answers, 'params_single' => $params) );
				}
			break;
			case 'multiple':
			
				if(isset($_POST['answers_multiple']) && $_POST['answers_multiple']){
				
					$answers		=	$_POST['answers_multiple'];
					$ans_id_correct	=	$_POST['answers_multiple_is-correct'];

					$params 		=	isset($_POST['answers_params_multiple']) ? $_POST['answers_params_multiple'] : array('columns' => 2);
					
					// assign correct answer
					if($ans_id_correct){
						foreach($answers as $id => &$value){
							if(in_array($id, $ans_id_correct)){
								$value['is_correct']	=	1;
							
							}else{
								$value['is_correct']	=	-1;
							}
						}
					}
					
					qm_update_post_meta( $post_id, array('answers' => $answers, 'params_multiple' => $params) );
				}
			break;
			case 'fill_blank':

				if(isset($_POST['anwsers_fill_blank']) && $_POST['anwsers_fill_blank']){
					
					qm_update_post_meta( $post_id, array('answers' => $_POST['anwsers_fill_blank']) );
				}
			break;
			case 'drag_match':
				
				if(isset($_POST['answers_drag_match']) && $_POST['answers_drag_match']){
					
					$answers	=	$_POST['answers_drag_match'];
					
					foreach($answers as $key => &$ans) {
						$ans['id'] 		=	$key;
						$ans['value']	=	md5($key . $ans['content']);
					}
					

					$params  =	isset($_POST['answers_params_drag_match']) ? $_POST['answers_params_drag_match'] : array('style' => 'style-left-to-right');

					qm_update_post_meta( $post_id, array('answers' => $answers, 'params_drag_match' => $params) );
				}
			break;
			case 'group_match':
				
				if(isset($_POST['answers_group_match']) && $_POST['answers_group_match']){
					
					$answers	=	$_POST['answers_group_match'];
					
					foreach($answers as $key => &$ans) {
						
						$ans['id'] 		=	$key;
						$ans['group'] 	=	trim($ans['group']);
						$ans['item']	=	trim($ans['item']);
					}

					$params  =	isset($_POST['answers_params_group_match']) ? $_POST['answers_params_group_match'] : array('style' => 'style-left-to-right');
					
					qm_update_post_meta( $post_id, array('answers' => $answers, 'params_group_match' => $params));
				}
			break;
			case 'order':

				if(isset($_POST['answers_order']) && $_POST['answers_order']){
					
					$answers		=	$_POST['answers_order'];

					$params 		=	isset($_POST['answers_params_order']) ? $_POST['answers_params_order'] : array('style' => 'style-line');
					
					foreach($answers as $key => &$ans) {
						$ans['id'] 		=	$key;
					}
					
					qm_update_post_meta( $post_id, array('answers' => $answers, 'params_order' => $params) );
				}
			break;
			case 'guess_word':

				if(isset($_POST['answers_guess_word']) && $_POST['answers_guess_word']){
				
					$answers		=	$_POST['answers_guess_word'];
					$ans_id_correct	=	$_POST['answers_guess_word_is-correct'];
					
					// assign correct answer
					if($ans_id_correct){
						foreach($answers as $id => &$value){
							if(in_array($id, $ans_id_correct)){
								$value['is_correct']	=	1;
							
							}else{
								$value['is_correct']	=	-1;
							}
						}
					}
					
					qm_update_post_meta( $post_id, array( 'answers' => $answers ) );
				}
			break;
			case 'keywords':

				if(isset($_POST['answers_keywords']) && $_POST['answers_keywords']){
				
					$answers		=	$_POST['answers_keywords'];
					
					$params 		=	isset($_POST['answers_params_keywords']) && $_POST['answers_params_keywords'] > 0 ? $_POST['answers_params_keywords'] : array('min_corrects' => 1);
					
					if( $params['min_corrects'] > count($answers) ) {

						$params['min_corrects'] = count($answers);

					}elseif( $params['min_corrects'] < 0 ) {

						$params['min_corrects'] = 1;
					}
					
					qm_update_post_meta( $post_id, array( 'answers' => $answers, 'params_keywords' => $params ) );
				}

			break;
		}

		apply_filters('meta-box-answer-data-save', $post_id, $_POST);
	}
}