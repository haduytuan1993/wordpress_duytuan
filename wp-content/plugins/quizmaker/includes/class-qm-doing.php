<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class QM_Doing {
	
	public $id;
	
	private $_result_tbl = 'quizmaker_results';
		
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	}
	
	public function __construct ()
	{
		global $wpdb;
		
		$this->_result_tbl	=	$wpdb->prefix . $this->_result_tbl;
	}

	public function start_test() 
	{	
		global $wpdb, $test;
		
		$test_id	=	absInt($_POST['id']);
		$user_id	=	get_current_user_id();
		$type_start	=	absInt($_POST['quizmaker_start_test']);
		
		do_action( 'quizmaker_before_start_test', $test_id );
		
		if( $type_start == 1 && !qm_can_do_test($test_id, $user_id) ) {
			
			qm_add_message( __( 'Sorry! You have not permession do this test', 'quizmaker' ), 'error' );
			
			wp_safe_redirect( get_permalink($test_id) );
			exit;
			
		}elseif( $type_start == 0 && qm_can_do_test_as_guest($test_id) ) {
			
			$user_id = 0;
		}
				
		$test	=	QM()->test_factory->get_test($test_id);

		$doing_data	=	$test->get_doing_data();
		
		if( $doing_data ){
			
			// get ids all questions separate by page
			$questions	=	$doing_data['page_question'];

			// get the params of all questions
			$question_params 	=	isset($doing_data['question_params']) ? $doing_data['question_params'] : array();

			$settings	=	$doing_data['settings'];
			$ids		=	$doing_data['ids_question'];
			
			$session    = 	new QM_Test_Session();
			
			$session_data	=	array(
				'uid'					=>	$user_id,
				'tid'					=>	$test->get_id(),
				'questions'				=>	$questions,
				'question_params' 		=>	$question_params,
				'settings'				=>	$settings,
				'ids'					=>	$ids,
				'duration'				=>	$test->get_duration(false),
				'instant_answers'		=>	array(),
				'doing_questions'		=>	array()
			);
			
			$session_data['time_start']		=	qm_get_date('now')->format('U');
			$session_data['time_passed']	=	0;
			
			if( $test->get_type() == 1 ){

				$session_data['adaptive_max_round']	=	$settings['adaptive_max_round'];
				$session_data['adaptive_times']		=	$settings['adaptive_times'];
				$session_data['adaptive_round']		=	1;
				$session_data['adaptive_questions']	=	array();
				$session_data['adaptive_percent']	=	0;

			}elseif ( $test->get_type() == 2 ) {

				$session_data['adaptive_max_round']	=	0;
				$session_data['adaptive_times']		=	1;
				$session_data['adaptive_round']		=	1;
				$session_data['adaptive_questions']	=	array();
				$session_data['adaptive_percent']	=	0;

				$session_data['settings']['adaptive_times'] 	= 1;
				$session_data['settings']['adaptive_max_round'] = 0;

			}

			$session->set('doing', apply_filters( 'quizmaker_start_doing_session_data', $session_data ));
			
			if( $user_id ) {
				
				qm_user_increase_attempt( $user_id, $test_id );
			}
			
			wp_redirect( 
				apply_filters( 'quizmaker_start_doing_redirect', add_query_arg( 'doing', 1, $test->get_permalink() ) ) 
			);
			exit;
		}
		
	}
	
	public function doing_questions( $page )
	{	
		global $test;
		
		ob_start();
				
		$session    = 	new QM_Test_Session();

		$session_data					=	$session->get('doing');

		$settings						=	$session_data['settings'];

		$test_id						=	absInt($session_data['tid']);
				
		$duration						=	qm_get_post_meta( $test_id, 'duration' );
		$duration						=	$duration ? $duration : 0;
		
		$session_data['time_passed']	=	qm_get_date('now')->format('U');
		
		if($duration > 0 && qm_is_expired( $session_data['time_start'], $session_data['time_passed'], $duration )) {
			
			$this->_reset_test();
			
			wp_send_json( array('is_expired' => 1) );
			exit();
		}
		
		$session->set('doing', $session_data);
		
		$page	=	$page ? $page - 1 : 0;
		
		if( isset($session_data['questions']) && $session_data['questions'] ){
			
			$ids				=	$session_data['questions'][$page];

			$question_params 	=	isset($session_data['question_params']) ? $session_data['question_params'] : array();

			$from_order			=	($page * $settings['display_perpage']) + 1;
			
			// Get questions by id from start session
			$questions	=	qm_get_doing_questions(array('ids' => $ids), $settings['is_shuffle_answers']);


			foreach($questions as $mqid => $mq){

				$session_data['doing_questions'][] = $questions[$mqid];
			}
			
			$args = array(
				'questions' 		=>	$questions,
				'page'				=>	$page,
				'order'				=>	$from_order,
				'settings'			=>	$session_data['settings'],
				'instant_answers'	=>	$session_data['instant_answers'],
				'question_params'	=>	$question_params
			);
			
			qm_get_template('single-test/doing/questions.php', $args);
			
		}
		
		$questions_html	=	ob_get_clean();
		
		$session->set('doing', $session_data);
		
		$data	=	array('questions_html' => $questions_html);
		
		wp_send_json( $data );
	}
	
	public function submit_test() {
		
		global $wpdb;
		
		$time_end		=	date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);

		$session    = 	new QM_Test_Session();
		
		$session_data	=	$session->get('doing');
		
		if(!$session_data) return false;

		$settings			=	$session_data['settings'];

		$test_id			=	$session_data['tid'];

		$test				=	QM()->test_factory->get_test($test_id);

		$type_testing		=	qm_get_type_testing( $test_id );

		$correct_questions	=	qm_get_doing_questions( $session_data );
		$doing_questions	=	$session_data['doing_questions'];

		foreach($correct_questions as &$cq){

			foreach($session_data['doing_questions'] as $dq){
				
				if($cq->ID == $dq->ID && !in_array($cq->answer_type, array('order', 'drag_match'))){

					$cq->answers = $dq->answers;
				}	
			}
		}

		$user_questions		=	$_POST['q'];
		
		$result 			=	$this->_check_questions( $correct_questions, $user_questions );

		$result_id			=	false;
		
		// Adaptive Test
		if( $type_testing == 1 ){

			$max_round		=	$settings['adaptive_max_round'];

			$doing_data		=	$test->get_doing_adaptive_data( $correct_questions, $result['answers'] );

			$session_data['adaptive_round']	=	isset($session_data['adaptive_round']) ? absint($session_data['adaptive_round']) : 1;

			// Next Round
			if( $doing_data['page_question'] && ( $max_round == 0 || $session_data['adaptive_round'] < $max_round ) ) {

				$session_data['questions']				=	$doing_data['page_question'];
				$session_data['ids']					=	$doing_data['ids_question'];
				$session_data['adaptive_questions']		=	$doing_data['adaptive_questions'];
				$session_data['adaptive_percent']		=	$doing_data['adaptive_percent'];
				
				if(!isset($session_data['results'])){

					$session_data['results']	=	array( 'answers' => array() );

					$all_questions_id	=	qm_get_values_by_key( $correct_questions, 'ID' );

					foreach( $correct_questions as $q ) {

						$session_data['results']['answers'][$q->ID] = array(
							'is_correct' => 1,
							'answers' => $q->answers
						);
					}

				}

				++$session_data['adaptive_round'];

				// echo '<pre/>';
				// var_dump($session_data['adaptive_round']); exit;
				$session->set('doing', $session_data);

				wp_redirect( qm_get_doing_test_url() );

				exit;

			// Calculate result
			}else{
				

					$num_corrects			=	$doing_data['adaptive_num_corrects'];
					$ids_corrects			=	$doing_data['adaptive_ids_corrects'];

					$results 	=	$session_data['results'];

					// get time user begin from session
					$time_start				=	$session_data['time_start'] ? qm_get_date( (int)$session_data['time_start'] ) : false;
					$time_end				=	qm_get_date('now');

					// all results
					$adaptive_questions		=	$doing_data['adaptive_questions'];

					// Time doing for storing
					$results['date_start']	=	$time_start->format('Y-m-d H:i:s');
					$results['date_end']	=	$time_end->format('Y-m-d H:i:s');
					$results['duration']	=	qm_get_duration( $results['date_start'], $results['date_end'] );

					$results['score']		=	qm_test_get_scores( $ids_corrects );
					$results['total_score']	=	qm_test_get_scores( qm_get_values_by_key( $adaptive_questions, 'id' ) );

					$results['percent']		=	$doing_data['adaptive_percent'];

					$results['others']		=	array(
						'adaptive_round' => $session_data['adaptive_round'],
						'questions' 	 =>	$session_data['question_params']
					);

					
					$session_results		=	array();
					
					$session_results[$session_data['tid']]	=	$results;
					
					$result_id = 0;

					if( apply_filters( 'quizmaker_can_store_result', true, $results, $test_id ) ) {
				
						$result_id	=	$this->_store_results( $results, $test_id );
						
						if( $result_id ){
							
							$test->update_played();
							
							do_action( 'quizmaker_after_submit_result', $result_id, $test_id );
							
							if( is_email_result($test_id) ){
								do_action( 'quizmaker_email_new_result', $result_id );
							}

							
						}

						
					}
					
					$session->set('doing', array());
					$session->set('test_id', $test_id);
					$session->set('result_id', $result_id);
					$session->set('result', $results);

			}
			
		// Infinite Test
		}elseif( $type_testing == 2 ){

			$max_round		=	$settings['adaptive_max_round'];

			$doing_data		=	$test->get_doing_infinite_data( $correct_questions, $result['answers'] );
			
			$session_data['adaptive_round']	=	isset($session_data['adaptive_round']) ? absint($session_data['adaptive_round']) : 1;

			// Next Round
			if( $doing_data['page_question'] && ( $max_round == 0 || $session_data['adaptive_round'] < $max_round ) ) {

				$session_data['questions']				=	$doing_data['page_question'];
				$session_data['ids']					=	$doing_data['ids_question'];
				$session_data['adaptive_questions']		=	$doing_data['adaptive_questions'];
				$session_data['adaptive_percent']		=	$doing_data['adaptive_percent'];
				
				if(!isset($session_data['results'])){

					$session_data['results']	=	array( 'answers' => array() );

					$all_questions_id	=	qm_get_values_by_key( $correct_questions, 'ID' );

					foreach( $correct_questions as $q ) {

						$session_data['results']['answers'][$q->ID] = array(
							'is_correct' => 1,
							'answers' => $q->answers
						);
					}

				}

				++$session_data['adaptive_round'];
				

				$session->set('doing', $session_data);

				wp_redirect( qm_get_doing_test_url() );

				exit;

			// Calculate result
			}else{
				
					$num_corrects			=	$doing_data['adaptive_num_corrects'];
					$ids_corrects			=	$doing_data['adaptive_ids_corrects'];

					$results 	=	$session_data['results'];
					
					foreach( $correct_questions as $q ) {

						$results ['answers'][$q->ID] = array(
							'is_correct' => 1,
							'answers' => $q->answers
						);
					}


					
					// get time user begin from session
					$time_start				=	$session_data['time_start'] ? qm_get_date( (int)$session_data['time_start'] ) : false;
					$time_end				=	qm_get_date('now');

					// all results
					$adaptive_questions		=	$doing_data['adaptive_questions'];

					// Time doing for storing
					$results['date_start']	=	$time_start->format('Y-m-d H:i:s');
					$results['date_end']	=	$time_end->format('Y-m-d H:i:s');
					$results['duration']	=	qm_get_duration( $results['date_start'], $results['date_end'] );

					$results['score']		=	qm_test_get_scores( $ids_corrects );
					$results['total_score']	=	qm_test_get_scores( qm_get_values_by_key( $adaptive_questions, 'id' ) );

					$results['percent']		=	$doing_data['adaptive_percent'];
					
					$results['others']		=	array(
						'adaptive_round' => $session_data['adaptive_round'],
						'questions' 	 =>	$session_data['question_params']
					);

					$session_results		=	array();
					
					$session_results[$session_data['tid']]	=	$results;
					
					$result_id = 0;

					if( apply_filters( 'quizmaker_can_store_result', true, $results, $test_id ) ) {
				
						$result_id	=	$this->_store_results( $results, $test_id );
						
						if( $result_id ){
							
							$test->update_played();
							
							do_action( 'quizmaker_after_submit_result', $result_id, $test_id );
							
							if( is_email_result($test_id) ){
								do_action( 'quizmaker_email_new_result', $result_id );
							}

							
						}

						
					}
					
					$session->set('doing', array());
					$session->set('test_id', $test_id);
					$session->set('result_id', $result_id);
					$session->set('result', $results);

			}

		}else{
			
			$time_start		=	$session_data['time_start'] ? qm_get_date( (int)$session_data['time_start'] ) : false;
			$time_end		=	qm_get_date('now');
			
			$duration		=	$settings['duration'];
			
			$result['date_start']					=	$time_start->format('Y-m-d H:i:s');
			$result['date_end']						=	$time_end->format('Y-m-d H:i:s');

			$result['duration']						=	qm_get_duration( $result['date_start'], $result['date_end'] );

			if( $duration > 0 ) {

				$dump_1 = $time_start;

				date_add($dump_1, date_interval_create_from_date_string(($duration * 60) . ' seconds'));

				$dump_2 = qm_get_seconds( $result['date_end'], $dump_1->format('Y-m-d H:i:s') );

				if($time_end >= $dump_1) {

					$result['duration']	=	qm_get_duration( $result['date_start'], $dump_1->format('Y-m-d H:i:s') );
				}
			}

			$result['others']['questions']  =	$session_data['question_params'];
						
			$result['orders']['is_view'] 	=	0;
			
			$session_results	=	array();
			
			$session_results[$session_data['tid']]	=	$result;
			
			$result_id = 0;
			
			if( apply_filters( 'quizmaker_can_store_result', true, $result, $test_id ) ) {
				
				$result_id	=	$this->_store_results( $result, $test_id );
				
				if( $result_id ){
					
					$test->update_played();
					
					do_action( 'quizmaker_after_submit_result', $result_id, $test_id );
					
					if( is_email_result($test_id) ){
						do_action( 'quizmaker_email_new_result', $result_id );
					}

					
				}

				
			}

			do_action( 'quizmaker_after_submit_doing', $test, $result, $result_id );
			
			$session->set('doing', array());
			$session->set('test_id', $test_id);
			$session->set('result_id', $result_id);
			$session->set('result', $result);
		}

		wp_redirect( apply_filters( 'quizmaker_doing_submit_redirect', $test->get_result_permalink( $result_id ) ) );
		exit;
	}

	private function _check_questions( $correct_questions, $user_questions ) {

		if( !$correct_questions || !$user_questions ) {
			return false;
		}
		
		$result		=	array(
			'answers' => array(), 
			'percent' => 0, 
			'total_score' => 0, 
			'score' => 0
		);

		$num_corrects	=	0;
		$num_completed  =   0;
		$total			=	0;

		foreach($correct_questions as &$q_1){
			
			$total++;

			if($user_questions){
				foreach($user_questions as $id => $ans){
				
					if($q_1->ID == $id){


						$q_1->is_correct = qm_question_is_correct( $q_1->answers, $ans, $q_1->answer_type, $q_1 );
										
						if( !in_array($q_1->answer_type, array(
							'order', 'drag_match', 'guess_word', 'keywords', 'group_match'
						)) && is_array($q_1->answers)){
							foreach($q_1->answers as $qai => &$qans){

								if(!is_array($ans)){
									$ans = array( $ans );
								}

								if(in_array($qai, $ans)){

									$qans['user_check'] = 1;
								}
							}
						}

						if( $q_1->answer_type == 'guess_word' 
							|| $q_1->answer_type == 'keywords' 
							|| $q_1->answer_type == 'group_match' 
							|| $q_1->answer_type == 'fill_blank'
							
						) { $q_1->answers = $ans; }

						$q_1->score = apply_filters( 'quizmaker_submit_loop_score_question', $q_1->score, $q_1, $ans );
						
						$result['answers'][$q_1->ID]	=	apply_filters( 'quizmaker_submit_loop_store_question', array( 
							'answers' 		=>	$q_1->answers,
							'is_correct'	=>	$q_1->is_correct,
							'score'			=>	$q_1->score,
							'params' 		=>	qm_get_question_params( $q_1->ID, $q_1->params )
						), $q_1 );

						$num_completed++;
					
					}
				
				}
				
			}else{
				
				$q_1->is_correct	=	false;
			}
			
			if( apply_filters( 'quizmaker_submit_loop_is_correct_question', $q_1->is_correct, $q_1 ) ) {

				$result['score']	+=	$q_1->score;
				$num_corrects++;
			}
			
			$result['total_score']	+=	$q_1->score;

			
		}

		$result['score']		=	apply_filters( 'quizmaker_submit_result_score', $result['score'], $correct_questions, $user_questions );

		$result['total_score'] 	= 	apply_filters( 'quizmaker_submit_total_score', $result['total_score'], $correct_questions, $user_questions );

		$result['percent']		=	apply_filters( 'quizmaker_submit_percent', 
			round(($num_corrects * 100) / count($correct_questions), 2), 
			$num_corrects, 
			count($correct_questions), 
			$result['score'], 
			$result['total_score']
		);
		
		$result['others']	=	array( 'completed' => $num_completed, 'total' => $total );

		return $result;
	}

	private function _store_results( $result = array(), $test_id ) {

		if( !$result || !$test_id ){
			return false;
		}

		$result['test_id']		=	absInt( $test_id );
		$result['user_id']		=	0;

		$current_user = wp_get_current_user();

		if ( ($current_user instanceof WP_User) && $current_user->ID > 0 ) {

			$result['user_id']	  =	$current_user->ID;
			
			$result['user_email'] = $current_user->user_email;

			$result['user_name']  = qm_get_formated_user_name( $current_user );
		}
				
		if( $result_answers = $result['answers'] ) {
			
			$result_answers = qm_object_to_array( $result_answers );
							
			$result['answers']	=	json_encode( $result_answers );
		}

		if( isset($result['others']) )
		{
			$result['others']	=	json_encode( $result['others'] );
		}
		
		$table_result	=	new QM_Table_Result();
		
		$table_result->save($result);
		
		return $table_result->getId();
	}

	private function _reset_test() {

		$session    = 	new QM_Test_Session();
		
		$session->set('doing', array(
			'tid'			=>	0,
			'questions'		=>	array(),
			'settings'		=>	array(),
			'ids'			=>	array(),
			'time_start'	=>	'',
			'time_passed'	=>	0,
			'results'		=>	array()
		));
		
		$session->destroy_session();
	}
	
}