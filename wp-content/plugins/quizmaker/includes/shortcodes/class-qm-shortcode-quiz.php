<?php

class QM_Shortcode_Quiz {
	
	public static function get( $atts ) {
		
		return QM_Shortcodes::shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}
	
	public static function output( $atts ) {

		$atts = shortcode_atts( array(
			'id'			=>	'',
			'total'			=>	10,
			'perpage'		=>	-1,
			'form'			=>	'',
			'show_answers'	=>	'yes'	
		), $atts );

		// $atts['id'] = 32;

		$attr = 'qm-shortcode-' . $atts['id'];

		// if( !isset($atts['ids']) && !isset($atts['category']) ) { return false; }

		// $ids = array();

		// if( isset($atts['ids']) && $atts['ids'] ) {

		// 	$ids = explode(',', $atts['ids']);

		// }elseif( isset($atts['categories']) && $atts['categories'] ) {

		// 	$cids = explode(',', $atts['categories']);

		// 	$q = qm_get_question_by_categories( $cids, $atts['total'] );

		// 	if($q){
		// 		$ids = qm_get_values_by_key( $q, 'ID' );
		// 	}
		// }

		// echo 'ada'; exit;

		// $session->set('result', array('a' => 1));		
		
		wp_enqueue_script( 'quizmaker_vendor' );
		wp_enqueue_script( 'vue-frontend-quizmaker' );
		
		wp_localize_script( 'vue-frontend-quizmaker', 'qm_front', array(
			'plugin_url'	=>	QUIZMAKER_URI,
			'ajax_url'		=>	admin_url( 'admin-ajax.php' ),
			'security'		=>	wp_create_nonce( "quiz-player" ),
			'current_url'   => 	( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']
		) );

	?>
		<script>
		  window.fbAsyncInit = function() {
		    FB.init({
		      appId            : '277439886020029',
		      autoLogAppEvents : true,
		      xfbml            : true,
		      version          : 'v2.10'
		    });
		    FB.AppEvents.logPageView();
		  };

		  (function(d, s, id){
		     var js, fjs = d.getElementsByTagName(s)[0];
		     if (d.getElementById(id)) {return;}
		     js = d.createElement(s); js.id = id;
		     js.src = "https://connect.facebook.net/en_US/sdk.js";
		     fjs.parentNode.insertBefore(js, fjs);
		   }(document, 'script', 'facebook-jssdk'));
		</script>
		<div class="qm-shortcode-quiz">
			<div id="<?php echo $attr; ?>" class="reset-vuetify" data-params="<?php //echo htmlspecialchars(json_encode( $js_params, JSON_FORCE_OBJECT )); ?>">
				<v-app>
					<quiz-player :id="<?php echo $atts['id']; ?>"></quiz-player>
				</v-app>
			</div>
		</div>

		<?php
	}

	

}