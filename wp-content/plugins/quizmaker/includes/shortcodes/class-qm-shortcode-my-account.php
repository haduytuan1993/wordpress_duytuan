<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

class QM_Shortcode_My_Account {
	
	public static function get( $atts ) {
		return QM_Shortcodes::shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}
	
	public static function output( $atts ) {
		global $wp;

		if ( is_user_logged_in() ) {
			
			if ( isset( $wp->query_vars['view-edit-account'] ) ) {

				self::edit_account();
				
			} elseif ( isset( $wp->query_vars['view-result'] ) ) {
				
				self::view_result();
			
			} elseif ( isset( $wp->query_vars['view-results'] ) ) {
				
				self::view_results();
			
			} elseif ( isset( $wp->query_vars['view-assigned-tests'] ) ) {
				
				self::view_assigned_tests();
				
			} elseif ( isset( $wp->query_vars['view-certificates'] ) ) {
				
				self::view_certificates();

			} elseif ( isset( $wp->query_vars['view-certificate'] ) ) {

				self::view_certificate();
			
			} else {

				$is_redirect = apply_filters( 'quizmaker_my_account_tabs_content', true, $wp->query_vars );

				if( $is_redirect ){
					
					wp_redirect(qm_get_endpoint_url( 'view-results', '', qm_get_page_permalink( 'myaccount' ) ));
				}
			}
			
		}else{
			
			if ( isset( $wp->query_vars['qm-lost-password'] ) ) {
				
				self::lost_password();
				
			} elseif ( isset( $wp->query_vars['qm-reset-password'] ) ) {
				
				self::reset_password();

			} elseif ( isset( $wp->query_vars['view-activation'] ) ) {

				self::view_activation();
				
			} else {
				
				self::login( $atts );
				
			}
			
		}
		
	}
	
	private static function my_account( $atts ) {
		extract( shortcode_atts( array(
	    	'test_count' => 15
		), $atts ) );

		qm_get_template( 'myaccount/my-account.php', array(
			'current_user' 	=> get_user_by( 'id', get_current_user_id() ),
			'test_count' 	=> 'all' == $test_count ? -1 : $test_count,
			'logout_url'	=>	qm_get_endpoint_url( 'member-logout', '', qm_get_page_permalink( 'myaccount' ) ),
			'edit_url'		=>	qm_get_endpoint_url( 'view-edit-account', '', qm_get_page_permalink( 'myaccount' ) )
		) );
	}
	
	private static function edit_account() {
		
		qm_get_template( 'myaccount/form-edit-account.php', array( 'user' => get_user_by( 'id', get_current_user_id() ) ) );
	}
	
	private static function view_result() {
		global $wp;
		
		$test_id	=	absInt($wp->query_vars['view-result']);
		$params		=	array();
		
		if($test_id){
			
			$test		=	QM()->test_factory->get_test($test_id);
			
			$results	=	$test->get_user_results(get_current_user_id(), -1);
			
			$params['test']	=	$test;
				
			if($results){
				
				$results['pagination']['link']	=	qm_get_endpoint_url( 'view-result', $test_id, qm_get_page_permalink( 'myaccount' ) ) . '/p/';
				
				$params['results']	=	$results;
			}
		}
		
		qm_get_template( 'myaccount/view-result.php', $params );
	}
	
	private static function view_results() {
		global $wp;
		
		$page		=	absInt($wp->query_vars['view-results']);
		
		$userid     =	apply_filters('quizmaker_view_results_of_user', get_current_user_id());
		
		$results	=	qm_get_user_results( $userid, $page );
		
		$results['pagination']['link']	=	qm_get_endpoint_url( 'view-results', '', qm_get_page_permalink( 'myaccount' ) );
		
		qm_get_template( 'myaccount/view-results.php', $results);
	}

	private static function view_certificates() {
		global $wp;
		
		$page		=	absInt($wp->query_vars['view-certificates']);
		
		$results	=	qm_get_user_results( get_current_user_id(), $page );
		
		$results['pagination']['link']	=	qm_get_endpoint_url( 'view-certificates', '', qm_get_page_permalink( 'myaccount' ) );
		
		qm_get_template( 'myaccount/view-certificates.php', $results);
	}

	private static function view_certificate() {
		global $wp;
		
		$cert_id	=	absInt($wp->query_vars['view-certificate']);
		$params		=	array();
	}
	
	private static function view_assigned_tests() {
		global $wp;
		
		$page		=	absInt($wp->query_vars['view-assigned-tests']);
		
		$tests		=	qm_get_user_tests(get_current_user_id(), $page);
		
		$tests['pagination']['link']	=	qm_get_endpoint_url( 'view-assigned-tests', '', qm_get_page_permalink( 'myaccount' ) );
		
		qm_get_template( 'myaccount/view-assigned-tests.php', $tests );
	}
	
	private static function login() {
		
		$redirect	=	qm_get_endpoint_url( 'view-results', '', qm_get_page_permalink( 'myaccount' ) );
		
		qm_get_template( 'myaccount/form-login.php', array('redirect' => $redirect) );
	}
	
	private static function lost_password() {
		
		$args = array( 'form' => 'lost_password' );

		if ( isset( $_GET['key'] ) && isset( $_GET['login'] ) ) {

			$user = self::check_password_reset_key( $_GET['key'], $_GET['login'] );

			if( is_object( $user ) ) {
				$args['form'] = 'reset_password';
				$args['key'] = esc_attr( $_GET['key'] );
				$args['login'] = esc_attr( $_GET['login'] );
			}
		} elseif ( isset( $_GET['reset'] ) ) {
			qm_add_message( __( 'Your password has been reset.', 'quizmaker' ) . ' <a href="' . qm_get_page_permalink( 'myaccount' ) . '">' . __( 'Log in', 'quizmaker' ) . '</a>' );
		}
		
		qm_get_template( 'myaccount/form-lost-password.php', $args );
	}
	
	public static function retrieve_password() {
		global $wpdb, $wp_hasher;

		$login = trim( $_POST['user_login'] );

		if ( empty( $login ) ) {

			qm_add_message( __( 'Enter a username or e-mail address.', 'quizmaker' ), 'error' );
			return false;

		} else {
			// Check on username first, as customers can use emails as usernames.
			$user_data = get_user_by( 'login', $login );
		}

		// If no user found, check if it login is email and lookup user based on email.
		if ( ! $user_data && is_email( $login ) && apply_filters( 'quizmaker_get_username_from_email', true ) ) {
			$user_data = get_user_by( 'email', $login );
		}

		do_action( 'lostpassword_post' );

		if ( ! $user_data ) {
			qm_add_message( __( 'Invalid username or e-mail.', 'quizmaker' ), 'error' );
			return false;
		}

		if ( is_multisite() && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
			qm_add_message( __( 'Invalid username or e-mail.', 'quizmaker' ), 'error' );
			return false;
		}

		// redefining user_login ensures we return the right case in the email
		$user_login = $user_data->user_login;

		do_action( 'retrieve_password', $user_login );

		$allow = apply_filters( 'allow_password_reset', true, $user_data->ID );

		if ( ! $allow ) {

			qm_add_message( __( 'Password reset is not allowed for this user', 'quizmaker' ), 'error' );
			return false;

		} elseif ( is_wp_error( $allow ) ) {

			qm_add_message( $allow->get_error_message(), 'error' );
			return false;
		}

		$key = wp_generate_password( 20, false );

		do_action( 'retrieve_password_key', $user_login, $key );

		// Now insert the key, hashed, into the DB.
		if ( empty( $wp_hasher ) ) {
			require_once ABSPATH . 'wp-includes/class-phpass.php';
			$wp_hasher = new PasswordHash( 8, true );
		}

		$hashed = $wp_hasher->HashPassword( $key );

		$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user_login ) );
		
		do_action( 'quizmaker_email_reset_password', $user_login, $key );

		qm_add_message( __( 'Check your e-mail for the confirmation link.', 'quizmaker' ) );
		return true;
	}
	
	public static function check_password_reset_key( $key, $login ) {
		global $wpdb, $wp_hasher;

		$key = preg_replace( '/[^a-z0-9]/i', '', $key );

		if ( empty( $key ) || ! is_string( $key ) ) {
			qm_add_message( __( 'Invalid key', 'quizmaker' ), 'error' );
			return false;
		}

		if ( empty( $login ) || ! is_string( $login ) ) {
			qm_add_message( __( 'Invalid key', 'quizmaker' ), 'error' );
			return false;
		}

		$user = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE user_login = %s", $login ) );
		
		if ( ! empty( $user ) ) {
			if ( empty( $wp_hasher ) ) {
				require_once ABSPATH . 'wp-includes/class-phpass.php';
				$wp_hasher = new PasswordHash( 8, true );
			}

			$valid = $wp_hasher->CheckPassword( $key, $user->user_activation_key );
		}

		if ( empty( $user ) || empty( $valid ) ) {
			qm_add_message( __( 'Invalid key', 'quizmaker' ), 'error' );
			return false;
		}

		return get_userdata( $user->ID );
	}
	
	public static function reset_password( $user, $new_pass ) {
		do_action( 'password_reset', $user, $new_pass );

		wp_set_password( $new_pass, $user->ID );

		wp_password_change_notification( $user );
	}
	
	private static function view_membership() {
		
		$data	=	qm_get_user_paids(get_current_user_id());
		
		$payment_link	=	qm_get_endpoint_url( 'upgrade', '', qm_get_page_permalink( 'memberships' ) );
		
		qm_get_template( 'myaccount/view-membership.php', array('data' => $data, 'payment_link' => $payment_link) );
	}

	public static function view_activation() {

		$status = -1;

		if( isset($_GET['qm-activation']) && $_GET['qm-activation'] ) {

			if( $_GET['qm-activation'] == 'new' ) {

				$status = 0;

			}elseif( $_GET['qm-activation'] == 'required' ){

				$status = 1;

			}else{

				$user = qm_user_validate_activation_key($_GET['qm-activation']);

				if( $user ) {

					$status = 2;

				}else{

					$status = 3;

				}

			}
		}

		if( $status == -1 ) {

			wp_redirect( site_url('/') ); exit;

		}else{

			qm_get_template( 'myaccount/view-activation.php', ['status' => $status] );
		}
	}
}