<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * QM_Woocommerce class.
 */
class QM_Woocommerce {
	
	public function __construct()
	{
		add_action( 'quizmaker_after_submit_result', array( $this, 'validate_assigned_member' ), 10, 2 );
		
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'options_test_data' ) );
		
		add_filter( 'product_type_selector', array( $this, 'add_test_product' ) );

		add_filter( 'product_type_options', array( $this, 'add_download_option' ) );

		add_action( 'admin_footer', array( $this, 'test_custom_js' ) );

		add_filter( 'woocommerce_product_data_tabs', array( $this, 'hide_attributes_data_panel' ) );

		add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'loop_is_show_playing' ), 10, 2 );

		add_action( 'woocommerce_thankyou', array( $this, 'payment_complete'), 10, 1 );

		add_action( 'woocommerce_process_product_meta_test', array( $this, 'save_test_option_field' ) );
		add_action( 'woocommerce_process_product_meta_variable_test', array( $this, 'save_test_option_field' ) );

		// Assign user to test belong to order status
		add_action( 'woocommerce_order_status_completed', array( $this, 'order_status_completed' ) );
		add_action( 'woocommerce_order_status_cancelled', array( $this, 'order_status_cancelled' ) );
		add_action( 'woocommerce_order_status_refunded', array( $this, 'order_status_cancelled' ) );
		add_action( 'woocommerce_order_status_pending', array( $this, 'order_status_cancelled' ) );
		add_action( 'woocommerce_order_status_failed', array( $this, 'order_status_cancelled' ) );
		add_action( 'woocommerce_order_status_on-hold', array( $this, 'order_status_processing' ) );
		add_action( 'woocommerce_order_status_processing', array( $this, 'order_status_processing' ) );
		
	}

	public function update_user_paid_test( $user_id, $test_id, $order_id ) {

		if( !isset($user_id) || !isset($test_id) || !isset($order_id) ) { return false; }

		$test_id 	=	absInt($test_id);
		$order_id 	=	absInt($order_id);

		$test 		=	QM()->test_factory->get_test( $test_id );

		if( !isset($test) || !$test ){ return false; }

		$paid_tests 	=	get_user_meta( $user_id, 'paid_tests', true );
		
		
		if( !isset( $paid_tests ) || !is_array( $paid_tests ) || is_string( $paid_tests ) )
		{
			$paid_tests = array();
		}

		$is_double = false;

		if( $paid_tests )
		{
			foreach( $paid_tests as $paid_test ) {
				if( isset($paid_test['test_id']) && $paid_test['test_id'] == $test_id )
				{
					$is_double = true;
				}
			}
		}

		if( !$is_double )
		{
			array_push( $paid_tests, array( 'test_id' => $test_id, 'order_id' => $order_id ) );
			update_user_meta( $user_id, 'paid_tests', $paid_tests );
		}
		
		$test->assign_users( array( $user_id ) );

	}

	public function update_user_paid_histories( $user_id, $order_id ) {

		if( !isset($user_id) || !isset($order_id) || !isset($params) ) { return false; }

		$order 			= 	wc_get_order( $order_id );
		$paid_histories	=	get_user_meta( $user_id, 'paid_histories', true );

		if( !isset( $paid_histories ) || !is_array( $paid_histories ) || is_string( $paid_histories ) )
		{
			$paid_histories = array();
		}

		$is_double = false;

		if( $paid_histories )
		{
			foreach( $paid_histories as $paid_history ) {
				if( isset($paid_history['order_id']) && $paid_history['order_id'] == $order_id )
				{
					$is_double = true;
				}
			}
		}

		if( !$is_double ) { 

			$params = array();

			$params['total'] 	=	$order->get_total();
			$params['status']	=	$order->get_status();
			$params['date']		=	$order->order_date;

			array_push( $paid_histories, array( 'test_id' => $test_id, 'order_id' => $order_id, 'params' => $params ) );

			update_user_meta( $user_id, 'paid_histories', $paid_histories );
		}
	}

	public function remove_user_paid_test( $user_id, $test_id )
	{
		$test	=	QM()->test_factory->get_test( $test_id );
		
		if( !$test || !$user_id ){ return false; }
		
		$paid_tests = get_user_meta( $user_id, 'paid_tests', true );
		
		if( !isset($paid_tests) || !is_array($paid_tests) ){ return false; }
		
		foreach( $paid_tests as $index => $paid_test )
		{
			if( isset($paid_test['test_id']) && $test_id == $paid_test['test_id'] )
			{
				unset($paid_tests[$index]);
			}
		}

		$test->remove_assigned_users( array($user_id) );
		update_user_meta( $user_id, 'paid_tests', $paid_tests );
	}

	public function validate_assigned_member( $result_id, $test_id )
	{

		$user_id	=	get_current_user_id();

		if( $test_id && $user_id )
		{
			
			$can_do 	=	qm_can_do_test( $test_id, $user_id );

			if( !$can_do )
			{
				$this->remove_user_paid_test( $user_id, $test_id );
			}
		}
	}

	public function options_test_data() {
		
		global $post;
		?>

		<div class="options_group show_if_test">
			<div class="form-field selected_test">
				<?php 

				$test_id 		=	get_post_meta( $post->ID, '_quizmaker_test_id', true);

				$test_selected	=	'';

				if( $test_id ){
					$post_test 		=	get_post( $test_id );
					$test_selected	=	wp_kses_post( html_entity_decode( $post_test->post_title, ENT_QUOTES, get_bloginfo( 'charset' ) ) );					
				}
				
				woocommerce_wp_select( array(
					'class'			=>	'qm-test-search',
					'id'			=> '_quizmaker_test_id',
					'label'			=> __( 'Test', 'quizmaker' ),
					'desc_tip'		=> 'true',
					'description'	=> __( 'Select a test from Quizmaker', 'quizmaker' ),
					'style'			=>	'width: 50%;',
					'type'			=>	'text',
					'placeholder'	=>	__('Search for Tests', 'quizmaker'),
					'custom_attributes'	=>	array(
						'data-action' 	=>	'quizmaker_json_search_tests',
						'data-multiple'	=>	'false',
						'data-minimum_input_length' => 3,
						'data-placeholder'	=>	__('Search for Tests', 'quizmaker'),
						'data-limit'	=>	10,
						'data-allow_clear' => false,
						'data-selected'	=>	esc_attr($test_selected),
						'data-exclude'	=>	$test_id
					),
					'value'			=>	$test_id
				) );

				 ?>
			</div>
		</div>

		<?php
	}

	public function add_test_product( $types )
	{

		$types[ 'test' ] = __( 'Quizmaker Test', 'quizmaker' );

		return $types;
	}

	public function add_download_option( $options ) {

		$options['downloadable']['wrapper_class'] .= ' show_if_test'; 

		return $options;
	}

	public function test_custom_js(){

		$post_type = get_post_type();

		if ( 'product' != $post_type ) {
			return;
		}
		//var_dump(WC_Product_Factory::get_product_type(get_the_ID())); exit;
	?><script type='text/javascript'>
		jQuery( function( $ ) {
		var is_test = <?php echo WC_Product_Factory::get_product_type(get_the_ID()) == 'test' ? 1 : 0; ?>;

		$( document ).ready( function() {
			jQuery( '.options_group.pricing' ).addClass( 'show_if_test' ).show();

			if( is_test ){
				jQuery( '.general_options' ).show();
				// jQuery('.options_group.show_if_downloadable').css('display', 'none');
			}

			jQuery( 'body' ).on( 'woocommerce-product-type-change', function( event, select_val, select ) {
				if(select_val == 'test'){
					// jQuery('.options_group.show_if_downloadable').css('display', 'none');
				}
			});

			jQuery( 'select.qm-test-search' ).each( function() {

				var select2_args = {
					allowClear:  jQuery( this ).data( 'allow_clear' ) ? true : false,
					placeholder: jQuery( this ).data( 'placeholder' ),
					minimumInputLength: 3,
					escapeMarkup: function (markup) { return markup; },
					ajax: {
						url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
						dataType:    'json',
						delay: 250,
						data: function( term ) {
							
							return {
								term:     term,
								action:   jQuery( this ).data( 'action' ),
								security: '<?php echo wp_create_nonce( "search_tests_nonce" ); ?>',
								exclude:  jQuery( this ).val(),
								limit:    jQuery( this ).data( 'limit' )
							};
						},
						processResults: function( data ) {
							var terms = [];
							if ( data ) {
								jQuery.each( data, function( id, text ) {
									terms.push( { id: id, text: text } );
								});
							}
							return {
								results: terms
							};
						},
						cache: false
					}
				};

				select2_args.multiple = false;
					select2_args.initSelection = function( element, callback ) {
						var data = {
							id: element.val(),
							text: element.attr( 'data-selected' )
						};
						return callback( data );
					};

				$(this).select2( select2_args );
				
			});

		});
		});
	</script><?php
	}

	public function hide_attributes_data_panel( $tabs ){

		$tabs['linked_product']['class'][]	= 'hide_if_test hide_if_variable_test';
		$tabs['shipping']['class'][]		= 'hide_if_test hide_if_variable_test';
		$tabs['inventory']['class'][]		= 'show_if_test';

		return $tabs;
	}

	public function save_test_option_field( $post_id ){

		if ( isset( $_POST['_quizmaker_test_id'] ) ) :
			update_post_meta( $post_id, '_quizmaker_test_id', sanitize_text_field( $_POST['_quizmaker_test_id'] ) );
		endif;
	}

	// Only assign user to test when products is free
	public function order_status_processing( $order_id ) {

		$order 		= wc_get_order( $order_id );
		$products 	= $order->get_items();

		if( isset($products) && is_array($products) && count($products) > 0 ){

			foreach( $products as $p ){

				$product_id = absint($p['product_id']);
				$product 	= WC()->product_factory->get_product( $product_id );

				if( $product instanceof WC_Product_Test && $product->get_price() == 0 ){
					
					$test_id = get_post_meta( $product_id, '_quizmaker_test_id', true );
					$user_id = $order->customer_user;

					if( isset($user_id) && $user_id > 0 && isset($test_id) && $test_id > 0 ){

						$test	=	QM()->test_factory->get_test( $test_id );
			
						if( isset($test) && $test ){
							
							$this->update_user_paid_test( $user_id, $test_id, $order_id );
							$this->update_user_paid_histories( $user_id, $order_id );
							
						}
					}

				}
			}
		}
	}

	public function order_status_completed( $order_id ) {

		$order 		= wc_get_order( $order_id );
		$products 	= $order->get_items();

		if( isset($products) && is_array($products) && count($products) > 0 ){

			foreach( $products as $p ){

				$product_id = absint($p['product_id']);
				$product 	= WC()->product_factory->get_product( $product_id );

				if( $product instanceof WC_Product_Test ){
					
					$test_id = get_post_meta( $product_id, '_quizmaker_test_id', true );
					$user_id = $order->get_customer_id();

					if( isset($user_id) && $user_id > 0 && isset($test_id) && $test_id > 0 ){

						$this->update_user_paid_test( $user_id, $test_id, $order_id );
						$this->update_user_paid_histories( $user_id, $order_id );
					}

				}
			}
		}

	}

	public function order_status_cancelled( $order_id ) {

		$order 		= wc_get_order( $order_id );
		$products 	= $order->get_items();

		if( isset($products) && is_array($products) && count($products) > 0 ){

			foreach( $products as $p ){

				$product_id = absint($p['product_id']);
				$product 	= WC()->product_factory->get_product( $product_id );

				if( $product instanceof WC_Product_Test ){
					
					$test_id = get_post_meta( $product_id, '_quizmaker_test_id', true );
					$user_id = $order->get_customer_id();

					if( isset($user_id) && $user_id > 0 && isset($test_id) && $test_id > 0 ){

						$test	=	QM()->test_factory->get_test( $test_id );
			
						if( isset($test) && $test ){
														
							$this->remove_user_paid_test( $user_id, $test_id );
						}
					}

				}
			}
		}

	}

	public function loop_is_show_playing( $html_add_to_cart, $product ) {

		if( $product instanceof WC_Product_Test ){

			$is_free =	$product->get_price() == 0;

		}

		return $html_add_to_cart;
	}

	public function payment_complete( $order_id ) {

		if ( ! $order_id ) {
			return;
		}

		$order = wc_get_order( $order_id );

		// No updated status for orders delivered with Bank wire, Cash on delivery and Cheque payment methods.
		if ( ( get_post_meta($order_id, '_payment_method', true) == 'bacs' ) || ( get_post_meta($order_id, '_payment_method', true) == 'cod' ) || ( get_post_meta($order_id, '_payment_method', true) == 'cheque' ) ) {
			return;
		} 

		// "completed" updated status for paid Orders with all others payment methods
		else {

			if($order->get_status() != 'completed'){
				$order->update_status( 'completed' );
			}
		}

		echo '<a href="' . qm_get_page_permalink('myaccount') . 'view-assigned-tests/" class="qm-button">' . __('Your Ordered Tests', 'quizmaker') . '</a>';
	}

}

function register_test_product_type() {

	class WC_Product_Test extends WC_Product_Simple {

		public function __construct( $product ) {

			$this->product_type  = 'test';
			$this->virtual = 'yes';
			$this->sold_individually = 'yes';
			$this->supports[]   = 'ajax_add_to_cart';

			parent::__construct( $product );

		}

		public function get_type() {

			return 'test';
		}

		/**
		 * Get the add to url used mainly in loops.
		 *
		 * @return string
		 */
		public function add_to_cart_url() {
			$url = $this->is_purchasable() && $this->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->id ) ) : get_permalink( $this->id );

			return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
		}

		/**
		 * Get the add to cart button text.
		 *
		 * @return string
		 */
		public function add_to_cart_text() {
			$text = $this->is_purchasable() && $this->is_in_stock() ? __( 'Add to cart', 'quizmaker' ) : __( 'Read more', 'quizmaker' );

			return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );
		}

	}

}
add_action( 'plugins_loaded', 'register_test_product_type' );

if ( ! function_exists( 'woocommerce_test_add_to_cart' ) ) {

	/**
	 * Output the simple product add to cart area.
	 *
	 * @subpackage	Product
	 */
	function woocommerce_test_add_to_cart() {
		wc_get_template( 'single-product/add-to-cart/simple.php' );
	}
}
add_action( 'woocommerce_test_add_to_cart', 'woocommerce_test_add_to_cart', 30 );

new QM_Woocommerce();