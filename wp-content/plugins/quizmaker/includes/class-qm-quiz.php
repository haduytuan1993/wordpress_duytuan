<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

class QM_Quiz {
	
	public $id = 0;
	
	public $post = null;
	
	public function __construct( $test ) {
		if ( is_numeric( $test ) ) {
			$this->id   = absint( $test );
			$this->post = get_post( $this->id );
		} elseif ( $test instanceof QM_Quiz ) {
			$this->id   = absint( $test->id );
			$this->post = $test->post;
		} elseif ( isset( $test->ID ) ) {
			$this->id   = absint( $test->ID );
			$this->post = $test;
		}
		
		$this->settings	=	$this->get_settings();
	}
	
	public function __isset( $key ) {
		return metadata_exists( 'post', $this->id, '_' . $key );
	}
	
	public function __get( $key ) {
		$value = get_post_meta( $this->id, '_' . $key, true );
		
		if ( false !== $value ) {
			$this->$key = $value;
		}
		
		return $value;
	}
	
	public function get_post_data() {
		return $this->post;
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_permalink() {
		return esc_url( get_permalink( $this->id ) );
	}

	public function get_result_permalink( $result_id ) {

		return esc_url( get_permalink( $this->id ) . '/result/' . $result_id );
	}

	public function get_html_permalink() {

		return '<a href="' . $this->get_permalink() . '">' . $this->get_title() . '</a>';
	}
	
	public function get_category( $formated = true ) {
		if( $formated ) {
			
			return get_the_category_list(' ', 'single', $this->id);
		}
	}

	public function get_type(){

		$type_testing	=	qm_get_post_meta( $this->id, 'type_testing' );

		if( !$type_testing ) {

			return 0;
		}

		return $type_testing;
	}
	
	public function get_image_id() {

		if ( has_post_thumbnail( $this->id ) ) {
			$image_id = get_post_thumbnail_id( $this->id );
		} elseif ( ( $parent_id = wp_get_post_parent_id( $this->id ) ) && has_post_thumbnail( $parent_id ) ) {
			$image_id = get_post_thumbnail_id( $parent_id );
		} else {
			$image_id = 0;
		}

		return $image_id;
	}
	
	public function get_author_id() {
		
		return $this->post->post_author;
	}
	
	public function get_title() {

		$title = $this->post->post_title;

		return apply_filters( 'quizmaker_test_title', $title, $this );
	}
	
	public function get_duration( $is_formated = true ) {
		
		if( $is_formated ) {
			$duration	=	$this->duration ? $this->duration . ' ' . __('minutes', 'quizmaker') : __('unlimited', 'quizmaker');
		}else{
			
			$duration = $this->duration;
		}
		
		return $duration;
	}
	
	public function get_attempt( $is_formated = true ) {
		
		if($is_formated){
			$attempt = $this->attempt ? $this->attempt : __('unlimited', 'quizmaker');
		}else{
			
			$attempt = $this->attempt;
		}
		
		return $attempt;
	}
	
	public function get_played(){
				
		return $this->played ? $this->played : 0;
	}

	public function get_image( $size = 'thumbnail', $attr = array() ) {
		if ( has_post_thumbnail( $this->id ) ) {
			$image = get_the_post_thumbnail( $this->id, $size, $attr );
		} elseif ( ( $parent_id = wp_get_post_parent_id( $this->id ) ) && has_post_thumbnail( $parent_id ) ) {
			$image = get_the_post_thumbnail( $parent_id, $size, $attr );
		}

		return $image;
	}
	
	public function get_publish() {
		
		return qm_get_post_meta( $this->id, 'publish_for' );
	}
	
	public function get_settings() {

		$settings = $this->settings;

		if( !$settings ) {

			$settings = array(
				'start_date' => false,
				'end_date' => false,
				'attempt' => 1,
				'is_answer' => true,
				'is_random' => false,
				'cover_image' => 0
			);
		}

		if( !isset($settings['is_share_for_view_result']) ) {

			$settings['is_share_for_view_result'] = false;
		}

		if( !isset($settings['is_share_for_plus_points']) ) {

			$settings['is_share_for_plus_points'] = false;
			$settings['plus_points_when_share'] = 1;
		}

		if(isset($settings['cover_image']) && $settings['cover_image']) {
			
			$settings['cover_image_src'] = wp_get_attachment_image_src( $settings['cover_image'], 'large');
		}

		return $settings;
	}

	public function get_setting( $name ){

		$settings	=	 $this->get_settings();


		
		return isset($settings[$name]) ? $settings[$name] : false;
	}
	
	public function set_settings( $key, $value ) {
			
		$settings	=	$this->test_settings ? $this->test_settings : array();
	
		$settings	=	wp_parse_args($settings, array(
			'display_perpage'	=>	10,
			'duration'			=>	20,
			'publish_for'		=>	0,
			'attempt'			=>	0,
			'assigned_users'	=>	array(),
			'memberships'		=>	array(),
			'played'			=>	0,
			'is_reviews'		=>	1,
			'is_email_result'	=>	1,
			'is_sidebar_tracking'	=>	1,
			'ranking'			=>	array(),
			'rating'			=>	array( 
										'1_star' => 0, 
										'2_star' => 0, 
										'3_star' => 0, 
										'4_star' => 0, 
										'5_star' => 0, 
										'final'	 => 0
									)
		));
	
		$settings[$key]	=	$value;
	
		return update_post_meta( $this->id, '_test_settings', $settings );
		
	}
	
	public function get_date_created( $time_format = '' ){
		
		if(!$time_format) {
			$time_format	=	get_option('date_format');
		}
		
		return get_the_date( $time_format, $this->id );
	}
	
	public function get_comments_number(){
		
		return get_comments_number( $this->id );
	}
	
	public function get_questions() {
		
		$questions			=	array();
		
		
		return $questions;
	}
		
	public function get_doing_data() {
		
		
		
		return array();
	}

	public function get_result_data( $result_id ) {
			
		
	}
	
	public static function get_result( $result_id, $is_include_data = true ) {
		
	}
	
	public function get_certificate( $result_id ) {
		
	}

	public function update_analytic_value( $name, $value = 1 ) {

		$analytic_id    = $this->analytic;

		update_post_meta( $analytic_id, '_' . $name, $value );
	}

	public function update_analytic_total_share() {

		$analytic_id    = $this->analytic;
		
		$total_share = get_post_meta( $analytic_id, '_total_share', true );

		$total_share++;
		
		update_post_meta( $analytic_id, '_total_share', $total_share );
	}
	
	public function update_analytic_view() {

		$analytic_id    = $this->analytic;

		$total_view = get_post_meta( $analytic_id, '_view', true );

		$total_view++;

		update_post_meta( $analytic_id, '_view', $total_view );
	}

	public function update_analytic_player() {

		global $wpdb;

		$total_player = $wpdb->get_var( 'SELECT COUNT(id) FROM ' . $wpdb->prefix . 'quizmaker_quiz_results WHERE quiz_id = ' . $this->id . ' GROUP BY user_email' );

		update_post_meta( $this->analytic, '_player', $total_player );
	}

	public function update_analytic_questions( $results ) {

		$analytic_id    = $this->analytic;
		
		$tracked_questions_1 = get_post_meta( $analytic_id, '_questions', true );

		if( !$tracked_questions_1 ) {

			$tracked_questions_1 = array();
		}

		$tracked_questions_2 = array();

		$questions = $this->questions;

		foreach( $questions as $q ) {

			$is_new = true;

			foreach( $tracked_questions_1 as $t ) {

				if( $q['id'] == $t['id'] ) {

					$tracked_questions_2[] = $t;

					$is_new = false;
				}
			}

			if( $is_new ) {

				$tracked_questions_2[] = array(
					'id' => $q['id'],
					'corrects' => 0,
					'wrongs'   => 0,
				);
			}
		}

		foreach( $tracked_questions_2 as &$t2 ) {

			foreach( $results as $r ) {

				if( $r[2] == $t2['id'] ) {

					if( $r[0] ) {

						$t2['corrects']++;

					}else{

						$t2['wrongs']++;
					}

				}
			}

		}
		// var_dump($tracked_questions_2); exit;
		update_post_meta( $analytic_id, '_questions', $tracked_questions_2 );
	}

	public function get_analytic_questions() {

		$tracked_questions = get_post_meta( $this->analytic, '_questions', true );

		$questions = $this->questions;

		foreach( $questions as &$q ) {

			if( $tracked_questions ) {

				foreach( $tracked_questions as &$t ) {

					if( $q['id'] == $t['id'] ) {

						$q = array_merge($q, $t);

					}
				}

			}else{

				$q['corrects'] = 0;
				$q['wrongs']   = 0;
			}
		}
		
		return $questions;
	}

	public function update_marketing( $data = array() ) {

		$is_enabled = get_option('quizmaker_marketing_app_enabled');

		if( $is_enabled == 'yes' ) {

			$marketing = $this->marketing_app;

			if( $marketing && is_array($marketing) ) {
				
				foreach( $marketing as $type => $value ) {

					if( $type == 'mailchimp' ) {
						
						$marketingType = new QM_Marketing_Mailchimp();

						$marketingType->pushUser( $data['user'], $value['lists'] );

					}elseif( $type == 'getresponse' ) {

						$marketingType = new QM_Marketing_Getresponse();

						$marketingType->pushUser( $data['user'], $value['lists'] );

					}

				}

			}
		}
	}

	public function get_rating( $user_id = false ) {
		
		$rating = get_post_meta( $this->id, '_rating', true );

		if( !$rating ){

			$rating = array( 
							'users'	 =>	array(),
							'stars'	 =>	array(
								1 => 0,
								2 => 0,
								3 => 0,
								4 => 0,
								5 => 0,
							),
							'star'	 => 0
						);
		}

		if( $user_id ) {

			if( $rating['users'] ){

				foreach( $rating['users'] as $user ) {

					if( $user['id'] == $user_id ) {

						return $user['star'];
					}
				}
			}

			return 0;
		}

		return $rating;
	}

	public function set_rating( $user_id, $star ) {

		$rating = $this->get_rating();

		if( !isset($rating['stars']) ) {

			$rating['stars'] = array(
								1 => 0,
								2 => 0,
								3 => 0,
								4 => 0,
								5 => 0,
							);
		}

		$is_rated = false;

		if( $rating['users'] ) {

			foreach( $rating['users'] as &$user ) {

				if( $user['id'] == $user_id ) {

					$is_rated = true;

					if( $user['star'] != $star ) {

						if( $rating['stars'][$user['star']] > 0 ){
							$rating['stars'][$user['star']]--;
						}

						$rating['stars'][$star]++;
					}

					$user['star'] = $star;

				}
			}
		}

		if( !$is_rated ) {

			$rating['stars'][$star]++;

			$rating['users'][] = array( 'id' => $user_id, 'star' => $star );
			
		}

		$max = 0;
		$n = 0;

		foreach ($rating['stars'] as $rate => $count) {
		    $max += $rate * $count;
		    $n += $count;
		}

		$rating['star'] = round($max / $n);
		
		update_post_meta( $this->id, '_rating', $rating );
	}

	public function reset_rating() {

		$rating = array( 
			'users'	 =>	array(),
			'stars'	 =>	array(
				1 => 0,
				2 => 0,
				3 => 0,
				4 => 0,
				5 => 0,
			),
			'star'	 => 0
		);

		update_post_meta( $this->id, '_rating', $rating );
	}

	public function html_rating( $echo = true ) {

		$rating = $this->get_rating();

		$html = aws_rating_html( $rating['star'], $rating['users'] );

		if( $echo ) {

			echo $html;

		}else{

			return $html;
		}
	}
	
	public function update_played() {
				
		$played	=	$this->played;
		
		$played	=	is_numeric( $played ) ? $played : 0;
		
		if( $played )
		{
			
			$played++;
			
		}else{
			
			$played = 1;
		}
		
		update_post_meta( $this->id, '_played', $played);
	}
	
	public function reset_played() {
		
		update_post_meta( $this->id, '_played', 0);
	}

	public function get_question( $id ) {

		$questions = get_post_meta( $this->id, '_questions', true );

		foreach( $questions as $question ) {

			if( $question['id'] == $id ) {

				$question['settings']['duration'] = absint( $question['settings']['duration'] );
				$question['settings']['points']   = absint( $question['settings']['points'] );

				foreach( $question['options'] as &$option ) {

					$option['is_correct'] = absint( $option['is_correct'] );
				}

				return $question;
			}
		}

		return false;
	}

	public function add_question( $data ) {

		if( !$data ){

			return false;
		}

		$stored_question = qm_update_or_new_array_meta( $this->id, $data, '_questions', false, 'id' );
		
		return $stored_question;
	}

	public function double_question( $id ) {

		if( !$id ){

			return false;
		}

		$questions = $this->questions;

		$new_question = array();

		foreach( $questions as $question ) {

			if( $question['id'] == $id ) {

				$new_question = $question;
			}
		}

		$new_question['id'] = uniqid();

		array_push($questions, $new_question);

	 	$questions = update_post_meta( $this->id, '_questions', $questions );

		return $questions;
	}

	public function remove_questions( $ids ) {


		if( !$ids ) {

			return false;
		}

		$stored_question = qm_remove_array_meta( $this->id, $ids, '_questions','id' );

		return $stored_question;
	}
	
	public function update_question( $data ) {

		if( !$data ){

			return false;
		}
		
		$questions = $this->questions;

		foreach( $questions as &$question ) {

			if( $question['id'] == $data['id'] ) {

				$question = $data;
			}
		}

	 	$questions = update_post_meta( $this->id, '_questions', $questions );

		return $questions;
	}

	public function sort_questions( $sort_ids ) {

		$questions = get_post_meta( $this->id, '_questions', true );

		if( $sort_ids && $questions ) {

			$new_questions = array();

			foreach( $sort_ids as $order => $sid ) {

				foreach( $questions as $question ) {

					if( $question['id'] == $sid ) {

						$question['order'] = $order;

						$new_questions[] = $question;
					}
				}
			}

			update_post_meta( $this->id, '_questions', $new_questions );

			return $new_questions;
		}

		return $questions;
	}

	public function get_question_ids( $is_random = false ) {

		$ids = array();

		if( !$this->questions ) {

			return $ids;
		}

		$questions = $this->questions;

		if( $is_random ){

			shuffle( $questions );
		}

		foreach( $questions as $question ) {

			$ids[] = $question['id'];
		}

		return $ids;
	}

	public function update_settings( $newSettings ) {

		$settings = $this->settings;

		$default_settings = array(
			'start_date' => '',
			'end_date' => '',
			'attempt' => 1,
			'is_answer' => true,
			'is_random' => false,
			'cover_image' => 0,
			'is_share_for_view_result' => false,
			'is_share_for_plus_points' => false,
			'plus_points_when_share' => 1
		);

		if( $settings ) {

			$settings = wp_parse_args( $settings, $default_settings );

			$settings = wp_parse_args( $newSettings, $settings );

		}else{

			$settings = $default_settings;
		}

		// if (DateTime::createFromFormat('Y-m-d', $settings['start_date']) == FALSE) {
			
		// 	$settings['start_date'] = date('Y-m-d');
		// }

		// if (DateTime::createFromFormat('Y-m-d', $settings['end_date']) == FALSE) {
			
		// 	$settings['end_date'] = date('Y-m-d');
		// }

		update_post_meta( $this->id, '_settings', $settings );
	}

	public function setRating( $rating ) {

		$data = $this->rating;

		if( !$data && !is_array($data) ) {

			$data = array('value' => 0, 'total' => 0);
		}

		$data['total']++;

		$data['value'] = ($data['value'] + $rating) / $data['total'];
		
		update_post_meta( $this->id, '_rating', $data );

		return $data;
	}

	public function calculate_result( $user_answers ) {

		$results = array();

		$points = 0;

		$total_points = 0;

		foreach( $user_answers as $u_ans ) {

			$result = $this->is_correct( $u_ans );

			$points += absint($result[1]);

			$total_points += absint($result[4]['points']);

			$results[] = $result;
		}

		$response = array(
			'points' 		=> $points,
			'total_points' 	=> $total_points,
			'answers'		=> $user_answers,
			'results'		=> $results
		);

		return $response;
	}

	public function store_result( $session ) {

		$user  		   = $session['user'];
		$user_answers  = $session['result'];

		$time_start    = qm_get_date( (int)$session['time_start'] );
		$time_end	   = qm_get_date('now');

		$result = $this->calculate_result( $user_answers );

		$result['date_start'] = $time_start->format('Y-m-d H:i:s');
		$result['date_end']   = $time_end->format('Y-m-d H:i:s');

		$result['duration'] = qm_get_duration( $result['date_start'], $result['date_end'] );

		if( isset($session['plus_points']) && $session['plus_points'] > 0 ) {

			$result['points'] += absint( $this->settings['plus_points_when_share'] );
		}

		$data = array(
			'quiz_id'		=> $this->id,
			'score' 		=> $result['points'],
			'total_score' 	=> $result['total_points'],
			'date_start'	=> $result['date_start'],
			'date_end'		=> $result['date_end'],
			'duration'		=> $result['duration'],
			'user_ip'		=> $session['ip']
		);

		if( $user ) {

			$data['user_name'] = isset($user['your_name']) ? $user['your_name'] : 'Guest';

			$data['user_email'] = isset($user['your_email']) ? $user['your_email'] : '';

			$user['your_ip'] = $session['ip'];
		}

		if( $result['answers'] ) {

			$data['answers'] = json_encode($result['answers']);
		}

		$table = new QM_Table_Quiz_Result();

		$table->save($data);

		$this->update_marketing( array('user' => $user ) );
		$this->update_analytic_player();
		$this->update_analytic_questions( $result['results'] );

		return $result;
	}

	public function get_html_result( $user_answers, $echo = false ) {

		$results = $this->calculate_result( $user_answers );

		ob_start();

		qm_get_template( 'quiz/result.php', $results );

		if ( $echo )

			echo ob_get_clean();
		else
			return ob_get_clean();
	}

	public function get_lastest_pagination_results( $params = array() ){
		
		global $wpdb;

		$params = wp_parse_args( $params, array('perpage' => 20, 'page' => 1, 'order' => 'r.date_start DESC') );
		
		$result_tbl	=	$wpdb->prefix . 'quizmaker_quiz_results';

		$page 		=	absInt($params['page']);
		$perpage	=	absInt($params['perpage']);

		$total 		= 	$this->get_total_lastest_results();
		
		$pages 		=	ceil($total / $perpage);
		$offset 	=	($page - 1) * $perpage;

		if($page > $pages){
			$page = 1;
		}

		$results = array();
		
		if( $total > 0 ){
			$query	=	$wpdb->prepare(
				'SELECT r.id, r.quiz_id, r.user_email, r.user_name, r.score, r.total_score, r.percent, r.duration, r.date_start, r.date_end, r.answers, r.others '.
					'FROM ' . $result_tbl . ' r ' .
					'WHERE r.quiz_id = %d AND r.date_start IN(SELECT max(dr.date_start) FROM ' . $result_tbl . ' dr GROUP BY dr.user_email) '.
					'GROUP BY r.user_email ORDER BY ' . $params['order'] . ' LIMIT %d, %d'
				, $this->id, $offset, $perpage);
			
			$results	= $wpdb->get_results($query, ARRAY_A);
		}

		return array( 
			'data' 		 =>  $results,
			'pagination' =>  array('total' => $total, 'pages' => $pages, 'perpage' => $perpage, 'page' => $page) );
	}

	public function get_total_lastest_results()
	{
		global $wpdb;

		$result_tbl	=	$wpdb->prefix . 'quizmaker_quiz_results';
		
		$total	=	$wpdb->get_col($wpdb->prepare(
			'SELECT COUNT(*) '.
				'FROM ' . $result_tbl . ' r ' .
				'WHERE r.quiz_id = %d AND r.date_start IN(SELECT max(dr.date_start) FROM ' . $result_tbl . ' dr GROUP BY dr.user_email) '.
				'GROUP BY r.user_email'
			, $this->id));
		
		if($total){
			return count($total);
		}else{
			return 0;
		}
	}


	public function get_user_ranking( $email ) {

		$results = $this->get_lastest_pagination_results(array(
			'order'   => 'r.score DESC',
			'perpage' => 2000000
		));

		$user_ranking = 1;

		if( isset($results['data']) && $results['data'] ) {

			foreach( $results['data'] as $index => $r ) {

				if( $r['user_email'] == $email ) {

					$user_ranking = $index + 1;
				}
			}
		}

		return $user_ranking;
	}

	public function is_correct( $answer ) {

		$question_id = $answer['id'];
		
		$questions = $this->questions;

		foreach( $questions as $question ) {

			if( $question['id'] == $question_id ) {

				return call_user_func( array($this, 'is_correct_type_' . $question['type']), $answer);
			}
		}
	}

	public function is_correct_type_1( $answer ) {

		$question_id = $answer['id'];
		$answer_id 	 = $answer['selected'];

		$questions = $this->questions;

		$result = false;

		$points = 0;

		$question_point = 0;

		foreach( $questions as $question ) {

			if( $question['id'] == $question_id ) {

				$question_point = absint($question['settings']['points']);

				foreach($question['options'] as $option) {

					if( $option['is_correct'] && $option['id'] == $answer_id ){

						$result = true;
						$points = $question_point;
					}
				}
			}
		}
		
		return array($result, $points, $question_id, $answer, array(
			'points' => $question_point
		));
	}

	public function is_correct_type_2( $answer ) {

		$question_id = $answer['id'];
		$answer_id 	 = $answer['selected'];

		$questions = $this->questions;

		$result = false;

		$points = 0;

		$question_point = 0;

		foreach( $questions as $question ) {

			if( $question['id'] == $question_id ) {

				$question_point = absint($question['settings']['points']);

				foreach($question['options'] as $option) {

					if( $option['is_correct'] && $option['id'] == $answer_id ){

						$result = true;
						$points = $question_point;
					}
				}
			}
		}
		
		return array($result, $points, $question_id, $answer, array(
			'points' => $question_point
		));
	}

	public function is_correct_type_3( $answer ) {

		$question_id = $answer['id'];
		
		$questions = $this->questions;

		$result = false;

		$points = 0;

		$question_point = 0;

		$correct_value = 0;

		foreach( $questions as $question ) {

			if( $question['id'] == $question_id ) {

				$question_point = absint($question['settings']['points']);

				$correct_value = $question['options']['value'];
				
				if( $question['options']['value'] == $answer['selected']) {

					$result = true;
					$points = $question_point;

					
				}

			}
		}
		
		return array($result, $points, $question_id, $answer, array(
			'points' => $question_point,
			'correct' => $correct_value
		));
	}

	public function is_correct_type_4( $answer ) {

		$question_id = $answer['id'];

		$userOrders  = $answer['selected'];
		
		$questions = $this->questions;

		$result = true;

		$points = 0;

		$question_point = 0;

		if( $questions ){

			foreach( $questions as $question ) {

				if( $question['id'] == $question_id ) {

					$question_point = absint($question['settings']['points']);

					foreach( $question['options'] as $index => $q ) {

						if( $userOrders[$q['order']] != $q['id'] ) {

							$result = false;
						}
					}

				}
			}

		}else{

			$result = false;
		}

		if( $result ) {

			$points = $question_point;
		}
		
		return array($result, $points, $question_id, $answer, array(
			'points' => $question_point
		));
	}

	// Guess Word
	public function is_correct_type_5( $answer ) {

		$question_id = $answer['id'];

		$userOrders  = $answer['selected'];
		
		$questions = $this->questions;

		$result = true;

		$points = 0;

		$question_point = 0;

		if( $questions ){

			foreach( $questions as $question ) {

				if( $question['id'] == $question_id ) {

					$question_point = absint($question['settings']['points']);

					foreach( $question['options'] as $index => $q ) {

						foreach( $userOrders as $uq ) {

							if( $uq['id'] == $q['id'] && isset($uq['answer']) ) {

								if( $uq['answer'] != $q['content'] ) {

									$result = false;
								}
							}

						}
					}

				}
			}

		}else{

			$result = false;
		}

		if( $result ) {

			$points = $question_point;
		}
		
		return array($result, $points, $question_id, $answer, array(
			'points' => $question_point
		));
	}

	// Keyword
	public function is_correct_type_6( $answer ) {

		$question_id = $answer['id'];

		$userOrders  = $answer['selected'];
		
		$questions = $this->questions;

		$result = true;

		$points = 0;

		$question_point = 0;

		if( $questions ){

			$num_corrects = 0;

			foreach( $questions as $question ) {

				if( $question['id'] == $question_id ) {

					$question_point = absint($question['settings']['points']);

					foreach( $question['options'] as $index => $q ) {

						if (strpos(strtolower($userOrders), strtolower($q['content'])) !== false) {
			
						    $num_corrects++;
						}
					}

				}
			}

		}

		if( $num_corrects >= 1 ) {

			$points = $question_point;
			
		}else{

			$result = false;
		}
		
		return array($result, $points, $question_id, $answer, array(
			'points' => $question_point
		));
	}

	// Item Match
	public function is_correct_type_7( $answer ) {

		$question_id = $answer['id'];

		$userOrders  = $answer['selected'];
		
		$questions = $this->questions;

		$result = true;

		$points = 0;

		$question_point = 0;

		if( $questions ){

			$num_corrects = 0;

			foreach( $questions as $question ) {

				if( $question['id'] == $question_id ) {

					$question_point = absint($question['settings']['points']);

					foreach( $question['options'] as $index => $q ) {

						foreach( $userOrders['item_1'] as $item_index => $item_1_id ) {

							if( $q['id'] == $item_1_id ) {

								if( absint($q['id']) != absint($userOrders['item_2'][$item_index]) ) {

									$result = false;
								}
							}
						}
					}

				}
			}

		}

		if( $result ) {

			$points = $question_point;
			
		}

		return array($result, $points, $question_id, $answer, array(
			'points' => $question_point
		));
	}

	// Group Match
	public function is_correct_type_8( $answer ) {

		$question_id = $answer['id'];

		$userOrders  = $answer['selected'];
		
		$questions = $this->questions;

		$result = true;

		$points = 0;

		$question_point = 0;

		if( $questions ){

			$num_corrects = 0;

			foreach( $questions as $question ) {

				if( $question['id'] == $question_id ) {

					$question_point = absint($question['settings']['points']);

					foreach( $question['options'] as $index => $q ) {

						foreach( $userOrders as $item_index => $g ) {

							if( $q['group'] == $g['group'] ) {

								if( !in_array( $q['item'], $g['items'] ) ) {

									$result = false;
								}
							}
						}
					}

				}
			}

		}

		if( $result ) {

			$points = $question_point;
			
		}

		return array($result, $points, $question_id, $answer, array(
			'points' => $question_point
		));
	}

	public function get_play_question( $page ) {

		$session = new QM_Quiz_Session();

		$quiz_data = $session->get('quiz');

		$s_questions = $quiz_data['questions'];

		$questions = $this->questions;

		$question  = $questions[0];

		foreach( $questions as $q ) {

			if( $q['id'] == $s_questions[$page] ) {

				$question = $q;
			}
		}

		if(isset($question['image']) && $question['image']){
			$question['image'] 		= wp_get_attachment_image_src( $question['image'], 'medium');
		}

		
		$question['pages'] 		= count($questions);
		$question['is_last']	= $page == ($question['pages'] - 1);

		if( $question['is_last'] ) {

			$question['user'] = $session->get('user');

		}else{

			$question['user'] = array( 'your_name' => '', 'your_email' => '' );
		}

		return call_user_func( array( $this, 'filter_question_type_' . $question['type'] ), $question );
	}

	public function filter_question_type_1( $question ) {


		return $question;
	}

	public function filter_question_type_2( $question ) {

		foreach($question['options'] as &$option) {

			if( isset($option['content']) && $option['content'] ){
				
				$option['content'] = wp_get_attachment_image_src( $option['content'], 'medium');
			}
		}

		return $question;
	}

	public function filter_question_type_3( $question ) {


		return $question;
	}

	public function filter_question_type_4( $question ) {


		return $question;
	}

	public function filter_question_type_5( $question ) {


		return $question;
	}

	public function filter_question_type_6( $question ) {


		return $question;
	}

	public function filter_question_type_7( $question ) {

		$question['item_1'] = $question['options'];
		$question['item_2'] = $question['options'];

		shuffle($question['item_1']);
		shuffle($question['item_1']);
		shuffle($question['item_2']);

		return $question;
	}

	public function filter_question_type_8( $question ) {

		$options = array();
		$groups  = array();
		$items   = array();

		foreach($question['options'] as $value) {

			if( !in_array($value['group'], $groups) ) {

				$groups[] =	$value['group'];
			}
			
			$items[] = $value['item'];
		}

		$items = array_chunk( $items, count($groups) );

		foreach( $items as $i => $gitem ) {

			$options[] = array(
				'group' => $groups[$i],
				'items'  => $gitem
			);
		}

		$question['options'] = $options;

		return $question;
	}
}