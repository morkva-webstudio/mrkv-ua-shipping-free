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
	}
}