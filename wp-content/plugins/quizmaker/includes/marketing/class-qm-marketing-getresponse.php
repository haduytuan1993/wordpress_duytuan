<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once( QM_PLUGIN_DIR . 'includes/libraries/getresponse.php' );

class QM_Marketing_Getresponse {

	public $api;

	function __construct() {

		$api_key   = get_option('quizmaker_marketing_app_getresponse_api_key');

		$this->api = new GetResponse( $api_key );
		
	}

	public function getLists() {

		return $this->api->getCampaigns();
	}

	public function pushUser( $user, $lists ) {

		if( $lists && $user ){
			
			foreach( $lists as $index => $list_id ) { 

				$result = $this->api->addContact(array(
					'name' => $user['your_name'],
					'email' => $user['your_email'],
					'dayOfCycle' => 0,
					'campaign' => array(
						'campaignId' => $list_id
					),
					'ipAddress' => $user['your_ip']
				));

			}

		}
	}
}