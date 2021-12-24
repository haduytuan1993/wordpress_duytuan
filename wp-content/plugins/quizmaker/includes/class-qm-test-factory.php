<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

class QM_Test_Factory {
	
	public function get_test( $the_test = false, $args = array() ) {
		
		if ( false === $the_test ) {
			$the_test = $GLOBALS['post'];
		} elseif ( is_numeric( $the_test ) ) {
			$the_test = get_post( $the_test );
		} elseif ( $the_test instanceof QM_Test ) {
			$the_test = get_post( $the_test->id );
		} elseif ( ! ( $the_test instanceof WP_Post ) ) {
			$the_test = false;
		}
		
		if ( ! $the_test ) {
			return false;
		}
		
		return new QM_Test( $the_test, $args );
	}
}

