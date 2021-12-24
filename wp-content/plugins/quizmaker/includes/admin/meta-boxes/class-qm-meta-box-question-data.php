<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class QM_Meta_Box_Question_Data {
	
	public static function output( $post ) {
		global $post, $thepostid;
				
		$score			=	qm_get_post_meta($post->ID, 'score', 1);
		$explanation	=	qm_get_post_meta($post->ID, 'explanation', '');
		$order_type		=	qm_get_post_meta($post->ID, 'order_type', 2);
		$timeout		=	qm_get_post_meta($post->ID, 'timeout', 0);
		
		$score			=	$score >= 0 ? $score : 10;
		$explanation	=	$explanation ? $explanation : '';
		$timeout		=	$timeout ? $timeout : 0;

?>
			
			<div class="question-data-panel">
				<div class="qm-groups-field">

					<div class="group-field">
						<label><?php _e('Score', 'quizmaker'); ?></label>
						<input type="text" name="qm_question_data[score]" value="<?php echo $score; ?>"/>
					</div>

					<div class="group-field">
						<label><?php _e('Order Type', 'quizmaker'); ?></label>
						<select name="qm_question_data[order_type]">
							<option value="0" <?php echo selected(0, $order_type) ?>><?php _e('None', 'quizmaker'); ?></option>
							<option value="1" <?php echo selected(1, $order_type) ?>>A,B,C,D</option>
							<option value="2" <?php echo selected(2, $order_type) ?>>1,2,3,4</option>
							<option value="3" <?php echo selected(3, $order_type) ?>>I,II,III,IV</option>
						</select>
					</div>

					<div class="top" style="padding: 5px 13px;">

						<label style="display: block; margin-bottom: 12px;"><?php _e('Explanation', 'quizmaker'); ?></label>

						<?php echo wp_editor( $explanation, 'd1', ['textarea_name' => 'qm_question_data[explanation]'] ); ?>

					</div>

				</div>
			</div>
			
<?php 
	}
	
	public static function save( $post_id, $post ) {
		
		if(!isset($_POST['qm_question_data'])) return false;
		
		$score 			=	10;
		$explanation	=	$_POST['qm_question_data']['explanation'];
		$order_type		=	1;
		$attachment 	=	0;
			
		if(isset($_POST['qm_question_data']['score']) && is_numeric($_POST['qm_question_data']['score'])){
			$score	=	absInt($_POST['qm_question_data']['score']);
		}
		
		if(isset($_POST['qm_question_data']['order_type']) && is_numeric($_POST['qm_question_data']['order_type'])){
			$order_type	=	absInt($_POST['qm_question_data']['order_type']);
		}

		if(isset($_POST['qm_question_data']['timeout']) && is_numeric($_POST['qm_question_data']['timeout'])){
			$timeout	=	absInt($_POST['qm_question_data']['timeout']);
		}
		
		qm_update_post_meta( $post_id, array(
			'score' => $score, 
			'explanation' => $explanation, 
			'order_type' => $order_type,
			'timeout' => $timeout,
			'attachment' => $attachment
		) );
	}
	
}