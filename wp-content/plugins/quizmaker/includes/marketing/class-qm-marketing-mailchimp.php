<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use \DrewM\MailChimp\MailChimp;
use \DrewM\MailChimp\Batch;

class QM_Marketing_Mailchimp {

	public $api;

	function __construct() {

		$api_key   = get_option('quizmaker_marketing_app_mailchimp_api_key');

		$this->api = new MailChimp( $api_key );
		
	}

	public function getLists() {

		return $this->api->get('lists');
	}

	public function pushUser( $user, $lists ) {

		if( $lists && $user ){

			$Batch 	   = $this->api->new_batch();

			foreach( $lists as $index => $list_id ) { 
						
				$Batch->post("op" . $index, "lists/$list_id/members", array(
					'email_address' => $user['your_email'],
					'status'        => 'subscribed'
				));
			}

			$result = $Batch->execute();

		}
	}
}