<?php

class QM_Member {
	
	public $id = 0;
	
	public $post = null;
	
	public function __construct( $test ) {
		if ( is_numeric( $test ) ) {
			$this->id   = absint( $test );
			$this->post = get_post( $this->id );
		} elseif ( $test instanceof QM_Member ) {
			$this->id   = absint( $test->id );
			$this->post = $test->post;
		} elseif ( isset( $test->ID ) ) {
			$this->id   = absint( $test->ID );
			$this->post = $test;
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
	
	public function get_settings() {
		
		$settings	=	$this->member_data ? $this->member_data : array();
		
		$settings	=	wp_parse_args($settings, array());
		
		return $settings;
	}
	
	public function set_settings( $key, $value ) {
		
		$settings	=	$this->member_data ? $this->member_data : array();
	
		$settings	=	wp_parse_args($settings, array());
		
		$settings[$key]	=	$value;
	
		return update_post_meta( $this->id, '_member_data', $settings );
		
	}
	
	public function get_membership() {
						
		$membership_id	=	qm_get_user_meta( $this->user_id, 'membership' );
		
		$membership		=	new QM_Membership( $membership_id );
		
		return $membership;
	}
	
	public function remove_membership() {
				
		$default	=	qm_get_membership_default();
		
		qm_update_user_meta( $this->user_id, array( 'membership' => $default, 'membership_expired' => -1 ) );
		qm_update_post_meta( $this->id, array( 'membership' => $default, 'membership_expired' => -1 ) );
	}
	
	public function get_admin_membership_link() {
		
		$membership	=	$this->get_membership();
		
		return $membership->get_admin_link();
	}
	
	public function get_payment() {
						
		$is_payment		=	qm_get_user_meta( $this->user_id, 'is_payment' );
		
		return $is_payment;
	}
	
	public function get_expired() {
						
		$membership_expired		=	qm_get_user_meta( $this->user_id, 'membership_expired' );
		
		return $membership_expired;
	}
}