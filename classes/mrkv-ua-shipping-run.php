<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_RUN'))
{
	/**
	 * Class for setup plugin 
	 */
	class MRKV_UA_SHIPPING_RUN
	{
		/**
		 * Constructor for plugin setup
		 * */
		function __construct()
		{
			# Setup woo plugin settings
			new MRKV_UA_SHIPPING_SETTINGS();

			# Setup woo plugin shipping methods
			new MRKV_UA_SHIPPING_METHODS();

			# Setup woo plugin woocommerce settings
			new MRKV_UA_SHIPPING_WOOCOMMERCE();
		}
	}
}