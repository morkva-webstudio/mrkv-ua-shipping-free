<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 
# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_CONNECTER'))
{
	/**
	 * Class for setup plugin 
	 */
	class MRKV_UA_SHIPPING_CONNECTER
	{
		/**
		 * Constructor for plugin setup
		 * */
		function __construct()
		{
            add_filter( 'woocommerce_shipping_methods', [$this, 'mrkv_ua_shipping_add_shipping_method_woo'] );
            add_filter( 'woocommerce_order_shipping_to_display', [$this, 'mrkv_ua_shipping_shipping_display_text'], 10, 3 );

            new MRKV_UA_SHIPPING_BLOCKS();
		}

        /**
         * Add new shipping methods class in the shipping list
         * @param array All shipping methods
         * 
         * @return array All shipping methods
         * */
        public function mrkv_ua_shipping_add_shipping_method_woo($methods)
        {
            // Include plugin constants
            require_once MRKV_UA_SHIPPING_PLUGIN_PATH .'constants-mrkv-ua-shipping-methods.php';
            
            $m_ua_active_plugins = get_option('m_ua_active_plugins');

            foreach(MRKV_UA_SHIPPING_LIST as $slug => $shipping)
            {
                if(isset($m_ua_active_plugins[$slug]['enabled']) && $m_ua_active_plugins[$slug]['enabled'] == 'on')
                {
                    foreach($shipping['method'] as $method)
                    {
                        # Add new shipping method
                        $methods[$method['slug']] = $method['class'];
                    }
                }
            }

            # Return all methods
            return $methods;
        }

        /**
         * Changing the shipping cost text when the price is zero
         * 
         * @param string $shipping
         * @param WC_Order $order
         * @param string $tax_display
         * 
         * @return string
         */
        public function mrkv_ua_shipping_shipping_display_text( $shipping, $order, $tax_display )
        {
            if ( (float) $order->get_shipping_total() !== 0.0 ) {
                return $shipping;
            }

            foreach ( $order->get_shipping_methods() as $shipping_item ) {
                $method_id = $shipping_item->get_method_id();

                if ( strpos( $method_id, 'mrkv_ua_shipping_' ) !== false ) {
                    $wc_shipping = WC_Shipping::instance();
                    $all_methods = $wc_shipping->get_shipping_methods();

                    if ( isset( $all_methods[ $method_id ] ) ) {
                        $shipping_class = $all_methods[ $method_id ];
                        $shipping_instance = is_string( $shipping_class ) ? new $shipping_class( $shipping_item->get_instance_id() ) : $shipping_class;

                        if ( 'yes' === $shipping_instance->get_option( 'enable_minimum_cost' ) ) {
                            $woo_cart_total = $order->get_subtotal();
                            
                            $settings_method = get_option('ukr-poshta_m_ua_settings');
                            if ( isset($settings_method['shipment']['cart_total']) && $settings_method['shipment']['cart_total'] == 'total' ) {
                                $woo_cart_total = $order->get_total() - $order->get_shipping_total();
                            }

                            $min_cost_total = (float) $shipping_instance->get_option( 'minimum_cost_total' );

                            if ( $woo_cart_total >= $min_cost_total ) {
                                return __( 'Free', 'mrkv-ua-shipping' );
                            }
                        }

                        return __( 'According to carrier rates', 'mrkv-ua-shipping' );
                    }
                }
            }

            return $shipping;
        }
	}
}