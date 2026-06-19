<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

# Include ajax shipping
require_once 'mrkv-ua-shipping-methods-ajax.php';
# Include checkout settings shipping
require_once 'mrkv-ua-shipping-methods-checkout.php';
# Include shipping cron
require_once 'mrkv-ua-shipping-methods-cron.php';
# Include order settings shipping
require_once 'mrkv-ua-shipping-methods-order.php';

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_METHODS'))
{
	/**
	 * Class for setup shipping methods 
	 */
	class MRKV_UA_SHIPPING_METHODS
	{
		/**
		 * Constructor for plugin shipping methods
		 * */
		function __construct()
		{
			# Setup woo plugin shipping methods ajax
			new MRKV_UA_SHIPPING_METHODS_AJAX();

			# Load settings page constants
			add_action( 'wp_loaded', array($this, 'get_shipping_admin_settings'));
			add_action( 'admin_init', array($this, 'get_shipping_admin_settings_admin'));
			add_filter('template_redirect', array($this, 'mrkv_save_page_context_to_session'), 10, 2);
		}

		public function mrkv_save_page_context_to_session() {

			if (is_admin() || !WC()->session) return;

			$url = '';
			if ( isset( $_SERVER['REQUEST_URI'] ) ) {
				$url = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
			}

			if ( empty( $url ) ) {
				return;
			}

			$shop_id     = wc_get_page_id( 'shop' );
            $shop_slug     = $shop_id > 0 ? wp_make_link_relative( get_permalink( $shop_id ) ) : '/shop';
			$is_forbidden = ( strpos( $url, 'product' ) !== false || strpos( $url, $shop_slug ) !== false );

			if ($is_forbidden) {
				WC()->session->set('mrkv_is_on_product_page', true);
			} else {
				WC()->session->set('mrkv_is_on_product_page', false);
			}

			WC()->session->save_data();

		}

		/**
		 * Add shippings settings admin page
		 * **/
		public function get_shipping_admin_settings_admin()
		{
			global $plugin_page;

			# Check page data
			if(isset( $plugin_page ))
			{
				# Loop all shipping
				foreach(MRKV_UA_SHIPPING_LIST as $slug => $shipping)
				{
					# Check if settings shipping page
					if($plugin_page == 'mrkv_ua_shipping_' . $slug)
					{
						define('MRKV_UA_SHIPPING_SETTINGS_SLUG', $slug);

						require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/settings/global/mrkv-ua-shipping-option-fields.php';
						require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/' . MRKV_UA_SHIPPING_SETTINGS_SLUG 
							. '/api/mrkv-ua-shipping-api-' . MRKV_UA_SHIPPING_SETTINGS_SLUG . '.php';

						global $mrkv_global_option_generator;
						$mrkv_global_option_generator = new MRKV_UA_SHIPPING_OPTION_FIELDS();
						define('MRKV_OPTION_OBJECT_NAME', MRKV_UA_SHIPPING_SETTINGS_SLUG . '_m_ua_settings');
						define('MRKV_SHIPPING_SETTINGS', get_option(MRKV_OPTION_OBJECT_NAME));
						
						$api_class = MRKV_UA_SHIPPING_LIST[MRKV_UA_SHIPPING_SETTINGS_SLUG]['api_class'];
						global $mrkv_global_shipping_object;
						$mrkv_global_shipping_object = new $api_class(MRKV_SHIPPING_SETTINGS);

						require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/' . $slug . '/settings/mrkv-ua-shipping-settings-' . $slug . '.php';

						$shipping_setting_class = MRKV_UA_SHIPPING_LIST[$slug]['settings_class'];

						new $shipping_setting_class();
					}
					elseif(str_contains($plugin_page, 'mrkv_ua_shipping_' . $slug))
					{
						define('MRKV_UA_SHIPPING_SETTINGS_SLUG', $slug);
						define('MRKV_UA_SHIPPING_SETTINGS_PAGE_SLUG', str_replace('mrkv_ua_shipping_' . $slug . '_', "", $plugin_page));

						if(MRKV_UA_SHIPPING_SETTINGS_PAGE_SLUG == 'invoices')
						{
							require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/' . MRKV_UA_SHIPPING_SETTINGS_SLUG 
							. '/api/mrkv-ua-shipping-api-' . MRKV_UA_SHIPPING_SETTINGS_SLUG . '.php';

							define('MRKV_OPTION_OBJECT_NAME', MRKV_UA_SHIPPING_SETTINGS_SLUG . '_m_ua_settings');
							define('MRKV_SHIPPING_SETTINGS', get_option(MRKV_OPTION_OBJECT_NAME));
						
							$api_class = MRKV_UA_SHIPPING_LIST[MRKV_UA_SHIPPING_SETTINGS_SLUG]['api_class'];
							global $mrkv_global_shipping_object;
							$mrkv_global_shipping_object = new $api_class(MRKV_SHIPPING_SETTINGS);
						}
					}
				}
			}
		}

		/**
		 * Add shippings settings admin page
		 * **/
		public function get_shipping_admin_settings()
		{
			$m_ua_active_plugins = get_option('m_ua_active_plugins');
			
			foreach(MRKV_UA_SHIPPING_LIST as $slug => $shipping)
			{
				if(isset($m_ua_active_plugins[$slug]['enabled']) && $m_ua_active_plugins[$slug]['enabled'] == 'on')
				{
					# Setup woo plugin shipping methods checkout
					new MRKV_UA_SHIPPING_METHODS_CHECKOUT();

					# Setup woo plugin shipping methods order
					new MRKV_UA_SHIPPING_METHODS_ORDER();

					# Setup woo plugin shipping methods cron
					new MRKV_UA_SHIPPING_METHODS_CRON();

					break;
				}
			}
		}
	}
}