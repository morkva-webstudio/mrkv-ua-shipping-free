<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_FREE_SHIPPING'))
{
	/**
	 * Free shipping threshold of an order, counted the same way as at checkout (calculate_shipping() of the methods)
	 */
	class MRKV_UA_SHIPPING_FREE_SHIPPING
	{
		/**
		 * Nova Poshta methods whose checkout follows the "Free shipping calculation" setting (shipment[cart_total]);
		 * the international ones always compare the subtotal
		 * */
		const CART_TOTAL_METHODS = array(
			'mrkv_ua_shipping_nova-poshta',
			'mrkv_ua_shipping_nova-poshta_poshtamat',
			'mrkv_ua_shipping_nova-poshta_address',
			'nova_poshta_shipping_method',
			'nova_poshta_shipping_method_poshtomat',
			'npttn_address_shipping_method',
		);

		/**
		 * Amount compared with the threshold: the subtotal, or the subtotal after promotional codes.
		 * Shipping is never part of it, as at checkout
		 * @param WC_Order Order
		 * @param string Shipping method id of the order
		 * @param array Carrier settings
		 *
		 * @return float
		 * */
		public static function get_order_amount($order, $method_id, $settings)
		{
			$amount = (float) $order->get_subtotal();

			if(in_array($method_id, self::CART_TOTAL_METHODS, true) && isset($settings['shipment']['cart_total']) && $settings['shipment']['cart_total'] == 'total')
			{
				# WC()->cart->cart_contents_total of the order: line totals after discounts, without tax and shipping
				$amount -= (float) $order->get_discount_total();
			}

			return $amount;
		}

		/**
		 * Whether the order reached the free shipping of its method
		 * @param WC_Order Order
		 * @param string Shipping method id of the order
		 * @param array Settings of the shipping method instance
		 * @param array Carrier settings
		 *
		 * @return bool
		 * */
		public static function is_reached($order, $method_id, $instance_settings, $settings)
		{
			if(empty($instance_settings['enable_minimum_cost']) || $instance_settings['enable_minimum_cost'] != 'yes' || !isset($instance_settings['minimum_cost_total']))
			{
				return false;
			}

			return self::get_order_amount($order, $method_id, $settings) >= (float) $instance_settings['minimum_cost_total'];
		}
	}
}
