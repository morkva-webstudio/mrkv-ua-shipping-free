<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Include nova post menu
require_once 'blocks/mrkv-ua-shipping-blocks.php'; 

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
			add_action( 'woocommerce_shipping_init', [$this, 'mrkv_ua_shipping_include_shipping_method'] );
            add_filter( 'woocommerce_shipping_methods', [$this, 'mrkv_ua_shipping_add_shipping_method_woo'] );

            new MRKV_UA_SHIPPING_BLOCKS();
		}

        public function mrkv_ua_shipping_include_shipping_method()
        {
            $m_ua_active_plugins = get_option('m_ua_active_plugins');

            // Include plugin constants
            require_once MRKV_UA_SHIPPING_PLUGIN_PATH .'constants-mrkv-ua-shipping-methods.php';

            foreach(MRKV_UA_SHIPPING_LIST as $slug => $shipping)
            {
                if(isset($m_ua_active_plugins[$slug]['enabled']) && $m_ua_active_plugins[$slug]['enabled'] == 'on')
                {
                    foreach($shipping['method'] as $method)
                    {
                        # Include Shipping method
                        require_once MRKV_UA_SHIPPING_PLUGIN_PATH_SHIP . '/' . $slug . '/woocommerce/' . $method['filename'] . '.php';
                    }
                }
            }
        }

        /**
         * Add new shipping methods class in the shipping list
         * @param array All shipping methods
         * 
         * @return array All shipping methods
         * */
        public function mrkv_ua_shipping_add_shipping_method_woo($methods)
        {
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
	}
}