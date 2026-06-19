<?php
/**
 * Plugin Name: morkva UA Shipping
 * Plugin URI: https://morkva.co.ua/product-category/plugins/
 * Description: 2-in-1: Nova Poshta and Ukrposhta delivery services. Create shipping methods and shipments easily
 * Version: 1.10.5
 * Author: morkva
 * Text Domain: mrkv-ua-shipping
 * Domain Path: /i18n/
 * Tested up to: 7.0
 * Requires at least: 5.0
 * WC requires at least: 3.8
 * WC tested up to: 10.0
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );

# Global File
define('MRKV_UA_SHIPPING_PLUGIN_FILE', __FILE__);

# Include CONSTANTS
require_once 'constants-mrkv-ua-shipping.php';

/**
 * Initialize the plugin after all plugins are loaded.
 */
function mrkv_ua_shipping_init() {
    // Ensure WooCommerce is active
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    // Load translations first
    mrkv_ua_shipping_load_textdomain();

    // Include and initialize the main plugin class
    require_once 'classes/mrkv-ua-shipping-run.php';
    new MRKV_UA_SHIPPING_RUN();
}

add_action( 'init', 'mrkv_ua_shipping_init' );

# Include new shipping methods
add_action( 'woocommerce_shipping_init', 'mrkv_ua_shipping_include_shipping_method' );

/**
 * Include shipping files
 */
function mrkv_ua_shipping_include_shipping_method()
{
    $m_ua_active_plugins = get_option('m_ua_active_plugins');

    // Include plugin constants
    require_once 'constants-mrkv-ua-shipping-methods.php';

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

# Setup new shipping methods
add_filter( 'woocommerce_shipping_methods', 'mrkv_ua_shipping_add_shipping_method_woo' );
/**
 * Add new shipping methods class in the shipping list
 * @param array All shipping methods
 * 
 * @return array All shipping methods
 * */
function mrkv_ua_shipping_add_shipping_method_woo($methods)
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

function mrkv_ua_shipping_load_textdomain()
{
    $site_locale = get_locale(); 
    $user_locale = get_user_locale();

    if (is_admin() && ($user_locale === 'ru_RU' || $user_locale === 'uk') && $site_locale !== $user_locale) {
        load_textdomain('mrkv-ua-shipping', dirname( plugin_basename( MRKV_UA_SHIPPING_PLUGIN_FILE ) ) . '/i18n/mrkv-ua-shipping-' . $user_locale . '.mo');
    } else {
        // phpcs:ignore PluginCheck.CodeAnalysis.DiscouragedFunctions.load_plugin_textdomainFound
        load_plugin_textdomain('mrkv-ua-shipping', false, dirname( plugin_basename( MRKV_UA_SHIPPING_PLUGIN_FILE ) ) . '/i18n/');
    }

    // Include plugin constants
    require_once 'constants-mrkv-ua-shipping-methods.php';
}    