<?php


function qm_question_get_settings( $post_id ){
	
	$settings 	=	get_post_meta($post_id, '_question_data', true);
	
	$settings	=	$settings ? $settings : array();
	
	$settings	=	wp_parse_args($settings, array(
		'score'	=>	10
	));
	
	return $settings;
}
	
function qm_question_set_answers( $questions = array(), $shuffle = false ){
	if( !$questions ) return array();
	
	if($questions){
		foreach($questions as &$question){
			
			$question->score		=	qm_get_post_meta( $question->ID, 'score' );
			$question->answer_type	=	qm_get_post_meta( $question->ID, 'answer-type' );
			$question->answers		=	qm_get_post_meta( $question->ID, 'answers' );
			$question->order_type	=	qm_get_post_meta( $question->ID, 'order_type' );
			$question->explanation	=	qm_get_post_meta( $question->ID, 'explanation' );

			$question->params 		=	qm_get_post_meta( $question->ID, 'params_' . $question->answer_type );
		}
		
		return $questions;
	}
	
	return array();
}

function qm_question_get_categories( $params )
{
	$categories	=	get_terms(array(
		'taxonomy' 		=> 'question_cat',
		'hide_empty'	=>	false
	));
	
	if( $categories ){

		foreach( $categories as $cat ) {
			
			$questions	=	get_posts(array(
				'post_type'			=>	'question',
				'post_status'		=>	'publish',
				'suppress_filters' 	=> true,
				'posts_per_page'	=>	-1,
				'tax_query'	=> array(
					array(
						'taxonomy'	=>	'question_cat',
						'field'		=>	'id',
						'terms'		=>	$cat->term_id
					)
				)
			));
			
			if(is_object($cat)){
				$cat->total_questions	=	count($questions);
			}
		}

	}
	
	return $categories;
}

function qm_question_by_ids( $ids = array() ) {

	if(!$ids || !is_array($ids)) return false;
	
	return get_posts(array(
		'posts_per_page'	=>	-1,
		'post_type'			=>	'question',
		'post__in' 			=> $ids,
		'orderby'			=>	'post__in'
	));
}

function qm_get_question_by_categories( $cids = array(), $limit = -1 ) {

	if(!$cids || !is_array($cids)) return false;

	$questions	=	get_posts(array(
		'post_type'			=>	'question',
		'post_status'		=>	'publish',
		'orderby'        	=> 'rand',
		'suppress_filters' 	=> 	true,
		'posts_per_page'	=>	$limit,
		'tax_query'	=> array(
			array(
				'taxonomy'	=>	'question_cat',
				'field'		=>	'id',
				'terms'		=>	$cids,
				'operator'	=>	'IN'
			)
		)
	));

	return $questions;
}

function qm_add_question_categories( $test_id )
{

	$selected_categories = array();

	$random_questions = get_post_meta( $test_id, '_randoms_questions', true );
	$fixed_questions  = get_post_meta( $test_id, '_fixed_questions', true );

	if( $random_questions )
	{
		$categories = $random_questions[ $random_questions['type'] ][ 'categories' ];

		if(isset($categories) && $categories){
			foreach( $categories as $key => $cat )
			{
				if($cat)
				{
					$selected_categories[] = $key;
				}
			}
		}
	}

	if( $fixed_questions )
	{
		$fixed_question_categories = array();

		foreach( $fixed_questions as $question_id )
		{
			$terms = get_the_terms( $question_id, 'question_cat' );

			if( $terms )
			{
				foreach( $terms as $term )
				{
					if( !in_array( $term->term_id, $selected_categories ) )
					{
						$selected_categories[] = $term->term_id;
					}


				}

				$fixed_question_categories[$question_id] = array('cat_id' => $terms[0]->term_id, 'cat_name' => $terms[0]->name);
			}
		}

		update_post_meta( $test_id, '_fixed_question_categories', $fixed_question_categories );
	}

	update_post_meta( $test_id, '_question_categories', $selected_categories );
}

function qm_question_get_fixed_categories( $test_id ) {

	$categories = get_post_meta( $test_id, '_fixed_question_categories', true );

	if( !$categories ) {

		qm_add_question_categories( $test_id );

		return get_post_meta( $test_id, '_fixed_question_categories', true );
	}

	return $categories;
}

function qm_question_get_fixed_items( $test_id )
{
	$fids 	=	get_post_meta($test_id, '_fixed_questions', true);
	
	if($fids)
	{
		$fixed_questions	=	get_posts(array(
			'posts_per_page'	=>	-1,
			'post_type'			=>	'question',
			'post__in' 			=> $fids,
			'orderby'			=>	'post__in'
		));

		if($fixed_questions)
		{

			$categories = qm_question_get_fixed_categories($test_id);

			foreach( $fixed_questions as &$question )
			{
				$question->params = array( 'id' => $question->ID );

				if( $categories ){
					foreach( $categories as $question_id => $category ) {
						if( $question->ID == $question_id )
						{
							$question->params['cat_id']		= $category['cat_id'];
							$question->params['cat_name'] 	= $category['cat_name'];
							
						}
					}	
				}
			}
		}

	}else{
		$fixed_questions	=	array();
	}
	
	return $fixed_questions;
}

function qm_question_get_random_items( $post_id )
{
	$randoms_questions 	=	qm_get_post_meta( $post_id, 'randoms_questions' );
	
	$randoms_questions	=	$randoms_questions ? $randoms_questions : array();
	
	$randoms_questions	=	wp_parse_args($randoms_questions, array(
		'type'	=>	'none',
		'selected' => array(
			'order' 		=>	'mixed',
			'position'		=>	'mixed_fquestions',
			'total'			=>	10,
			'categories'	=>	array()
		),
		'per'	=>	array(
			'order' 		=>	'mixed',
			'position'		=>	'mixed_fquestions',
			'categories'	=>	array()
		)
	));
	
	return $randoms_questions;
}

function qm_question_categories( $test_id )
{
	if(!isset($test_id) || !absInt($test_id)){ return false; }

	$randoms_categories =	$this->qm_question_get_random_items( $test_id );

	$fixed_questions 	=	$this->qm_question_get_fixed_items( $test_id );

	
}

function qm_question_get_random_doing_items( $post_id, $fixed_questions = array() )
{
	global $wpdb;
	
	$questions			=	array();

	$fixed_questions 	=	(isset($fixed_questions) && $fixed_questions) ? $fixed_questions : array();
	$random_questions	=	qm_question_get_random_items($post_id);
	
	if($random_questions['type'] == 'none') {
		return $questions;
	}

	$random_se 	=	array();

	$cat_ids	=	array();
	
	if(isset($random_questions[$random_questions['type']])){
		$random_se		=	$random_questions[$random_questions['type']];
	}
		
	if( $random_questions['type'] == 'per' ){
		
		$random_cats	=	$random_se['categories'];
		
		$cat_ids	=	array_keys( $random_cats );

		$categories = 	qm_get_categories( $cat_ids );
				
		foreach( $categories as $cat_id => $cat_name ){
			
			if( absInt($random_cats[$cat_id]) ){

				$question	=	get_posts(array(
					'post_type'			=>	'question',
					'post_status'		=>	'publish',
					'orderby' 			=> 'rand',
					'suppress_filters' 	=> true,
					'posts_per_page'	=>	absInt($random_cats[$cat_id]),
					'exclude'			=>	$fixed_questions,
					'tax_query'	=> array(
						array(
							'taxonomy'	=>	'question_cat',
							'field'		=>	'id',
							'terms'		=>	$cat_id
						)
					)
				));
				
				if($question)
				{
					$excludes	=	array_merge( $fixed_questions, qm_get_values_by_key( $question, 'ID' ) );

					foreach( $question as &$q )
					{ 
						
						$q->params = array('id' => $q->ID, 'cat_id' => $cat_id, 'cat_name' => $cat_name);
					}

					$questions	=	array_merge( $questions, $question );
				}
				
			}
		}
		
		if($random_se['order'] == 'mixed'){
			shuffle($questions);
		}
		
	}elseif( $random_questions['type'] == 'selected' ){

		if( isset($random_se['categories']) && $random_se['categories'] )
		{
			
			$orderby = 'menu_order';

			if($random_se['order'] == 'mixed'){
				$orderby = 'rand';
			}

			$questions	=	get_posts(array(
				'post_type'			=>	'question',
				'post_status'		=>	'publish',
				'suppress_filters' 	=>  true,
				'orderby'			=>	$orderby,
				'order'				=>	'ASC',
				'posts_per_page'	=>	$random_se['total'],
				'numberposts'		=>	$random_se['total'],
				'exclude'			=>	$fixed_questions,
				'tax_query'	=> array(
					array(
						'taxonomy'	=>	'question_cat',
						'field'		=>	'id',
						'terms'		=>	array_values($random_se['categories'])
					)
				)
			));

			if($questions)
			{
				foreach( $questions as &$q )
				{ 
					$q->params = array('id' => $q->ID);
				}

			}

		}
	}
	
	if($questions)
	{

		$random_se['type']		=	$random_questions['type'];
		$random_se['questions']	=	$questions;
		
		return $random_se;
	}
	
	return array();
}

function qm_get_question_params( $question_id, $question_params, $name = false )
{
	if(!isset($question_params) || is_array($question_params) || $question_params ) { return false; }

	foreach( $question_params as $params )
	{
		if(isset($params['id']) && $params['id'] == $question_id)
		{
			if( $name ){
				return $params[$name];
			}else{
				return $params;
			}
		}
	}
}

function qm_is_single_answers_correct($answers, $correct) {
	
	$is_correct = 0;

	if( !is_numeric($correct) || is_array($correct) ){
		return 0;
	}
	
	foreach($answers as $id => $ans){
		
		if($ans['is_correct'] == 1 && $correct == $id){
			$is_correct	=	1;
		}
	}
	
	return $is_correct;
}

function qm_is_multiple_answers_correct($answers = array(), $corrects = array()) {
	
	if( !$corrects || !is_array($corrects) || !is_array($answers) || !$answers ){
		return 0;
	}

	
	$num_corrects	=	count(array_filter( $answers, function( $value ){

		return $value['is_correct'] == 1;
	}));

	if($num_corrects != count($corrects)){

		return 0;
	}
	
	$is_correct = 1;

	foreach($answers as $id => $ans){
		if(in_array($id, $corrects)){
			if( $ans['is_correct'] == -1 ) {
				$is_correct	=	0;
			}
		}
	}
	
	return $is_correct;
}

function qm_is_fill_blank_answers_correct($answers, $corrects = '') {
	
	if(is_array($answers) || is_array($corrects)) return 0;
	
	$is_correct = strtolower($answers) == strtolower($corrects) ? 1 : 0;
	
	return $is_correct;
	
}

function qm_is_drag_match_answers_correct( $answers, $corrects = array() ) {
	
	$is_correct = 1;
	
	if( !$answers || !$corrects ) return 0;
		
	ksort($corrects);
	
	foreach( $answers as $key => $ans ) {
		
		foreach( $corrects as $id => $value ) {
			
			if($ans['id'] == $id ) {
				
				if( $ans['value'] != $value ) {
					
					$is_correct	=	0;
				}
			}
			
		}
		
	}
	
	return $is_correct;
}

function qm_is_group_match_answers_correct( $answers, $corrects = array() ) {

	$is_correct = 1;

	if( $answers && $corrects ) {

		foreach ($answers as $answer) {
			
			foreach ($corrects as $group_name => $values) {
				
				if( $values != '' ){
					if( sanitize_title( $answer['group'] ) == $group_name ) {

						if( $values ) {

							$values = explode(',', $values);

							if( $values ) {
								if( !in_array( $answer['id'], $values ) ) {

									$is_correct = 0;
								}
							}else{

								$is_correct = 0;
							}
						}
					}
				}else{

					$is_correct = 0;
				}

			}
		}

	}else{

		$is_correct = 0;
	}

	return $is_correct;
}

function qm_is_order_answers_correct( $answers, $corrects = array() ) {
	
	$is_correct = 1;
	
	if( !$answers || !$corrects ) return 0;
	
	$answers = array_values($answers);

	foreach( $answers as $key => $ans ) {
		
		if( absint($ans['id']) != absint($corrects[$key]) ){
			$is_correct = 0;
		}
	}
	
	return $is_correct;
}

function qm_is_guess_word_answers_correct($answers, $corrects = '') {
	
	if(!is_array($answers) || !is_array($corrects) || !$answers || !$corrects) return 0;

	$str_answers  = array();
	$str_corrects = array();

	foreach( $answers as $ans ) {

		$str_answers[] = strtolower( $ans['content'] );
	}

	array_walk( $corrects, function( &$value ){
		
		$value = strtolower( $value );
	});

		
	$result = array_diff( $str_answers, $corrects );
	
	if( empty( $result ) ) {

		return 1;

	}else{

		return 0;
	}
}

function qm_is_keywords_answers_correct($answers, $corrects = '', $params = array()) {
	
	if(is_array($corrects) || !$corrects) return 0;
	
	$num_corrects = 0;

	foreach( $answers as $word ) {
		
		if (strpos(strtolower($corrects), strtolower($word['content'])) !== false) {
			
		    $num_corrects++;
		}
	}
	
	if( $num_corrects >= $params['min_corrects'] ) {

		return 1;
	}

	return 0;
}

function qm_question_get_answer_type( $type = '' ) {
	
	$types	=	 apply_filters( 'qm_question_answer_types', array(
			'single'		=> __( 'Single choice', 'quizmaker' ),
			'multiple'		=> __( 'Multiple choices', 'quizmaker' ),
			'fill_blank'	=> __( 'Fill in the blank', 'quizmaker' ),
			'drag_match'	=> __( 'Item match', 'quizmaker' ),
			'group_match'	=> __( 'Group match', 'quizmaker' ),
			'order'			=> __( 'Order', 'quizmaker'),
			'guess_word'	=> __( 'Guess Word', 'quizmaker'),
			'keywords'		=> __( 'Keywords', 'quizmaker'),
		));
	
	if($type){
		
		return $types[$type];
		
	}else{
		
		return $types;
	}
}

function qm_question_is_instanted_answer( $question_id, $instant_answer ) {

	return false;
}

function qm_question_is_correct( $question_ans, $user_ans, $type = 'single', $question = false ) {

	$is_correct = false;

	switch( $type ){
		case 'single':
		
			$is_correct = qm_is_single_answers_correct( $question_ans, $user_ans );
			
			break;
		case 'multiple':
		
			$is_correct = qm_is_multiple_answers_correct( $question_ans, $user_ans );
			
			break;
		case 'fill_blank':
		
			$is_correct = qm_is_fill_blank_answers_correct( $question_ans, $user_ans );
			
			break;
		case  'drag_match':
			
			$is_correct = qm_is_drag_match_answers_correct( $question_ans, $user_ans );
			
			break;
		case  'group_match':
			
			$is_correct = qm_is_group_match_answers_correct( $question_ans, $user_ans );
			
			break;
		case  'order':
			
			$is_correct = qm_is_order_answers_correct( $question_ans, $user_ans );

			break;
		case  'guess_word':
			
			$is_correct = qm_is_guess_word_answers_correct( $question_ans, $user_ans );

			break;
		case  'keywords':
			
			$is_correct = qm_is_keywords_answers_correct( $question_ans, $user_ans, $question->params_keywords );

			break;
	}

	return $is_correct;
}

function qm_questions_get_results( $questions ) {

	if( !isset($questions) || !$questions ) { return array(); }

	$result = array( 'score' => 0, 'total' => 0, 'num_corrects' => 0, 'questions' => array() );

	foreach( $questions as &$question ) {

		$correct_answers = qm_get_post_meta( $question['id'], 'answers' );

		$question['is_correct'] =	qm_question_is_correct( $correct_answers, $question['value'], $question['type'] );
		$question['answer']		=	$correct_answers;

		$score = absint(qm_get_post_meta( $question['id'], 'score' ));

		$result['total'] += $score;

		if( $question['is_correct'] ) {

			$result['score'] += $score;

			$result['num_corrects']++;
		}
	}

	$result['percent']	=	round(($result['num_corrects'] * 100) / count( $questions ), 1);

	$result['questions'] = $questions;

	return $result;
}

function qm_questions_group_match_get_groups( $answers ) {

	$groups = array();

	if( $answers ) {

		foreach( $answers as $answer ) {

			if( !in_array( $answer['group'], $groups ) ) {

				$groups[sanitize_title($answer['group'])] = $answer['group'];
			}
		}
	}

	return $groups;
}

function qm_questions_group_match_is_play( $answers ) {

	$is_play = false;

	if( $answers ){

		foreach( $answers as $answer ) {

			if( $answer != '' ) {

				$is_play = true;
			}
		}
	}

	return $is_play;
}