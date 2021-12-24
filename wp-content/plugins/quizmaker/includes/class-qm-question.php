<?php

class QM_Question {
	
	public $id = 0;
	
	public $post = null;
	
	public function __construct( $question ) {
		if ( is_numeric( $question ) ) {
			$this->id   = absint( $question );
			$this->post = get_post( $this->id );
		} elseif ( $question instanceof QM_Question ) {
			$this->id   = absint( $question->id );
			$this->post = $question->post;
		} elseif ( isset( $question->ID ) ) {
			$this->id   = absint( $question->ID );
			$this->post = $question;
		}
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
		return get_permalink( $this->id );
	}
	
	public function get_author_id() {
		
		return $this->post->post_author;
	}
	
	public function get_settings(){
		
		$settings	=	 qm_question_get_settings( $this->id );
		
		return $settings;
	}
	
	public function get_answer_type( $formated = true ){
		
		$answer_type	=	get_post_meta($this->id, '_answer-type', true);
		
		if($formated){
			return qm_question_get_answer_type($answer_type);
		}
		
		return $answer_type;
	}

	public function get_instant_answer( $answer, $params = array() ){

		ob_start();

		$is_correct	=	$this->is_correct( $answer );

		$explanation	=	qm_get_post_meta( $this->id, 'explanation' );

		qm_get_template( 'single-test/doing/instant_answer.php', array( 'is_correct' => $is_correct, 'explanation' => $explanation, 'params' => $params ) );

		$html = ob_get_clean();

		return array( 'is_correct' => $is_correct, 'html' => $html );
	}

	public function is_correct( $answer ) {

		$type 	=	$this->get_answer_type(false);

		return call_user_func( 'qm_is_' . $type . '_answers_correct', $this->answers, $answer );
	}

	public function filter_answer_post_data( $answer ){

		$type 	=	$this->get_answer_type(false);

		switch ( $type ) {
			case 'single':
				
				if(is_array($answer) && isset($answer[0]['value'])){

					return $answer[0]['value'];
				}

				return false;

				break;
			
			case 'multiple':

				if( is_array($answer) && isset( $answer[0]['value'] ) ){

					return qm_get_values_by_key( $answer, 'value' );
				}

				break;

			case 'order':

				if( is_array($answer) && isset( $answer[0]['value'] ) ){

					return qm_get_values_by_key( $answer, 'value' );
				}

				break;

			case 'fill_blank':

				if(is_array($answer) && isset($answer[0]['value'])){

					return $answer[0]['value'];
				}

				return false;

				break;

			case 'drag_match':

				if( is_array($answer) && $answer ){

					$answers = array();

					foreach( $answer as $ans ){
						if( is_string($ans['name']) ){
							preg_match("/q\[+([0-9]+)+\]\[+([0-9]+)/", $ans['name'], $id);

							$id = array_pop($id);

							$answers[$id]	=	$ans['value'];
						}
					}

					return $answers;
				}

				return array();

				break;

			case 'group_match':

				

				break;

			case 'guess_word':

				if( is_array($answer) && isset( $answer[0]['value'] ) ){

					return qm_get_values_by_key( $answer, 'value' );
				}

				break;

			default:
				
				return $answer;

				break;
		}

	}
	
	public function get_score(){
		
		$score	=	qm_get_post_meta( $this->id, 'score' );
		
		$score	=	isset($score) ? $score : 0;
		
		return $score;
	}
}