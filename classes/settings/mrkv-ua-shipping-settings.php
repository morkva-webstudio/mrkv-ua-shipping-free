<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 
# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_SETTINGS'))
{
	/**
	 * Class for setup plugin settings
	 */
	class MRKV_UA_SHIPPING_SETTINGS
	{
		/**
		 * Constructor for plugin settings
		 * */
		function __construct()
		{
			# Setup woo plugin settings options
			new MRKV_UA_SHIPPING_OPTIONS();

			# Setup woo plugin settings menu
			new MRKV_UA_SHIPPING_MENU();

			# Setup woo plugin settings assets
			new MRKV_UA_SHIPPING_ADMIN_ASSETS();

			# Setup woo plugin settings notification
			new MRKV_UA_SHIPPING_NOTIFICATION();
		}
	}
}