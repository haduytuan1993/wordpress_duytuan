<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class QM_Wpcf7 {

	public function __construct() {

		add_filter( 'wpcf7_mail_components', array( $this, 'custom_mail_shortcode_components' ) );
	}

	public function custom_mail_shortcode_components() {

		if( isset( $_POST['quizmaker_shortcode_viral_wpcf7'] ) ){
 
       	 	$post = get_post( $_GET['my_post_id'], ARRAY_A );
 			
	        $str = $post['post_title']. ' ' . get_permalink( $_GET['my_post_id'] ) ;
			$wpcf7_data['body'] = str_replace('[my_post_id]', $str , $wpcf7_data['body'] );
	    }
	 
	    return $wpcf7_data;
	}
}