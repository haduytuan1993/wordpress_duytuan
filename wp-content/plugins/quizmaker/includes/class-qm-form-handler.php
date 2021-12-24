<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class QM_Form_Handler {
	
	public static function init() {
		
		add_action( 'template_redirect', array( __CLASS__, 'save_account_details' ) );
		add_action( 'template_redirect', array( __CLASS__, 'save_register' ) );
		
		add_action( 'template_redirect', array( __CLASS__, 'download_certificate' ) );
				
		add_action( 'wp_loaded', array( __CLASS__, 'start_test' ), 20 );
		add_action( 'wp_loaded', array( __CLASS__, 'submit_test' ), 20 );
		add_action( 'wp_loaded', array( __CLASS__, 'process_login' ), 20 );
		add_action( 'wp_loaded', array( __CLASS__, 'process_lost_password' ), 20 );
		add_action( 'wp_loaded', array( __CLASS__, 'process_reset_password' ), 20 );

		add_action( 'wp_loaded', array( __CLASS__, 'register_certificate' ), 20 );

		add_action( 'wp_loaded', array( __CLASS__, 'submit_doing_fillform' ), 20 );

		add_action( 'wp_loaded', array( __CLASS__, 'guest_register' ), 20 );
		add_action( 'wp_logout', array( __CLASS__, 'update_username' ), 20 );

		add_filter( 'wpcf7_skip_mail', array( __CLASS__, 'skip_mail'), 10, 2 );
	}

	public static function skip_mail( $skip, $form ) {
		
		return qm_is_debug();
	}

	public static function download_certificate() {
		global $wp_query;
		
		if(absInt(get_query_var('view-certificate'))) {
			
			$test_id	=	get_the_ID();
			
				
				$result_id	=	absInt(get_query_var('view-certificate'));
				
				$test		=	new QM_Test( $test_id );

				$result		=	QM_Test::get_result( $result_id );

				if( $result ) {

					$ranking	=	qm_is_ranking($test_id, $result['score'], $result['total_score'], false);
				

					if( isset($ranking['certificate']) && $ranking['certificate'] ) {

						$result['ranking']			=	$ranking['name'];
						$result['certificate_id']	=	$ranking['certificate'];
						$result['ranking_data']		=	$ranking;

					}

					$test->get_certificate( $result );
				}

				
		}
	}
	
	public static function start_test(){
		
		if(isset($_POST['quizmaker_start_test']))
		{

			$response = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
			
			$result = check_recaptcha( array( 
				'response'	=>	$response,
				'remoteip'	=>	$_SERVER['REMOTE_ADDR']
			) );

			if( $result ){
				
				QM()->doing()->start_test();
			}
		}
	}
	
	public static function submit_test(){
		
		if(isset($_POST['quizmaker_submit_test']))
		{
			QM()->doing()->submit_test();
		}
	}
	
	public static function save_account_details() {

		global $wpdb; 
		
		if ( 'POST' !== strtoupper( $_SERVER[ 'REQUEST_METHOD' ] ) ) {
			return;
		}
		
		if ( empty( $_POST[ 'action' ] ) || 'qm_save_account_details' !== $_POST[ 'action' ] || empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'qm_save_account_details' ) ) {
			return;
		}
		
		$errors       = new WP_Error();
		$user         = new stdClass();

		$user->ID     = (int) get_current_user_id();
		$current_user = get_user_by( 'id', $user->ID );

		if ( $user->ID <= 0 ) {
			return;
		}
		
		$account_first_name = ! empty( $_POST[ 'account_first_name' ] ) ? qm_clean( $_POST[ 'account_first_name' ] ) : '';
		$account_last_name  = ! empty( $_POST[ 'account_last_name' ] ) ? qm_clean( $_POST[ 'account_last_name' ] ) : '';
		$account_email      = ! empty( $_POST[ 'account_email' ] ) ? sanitize_email( $_POST[ 'account_email' ] ) : '';

		$username 			= ! empty( $_POST[ 'account_username' ] ) ? sanitize_user( $_POST[ 'account_username' ], 50 ) : $current_user->user_login;
		$pass1              = ! empty( $_POST[ 'password_1' ] ) ? $_POST[ 'password_1' ] : '';
		$pass2              = ! empty( $_POST[ 'password_2' ] ) ? $_POST[ 'password_2' ] : '';
		$save_pass          = true;
		
		$user->first_name   = $account_first_name;
		$user->last_name    = $account_last_name;

		// Prevent emails being displayed, or leave alone.
		$user->display_name = is_email( $current_user->display_name ) ? $user->first_name : $current_user->display_name;
		
		// Handle required fields
		$required_fields = apply_filters( 'quizmaker_save_account_details_required_fields', array(
			'account_first_name' => __( 'First Name', 'quizmaker' ),
			'account_last_name'  => __( 'Last Name', 'quizmaker' ),
			'account_email'      => __( 'Email address', 'quizmaker' ),
		) );
		
		foreach ( $required_fields as $field_key => $field_name ) {
			if ( empty( $_POST[ $field_key ] ) ) {
				qm_add_message( '<strong>' . esc_html( $field_name ) . '</strong> ' . __( 'is a required field.', 'quizmaker' ), 'error' );
			}
		}
		
		if ( $account_email ) {
			if ( ! is_email( $account_email ) ) {
				qm_add_message( __( 'Please provide a valid email address.', 'quizmaker' ), 'error' );
			} elseif ( email_exists( $account_email ) && $account_email !== $current_user->user_email ) {
				qm_add_message( __( 'This email address is already registered.', 'quizmaker' ), 'error' );
			}
			$user->user_email = $account_email;
		}

		if( $username != $current_user->user_login ) {
			
			if( username_exists( $username ) ) {

				qm_add_message( __( 'This username is already registered.', 'quizmaker' ), 'error' );

			}
					
		}

		if ( ! empty( $pass1 ) && empty( $pass2 ) ) {
			qm_add_message( __( 'Please re-enter your password.', 'quizmaker' ), 'error' );
			$save_pass = false;
		} elseif ( ( ! empty( $pass1 ) || ! empty( $pass2 ) ) && $pass1 !== $pass2 ) {
			qm_add_message( __( 'New passwords do not match.', 'quizmaker' ), 'error' );
			$save_pass = false;
		}
		
		if ( $pass1 && $save_pass ) {
			$user->user_pass = $pass1;
		}
		
		// Allow plugins to return their own errors.
		do_action_ref_array( 'quizmaker_save_account_details_errors', array( &$errors, &$user ) );
		
		if ( $errors->get_error_messages() ) {
			foreach ( $errors->get_error_messages() as $error ) {
				qm_add_message( $error, 'error' );
			}
		}
		
		if ( qm_message_count( 'error' ) === 0 ) {
			
			wp_update_user( $user ) ;
			delete_user_meta( $user->ID, 'is_guest' );

			if( $username != $current_user->user_login ) {

				$wpdb->update( $wpdb->prefix . 'users', array(
							'user_login'	=>	$username,
							'user_nicename' =>	$username,
							'display_name'	=>	$username
							), array('ID' => $user->ID), array('%s', '%s', '%s') );

			}
			
			qm_add_message( __( 'Account details changed successfully.', 'quizmaker' ) );

			do_action( 'quizmaker_save_account_details', $user->ID );
			
			if(isset($_POST['redirect'])){
				
				wp_redirect( $_POST['redirect'] );
				exit;
			}else{

				wp_safe_redirect( qm_get_endpoint_url( 'view-edit-account', '', qm_get_page_permalink( 'myaccount' ) ) );
			}

		}
		
	}
	
	public static function save_register() {
		
		if ( 'POST' !== strtoupper( $_SERVER[ 'REQUEST_METHOD' ] ) ) {
			return;
		}
		
		if ( empty( $_POST[ 'action' ] ) || 'qm_save_register' !== $_POST[ 'action' ] || empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'qm_save_register' ) ) {
			return;
		}
		
		$errors       = new WP_Error();

		$params_new_user	=	array();

		$params_new_user['is_email_for_username']	=	get_option('quizmaker_is_email_for_username');
		
		$user	=	qm_get_new_user( $_POST, $params_new_user );

		do_action_ref_array( 'quizmaker_before_register', array( $_POST, &$user, &$errors ) );
		
		do_action_ref_array( 'quizmaker_save_register_errors', array( &$errors, &$user ) );
		
		if ( $errors->get_error_messages() ) {
			foreach ( $errors->get_error_messages() as $error ) {
				qm_add_message( $error, 'error' );
			}
		}
		
		if ( qm_message_count( 'error' ) === 0 ) {
			
			$user_id = wp_insert_user( $user ) ;

			$activation_key = qm_user_generate_activation_key( $user_id, $user->user_email );
			
			// wp_set_current_user($user_id);
			// wp_set_auth_cookie($user_id);
			
			do_action( 'quizmaker_after_register', $user_id );
			do_action( 'quizmaker_email_new_member', $user_id, $activation_key );
			
			// qm_add_message( __( 'Account registered successfully', 'quizmaker' ), 'success' );

			$redirect = qm_get_endpoint_url( 'view-activation', '?qm-activation=new', qm_get_page_permalink( 'myaccount' ) );
			
			// if( isset($_POST['redirect']) && $_POST['redirect'] ) {

			// 	$redirect = $_POST['redirect'];
			// }

			wp_safe_redirect( $redirect );
			exit;
		}
		
	}
	
	public static function process_login() {
		
		if ( ! empty( $_POST['login'] ) && ! empty( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'quizmaker-login' ) ) {
			
			try {
				$creds    = array();
				$username = trim( $_POST['username'] );

				$validation_error = new WP_Error();
				$validation_error = apply_filters( 'quizmaker_process_login_errors', $validation_error, $_POST['username'], $_POST['password'] );

				if ( $validation_error->get_error_code() ) {
					throw new Exception( '<strong>' . __( 'Error', 'quizmaker' ) . ':</strong> ' . $validation_error->get_error_message() );
				}

				if ( empty( $username ) ) {
					throw new Exception( '<strong>' . __( 'Error', 'quizmaker' ) . ':</strong> ' . __( 'Username is required.', 'quizmaker' ) );
				}

				if ( empty( $_POST['password'] ) ) {
					throw new Exception( '<strong>' . __( 'Error', 'quizmaker' ) . ':</strong> ' . __( 'Password is required.', 'quizmaker' ) );
				}

				if ( is_email( $username ) && apply_filters( 'quizmaker_get_username_from_email', true ) ) {
					$user = get_user_by( 'email', $username );

					if ( isset( $user->user_login ) ) {
						$creds['user_login'] = $user->user_login;
					} else {
						throw new Exception( '<strong>' . __( 'Error', 'quizmaker' ) . ':</strong> ' . __( 'A user could not be found with this email address.', 'quizmaker' ) );
					}

				} else {
					$creds['user_login'] = $username;
				}

				if( !qm_user_is_activation($creds['user_login']) ) {

					throw new Exception( '<strong>' . __( 'Error', 'quizmaker' ) . ':</strong> ' . __( 'Check your email and click on the activation link.', 'quizmaker' ) );
				}

				$creds['user_password'] = $_POST['password'];
				$creds['remember']      = isset( $_POST['rememberme'] );
				$secure_cookie          = is_ssl() ? true : false;



				$user                   = wp_signon( apply_filters( 'quizmaker_login_credentials', $creds ), $secure_cookie );
				
				if ( is_wp_error( $user ) ) {
					$message = $user->get_error_message();
					$message = str_replace( '<strong>' . esc_html( $creds['user_login'] ) . '</strong>', '<strong>' . esc_html( $username ) . '</strong>', $message );
					throw new Exception( $message );
				} else {

					if ( ! empty( $_POST['redirect'] ) ) {
						$redirect = $_POST['redirect'];
					} elseif ( wp_get_referer() ) {
						$redirect = wp_get_referer();
					} else {
						$redirect = qm_get_page_permalink( 'myaccount' );
					}

					// Feedback
					qm_add_message( sprintf( __( 'You are now logged in as <strong>%s</strong>', 'quizmaker' ), $user->display_name ) );

					wp_redirect( apply_filters( 'quizmaker_login_redirect', $redirect, $user ) );
					exit;
				}

			} catch (Exception $e) {	

				qm_add_message( apply_filters('login_errors', $e->getMessage() ), 'error' );

			}
			
		}
	}
	
	public static function process_lost_password() {
		if ( ! empty( $_POST['user_login'] ) && ! empty( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'quizmaker-reset-password' ) ) {
			
			QM_Shortcode_My_Account::retrieve_password();			
		}
	}
	
	public static function process_reset_password() {
		
		$posted_fields = array( 'password_1', 'password_2', 'reset_key', 'reset_login', '_wpnonce' );

		foreach ( $posted_fields as $field ) {
			if ( ! isset( $_POST[ $field ] ) ) {
				return;
			}
			$posted_fields[ $field ] = $_POST[ $field ];
		}

		if ( ! wp_verify_nonce( $posted_fields['_wpnonce'], 'quizmaker-reset-password' ) ) {
			return;
		}

		$user = QM_Shortcode_My_Account::check_password_reset_key( $posted_fields['reset_key'], $posted_fields['reset_login'] );

		if ( $user instanceof WP_User ) {
			if ( empty( $posted_fields['password_1'] ) ) {
				qm_add_message( __( 'Please enter your password.', 'quizmaker' ), 'error' );
			}

			if ( $posted_fields[ 'password_1' ] !== $posted_fields[ 'password_2' ] ) {
				qm_add_message( __( 'Passwords do not match.', 'quizmaker' ), 'error' );
			}

			$errors = new WP_Error();

			do_action( 'validate_password_reset', $errors, $user );
			
			if ( qm_message_count( 'error' ) === 0 ) {
				QM_Shortcode_My_Account::reset_password( $user, $posted_fields['password_1'] );

				do_action( 'quizmaker_reset_password', $user );

				wp_redirect( add_query_arg( 'reset', 'true', remove_query_arg( array( 'key', 'login' ) ) ) );
				exit;
			}
		}
		
	}

	public static function guest_register() {

		if(isset($_POST['quizmaker_guest_register']))
		{

			$errors       = new WP_Error();
			$user         = new stdClass();

			$user_id 	  = get_current_user_id();
			$user->ID     = $user_id;
			$current_user = get_user_by( 'id', $user->ID );

			if ( $user->ID <= 0 ) {
				return;
			}

			$result_id 	=	absInt($_POST['quizmaker_guest_register']);

			$is_account =	! empty( $_POST[ 'is_account' ] ) ? true : false;

			$account_first_name = ! empty( $_POST[ 'account_first_name' ] ) ? qm_clean( $_POST[ 'account_first_name' ] ) : '';
			$account_last_name  = ! empty( $_POST[ 'account_last_name' ] ) ? qm_clean( $_POST[ 'account_last_name' ] ) : '';

			
			$account_email      = ! empty( $_POST[ 'account_email' ] ) ? sanitize_email( $_POST[ 'account_email' ] ) : '';
			$username			= ! empty( $_POST[ 'account_username' ] ) ? sanitize_user( $_POST[ 'account_username' ], 50 ) : $account_email;
			$pass1              = ! empty( $_POST[ 'password_1' ] ) ? $_POST[ 'password_1' ] : '';
			$pass2              = ! empty( $_POST[ 'password_2' ] ) ? $_POST[ 'password_2' ] : '';
			$save_pass          = true;

			$user->first_name   = $account_first_name;
			$user->last_name    = $account_last_name;

			// Prevent emails being displayed, or leave alone.
			$user->display_name = is_email( $current_user->display_name ) ? $user->first_name : $current_user->display_name;

			// Handle required fields
			$required_fields = apply_filters( 'quizmaker_update_guest_account_details_required_fields', array(
				'account_first_name' => __( 'First Name', 'quizmaker' ),
				'account_last_name'  => __( 'Last Name', 'quizmaker' ),
				'account_email'      => __( 'Email address', 'quizmaker' ),
			) );

			foreach ( $required_fields as $field_key => $field_name ) {
				if ( empty( $_POST[ $field_key ] ) ) {
					qm_add_message( '<strong>' . esc_html( $field_name ) . '</strong> ' . __( 'is a required field.', 'quizmaker' ), 'error' );
				}
			}

			if ( $account_email ) {
				if ( ! is_email( $account_email ) ) {
					qm_add_message( __( 'Please provide a valid email address.', 'quizmaker' ), 'error' );
				} elseif ( email_exists( $account_email ) && $account_email !== $current_user->user_email ) {
					qm_add_message( __( 'This email address is already registered.', 'quizmaker' ), 'error' );
				}
				$user->user_email = $account_email;
			}

			if( $is_account ){

				if( $username ) {
			
					if( username_exists( $username ) ) {
						qm_add_message( __( 'This username is already registered.', 'quizmaker' ), 'error' );
					}
					
				}else{
					
					qm_add_message( __( 'This username is empty.', 'quizmaker' ), 'error' );
				}

				if ( ! empty( $pass1 ) && empty( $pass2 ) ) {
					qm_add_message( __( 'Please re-enter your password.', 'quizmaker' ), 'error' );
					$save_pass = false;
				} elseif ( ( ! empty( $pass1 ) || ! empty( $pass2 ) ) && $pass1 !== $pass2 ) {
					qm_add_message( __( 'New passwords do not match.', 'quizmaker' ), 'error' );
					$save_pass = false;
				}

				if ( $pass1 && $save_pass ) {
					$user->user_pass = $pass1;
				}

			}

			if ( $errors->get_error_messages() ) {
				foreach ( $errors->get_error_messages() as $error ) {
					qm_add_message( $error, 'error' );
				}
			}


			if ( qm_message_count( 'error' ) === 0 ) {
			
				wp_update_user( $user ) ;
				update_user_meta( $user->ID, 'is_guest', 0 );

				if( $is_account ){

					update_user_meta( $user_id, 'pending_username', $username );
					
				}

				qm_add_message( __( 'Account details changed successfully.', 'quizmaker' ) );

				do_action( 'quizmaker_after_update_guest_account', $user_id );
				
				wp_redirect( qm_get_result_url($result_id) );
				exit;
			}
		}

	}

	public static function update_username() {
		global $wpdb;

		$current_user 	=	wp_get_current_user();

		$user_id 		=	$current_user->data->ID;

		$username = get_user_meta( $user_id, 'pending_username', true );

		if( isset($username) && $username ){
			$wpdb->update( $wpdb->prefix . 'users', array(
							'user_login'	=>	$username,
							'user_nicename' =>	$username,
							'display_name'	=>	$username
							), array('ID' => $user_id), array('%s', '%s', '%s') );

			delete_user_meta( $user_id, 'pending_username' );
		}

	}

	public static function register_certificate() {

		if( isset($_POST['quizmaker_register_certificate']) && absint( $_POST['quizmaker_register_certificate'] ) ) {

			$certid = absint( $_POST['quizmaker_register_certificate'] );

			$table = new QM_Table_Certificate();

			$table->save(array(
				'user_id' => get_current_user_id()
			));
			
		}
	}

	public static function submit_doing_fillform() {

		if( isset($_POST['quizmaker_doing_fillform']) && absint( $_POST['quizmaker_doing_fillform'] ) ) {

			$session    = 	new QM_Test_Session();
			
			$result_id  =	$session->get('result_id');
			$test_id    =	$session->get('test_id');

			$user_name  =   isset($_POST['name']) ? sanitize_text_field($_POST['name']) : false;
			$user_email = 	isset($_POST['email']) ? sanitize_text_field($_POST['email']) : false;

			$current_user = wp_get_current_user();

			if( ($current_user instanceof WP_User) && $current_user->ID > 0 ) {

				$user_name  = qm_get_formated_user_name( $current_user );
				$user_email = $current_user->user_email;
			}

			if( !$user_name || !$user_email ) { 

				wp_redirect( get_permalink( $test_id ) );
				exit;
			}
			

			$user_meta  = array();  

			if( isset($_POST['user_meta']) && is_array($_POST['user_meta']) && $_POST['user_meta'] ) {

				$form_data = get_form_data_after_play_test();

				if( $form_data ){

					foreach( $_POST['user_meta'] as $u_name => $u_value ) {

						foreach( $form_data as $fd ) {

							if( isset($fd['name']) && ($u_name == $fd['name']) ) {

								if( in_array($fd['type'], array('select', 'radio-group')) && $fd['values'] ) {

									foreach( $fd['values'] as $fd_v ) {

										if( $fd_v['value'] == $u_value ) {

											$user_meta[$fd['label']] = $fd_v['label'];
										}
									}

								}elseif ( in_array($fd['type'], array('text', 'number')) ) {
									
									$user_meta[$fd['label']] = $u_value;

								}elseif ( $fd['type'] == 'checkbox-group' ) {

									foreach( $fd['values'] as $fd_v ) {
										
										if( is_array($u_value) && in_array($fd_v['value'], $u_value) ) {

											$user_meta[$fd['label']][] = $fd_v['label'];
										}
									}

									if( isset($user_meta[$fd['label']]) && $user_meta[$fd['label']] ) {

										$user_meta[$fd['label']] = implode(', ', $user_meta[$fd['label']]);
									}

								}

								
							}
						}
					}
					
				}
			}

			$result = $session->get('result');

			if( $result ){

				$result_id = QM_Test::store_results( $session->get('result'), $test_id );
				
				if( $result_id ) {

					$tblResult  =	new QM_Table_Result();

					$tblResult->save(array(
						'id' 			=> $result_id,
						'user_name' 	=> $user_name,
						'user_email' 	=> $user_email,
						'user_meta' 	=> json_encode( $user_meta )
					));

					$test = new QM_Test( $test_id );

					$test->update_played();
					
					do_action( 'quizmaker_after_submit_result', $result_id, $test_id );

					if( is_email_result($test_id) ){

						do_action( 'quizmaker_email_new_result', $result_id );
					}
					
					wp_redirect( add_query_arg( 'result', $result_id, get_permalink( $test_id ) ) );
					exit;

				}else{
					
					wp_redirect( get_permalink( $test_id ) );
					exit;
				}

			}
			
		}
	}
}

QM_Form_Handler::init();