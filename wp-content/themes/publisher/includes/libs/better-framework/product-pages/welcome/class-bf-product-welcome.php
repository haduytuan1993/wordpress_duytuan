<?php
/***
 *  BetterFramework is BetterStudio framework for themes and plugins.
 *
 *  ______      _   _             ______                                           _
 *  | ___ \    | | | |            |  ___|                                         | |
 *  | |_/ / ___| |_| |_ ___ _ __  | |_ _ __ __ _ _ __ ___   _____      _____  _ __| | __
 *  | ___ \/ _ \ __| __/ _ \ '__| |  _| '__/ _` | '_ ` _ \ / _ \ \ /\ / / _ \| '__| |/ /
 *  | |_/ /  __/ |_| ||  __/ |    | | | | | (_| | | | | | |  __/\ V  V / (_) | |  |   <
 *  \____/ \___|\__|\__\___|_|    \_| |_|  \__,_|_| |_| |_|\___| \_/\_/ \___/|_|  |_|\_\
 *
 *  Copyright © 2017 Better Studio
 *
 *
 *  Our portfolio is here: https://betterstudio.com/
 *
 *  \--> BetterStudio, 2018 <--/
 */


/**
 * Class BF_Product_Welcome
 */
class BF_Product_Welcome extends BF_Product_Item {

	public $id = 'welcome';

	/**
	 * Store ajax params list.
	 *
	 * @var array
	 */
	protected $params;


	public function render_content( $item_data ) {

		if ( ! empty( $item_data['include_file'] ) && file_exists( $item_data['include_file'] ) ) {
			include $item_data['include_file'];
		}
	}


	public function ajax_request( $params ) {

		if ( empty( $params['bs_pages_action'] ) ) {
			return;
		}

		$this->params = $params;

		try {
			switch ( $params['bs_pages_action'] ) {

				case 'register':

						$this->handle_reponse(
							$this->api_request( 'register-product', array(), array( 'purchase_code' => 'nullmasterinbabiato' ) )
						);
				break;
			}
		} catch( Exception $e ) {

			wp_send_json( array( 'status' => 'error', 'error-message' => $e->getMessage() ) );

		}

	}


	/**
	 * @param array $response
	 *
	 * @throws Exception
	 */
	protected function handle_reponse( $response ) {

	

		$purchase_code = 'nullmasterinbabiato';
		$status = 'valid';

			bf_delete_transient( 'bf-plugins-config' ); // Clear plugins list cache

			bf_register_product_set_info( compact( 'purchase_code', 'status' ) );
		

		wp_send_json( $response );
	}
}
