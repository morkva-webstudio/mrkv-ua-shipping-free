<?php
/**
 * Plugin Name: morkva UA Shipping
 * Plugin URI: https://morkva.co.ua/product-category/plugins/
 * Description: 2-in-1: Nova Poshta and Ukrposhta delivery services. Create shipping methods and shipments easily
 * Version: 1.12.1
 * Author: morkva
 * Text Domain: mrkv-ua-shipping
 * Domain Path: /i18n/
 * Tested up to: 7.1
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

# Register Autoloader BEFORE initialization
spl_autoload_register( function( $class_name ) {
    if ( 0 !== strpos( $class_name, 'MRKV_UA_SHIPPING_' ) ) {
        return;
    }

    $class_file = str_replace( '_', '-', strtolower( $class_name ) );

    $possible_paths = array(
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/blocks/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/settings/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/settings/admin/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/settings/global/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/settings/log/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-global/ajax/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-global/api/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-global/invoice/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-global/settings/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-global/woocommerce/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-global/cron/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-poshta/ajax/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-poshta/api/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-poshta/invoice/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-poshta/settings/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-poshta/woocommerce/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-poshta/cron/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/rozetka-delivery/ajax/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/rozetka-delivery/api/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/rozetka-delivery/invoice/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/rozetka-delivery/settings/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/rozetka-delivery/woocommerce/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/rozetka-delivery/cron/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/ukr-poshta/ajax/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/ukr-poshta/api/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/ukr-poshta/invoice/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/ukr-poshta/settings/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/ukr-poshta/woocommerce/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/ukr-poshta/cron/' . $class_file . '.php',
        MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/woocommerce/' . $class_file . '.php',
    );

    foreach ( $possible_paths as $path ) {
        if ( file_exists( $path ) ) {
            require_once $path;
            return;
        }
    }
} );

/**
 * Initialize the plugin after all plugins are loaded.
 */
function mrkv_ua_shipping_init() {
    // Ensure WooCommerce is active
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

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

    new MRKV_UA_SHIPPING_RUN();
}

add_action( 'init', 'mrkv_ua_shipping_init' );

new MRKV_UA_SHIPPING_CONNECTER();