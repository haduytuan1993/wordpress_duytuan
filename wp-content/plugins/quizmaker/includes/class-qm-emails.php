<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }


class QM_Emails {

	/** @var array Array of email notification classes */
	public $emails;
	
	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 2.1
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'quizmaker' ), '1.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 2.1
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'quizmaker' ), '1.0' );
	}

	/**
	 * Hook in all transactional emails.
	 */
	public static function init_transactional_emails() {
		$email_actions = apply_filters( 'quizmaker_email_actions', array(
			'quizmaker_email_new_member',
			'quizmaker_email_new_membership',
			'quizmaker_email_new_result',
			'quizmaker_email_assigned_users',
			'quizmaker_email_reset_password'
		) );

		foreach ( $email_actions as $action ) {
			add_action( $action, array( __CLASS__, 'send_transactional_email' ), 10, 10 );
		}
	}

	/**
	 * Init the mailer instance and call the notifications for the current filter.
	 * @internal param array $args (default: array())
	 */
	public static function send_transactional_email() {
		self::instance();
		$args = func_get_args();
		
		do_action_ref_array( current_filter() . '_notification', $args );
	}

	/**
	 * Constructor for the email class hooks in all emails that can be sent.
	 *
	 */
	public function __construct() {
		$this->init();

		// Email Header, Footer and content hooks
		add_action( 'quizmaker_email_header', array( $this, 'email_header' ) );
		add_action( 'quizmaker_email_footer', array( $this, 'email_footer' ) );
		
		add_action( 'quizmaker_email_new_member_notification', array( $this, 'new_member' ), 10, 3 );
		add_action( 'quizmaker_email_new_result_notification', array( $this, 'new_result' ), 10, 3 );
		add_action( 'quizmaker_email_assigned_users_notification', array( $this, 'assign_users' ), 10, 3 );
		add_action( 'quizmaker_email_reset_password_notification', array( $this, 'reset_password' ), 10, 3 );
		
		// Let 3rd parties unhook the above via this hook
		do_action( 'quizmaker_email', $this );
	}

	/**
	 * Init email classes.
	 */
	public function init() {
		
		
		
	}

	/**
	 * Return the email classes - used in admin to load settings.
	 *
	 * @return array
	 */
	public function get_emails() {
		return $this->emails;
	}

	/**
	 * Get from name for email.
	 *
	 * @return string
	 */
	public function get_from_name() {
		return wp_specialchars_decode( get_option( 'quizmaker_email_from_name' ), ENT_QUOTES );
	}

	/**
	 * Get from email address.
	 *
	 * @return string
	 */
	public function get_from_address() {
		return sanitize_email( get_option( 'quizmaker_email_from_address' ) );
	}

	/**
	 * Get the email header.
	 *
	 * @param mixed $email_heading heading for the email
	 */
	public function email_header( $email_heading ) {
		qm_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );
	}

	/**
	 * Get the email footer.
	 */
	public function email_footer() {

		$email_footer = get_option('quizmaker_email_footer_text');

		qm_get_template( 'emails/email-footer.php', array( 'email_footer' => $email_footer ) );
	}
	
	public function wrap_message( $email_heading, $message, $plain_text = false ) {
		// Buffer
		ob_start();

		do_action( 'quizmaker_email_header', $email_heading );

		echo wpautop( wptexturize( $message ) );

		do_action( 'quizmaker_email_footer' );

		// Get contents
		$message = ob_get_clean();

		return $message;
	}
	
	public function send( $to, $subject, $message, $headers = "Content-Type: text/html\r\n", $attachments = "" ) {
		// Send
		
		if( !is_array($headers) ){

			$headers	=	array();
		}
		
		$headers[]	=	"Content-Type: text/html\r\n";
		
		$from_name		=	get_option('quizmaker_email_from_name');
		$from_address	=	get_option('quizmaker_email_from_address');
		
		if(!$from_name){
			
			$from_name	=	'Quizmaker';
		}
		
		if(!$from_address || !is_email($from_address)){
			
			$from_address	=	get_option('admin_email');
		}
		
		$headers[]	=	sprintf('From: %s <%s>', $from_name, $from_address);
			
		wp_mail( $to, $subject, $message, $headers, $attachments );
	}
	
	public function new_member( $user_id, $activation_key ) {
		
		if( !$user_id ) return false;
		
		$user	=	new WP_User($user_id);
		
		$to				=	$user->user_email;
		
		$subject		=	get_option('quizmaker_email_new_member_subject');
		$email_heading	=	get_option('quizmaker_email_new_member_heading');
		
		$email_content	=	$this->get_content('emails/new-member.php', array('user' => $user));
		
		$activation_link = qm_get_endpoint_url( 'view-activation', '?qm-activation=' . $activation_key, qm_get_page_permalink( 'myaccount' ) );

		$patterns	=	array( 
			'user_name' 	=>  $user->user_login,
			'activation_link' => $activation_link
		);

		foreach( $patterns as $pattern => $value ) {
			
			$pattern	=	'[{' . $pattern . '}]';
			
			$subject		=	str_replace( $pattern, $value, $subject );
			$email_heading	=	str_replace( $pattern, $value, $email_heading );
			$email_content	=	str_replace( $pattern, $value, $email_content );
		}
		
		$message	=	$this->wrap_message( $email_heading, $email_content );
		
		$this->send( $to, $subject, $message );
	}
	
	public function new_result( $result_id ) {
		
		if( !$result_id ) return false;
		
		$result	=	qm_get_result( $result_id );
		
		if( !$result ) return false;

		$headers	=	array();
		
		$test	=	$result['test'];

		$user_email		=	$result['user_email'];
		$user_nicename	=	$result['user_name'];

		$user	=	new WP_User($result['user_id']);

		if( ($user instanceof WP_User) && $user->ID > 0 ){
			
			$user_email		=	$user->user_email;
			$user_nicename	=	$user->user_nicename;
		}
		
		$to = array( $user_email );
		
		$subject		=	get_option('quizmaker_email_new_result_subject');
		$email_heading	=	get_option('quizmaker_email_new_result_heading');
		$admin_email 	= 	get_option('quizmaker_email_notification_new_result_recipient');

		if( $admin_email ) {

			$headers[] = 'Cc:' . $admin_email;
		}
		
		$email_content	=	$this->get_content('emails/new-result.php', array('result' => $result));
		
		$patterns	=	array( 
			'test_title' 	=>  $test->get_title(),
			'test_link'		=>	$test->get_permalink(),	
			'duration'		=>	$result['duration'],
			'score'			=>	$result['score'],
			'total_score'	=>	$result['total_score'],
			'date'			=>	$result['date_start'],
			'user_nicename'	=>	$user_nicename,
			'user_email'    =>  $user_email,
			'user_meta'		=>	qm_get_formated_user_meta($result['user_meta'])
		);

		$ranking = qm_is_ranking( $test->get_id(), $result['score'], $result['total_score'], false );

		$attached_cert = array();

		if( $ranking )
		{
			$get_certificate = qm_view_certificate_test_url( $test->get_id(), $result_id );

			$patterns['user_cert_id'] 		  = $result['cert_id'];

			$patterns['ranking'] 			  = $ranking['name'];
			$patterns['ranking_message'] 	  = isset($ranking['message']) ? $ranking['message'] : '';
			
			$patterns['download_certificate'] = '<a href="' . $get_certificate . '">' . __('Download Certificate', 'quizmaker') . '</a>';

			if( isset($ranking['certificate']) && $ranking['certificate'] ) {

				$result['ranking']			=	$ranking['name'];
				$result['certificate_id']	=	$ranking['certificate'];
				$result['ranking_data']		=	$ranking;

			}
			
			$attached_cert[] = $test->get_certificate( $result, false );
			
		}else{
			
			$patterns['ranking'] = '';
			$patterns['user_cert_id'] = '';
			$patterns['download_certificate'] = '';
		}

		foreach( $patterns as $pattern => $value ) {
			
			$pattern	=	'[{' . $pattern . '}]';
			
			$subject		=	str_replace( $pattern, $value, $subject );
			$email_heading	=	str_replace( $pattern, $value, $email_heading );
			$email_content	=	str_replace( $pattern, $value, $email_content );
		}
		
		$message	=	$this->wrap_message( $email_heading, $email_content );
		
		$this->send( $to, $subject, $message, $headers, $attached_cert );

		if( $attached_cert ) {

			foreach( $attached_cert as $ac ) {

				if( file_exists( $ac ) ) {

					unlink( $ac );
				}
			}
		}
	}
	
	public function assign_users( $users_id = array(), $test_id ) {
		
		if( !$users_id || !is_array($users_id) || !$test_id || !absInt($test_id) ) return false;
		
		foreach( $users_id as $user_id ) {
			
			$user	=	new WP_User(absInt($user_id));
					
			$to				=	$user->user_email;
			
			$subject		=	get_option('quizmaker_email_assign_test_subject');
			$email_heading	=	get_option('quizmaker_email_assign_test_heading');
			
			$email_content	=	$this->get_content('emails/assign_users.php');

			$test 		=	new QM_Test($test_id);
		
			$patterns	=	array( 
				'test_title' 	=>  $test->get_title(),
				'test_link'		=>	$test->get_permalink(),	
				'name' 			=>  $user->user_nicename,
				'email'			=>	$user->user_email
			);
		
			foreach( $patterns as $pattern => $value ) {
			
				$pattern	=	'[{' . $pattern . '}]';
			
				$subject		=	str_replace( $pattern, $value, $subject );
				$email_heading	=	str_replace( $pattern, $value, $email_heading );
				$email_content	=	str_replace( $pattern, $value, $email_content );
			}
			
			$message	=	$this->wrap_message( $email_heading, $email_content );
			
			$this->send( $to, $subject, $message );
			
		}

		return true;
		
	}
	
	public function reset_password( $user_login = '', $reset_key = '' ) {
		
		if ( $user_login && $reset_key ) {
			$user			=	get_user_by( 'login', $user_login );

			$to				=	stripslashes( $user->user_email );
			
			$subject		=	get_option('quizmaker_email_reset_password_subject');
			$email_heading	=	get_option('quizmaker_email_reset_password_heading');
			
			$link_reset		=	esc_url( add_query_arg( array( 'key' => $reset_key, 'login' => rawurlencode( $user_login ) ), qm_get_endpoint_url( 'qm-lost-password', '', qm_get_page_permalink( 'myaccount' ) ) ) );
			
			$email_content	=	$this->get_content('emails/reset-password.php', array('user_login' => $user_login, 'link_reset' => $link_reset));
			
			$message	=	$this->wrap_message( $email_heading, $email_content );
			
			$this->send( $to, $subject, $message );
		}
	}
	
	public function get_content( $template, $args = array() ) {
		
		ob_start();
		
		qm_get_template( $template, $args );
		
		return ob_get_clean();
	}
	
}
