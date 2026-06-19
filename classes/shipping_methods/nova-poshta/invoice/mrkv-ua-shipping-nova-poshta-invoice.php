<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_NOVA_POSHTA_INVOICE'))
{
	/**
	 * Class for setup shipping methods invoice create
	 */
	class MRKV_UA_SHIPPING_NOVA_POSHTA_INVOICE
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
		 * @param object Shipping object api
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
			$this->post_fields = apply_filters('mrkv_ua_shipping_invoice_post_data', $post_fields,$order,$settings_shipping, 'nova-poshta');
			$this->shipping_api = $shipping_api;
			$this->settings_shipping = $settings_shipping;
		}

		public function mrkv_ua_ship_create_invoice()
		{
			$current_shipping_method = '';
			$key_ship = 'nova-poshta';

			foreach($this->order->get_items( 'shipping' ) as $method_data)
			{
				$current_shipping_method = $method_data->get_method_id();

				if(in_array($method_data->get_method_id(), MRKV_UA_SHIPPING_LIST[$key_ship]['old_slugs']))
				{
					$current_shipping_method = array_search($method_data->get_method_id(), MRKV_UA_SHIPPING_LIST[$key_ship]['old_slugs']);
				}
			}

			$mrkv_ua_shipping_args = array();
			$status = '';
			$message = '';
			$invoice = '';

			# Payer data
			$payer_type = $this->get_payer_type();
			$delivery_payment_method = $this->get_delivery_payment_method($payer_type);
			$date_time_data = $this->get_invoice_date_time();
			$servicetype = $this->get_service_sender_type() . $this->get_service_recipien_type($current_shipping_method);

			$dimension_unit = get_option( 'woocommerce_dimension_unit' );
			
			# Cargo data
			$cargo_type = $this->get_cargo_type();
			$cargo_weight = $this->get_cargo_weight();
			$cargo_length = $this->get_cargo_length($dimension_unit);
			$cargo_width = $this->get_cargo_width($dimension_unit);
			$cargo_height = $this->get_cargo_height($dimension_unit);

			# Seats
			$seats_amount = $this->get_seats_amount();
			$option_seats = $this->get_option_seats($seats_amount, $cargo_weight, $cargo_length, $cargo_width, $cargo_height);

			# Description data
			$description = $this->get_description();
			$cost = $this->order->get_total();

			# Sender data
			$sender_ref = $this->get_sender_ref();
			$sender_city_ref = (isset($this->settings_shipping['sender']['city']['ref']) && $this->settings_shipping['sender']['city']['ref']) ? $this->settings_shipping['sender']['city']['ref'] : '';
			$sender_address_ref = $this->get_sender_address_ref();
			$sender_counterparty_ref = $this->get_sender_counterparty_ref();
			$sender_phone = $this->get_sender_phone();

			# Recipient data
			$recipient_first_name = $this->get_recipient_first_name();
			$recipient_middle_name = $this->get_recipient_middle_name($current_shipping_method);
			$recipient_last_name = $this->get_recipient_last_name();

			if(preg_match('/[A-Za-z]/', $recipient_first_name))
			{
				$this->order->add_order_note(__('Error with Recipient First Name. Contains latin letters','mrkv-ua-shipping'), $is_customer_note = 0, $added_by_user = false);
				return array(
					'status' => 'failed',
					'message' => __('Error with Recipient First Name. Contains latin letters','mrkv-ua-shipping'),
					'invoice' => '',
					'arguments' => array(),
					'print' => '',
					'form_print' => '',
					'print_sticker' =>  ''
				);
			}
			if(preg_match('/[A-Za-z]/', $recipient_last_name))
			{
				$this->order->add_order_note(__('Error with Recipient Lastname. Contains latin letters','mrkv-ua-shipping'), $is_customer_note = 0, $added_by_user = false);
				return array(
					'status' => 'failed',
					'message' => __('Error with Recipient Lastname. Contains latin letters','mrkv-ua-shipping'),
					'invoice' => '',
					'arguments' => array(),
					'print' => '',
					'form_print' => '',
					'print_sticker' =>  ''
				);
			}

			$recipient_phone = $this->get_recipient_phone();
			require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-poshta/api/mrkv-ua-shipping-recipient-nova-poshta.php';
			$mrkv_recipient_object = new MRKV_UA_SHIPPING_RECIPIENT_NOVA_POSHTA($this->shipping_api);
			$recipient = $mrkv_recipient_object->get_recipient_ref($recipient_first_name, $recipient_middle_name, $recipient_last_name, $recipient_phone);
			$recipient_contact_ref = $mrkv_recipient_object->contact_recipient_ref;
			$recipient_address = '';
			if($current_shipping_method == 'mrkv_ua_shipping_nova-poshta_address')
			{
				$recipient_address = $this->order->get_meta($current_shipping_method . '_address_ref');

				if(!$recipient_address)
				{
					$city_ref = $this->order->get_meta($current_shipping_method . '_city_ref') ? $this->order->get_meta($current_shipping_method . '_city_ref') : $this->order->get_meta('np_city_ref');
					$recipient_address = $mrkv_recipient_object->get_recipient_address_ref($recipient, $city_ref, $this->order, true);
				}
			}
			elseif($current_shipping_method == 'mrkv_ua_shipping_nova-poshta_poshtamat')
			{
				$recipient_address = $this->order->get_meta($current_shipping_method . '_ref');
			}
			else
			{
				$recipient_address = $this->order->get_meta($current_shipping_method . '_warehouse_ref');
			}

			if(!$recipient_address)
			{
				$city_ref = $this->order->get_meta($current_shipping_method . '_city_ref') ? $this->order->get_meta($current_shipping_method . '_city_ref') : $this->order->get_meta('np_city_ref');

				if(($current_shipping_method != 'mrkv_ua_shipping_nova-poshta_address'))
				{
					$recipient_address = $this->order->get_meta(MRKV_UA_SHIPPING_LIST[$key_ship]['method'][$current_shipping_method]['checkout_fields']['_warehouse_ref']['old_slug']);
				}
				else
				{
					$recipient_address = $mrkv_recipient_object->get_recipient_address_ref($recipient, $city_ref, $this->order);
				}

				if(!$recipient_address)
				{
					$recipient_address = $mrkv_recipient_object->get_recipient_warehouse_ref($this->order->get_billing_address_1(), $city_ref);
				}
			}

			# Additional data
			$order_id = $this->order->get_id();

			# Shipping classes
			$product_classes_data = [];
            $classes_enabled = (isset($this->settings_shipping['shipment']['class']['enabled']) && $this->settings_shipping['shipment']['class']['enabled']) ? true : false;
            $shipment_class_list = (isset($this->settings_shipping['shipment']['class']['list']) && is_array($this->settings_shipping['shipment']['class']['list']) && !empty($this->settings_shipping['shipment']['class']['list'])) ? $this->settings_shipping['shipment']['class']['list'] : [];
            $has_class_error = false;
            $cargo_details = [];

            if($classes_enabled)
            {
            	foreach ( $this->order->get_items() as $item_id => $item ) 
            	{
            		$product = $item->get_product();

				    if ( ! $product ) {
				        continue;
				    }

				    $shipping_class_id = (int) $product->get_shipping_class_id();
				    if ( $shipping_class_id === 0 ) {
			            $product_classes_data[ $item_id ] = $cargo_type;
			            continue;
			        }

			        $matched_cargo_type = null;

			        foreach ( $shipment_class_list as $cargo_type_key => $class_ids ) {

			            if ( in_array( $shipping_class_id, $class_ids, true ) ) {
			                $matched_cargo_type = $cargo_type_key;
			                break;
			            }
			        }

			        $product_classes_data[ $item_id ] = $matched_cargo_type ?: $cargo_type;
            	}

            	if(!empty($product_classes_data))
            	{
            		$cargo_types = array_unique(array_values($product_classes_data));

                    if (count($cargo_types) === 1) 
                    {
                        $cargo_type = reset($cargo_types);
                    }
                    else
                    {
                    	$has_class_error = false;
                    }
            	}
            }

            if($cargo_type == 'TiresWheels')
            {
            	$cargo_details = $this->get_cargo_details();

            	if(empty($cargo_details))
                {
                    $has_class_error = true;
                }

                $cargo_details = array_values( $cargo_details );
            }

            if($cargo_type == 'Documents')
            {
                $cargo_weight = $this->get_cargo_weight_document();
            }

            if($has_class_error)
            {
            	$this->order->add_order_note(__('Error. Check product shipping classes','mrkv-ua-shipping'), $is_customer_note = 0, $added_by_user = false);
				return array(
					'status' => 'failed',
					'message' => __('Error. Check product shipping classes','mrkv-ua-shipping'),
					'invoice' => '',
					'arguments' => array(),
					'print' => '',
					'form_print' => '',
					'print_sticker' =>  ''
				);
            }

			# Args for query
			$mrkv_ua_shipping_args = array(
				"PayerType" => $payer_type,
				"PaymentMethod" => $delivery_payment_method,
				"DateTime" => $date_time_data,
				"ServiceType" => $servicetype,
				"CargoType" => $cargo_type,
				"Weight" => $cargo_weight,
				"SeatsAmount" => $seats_amount,
				"OptionsSeat" => $option_seats,
				"Description" => $description,
				"Cost" => $cost,
				"Sender" => $sender_counterparty_ref,
				"CitySender" => $sender_city_ref,
				"SenderAddress" => $sender_address_ref,
				"ContactSender" => $sender_ref,
				"SendersPhone" => $sender_phone,
				"InfoRegClientBarcodes" => $order_id,
				"AdditionalInformation"=> $description,
				"CityRecipient" => $this->order->get_meta($current_shipping_method . '_city_ref'),
				"Recipient" => $recipient,
				"RecipientAddress" => $recipient_address,
				"ContactRecipient" => $recipient_contact_ref,
				"RecipientsPhone" => $recipient_phone
			);

			if(!empty($cargo_details) && $cargo_type == 'TiresWheels')
			{
				$mrkv_ua_shipping_args['CargoDetails'] = $cargo_details; 
			}

			if($cargo_type == 'Documents')
			{
				unset($mrkv_ua_shipping_args['OptionsSeat']);
			}



			if($mrkv_ua_shipping_args["Cost"] < 200)
			{
				$mrkv_ua_shipping_args["Cost"] = 200;
			}

			$invoice_arg = array(
				"apiKey" => $this->shipping_api->get_api_key(),
				"modelName" => "InternetDocument",
				"calledMethod" => "save",
				"methodProperties" => $mrkv_ua_shipping_args,
			);

			$invoice_arg = apply_filters( 'mrkv_ua_shipping_arg_invoice_data', $invoice_arg, $this->order, $key_ship );

			# Send request
	        $obj = $this->shipping_api->send_post_request( $invoice_arg );

	        if(isset($obj['data'][0]['Ref']))
	        {
	        	$status = 'completed';
	        	$message = __('Invoice created','mrkv-ua-shipping') . ' #' . $obj['data'][0]['IntDocNumber']  . '<img src="' .  esc_url(MRKV_UA_SHIPPING_IMG_URL . '/global') . '/copy-ttn.svg" alt="' . esc_html__('Copy invoice', 'mrkv-ua-shipping') . '" title="' . esc_html__('Copy invoice', 'mrkv-ua-shipping') . '">';
	        	$invoice = $obj['data'][0]['IntDocNumber'];

	        	$this->order->update_meta_data('mrkv_ua_ship_invoice_number', $obj['data'][0]['IntDocNumber']);
	        	$this->order->update_meta_data('mrkv_ua_ship_invoice_ref', $obj['data'][0]['Ref']);

				if(isset($this->post_fields['mrkv_ua_ship_invoice_sender_ref']) && $this->post_fields['mrkv_ua_ship_invoice_sender_ref'])
	        	{
	        		$this->order->add_order_note(__('Manually created invoice','mrkv-ua-shipping') . ': ' . $obj['data'][0]['IntDocNumber'], $is_customer_note = 0, $added_by_user = false);
	        	}
	        	else
	        	{
	        		$this->order->add_order_note(__('Automatic created invoice','mrkv-ua-shipping') . ': ' . $obj['data'][0]['IntDocNumber'], $is_customer_note = 0, $added_by_user = false);
	        	}

	        	$this->order->save();

	        	# Send invoice number
        		do_action('mrkv_keycrm_send_invoice_number', $this->order->get_id());
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
        		do_action('salesdrive_send_order_send_ttn', $this->order->get_id(), $invoice, 'np');
	        }
	        else
	        {
	        	$status = 'failed';

	        	if(isset($obj['errors'][0]))
	        	{
	        		$this->shipping_api->debug_log->add_data_error($obj['errors'][0]);
	        		$message = $obj['errors'];
	        	}
	        	else{
	        		$this->shipping_api->debug_log->add_data_error(__('Error with create invoice','mrkv-ua-shipping'));
	        		$message = array(__('Error with create invoice','mrkv-ua-shipping'));
	        	}

	        	$this->order->add_order_note(__('Error create invoice','mrkv-ua-shipping') . ': ' . wp_json_encode( $message, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ), $is_customer_note = 0, $added_by_user = false);
	        }

			return array(
				'status' => $status,
				'message' => $message,
				'invoice' => $invoice,
				'arguments' => $mrkv_ua_shipping_args,
				'print' => 'https://my.novaposhta.ua/orders/printDocument/orders%5B%5D/' . $invoice . '/type/pdf/apiKey/' . $this->settings_shipping['api_key'],
				'form_print' => '',
				'print_sticker' =>  'https://my.novaposhta.ua/orders/printMarkings/orders[]/' . $invoice . '/type/pdf/apiKey/' . $this->settings_shipping['api_key']
			);
		}

		private function get_sender_ref()
		{
			if(isset($this->post_fields['mrkv_ua_ship_invoice_sender_ref']) && $this->post_fields['mrkv_ua_ship_invoice_sender_ref'])
			{
				return $this->post_fields['mrkv_ua_ship_invoice_sender_ref'];
			}
			else
			{
				return (isset($this->settings_shipping['sender']['ref']) && $this->settings_shipping['sender']['ref']) ? $this->settings_shipping['sender']['ref'] : '';
			}
		}

		private function get_sender_counterparty_ref()
		{
			if(isset($this->post_fields['mrkv_ua_ship_invoice_sender_counterparty_ref']) && $this->post_fields['mrkv_ua_ship_invoice_sender_counterparty_ref'])
			{
				return $this->post_fields['mrkv_ua_ship_invoice_sender_counterparty_ref'];
			}
			else
			{
				return (isset($this->settings_shipping['sender']['counterparty_ref']) && $this->settings_shipping['sender']['counterparty_ref']) ? $this->settings_shipping['sender']['counterparty_ref'] : '';
			}
		}

		private function get_sender_phone()
		{
			if(isset($this->post_fields['mrkv_ua_ship_invoice_sender_phones']) && $this->post_fields['mrkv_ua_ship_invoice_sender_phones'])
			{
				return $this->post_fields['mrkv_ua_ship_invoice_sender_phones'];
			}
			else
			{
				return (isset($this->settings_shipping['sender']['phones']) && $this->settings_shipping['sender']['phones']) ? $this->settings_shipping['sender']['phones'] : '';
			}
		}

		private function get_payer_type()
		{
			if(isset($this->post_fields['mrkv_ua_ship_invoice_payer_delivery']) && $this->post_fields['mrkv_ua_ship_invoice_payer_delivery'])
			{
				return $this->post_fields['mrkv_ua_ship_invoice_payer_delivery'];
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
				        return 'Sender';
				    }
	            }
				if(isset($this->settings_shipping['payer']['delivery']) && $this->settings_shipping['payer']['delivery'])
				{
					return $this->settings_shipping['payer']['delivery'];
				}
				else
				{
					return 'Recipient';
				}
			}
		}

		private function get_delivery_payment_method($payer_type)
		{
			if($payer_type == 'Recipient')
			{
				return 'Cash';
			}
			else
			{
				if(isset($this->settings_shipping['shipment']['payment']) && $this->settings_shipping['shipment']['payment']){
					return $this->settings_shipping['shipment']['payment'];
				}
				else
				{
					return 'Cash';
				}
			}
		}

		private function get_invoice_date_time()
		{
			$datetime = new DateTime('now', wp_timezone());
			$current_hour = (int) $datetime->format('H');

			if ( $current_hour >= 22 ) {
			    $datetime->modify('+1 day');
			}

			$delivery_date = $datetime->format('d.m.Y');

	        return $delivery_date;
		}

		private function get_service_sender_type()
		{
			if(isset($this->settings_shipping['sender']['address_type']) && $this->settings_shipping['sender']['address_type'])
			{
				if($this->settings_shipping['sender']['address_type'] == 'W')
				{
					return 'Warehouse';
				}
				else
				{
					return 'Doors';
				}
			}
			
			return 'Warehouse';
		}

		private function get_service_recipien_type($current_shipping_method)
		{
			if($current_shipping_method == 'mrkv_ua_shipping_nova-poshta')
			{
				return 'Warehouse';
			}
			elseif($current_shipping_method == 'mrkv_ua_shipping_nova-poshta_poshtamat')
			{
				return 'Postomat';
			}
			elseif($current_shipping_method == 'mrkv_ua_shipping_nova-poshta_address')
			{
				return 'Doors';
			}
			else
			{
				return 'Warehouse';
			}
		}

		private function get_cargo_type()
		{
			if(isset($this->post_fields['mrkv_ua_ship_invoice_shipment_type']) && $this->post_fields['mrkv_ua_ship_invoice_shipment_type'])
			{
				return $this->post_fields['mrkv_ua_ship_invoice_shipment_type'];
			}
			else
			{
				if(isset($this->settings_shipping['shipment']['type']) && $this->settings_shipping['shipment']['type'])
				{
					return $this->settings_shipping['shipment']['type'];
				}
			}

			return 'Parcel';
		}

		private function get_cargo_weight()
		{
			if(isset($this->post_fields['mrkv_ua_ship_invoice_shipment_weight']) && $this->post_fields['mrkv_ua_ship_invoice_shipment_weight'])
			{
				return $this->post_fields['mrkv_ua_ship_invoice_shipment_weight'];
			}
			else
			{
				$default_weight = 0.5;
				$weight = 0;

				if(isset($this->settings_shipping['shipment']['weight']) && $this->settings_shipping['shipment']['weight'])
				{
					$default_weight = $this->settings_shipping['shipment']['weight'];
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
				if ( 'g' == $weight_unit ) $weight_coef = 0.001;
	            if ( 'kg' == $weight_unit ) $weight_coef = 1;
	            if ( 'lbs' == $weight_unit ) $weight_coef = 0.45359;
	            if ( 'oz' == $weight_unit ) $weight_coef = 0.02834;

				$weight = $weight_coef * $weight;
				$weight = number_format($weight, 2);

				return max($default_weight, $weight);
			}
		}

		private function check_cargo_weight($cargo_type, $cargo_weight)
		{
			if($cargo_type == 'Documents')
			{
				$cargo_weight = 1;
			}

			return $cargo_weight;
		}

		private function get_cargo_details()
		{
			$cargo_details = [];

			foreach ( $this->order->get_items() as $item_id => $item ) 
			{
			    $product = $item->get_product();

			    if ( ! $product instanceof WC_Product ) {
			        continue;
			    }

			    $product_id = $product->get_id();
			    $quantity   = (int) $item->get_quantity();
			    $mrkv_tire_type = get_post_meta( $product_id, '_mrkv_tire_type', true );

			    if ( empty( $mrkv_tire_type ) ) {
			        continue;
			    }

			    $key = sanitize_text_field( $mrkv_tire_type );
			    if ( isset( $cargo_details[ $key ] ) ) {

			        $cargo_details[ $key ]['Amount'] += $quantity;

			    } else {

			        $cargo_details[ $key ] = [
			            'CargoDescription' => $key,
			            'Amount'           => $quantity,
			        ];
			    }
			}

			return $cargo_details;
		}

		private function get_cargo_weight_document()
		{
			$weight = 0;

			foreach ( $this->order->get_items() as $item_id => $item ) 
			{
			    $product = $item->get_product();

			    if ( ! $product instanceof WC_Product ) {
			        continue;
			    }

			    $product_id = $product->get_id();
			    $quantity   = (int) $item->get_quantity();
			    $_mrkv_document_weight = get_post_meta( $product_id, '_mrkv_document_weight', true );

                if ( empty( $_mrkv_document_weight ) ) 
                {
                    $weight += 0.1 * $quantity;
                }
                else
                {
                    $weight += $_mrkv_document_weight * $quantity;
                }
			}

			if($weight > 0.1 && $weight <= 0.5){
                $weight = 0.5;
            }
            elseif($weight > 0.5)
            {
                $weight = 1;
            }

			return $weight;
		}

		private function get_cargo_length($dimension_unit)
		{
			if(isset($this->post_fields['mrkv_ua_ship_invoice_shipment_length']) && $this->post_fields['mrkv_ua_ship_invoice_shipment_length'])
			{
				return $this->post_fields['mrkv_ua_ship_invoice_shipment_length'];
			}
			else
			{
				$length = 10;

				if(isset($this->settings_shipping['shipment']['length']) && $this->settings_shipping['shipment']['length'])
				{
					$length = $this->settings_shipping['shipment']['length'];
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

		private function get_cargo_width($dimension_unit)
		{
			if(isset($this->post_fields['mrkv_ua_ship_invoice_shipment_width']) && $this->post_fields['mrkv_ua_ship_invoice_shipment_width'])
			{
				return $this->post_fields['mrkv_ua_ship_invoice_shipment_width'];
			}
			else
			{
				$width = 10;

				if(isset($this->settings_shipping['shipment']['width']) && $this->settings_shipping['shipment']['width'])
				{
					$width = $this->settings_shipping['shipment']['width'];
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

		private function get_cargo_height($dimension_unit)
		{
			if(isset($this->post_fields['mrkv_ua_ship_invoice_shipment_height']) && $this->post_fields['mrkv_ua_ship_invoice_shipment_height'])
			{
				return $this->post_fields['mrkv_ua_ship_invoice_shipment_height'];
			}
			else
			{
				$height = 10;

				if(isset($this->settings_shipping['shipment']['height']) && $this->settings_shipping['shipment']['height'])
				{
					$height = $this->settings_shipping['shipment']['height'];
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

		private function get_seats_amount()
		{
			if(isset($this->post_fields['mrkv_ua_ship_invoice_shipment_seats']) && $this->post_fields['mrkv_ua_ship_invoice_shipment_seats'])
			{
				return $this->post_fields['mrkv_ua_ship_invoice_shipment_seats'];
			}

			return 1;
		}

		private function get_option_seats($seats_amount, $cargo_weight, $cargo_length, $cargo_width, $cargo_height)
		{
			$option_seats = array();

			for ($i=0; $i < $seats_amount; $i++) 
			{ 
				$option_seats[] = array (
					"volumetricVolume" => ((int) $cargo_length * (int) $cargo_width * (int) $cargo_height) / 4000,
					"volumetricLength" => $cargo_length,
					"volumetricWidth" => $cargo_width,
					"volumetricHeight" => $cargo_height,
					"weight" => $cargo_weight / $seats_amount,
				);
			}

			return $option_seats;
		}

		private function get_description()
		{
			$description = '';
			if(isset($this->post_fields['mrkv_ua_ship_invoice_shipment_description']) && $this->post_fields['mrkv_ua_ship_invoice_shipment_description'])
			{
				$description = $this->post_fields['mrkv_ua_ship_invoice_shipment_description'];
			}
			else
			{
				if(isset($this->settings_shipping['shipment']['description']) && $this->settings_shipping['shipment']['description'])
				{
					$description = $this->settings_shipping['shipment']['description'];
				}
			}

			$description = $this->convert_description($description);
			$description = str_replace('#', '', $description);


			return substr($description, 0, 100);
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

		private function get_sender_address_ref()
		{
			if(isset($this->settings_shipping['sender']['address_type']) && $this->settings_shipping['sender']['address_type'])
			{
				if($this->settings_shipping['sender']['address_type'] == 'W')
				{
					$sender_address_ref = (isset($this->settings_shipping['sender']['warehouse']['ref']) && $this->settings_shipping['sender']['warehouse']['ref']) 
						? $this->settings_shipping['sender']['warehouse']['ref'] : '';
				}
				else
				{
					$sender_address_ref = $this->get_sender_address_ref_inline();
				}

				return $sender_address_ref;
			}

			return '';
		}

		private function get_sender_address_ref_inline()
		{
			require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-poshta/api/mrkv-ua-shipping-sender-nova-poshta.php';
			$mrkv_sender_object_nova_poshta = new MRKV_UA_SHIPPING_SENDER_NOVA_POSHTA($this->shipping_api);

			$sender_street_ref = (isset($this->settings_shipping['sender']['street']['ref']) && $this->settings_shipping['sender']['street']['ref']) 
						? $this->settings_shipping['sender']['street']['ref'] : '';
			$sender_building_number = (isset($this->settings_shipping['sender']['street']['house']) && $this->settings_shipping['sender']['street']['house']) 
						? $this->settings_shipping['sender']['street']['house'] : '';
			$sender_flat = (isset($this->settings_shipping['sender']['street']['flat']) && $this->settings_shipping['sender']['street']['flat']) 
						? $this->settings_shipping['sender']['street']['flat'] : '';

	        # Send request
	        $ref = $mrkv_sender_object_nova_poshta->get_sender_address_ref($sender_street_ref, $sender_building_number, $sender_flat);
	        $ref = str_replace('"', "", $ref);

	        if($ref)
	        {
	        	return $ref;
	        }

	        return '';
		}

		private function get_recipient_first_name()
		{
			
			if(isset($this->post_fields['mrkv_ua_ship_invoice_first_name']) && $this->post_fields['mrkv_ua_ship_invoice_first_name'])
			{
				return $this->post_fields['mrkv_ua_ship_invoice_first_name'];
			}
			else
			{
				$recipient_first_name = ( $this->order->get_shipping_first_name() ) ? $this->order->get_shipping_first_name() : $this->order->get_billing_first_name();
				$recipient_first_name = str_replace("ʼ", "'", $recipient_first_name);
				return html_entity_decode($recipient_first_name, ENT_QUOTES, 'UTF-8');	
			}
		}

		private function get_recipient_last_name()
		{
			if(isset($this->post_fields['mrkv_ua_ship_invoice_last_name']) && $this->post_fields['mrkv_ua_ship_invoice_last_name'])
			{
				$recipient_last_name = str_replace("ʼ", "'", $this->post_fields['mrkv_ua_ship_invoice_last_name']);
				return $recipient_last_name;
			}
			else
			{
				$recipient_last_name = ( $this->order->get_shipping_last_name() ) ? $this->order->get_shipping_last_name() : $this->order->get_billing_last_name();
				$recipient_last_name = str_replace("ʼ", "'", $recipient_last_name);
				return html_entity_decode($recipient_last_name, ENT_QUOTES, 'UTF-8');	
			}
		}

		private function get_recipient_middle_name($current_shipping_method)
		{
			if(isset($this->post_fields['mrkv_ua_ship_invoice_patronymic']) && $this->post_fields['mrkv_ua_ship_invoice_patronymic'])
			{
				$recipient_last_name = str_replace("ʼ", "'", $this->post_fields['mrkv_ua_ship_invoice_patronymic']);
				return $recipient_last_name;
			}
			else
			{
				$recipient_middle_name = $this->order->get_meta($current_shipping_method . '_patronymic');
				$recipient_middle_name = str_replace("ʼ", "'", $recipient_middle_name);
				return html_entity_decode($recipient_middle_name, ENT_QUOTES, 'UTF-8');	
			}
		}

		private function get_recipient_phone()
		{
			if(isset($this->post_fields['mrkv_ua_ship_invoice_phone']) && $this->post_fields['mrkv_ua_ship_invoice_phone'])
			{
				$recipient_phone = $this->post_fields['mrkv_ua_ship_invoice_phone'];
			}
			else
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

			return $recipient_phone;
		}
	}
}