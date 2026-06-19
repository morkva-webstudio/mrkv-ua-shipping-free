<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_UKR_POSHTA_INVOICE'))
{
	/**
	 * Class for setup shipping methods invoice create
	 */
	class MRKV_UA_SHIPPING_UKR_POSHTA_INVOICE
	{
		/**
		 * @param object Order
		 * */
		private $order;

		/**
		 * @param array POST field
		 * */
		private $post_fields;

		/**
		 * @param object Shipping object
		 * */
		private $shipping_api;

		/**
		 * @param object Shipping settings
		 * */
		private $settings_shipping;

		/**
		 * Constructor for plugin shipping methods invoice create
		 * */
		function __construct($order, $post_fields, $shipping_api, $settings_shipping)
		{
			# Set all variable
			$this->order = $order;
			$this->post_fields = apply_filters('mrkv_ua_shipping_invoice_post_data', $post_fields,$order,$settings_shipping, 'ukr-poshta');
			$this->shipping_api = $shipping_api;
			$this->settings_shipping = $settings_shipping;
		}

		public function mrkv_ua_ship_create_invoice()
		{
			$current_shipping_method = '';

			foreach($this->order->get_items( 'shipping' ) as $method_data)
			{
				$current_shipping_method = $method_data->get_method_id();
			}

			$mrkv_ua_shipping_args = array();
			$status = '';
			$message = '';
			$invoice = '';

			$declared_price = $this->get_declared_price();

			# Sender data 
			$sender_address_id = $this->get_sender_address_id();
			$sender = $this->get_sender_uuid($sender_address_id);				

			# Recipient data 
			$recipient = $this->get_recipient_uuid($current_shipping_method);
			$paid_by_recipient = $this->get_paid_by_recipient('shipment');

			# Shipment data
			$shipment_type = $this->get_shipment_type();
			$delivery_type = ($current_shipping_method == 'mrkv_ua_shipping_ukr-poshta_address') ? 'W2D' : 'W2W';
			$dimension_unit = get_option( 'woocommerce_dimension_unit' );

			# Additional data
			$description = $this->get_description();
			$receive_type = $this->get_receive_type();
			$post_pay = $this->get_postpay();
			$post_pay_recipient = $this->get_post_pay_recipient('shipment');
			$transfer_post_pay = $this->get_transfer_post_pay($declared_price, $post_pay);
			$weight = $this->get_weight('shipment');
			$length = $this->get_length('shipment', $dimension_unit);
			$height = $this->get_height_shipment('shipment', $dimension_unit);
			$width = $this->get_width_shipment('shipment', $dimension_unit);

			if($paid_by_recipient)
			{
				$receive_type = 'RETURN';
			}

			$mrkv_ua_shipping_args = array(
		    	"sender" 			=> array( "uuid" => $sender ),
		    	"recipient" 		=> array( "uuid" => $recipient ),
		    	"type" 				=> $shipment_type,
		        "senderAddressId"   => $sender_address_id,
		    	"deliveryType"		=> $delivery_type,
		    	"paidByRecipient"	=> $paid_by_recipient,
		        "weight"            => intval($weight),
		        "length"            => $length,
		    	"description" 		=> $description,
		    	"onFailReceiveType" => $receive_type,
		    	"postPay" 			=> $post_pay,
		    	"postPayPaidByRecipient"   => $post_pay_recipient,
		    	"parcels"			=> array( array(
		    		"weight"			=> intval($weight),
		    		"length"			=> $length,
		    		"height"			=> $height,
		    		"width"			=> $width,
		    		"declaredPrice" 	=> $declared_price,
			    )),
			    "checkOnDelivery"	=> true,
			    "transferPostPayToBankAccount" => $transfer_post_pay
		    );

		    $mrkv_ua_shipping_args = apply_filters( 'mrkv_ua_shipping_arg_invoice_data', $mrkv_ua_shipping_args, $this->order, $current_shipping_method );

			# Send request
	        $obj = $this->shipping_api->send_post_request_curl('ecom/0.0.1/shipments', 'POST', $mrkv_ua_shipping_args, 'token');

	        if(isset($obj['barcode']))
	        {
	        	$status = 'completed';
	        	$message = __('Invoice created','mrkv-ua-shipping') . ' #' . $obj['barcode']  . '<img src="' .  esc_url(MRKV_UA_SHIPPING_IMG_URL . '/global') . '/copy-ttn.svg" alt="' . esc_html__('Copy invoice', 'mrkv-ua-shipping') . '" title="' . esc_html__('Copy invoice', 'mrkv-ua-shipping') . '">';
	        	$invoice = $obj['barcode'];

	        	$this->order->update_meta_data('mrkv_ua_ship_invoice_number', $obj['barcode']);
	        	$this->order->update_meta_data('mrkv_ua_ship_invoice_ref', $obj['uuid']);

	        	if(isset($this->post_fields['mrkv_ua_ship_invoice_first_name']) && $this->post_fields['mrkv_ua_ship_invoice_first_name'])
	        	{
	        		$this->order->add_order_note(__('Manually created invoice','mrkv-ua-shipping') . ': ' . $obj['barcode'], $is_customer_note = 0, $added_by_user = false);
	        	}
	        	else
	        	{
	        		$this->order->add_order_note(__('Automatic created invoice','mrkv-ua-shipping') . ': ' . $obj['barcode'], $is_customer_note = 0, $added_by_user = false);
	        	}

	        	$this->order->save();

	        	# Send invoice number
        		do_action('mrkv_keycrm_send_invoice_number', $this->order->get_id());
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
        		do_action('salesdrive_send_order_send_ttn', $this->order->get_id(), $invoice, '');
	        }
	        else
	        {
	        	$status = 'failed';

	        	if(isset($obj) && $obj)
		    	{
		    		$error_message = array($obj);
		    	}
		    	else
		    	{
		    		$error_message = array(__('Error with create invoice', 'mrkv-ua-shipping'));
		    	}

		    	# Save to log
				$this->shipping_api->debug_log->add_data_error($error_message);
				$this->order->add_order_note(__('Error create invoice','mrkv-ua-shipping') . ': ' . wp_json_encode( $error_message, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ), $is_customer_note = 0, $added_by_user = false);
				$message = $error_message; 
	        }

			return array(
				'status' => $status,
				'message' => $message,
				'invoice' => $invoice,
				'arguments' => $mrkv_ua_shipping_args,
				'print' => '',
				'form_print' => 'form-ukr-poshta-ttn',
				'print_sticker' => ''
			);
		}

		private function get_sender_uuid($sender_address_id)
		{
			$sender_type = (isset($this->settings_shipping['sender']['type']) && $this->settings_shipping['sender']['type']) ? $this->settings_shipping['sender']['type'] : '';

			$sender_name = '';
			$sender_edprou = '';
			$sender_tin = '';
			$sender_first_name = '';
			$sender_last_name = '';
			$sender_middle_name = '';
			$sender_phone = '';
			$sender_bank = '';

			if($sender_type == 'INDIVIDUAL')
			{
				if(isset($this->settings_shipping['sender']['individual']['name']) && $this->settings_shipping['sender']['individual']['name']){
					$sender_first_name = $this->settings_shipping['sender']['individual']['name'];
				}
				if(isset($this->settings_shipping['sender']['individual']['lastname']) && $this->settings_shipping['sender']['individual']['lastname']){
					$sender_last_name = $this->settings_shipping['sender']['individual']['lastname'];
				}
				if(isset($this->settings_shipping['sender']['individual']['middlename']) && $this->settings_shipping['sender']['individual']['middlename']){
					$sender_middle_name = $this->settings_shipping['sender']['individual']['middlename'];
				}
				if(isset($this->settings_shipping['sender']['individual']['phone']) && $this->settings_shipping['sender']['individual']['phone']){
					$sender_phone = $this->settings_shipping['sender']['individual']['phone'];
				}
			}
			else
			{
				$sender_type = 'INDIVIDUAL';
				$sender_last_name = (isset($this->settings_shipping['sender']['company']['lastname']) && $this->settings_shipping['sender']['company']['lastname']) ? $this->settings_shipping['sender']['company']['lastname'] : $sender_last_name;

				if(!$sender_last_name){
					$sender_last_name = (isset($this->settings_shipping['sender']['private']['lastname']) && $this->settings_shipping['sender']['private']['lastname']) ? $this->settings_shipping['sender']['private']['lastname'] : $sender_last_name;
				}

				$sender_first_name = (isset($this->settings_shipping['sender']['company']['name']) && $this->settings_shipping['sender']['company']['name']) ? $this->settings_shipping['sender']['company']['name'] : $sender_first_name;

				if(!$sender_first_name)
				{
					$sender_first_name = (isset($this->settings_shipping['sender']['private']['name']) && $this->settings_shipping['sender']['private']['name']) ? $this->settings_shipping['sender']['private']['name'] : $sender_first_name;
				}

				$sender_middle_name = (isset($this->settings_shipping['sender']['private']['middlename']) && $this->settings_shipping['sender']['private']['middlename']) ? $this->settings_shipping['sender']['private']['middlename'] : $sender_middle_name;

				$sender_phone = (isset($this->settings_shipping['sender']['company']['phone']) && $this->settings_shipping['sender']['company']['phone']) ? $this->settings_shipping['sender']['company']['phone'] : $sender_phone;

				if(!$sender_phone)
				{
					$sender_phone = (isset($this->settings_shipping['sender']['private']['phone']) && $this->settings_shipping['sender']['private']['phone']) ? $this->settings_shipping['sender']['private']['phone'] : $sender_phone;
				}
			}

			if($sender_phone)
			{
				# Get current user uuid if exist
				$obj = $this->shipping_api->send_post_request_curl('ecom/0.0.1/clients/phone', 'GET', array(), '&countryISO3166=UA&phoneNumber=' . $sender_phone);

				if(is_array($obj) && isset($obj[0]['uuid']))
				{
					$this->shipping_api->send_post_request_curl('ecom/0.0.1/clients/' . $obj[0]['uuid'], 'PUT', [
					    'addresses' => [
					        [
					            'addressId' => $sender_address_id,
					            'main' => true,
					        ],
					    ],
					], 'token');

					return $obj[0]['uuid'];
				}
			}

			# Send request
	        $obj = $this->shipping_api->send_post_request_curl('ecom/0.0.1/clients', 'POST', array(
				"type"			=> $sender_type,
	    	    "name"			=> $sender_name,
	    	    "edrpou"		=> $sender_edprou,
	            "tin"           => $sender_tin,
	    	    "firstName"		=> $sender_first_name,
	    	    "middleName"    => $sender_middle_name,
	    	    "lastName"		=> $sender_last_name,
	    	    "addressId"		=> $sender_address_id,
	    	    "phoneNumber"	=> $sender_phone,
	    	    "bankAccount"	=> $sender_bank,
			), 'token');

			return isset($obj['uuid']) ? $obj['uuid'] : '';
		}

		private function get_recipient_uuid($current_shipping_method)
		{
			$recipient_first_name = (isset($this->post_fields['mrkv_ua_ship_invoice_first_name']) && $this->post_fields['mrkv_ua_ship_invoice_first_name']) ? $this->post_fields['mrkv_ua_ship_invoice_first_name'] : '';
			$recipient_last_name = (isset($this->post_fields['mrkv_ua_ship_invoice_last_name']) && $this->post_fields['mrkv_ua_ship_invoice_last_name']) ? $this->post_fields['mrkv_ua_ship_invoice_last_name'] : '';
			$recipient_middle_name = (isset($this->post_fields['mrkv_ua_ship_invoice_patronymic']) && $this->post_fields['mrkv_ua_ship_invoice_patronymic']) ? $this->post_fields['mrkv_ua_ship_invoice_patronymic'] : '';
			$recipient_phone = (isset($this->post_fields['mrkv_ua_ship_invoice_phone']) && $this->post_fields['mrkv_ua_ship_invoice_phone']) ? $this->post_fields['mrkv_ua_ship_invoice_phone'] : '';

			if(!$recipient_first_name)
			{
				$recipient_first_name = ( $this->order->get_shipping_first_name() ) ? $this->order->get_shipping_first_name() : $this->order->get_billing_first_name();
			}
			if(!$recipient_last_name)
			{
				$recipient_last_name = ( $this->order->get_shipping_last_name() ) ? $this->order->get_shipping_last_name() : $this->order->get_billing_last_name();
			}
			if(!$recipient_middle_name)
			{
				$recipient_middle_name = $this->order->get_meta($current_shipping_method . '_patronymic');
			}
			if(!$recipient_phone)
			{
				$recipient_phone = ( $this->order->get_shipping_phone() ) ? $this->order->get_shipping_phone() : $this->order->get_billing_phone();
			}

			$recipient_phone = str_replace( array('+', ' ', '(' , ')', '-'), '', $recipient_phone );

			$len = strlen( '38' );
			if ( substr( $recipient_phone, 0, $len ) === '38' ){
				$recipient_phone = substr( $recipient_phone, 2 );
			}

			$len = strlen( '+38' );
			if ( substr( $recipient_phone, 0, $len ) === '+38' ){
				$recipient_phone = substr( $recipient_phone, 3 );
			}

			$len = strlen( '8' );
			if ( substr( $recipient_phone, 0, $len ) === '8' ){
				$recipient_phone = substr( $recipient_phone, 1 );
			}

			if (strlen($recipient_phone) > 9) {
		        $recipient_phone = substr($recipient_phone, -9);
		    }

		    if (strlen($recipient_phone) === 9) {
		        $recipient_phone = '0' . $recipient_phone;
		    }

			$address_id = $this->order->get_meta($current_shipping_method . '_address_ref');

			if(!$address_id)
			{
				$address_id = $this->get_recipient_address_ref();
			}

			if($recipient_phone)
			{
				# Get current user uuid if exist
				$obj = $this->shipping_api->send_post_request_curl('ecom/0.0.1/clients/phone', 'GET', array(), '&countryISO3166=UA&phoneNumber=' . $recipient_phone);

				if(is_array($obj) && isset($obj[0]['uuid']))
				{
					$this->shipping_api->send_post_request_curl('ecom/0.0.1/clients/' . $obj[0]['uuid'], 'PUT', [
						'middleName' => $recipient_middle_name,
					    'addresses' => [
					        [
					            'addressId' => $address_id,
					            'main' => true,
					        ],
					    ],
					], 'token');

					return $obj[0]['uuid'];
				}
			}

			# Send request
	        $obj = $this->shipping_api->send_post_request_curl('ecom/0.0.1/clients', 'POST', array(
				"type"			=> 'INDIVIDUAL',
	    	    "firstName"		=> html_entity_decode($recipient_first_name, ENT_QUOTES, 'UTF-8'),
	    	    "middleName"	=> html_entity_decode($recipient_middle_name, ENT_QUOTES, 'UTF-8'),
	    	    "lastName"		=> html_entity_decode($recipient_last_name, ENT_QUOTES, 'UTF-8'),
	    	    "addressId"		=> $address_id,
	    	    "phoneNumber"	=> $recipient_phone,
	    	    "checkOnDeliveryAllowed" => true,
			), 'token');

			return isset($obj['uuid']) ? $obj['uuid'] : '';
		}

		public function get_recipient_address_ref()
		{
			$postcode = $this->order->get_billing_postcode() ? $this->order->get_billing_postcode() : $this->order->get_shipping_postcode();
			$country = $this->order->get_billing_country() ? $this->order->get_billing_country() : $this->order->get_shipping_country();
			$country = $country ? $country : 'UA';
			$region = $this->order->get_billing_state() ? $this->order->get_billing_state() : $this->order->get_shipping_state();
			$city = $this->order->get_billing_city() ? $this->order->get_billing_city() : $this->order->get_shipping_city();
			$street = $this->order->get_billing_address_1() ? $this->order->get_billing_address_1() : $this->order->get_shipping_address_1();
			$apartment_number = $this->order->get_billing_address_2() ? $this->order->get_billing_address_2() : $this->order->get_shipping_address_2();

			# Send request
	        $obj = $this->shipping_api->send_post_request_curl('ecom/0.0.1/addresses', 'POST', array(
				"postcode" => $postcode,
				"country" => $country,
				"region" => $region,
				"city" => $city,
				"street" => $street,
				"apartmentNumber" => $apartment_number,
			));

	        if(isset($obj['id']))
       		{
       			return $obj['id'];
       		}	

       		return '';
		}

		private function get_shipment_type()
		{
			if(isset($this->post_fields['mrkv_ua_ship_invoice_shipment_type']) && $this->post_fields['mrkv_ua_ship_invoice_shipment_type'])
			{
				return $this->post_fields['mrkv_ua_ship_invoice_shipment_type'];
			}

			$shipping_methods = $this->order->get_shipping_methods();

			foreach ($shipping_methods as $shipping_method) 
			{
				$shipping_method_id = $shipping_method->get_method_id();
				$mrkv_ua_shipping_instance_id = $shipping_method->get_instance_id();

				$shipping_settings = get_option("woocommerce_{$shipping_method_id}_{$mrkv_ua_shipping_instance_id}_settings");

				if (!empty($shipping_settings) && isset($shipping_settings['shipping_type'])) {
			        return $shipping_settings['shipping_type'];
			    }
			}

			if(isset($this->settings_shipping['shipment']['type']) && $this->settings_shipping['shipment']['type'])
			{
				return $this->settings_shipping['shipment']['type'];
			}

			return 'STANDARD';
		}

		private function get_sender_address_id()
		{
			if(isset($this->settings_shipping['sender']['warehouse']['name']) && $this->settings_shipping['sender']['warehouse']['name'])
			{
				$postcode = $this->settings_shipping['sender']['warehouse']['name'];

				# Send request
		        $obj = $this->shipping_api->send_post_request_curl('ecom/0.0.1/addresses', 'POST', array(
					"postcode" => $postcode,
				), 'token');

				return isset($obj['id']) ? $obj['id'] : '';
			}

			return '';
		}

		private function get_paid_by_recipient($shipment_type)
		{
			if(isset($this->post_fields['mrkv_ua_ship_invoice_payer_delivery']) && $this->post_fields['mrkv_ua_ship_invoice_payer_delivery'])
			{
				return ($this->post_fields['mrkv_ua_ship_invoice_payer_delivery'] == 'Recipient') ? true : false;
			}
			else
			{
				foreach($this->order->get_items( 'shipping' ) as $item_id => $item)
	            {
	            	$mrkv_ua_shipping_instance_id = $item->get_instance_id();

	            	$shipping_instance_settings = get_option('woocommerce_' . $item->get_method_id() . '_' . $mrkv_ua_shipping_instance_id . '_settings');

	            	$order_total_for_min = $this->order->get_total();

	            	if(isset($this->settings_shipping['shipment']['cart_total']) && $this->settings_shipping['shipment']['cart_total'] == 'subtotal')
	            	{
	            		$order_total_for_min = $this->order->get_subtotal();
	            	}


	            	if(isset($shipping_instance_settings['enable_minimum_cost']) && $shipping_instance_settings['enable_minimum_cost'] == 'yes' 
	            		&& isset($shipping_instance_settings['minimum_cost_total']) && $shipping_instance_settings['minimum_cost_total'] < $order_total_for_min)
				    {
				        return false;
				    }
	            }
	            if($shipment_type == 'shipment')
	            {
	            	if(isset($this->settings_shipping['payer']['delivery']) && $this->settings_shipping['payer']['delivery'])
					{
						return ($this->settings_shipping['payer']['delivery'] == 'Recipient') ? true : false;
					}
	            }
			}

			return true;
		}

		private function get_weight($shipment_type)
		{
			if(isset($this->post_fields['mrkv_ua_ship_invoice_shipment_weight']) && $this->post_fields['mrkv_ua_ship_invoice_shipment_weight'])
			{
				return $this->post_fields['mrkv_ua_ship_invoice_shipment_weight'];
			}
			else
			{
				$weight = 0;
				$default_weight = 0;

				if(isset($this->settings_shipping[$shipment_type]['weight']) && $this->settings_shipping[$shipment_type]['weight'])
				{
					$default_weight = $this->settings_shipping[$shipment_type]['weight'];
				}


				foreach ( $this->order->get_items() as $item_id => $product_item ) 
	            {
	            	$product_id = $product_item->get_variation_id() ? $product_item->get_variation_id() : $product_item->get_product_id();
			            	
	            	$product = wc_get_product($product_id);

					if ( ! $product ) continue;

					$item_weight = ( null !== $product->get_weight() && $product->get_weight()) ? (floatval($product->get_weight()) * intval($product_item->get_quantity())) : 0.00;

	            	$weight += $item_weight;
				}

				$weight_unit = get_option('woocommerce_weight_unit');
				$weight_coef = 1;
				if ( 'g' == $weight_unit ) $weight_coef = 1;
	            if ( 'kg' == $weight_unit ) $weight_coef = 1000;
	            if ( 'lbs' == $weight_unit ) $weight_coef = 453.59;
	            if ( 'oz' == $weight_unit ) $weight_coef = 28.34;

				$weight = $weight_coef * $weight;
				$weight = number_format($weight, 2);

				return max($default_weight, $weight);
			}
		}

		private function get_length($shipment_type, $dimension_unit)
		{
			if(isset($this->post_fields['mrkv_ua_ship_invoice_shipment_length']) && $this->post_fields['mrkv_ua_ship_invoice_shipment_length'])
			{
				return floatval($this->post_fields['mrkv_ua_ship_invoice_shipment_length']);
			}
			else
			{
				$length = 0;

				if(isset($this->settings_shipping[$shipment_type]['length']) && $this->settings_shipping[$shipment_type]['length'])
				{
					$length = $this->settings_shipping[$shipment_type]['length'];
				}


				foreach ( $this->order->get_items() as $item_id => $product_item ) 
	            {
	            	$product_id = $product_item->get_variation_id() ? $product_item->get_variation_id() : $product_item->get_product_id();
			            	
	            	$product = wc_get_product($product_id);

					if ( ! $product ) continue;

					$item_length = ( null !== $product->get_length() && $product->get_length()) ? wc_get_dimension( $product->get_length(), 'cm', $dimension_unit ) : 0.00;

	            	$length = ($item_length > $length) ? $item_length : $length;
				}

				return $length;
			}
		}

		private function get_height_shipment($shipment_type, $dimension_unit)
		{
			if(isset($this->post_fields['mrkv_ua_ship_invoice_shipment_height']) && $this->post_fields['mrkv_ua_ship_invoice_shipment_height'])
			{
				return floatval($this->post_fields['mrkv_ua_ship_invoice_shipment_height']);
			}
			else
			{
				$height = 0;

				if(isset($this->settings_shipping[$shipment_type]['height']) && $this->settings_shipping[$shipment_type]['height'])
				{
					$height = $this->settings_shipping[$shipment_type]['height'];
				}


				foreach ( $this->order->get_items() as $item_id => $product_item ) 
	            {
	            	$product_id = $product_item->get_variation_id() ? $product_item->get_variation_id() : $product_item->get_product_id();
			            	
	            	$product = wc_get_product($product_id);

					if ( ! $product ) continue;

					$item_height = ( null !== $product->get_height() && $product->get_height()) ? wc_get_dimension( $product->get_height(), 'cm', $dimension_unit ) : 0.00;

	            	$height = ($item_height > $height) ? $item_height : $height;
				}

				return $height;
			}
		}

		private function get_width_shipment($shipment_type, $dimension_unit)
		{
			if(isset($this->post_fields['mrkv_ua_ship_invoice_shipment_width']) && $this->post_fields['mrkv_ua_ship_invoice_shipment_width'])
			{
				return floatval($this->post_fields['mrkv_ua_ship_invoice_shipment_width']);
			}
			else
			{
				$width = 0;

				if(isset($this->settings_shipping[$shipment_type]['width']) && $this->settings_shipping[$shipment_type]['width'])
				{
					$width = $this->settings_shipping[$shipment_type]['width'];
				}


				foreach ( $this->order->get_items() as $item_id => $product_item ) 
	            {
	            	$product_id = $product_item->get_variation_id() ? $product_item->get_variation_id() : $product_item->get_product_id();
			            	
	            	$product = wc_get_product($product_id);

					if ( ! $product ) continue;

					$item_width = ( null !== $product->get_width() && $product->get_width()) ? wc_get_dimension( $product->get_width(), 'cm', $dimension_unit ) : 0.00;

	            	$width = ($item_width > $width) ? $item_width : $width;
				}

				return $width;
			}
		}

		private function get_description()
		{
			$description = '';

			if(isset($this->post_fields['mrkv_ua_ship_invoice_shipment_description']) && $this->post_fields['mrkv_ua_ship_invoice_shipment_description'])
			{
				$description = $this->post_fields['mrkv_ua_ship_invoice_shipment_description'];
			}
			elseif(isset($this->settings_shipping['shipment']['description']) && $this->settings_shipping['shipment']['description'])
			{
				$description = $this->settings_shipping['shipment']['description'];
			}

			$description = $this->convert_description($description);

			return $description;
		}

		public function convert_description($description)
		{
			if(str_contains($description, '[order_id]'))
			{
				$description = str_replace( "[order_id]", $this->order->get_id(), $description );
			}
			if(str_contains($description, '[product_list]'))
			{
				$product_list = '';

				foreach($this->order->get_items() as $item_id => $item)
				{
					$product_list .= $item->get_name() . '(' . $item->get_quantity() . __('pcs.', 'mrkv-ua-shipping') . '); ';
				}

				$description = str_replace( "[product_list]", $product_list, $description );
			}
			if(str_contains($description, '[product_list_qa]'))
			{
				$product_list = '';

				foreach($this->order->get_items() as $item_id => $item)
				{
					$product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();
			        
			        $product = wc_get_product($product_id);

					$product_list .= $item->get_name() . ' '. $product->get_sku() . ' ' . '(' . $item->get_quantity() . __('pcs.', 'mrkv-ua-shipping') . '); ';
				}

				$description = str_replace( "[product_list_qa]", $product_list, $description );
			}
			if(str_contains($description, '[product_list_a]'))
			{
				$product_list = '';

				foreach($this->order->get_items() as $item_id => $item)
				{
					$product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();
			        
			        $product = wc_get_product($product_id);

					$product_list .= $product->get_sku() . ' ' . '(' . $item->get_quantity() . __('pcs.', 'mrkv-ua-shipping') . '); ';
				}

				$description = str_replace( "[product_list_a]", $product_list, $description );
			}
			if(str_contains($description, '[quantity]'))
			{
				$quantity = 0;

				foreach($this->order->get_items() as $item_id => $item)
				{
					$quantity += $item->get_quantity();
				}
				$description = str_replace( "[quantity]", $quantity, $description );
			}
			if(str_contains($description, '[quantity_p]'))
			{
				$quantity = count($this->order->get_items());
				$description = str_replace( "[quantity_p]", $quantity, $description );
			}
			if(str_contains($description, '[cost]'))
			{
				$cost = $this->order->get_total();
				$description = str_replace( "[cost]", $quantity, $description );
			}
			
			return $description;
	    }

	    private function get_receive_type()
		{
			if(isset($this->post_fields['mrkv_ua_ship_invoice_return']) && $this->post_fields['mrkv_ua_ship_invoice_return'])
			{
				return $this->post_fields['mrkv_ua_ship_invoice_return'];
			}

			return 'RETURN';
		}

		private function get_postpay()
		{
			if(isset($this->post_fields['mrkv_ua_ship_invoice_cost_back']) && $this->post_fields['mrkv_ua_ship_invoice_cost_back'])
			{
				return $this->post_fields['mrkv_ua_ship_invoice_cost_back'];
			}

			return 0;
		}

		private function get_post_pay_recipient($shipment_type)
		{
			return true;
		}

		private function get_declared_price()
		{
			if(isset($this->post_fields['mrkv_ua_ship_invoice_cost']) && $this->post_fields['mrkv_ua_ship_invoice_cost'])
			{
				return $this->post_fields['mrkv_ua_ship_invoice_cost'];
			}

			return $this->order->get_total();
		}

		private function get_transfer_post_pay($declared_price, $post_pay)
		{
			if(!$post_pay || $post_pay == 0)
			{
				return false;
			}
			$sender_type = (isset($this->settings_shipping['sender']['type']) && $this->settings_shipping['sender']['type']) ? $this->settings_shipping['sender']['type'] : '';

			if($declared_price && $this->order->get_payment_method() == 'cod')
			{
				if('INDIVIDUAL' == $sender_type)
				{
					return false;
				}
			}

			if($post_pay && $post_pay > 0)
			{
				return true;
			}
		    
		    return false;
		}

		private function get_width()
		{
			$dimension_unit = \get_option( 'woocommerce_dimension_unit' );
		    foreach ( $this->order->get_items() as $item_id => $item ) 
		    {
		        $_product = $item->get_product();
		        $products_widths[] = array( wc_get_dimension( $_product->get_width(), 'cm', $dimension_unit ) );
		    }
		    return ( max( $products_widths ) > 0 ) ? floatval( max( $products_widths ) ) : 0;
		}

		private function get_height()
		{
			$dimension_unit = \get_option( 'woocommerce_dimension_unit' );
		    foreach ( $this->order->get_items() as $item_id => $item ) 
		    {
		        $_product = $item->get_product();
		        $products_heights[] = array( wc_get_dimension( $_product->get_height(), 'cm', $dimension_unit ) );
		    }
		    return ( max( $products_heights ) > 0 ) ? floatval( max( $products_heights ) ) : 0;
		}
	}
}