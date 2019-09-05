<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'TREMtrPayments' ) ) {
/**
 * Class to handle a reservation for Cafe Table Bookings
 *
 * @since 0.0.1
 */
	class TREMtrPayments {

		public function __construct() {
			require_once( ABSPATH . '/wp-content/plugins/woocommerce/woocommerce.php' );

			// PC::magic_tag($settings);

			// $this->payments_enable = array_values(array_values($settings)[0])[0];
			// $this->payments_enable = '0';
			// PC::magic_tag($settings);

			// $this->payment();

			// add_action( 'my_new_event1',array($this, 'payment') );
			// wp_schedule_single_event( time() + 180, 'my_new_event1' );
			
			
			// public function update_products($payments_settings){

			// 	foreach ($payments_settings as $cafe) {
			// 		if ($cafe[0] == 1 && $cafe[4] != null) {
			// 			$_product = wc_get_product( $id );
			// 			if (condition) {
			// 				# code...
			// 			}
			// 		}

			// 		$_product = $_pf->get_product($id);
				
			// 		// from here $_product will be a fully functional WC Product object, 
			// 		// you can use all functions as listed in their api
			// 	}
			// 	return $payments_settings;
			// }


		}

		public function update_assoc_products($settings)
		{

			foreach ($settings as $cafe) {

				if (!$cafe[1] && $cafe[5] == 'publish') {
					$new_id = $this->create_new_assoc_prod($cafe);
					update_post_meta( $cafe[0], 'tremtr_fee_id', $new_id);
				}
	
				if ($cafe[1] && $cafe[5] != 'publish') {
					$_product = wc_get_product($cafe[1][0]);
						if ($_product) {
							$_product->delete();
						}
				}
	
				if ($cafe[1] && $cafe[5] == 'publish') {
					$_product = wc_get_product($cafe[1][0]);
						if ($_product->get_price() != $cafe[3]) {
							$_product->set_regular_price($cafe[3]);
							$_product->save();
						}
				}
			}
		}

		public function create_new_assoc_prod($cafe)
		{
			$objProduct = new WC_Product();
			$objProduct->set_name(get_the_title($cafe[0])); //Set product name.
			$objProduct->set_status('private'); //Set product status.
			$objProduct->set_catalog_visibility('visible'); //Set catalog visibility.                   | string $visibility Options: 'hidden', 'visible', 'search' and 'catalog'.
			$objProduct->set_description('Associated product for Cafe'); //Set product description.
			$objProduct->set_short_description('Associated product for Cafe'); //Set product short description.
			$objProduct->set_regular_price($cafe[3]);
			$objProduct->set_virtual(TRUE);
			$objProduct->set_reviews_allowed(FALSE); //Set if reviews is allowed.                        | bool
			$new_product_id = $objProduct->save(); //Saving the data to create new product, it will return product ID.

			return $new_product_id;
		}


		public function create_new_order($order_data)
		{
			$address = array(
				'first_name' => $order_data['name'],
				'last_name'  => '',
				'company'    => '',
				'email'      => $order_data['email'],
				'phone'      => $order_data['phone'],
				'address_1'  => '',
				'address_2'  => '',
				'city'       => '',
				'state'      => '',
				'postcode'   => '',
				'country'    => ''
			);

			$order = wc_create_order();

			$productId = $order_data['product_id'];

			$order->add_product( get_product( $productId ), 1 );

			$order->set_address( $address, 'billing' );
			$order->calculate_totals();

			update_post_meta( $order->id, 'cafe_name', $order_data['cafe_name'] );
			update_post_meta( $order->id, 'cafe_id', $order_data['cafe_id'] );
			update_post_meta( $order->id, 'reservation_id', $order_data['reservation_id'] );

			update_post_meta( $order->id, 'table', $order_data['cafe_name'] );
			update_post_meta( $order->id, 'people', $order_data['people'] );
			update_post_meta( $order->id, 'reservation_date', $order_data['reservation_date'] );
			update_post_meta( $order->id, 'reservation_time_begin', $order_data['reservation_time_begin'] );
			update_post_meta( $order->id, 'reservation_time_end', $order_data['reservation_time_end'] );
			update_post_meta( $order->id, 'message', $order_data['message'] );

			update_post_meta( $order->id, '_payment_method', 'paypal' );
			update_post_meta( $order->id, '_payment_method_title', 'paypal' );

			// Process Payment
			$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

			$result = $available_gateways[ 'paypal' ]->process_payment( $order->id );

			// // Redirect to success/confirmation/payment page
			if ( $result['result'] == 'success' ) {

				$result = apply_filters( 'woocommerce_payment_successful_result', $result, $order->id );

				return $result['redirect'];
			}
			return false;
		}

	}
} // endif;
