<?php

class QM_Shortcode_Viral {
	
	public static function get( $atts ) {
		
		return QM_Shortcodes::shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}
	
	public static function output( $atts ) {

		$atts = shortcode_atts( array(
			'ids'			=>	'',
			'categories' 	=>	'',
			'total'			=>	10,
			'perpage'		=>	-1,
			'form'			=>	'',
			'show_answers'	=>	'yes'	
		), $atts );

		if( !isset($atts['ids']) && !isset($atts['category']) ) { return false; }

		$ids = array();

		if( isset($atts['ids']) && $atts['ids'] ) {

			$ids = explode(',', $atts['ids']);

		}elseif( isset($atts['categories']) && $atts['categories'] ) {

			$cids = explode(',', $atts['categories']);

			$q = qm_get_question_by_categories( $cids, $atts['total'] );

			if($q){
				$ids = qm_get_values_by_key( $q, 'ID' );
			}
		}

		$questions = qm_get_doing_questions( array('ids' => $ids) );

		$settings  = array( 
			'show_explanation' => true, 
			'instant_answers' => false 
		);

		$total = count($questions);

		$pages = ceil( $total / $atts['perpage'] );

		shuffle( $questions );

		$pages_questions = array_chunk( $questions, $atts['perpage'] );

		$js_params	=	array( 
			'total' => $total, 
			'pages' => $pages,
			'result_nonce' => wp_create_nonce('load_html_result'),
			'is_form' => ($atts['form'] ? 'yes':'no'),
			'is_show_answers' => $atts['show_answers']
		);

		$question_params = array();

		$min_js	=	qm_is_debug() ? '' : '.min';
		
		wp_enqueue_script( 'quizmaker-viral' );
		
		wp_localize_script( 'quizmaker-viral', 'qmviral', array(

			'plugin_url'	=>	QUIZMAKER_URI,
			'ajax_url'		=>	admin_url( 'admin-ajax.php' )
		) );

	?>
		
		<div class="quizmaker-viral" data-params="<?php echo htmlspecialchars(json_encode( $js_params, JSON_FORCE_OBJECT )); ?>">
			
			<?php for( $i = 0; $i < $pages; $i++ ): $order = ($i * $atts['perpage']) + 1; ?>

			<div class="page" data-page="<?php echo $i+1; ?>">
				
				<?php qm_get_template('content-questions.php', array(
						'questions' => $pages_questions[$i], 
						'order' => $order, 
						'settings' => $settings,
						'question_params' => $question_params,
						'show_question_title' => true)); ?>

			</div>

			<?php endfor;?>
			
			<div class="actions">
				
				<div class="pagination">
					<ul>
						<?php for( $i = 1; $i <= $pages; $i++): ?>
						<li><a data-page="<?php echo $i; ?>"><?php echo $i; ?></a></li>
						<?php endfor; ?>
					</ul>
					<div class="prev"><?php _e('PREVIOUS', 'quizmaker'); ?></div>
					<div class="next"><?php _e('CONTINUE', 'quizmaker'); ?></div>
				</div>

				<button class="qm-submit"><?php _e('Finish', 'quizmaker'); ?></button>
				
				<?php if( absint( $atts['form'] ) ){
					
					$form = do_shortcode( '[contact-form-7 id="' . absint( $atts['form'] ) . '" title="Contact form 2"]', false );

					$form = explode('</form>', $form);

					echo '<div class="action-form">' . $form[0] . '<input type="hidden" name="quizmaker_shortcode_viral_wpcf7" value="1"/>' . $form[1] . '</div>';
				} ?>
				
				
			</div>

			<div class="results"></div>
		</div>

		<?php
	}

	

}