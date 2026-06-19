<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_MENU'))
{
	/**
	 * Class for setup plugin admin menu
	 */
	class MRKV_UA_SHIPPING_MENU
	{
		/**
	     * Slug for page in Woo Tab Sections
	     * 
	     * */
	    private $slug = 'mrkv_ua_shipping_settings';

		/**
		 * Constructor for plugin admin menu
		 * */
		function __construct()
		{
			# Register page settings
			add_action('admin_menu', array($this, 'mrkv_ua_shipping_register_plugin_page'), 99);
		}

		/**
		 * Add settings page to menu
		 * */
		public function mrkv_ua_shipping_register_plugin_page()
		{
			# Add menu to WP
	        add_menu_page(__('morkva UA Shipping', 'mrkv-ua-shipping'), __('morkva UA Shipping', 'mrkv-ua-shipping'), 'manage_options', $this->slug, array($this, 'mrkv_ua_shipping_get_plugin_settings_content'), MRKV_UA_SHIPPING_IMG_URL . '/global/morkva-icon-20x20.svg');

			add_submenu_page($this->slug, __('Global Settings', 'mrkv-ua-shipping'), __('Global Settings', 'mrkv-ua-shipping'), 'manage_options', $this->slug, array($this, 'mrkv_ua_shipping_get_plugin_settings_content'));

	        $m_ua_active_plugins = get_option('m_ua_active_plugins');

	        foreach(MRKV_UA_SHIPPING_LIST as $slug => $shipping)
			{
				# Add submenu page
	        	add_submenu_page($this->slug, $shipping['name'], $shipping['name'], 'manage_options', 'mrkv_ua_shipping_' . $slug, array($this, 'mrkv_ua_shipping_method_page_content'));

	        	if(isset($shipping['pages']))
	        	{
	        		foreach($shipping['pages'] as $page_slug => $page_name)
		        	{
		        		# Add submenu page
		        		add_submenu_page('mrkv_ua_shipping_' . $slug, $page_name, $page_name, 'manage_options', 'mrkv_ua_shipping_' . $slug . '_' . $page_slug, array($this, 'mrkv_ua_shipping_method_page_info_content'));

		        		if($page_slug == 'invoices')
		        		{
		        			if(isset($m_ua_active_plugins[$slug]['enabled']) && $m_ua_active_plugins[$slug]['enabled'] == 'on')
							{
								# Add submenu page
		        				add_submenu_page('woocommerce', $page_name . ' ' . $shipping['name'], $page_name  . ' ' . $shipping['name'], 'manage_options', 'admin.php?page=' . 'mrkv_ua_shipping_' . $slug . '_' . $page_slug);
							}
		        		}
		        	}
	        	}
			}

			# Add submenu page
        	add_submenu_page($this->slug, __('About us', 'mrkv-ua-shipping'), __('About us', 'mrkv-ua-shipping'), 'manage_options', 'mrkv_ua_shipping_about_us', array($this, 'mrkv_ua_shipping_method_about_content'));
		}

		/**
		 * Get settings page
		 * */
		public function mrkv_ua_shipping_get_plugin_settings_content()
		{
			# Include template
			include MRKV_UA_SHIPPING_PLUGIN_PATH_TEMP . '/settings/template-mrkv-ua-shipping-settings.php';
		}

		public function mrkv_ua_shipping_method_page_content()
		{
			# Include template
			include MRKV_UA_SHIPPING_PLUGIN_PATH_TEMP . '/settings/methods/template-mrkv-ua-shipping-global.php';
		}

		/**
		 * Get content shipping pages
		 * */
		public function mrkv_ua_shipping_method_page_info_content()
		{
			# Include template
			include MRKV_UA_SHIPPING_PLUGIN_PATH_TEMP . '/settings/methods/template-mrkv-ua-shipping-pages-global.php';
		}

		/**
		 * Get content shipping pages
		 * */
		public function mrkv_ua_shipping_method_about_content()
		{
			# Include template
			include MRKV_UA_SHIPPING_PLUGIN_PATH_TEMP . '/settings/template-mrkv-ua-shipping-about-us.php';
		}
	}
}