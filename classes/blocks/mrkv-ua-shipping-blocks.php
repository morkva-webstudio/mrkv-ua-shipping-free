<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_BLOCKS'))
{
	/**
	 * Class for setup plugin 
	 */
	class MRKV_UA_SHIPPING_BLOCKS
	{
		/**
		 * Constructor for plugin setup
		 * */
		function __construct()
		{
            new MRKV_UA_SHIPPING_BLOCKS_FIELDS();
            new MRKV_UA_SHIPPING_BLOCKS_ASSETS();
            new MRKV_UA_SHIPPING_BLOCKS_ORDER();
            new MRKV_UA_SHIPPING_BLOCKS_VALIDATION();
        }
    }
}