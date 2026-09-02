<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 
# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_WOOCOMMERCE'))
{
	/**
	 * Class for setup WOO settings
	 */
	class MRKV_UA_SHIPPING_WOOCOMMERCE
	{
		/**
		 * Constructor for WOO settings
		 * */
		function __construct()
		{
			$m_ua_active_plugins = get_option('m_ua_active_plugins');
			$active_shippings = false;

			if($m_ua_active_plugins && is_array($m_ua_active_plugins) && !empty($m_ua_active_plugins))
			{
				foreach(MRKV_UA_SHIPPING_LIST as $slug => $shipping)
                {
                    if($m_ua_active_plugins && isset($m_ua_active_plugins[$slug]['enabled']) && $m_ua_active_plugins[$slug]['enabled'] == 'on')
                    {
                    	$active_shippings = true;
                    	break;
                    }
                }
			}

			if($active_shippings)
			{
				# Setup woo plugin woocommerce orders
				new MRKV_UA_SHIPPING_WOO_ORDERS();

				# Setup woo plugin woocommerce order
				new MRKV_UA_SHIPPING_WOO_ORDER();

				# Setup woo plugin woocommerce product
				new MRKV_UA_SHIPPING_WOO_PRODUCT();
			}
		}
	}
}