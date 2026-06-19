<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

if (!class_exists('MRKV_UA_SHIPPING_METHODS_ORDER'))
{
	class MRKV_UA_SHIPPING_METHODS_ORDER
	{
		function __construct()
		{
			add_action('woocommerce_checkout_create_order', array($this, 'mrkv_ua_ship_create_order'));
			add_action('woocommerce_checkout_update_user_meta', array($this, 'mrkv_ua_ship_update_user_meta'), 10, 2);
		}

		public function mrkv_ua_ship_create_order($order)
		{
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
	        if ( isset( $_POST['shipping_method'][0] ) ) 
	        {
				$shipping_method_post = sanitize_text_field( wp_unslash( $_POST['shipping_method'][0] ) );
	        	$keys_shipping = array_keys(MRKV_UA_SHIPPING_LIST);
	        	$key = '';
	        	$current_shipping = '';
	        	$has_mrkv_ua_ship = '';

				foreach($keys_shipping as $key_ship)
				{
					if(str_contains($shipping_method_post, $key_ship))
					{
						$key = $key_ship;
					}
				}

				if($key)
				{
					foreach(MRKV_UA_SHIPPING_LIST[$key]['method'] as $method_slug => $method_data)
					{
						$clean_shipping_method = preg_replace('/_\d+$/', '', $shipping_method_post);
						
						if($clean_shipping_method == $method_slug)
						{
							$current_shipping = $method_slug;
							$has_mrkv_ua_ship = true; 
							break;
						}
					}

					if($has_mrkv_ua_ship && $current_shipping)
					{
						foreach(MRKV_UA_SHIPPING_LIST[$key]['method'][$current_shipping]['checkout_fields'] as $field_id => $field_val)
		    			{
		    				if(isset($_POST[$current_shipping . $field_id]) && isset($field_val['replace']))
		    				{
								$mrkv_ua_shipping_post_field_data = sanitize_text_field( wp_unslash($_POST[$current_shipping . $field_id]));
		    					
								if($field_val['replace'] == '_city'){
		    						$billing_city_unslashed = stripslashes( $mrkv_ua_shipping_post_field_data );
						            $order->set_billing_city( $billing_city_unslashed );
						            $order->set_shipping_city( $billing_city_unslashed );
									$order->update_meta_data( '_' . $current_shipping . $field_id, $billing_city_unslashed );
		    					}
		    					elseif($field_val['replace'] == '_state')
		    					{
		    						$billing_state_unslashed = stripslashes( $mrkv_ua_shipping_post_field_data );
		    						$order->set_billing_state(esc_attr($billing_state_unslashed) );
	              					$order->set_shipping_state( esc_attr($billing_state_unslashed) );
									$order->update_meta_data( '_' . $current_shipping . $field_id, $billing_state_unslashed );
		    					}
		    					elseif($field_val['replace'] == '_address_1')
		    					{
		    						$address = $mrkv_ua_shipping_post_field_data;
		    						$address = str_replace('\"', '', $address);
		    						$address = str_replace("\\'", "'", $address);
		    						$address = stripslashes( $address );

		    						$order->set_billing_address_1(esc_attr($address) );
	              					$order->set_shipping_address_1( esc_attr($address) );
									$order->update_meta_data( '_' . $current_shipping . $field_id, $address );
		    					}
		    					elseif($field_val['replace'] == '_postcode')
		    					{
		    						$order->set_billing_postcode(esc_attr($mrkv_ua_shipping_post_field_data) );
	              					$order->set_shipping_postcode( esc_attr($mrkv_ua_shipping_post_field_data) );
									$order->update_meta_data( '_' . $current_shipping . $field_id, $mrkv_ua_shipping_post_field_data );
		    					}
		    					elseif($field_val['replace'] == '_address_2')
		    					{
		    						$billing_address_unslashed = stripslashes( $mrkv_ua_shipping_post_field_data );
		    						$order->set_billing_address_2(esc_attr($billing_address_unslashed) );
	              					$order->set_shipping_address_2( esc_attr($billing_address_unslashed) );
									$order->update_meta_data( '_' . $current_shipping . $field_id, $billing_address_unslashed );
		    					}
		    					else
		    					{
		    						$billing_additional_unslashed = stripslashes( $mrkv_ua_shipping_post_field_data );
		    						$order->update_meta_data( $current_shipping . $field_id, esc_attr($billing_additional_unslashed) );
		    					}
		    				}
		    			}
		    			
		    			$order->save();
					}
				}
	        }
		}

		public function mrkv_ua_ship_update_user_meta($user_id, $posted)
		{
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
			if ( ! isset( $_POST['shipping_method'][0] ) ) {
				return;
			}

			$shipping_method_post = sanitize_text_field( wp_unslash( $_POST['shipping_method'][0] ) );
			$keys_shipping = array_keys(MRKV_UA_SHIPPING_LIST);
			$key = '';
			$current_shipping = '';
			
			foreach($keys_shipping as $key_ship)
			{
				if(str_contains($shipping_method_post, $key_ship))
				{
					$key = $key_ship;
				}
			}

			if($key)
			{
				foreach(MRKV_UA_SHIPPING_LIST[$key]['method'] as $method_slug => $method_data)
				{
					$clean_shipping_method = preg_replace('/_\d+$/', '', $shipping_method_post);
					if($clean_shipping_method == $method_slug)
					{
						$current_shipping = $method_slug;
						break;
					}
				}

				if($current_shipping)
				{
					foreach(MRKV_UA_SHIPPING_LIST[$key]['method'][$current_shipping]['checkout_fields'] as $field_id => $field_val)
					{
						if(isset($_POST[$current_shipping . $field_id]) && isset($field_val['replace']))
						{
							$field_data = sanitize_text_field( wp_unslash($_POST[$current_shipping . $field_id]));

							if($field_val['replace'] == '_city') {
								$clean_data = stripslashes($field_data);
								update_user_meta($user_id, 'billing_city', $clean_data);
								update_user_meta($user_id, 'shipping_city', $clean_data);
								update_user_meta($user_id, $current_shipping . $field_id, $clean_data);
							}
							elseif($field_val['replace'] == '_state') {
								$clean_data = stripslashes($field_data);
								update_user_meta($user_id, 'billing_state', $clean_data);
								update_user_meta($user_id, 'shipping_state', $clean_data);
								update_user_meta($user_id, $current_shipping . $field_id, $clean_data);
							}
							elseif($field_val['replace'] == '_address_1') {
								$clean_data = stripslashes(str_replace(array('\"', "\\'"), array('', "'"), $field_data));
								update_user_meta($user_id, 'billing_address_1', $clean_data);
								update_user_meta($user_id, 'shipping_address_1', $clean_data);
								update_user_meta($user_id, $current_shipping . $field_id, $clean_data);
							}
							elseif($field_val['replace'] == '_postcode') {
								update_user_meta($user_id, 'billing_postcode', $field_data);
								update_user_meta($user_id, 'shipping_postcode', $field_data);
								update_user_meta($user_id, $current_shipping . $field_id, $field_data);
							}
							elseif($field_val['replace'] == '_address_2') {
								$clean_data = stripslashes($field_data);
								update_user_meta($user_id, 'billing_address_2', $clean_data);
								update_user_meta($user_id, 'shipping_address_2', $clean_data);
								update_user_meta($user_id, $current_shipping . $field_id, $clean_data);
							}
						}
					}
				}
			}
		}
	}
}