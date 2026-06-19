<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
# Get plugin data
require_once ABSPATH . 'wp-admin/includes/plugin.php';
$mrkv_ua_shipping_plug_data = get_plugin_data(MRKV_UA_SHIPPING_PLUGIN_FILE,false, false);

# Constans 

# Directories
define('MRKV_UA_SHIPPING_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MRKV_UA_SHIPPING_PLUGIN_PATH_TEMP', plugin_dir_path(__FILE__) . 'templates');
define('MRKV_UA_SHIPPING_PLUGIN_PATH_SHIP', plugin_dir_path(__FILE__) . 'classes/shipping_methods');

# Links
define('MRKV_UA_SHIPPING_PLUGIN_DIR', plugin_dir_url(__FILE__));
define('MRKV_UA_SHIPPING_ASSETS_URL', plugin_dir_url(__FILE__) . 'assets');
define('MRKV_UA_SHIPPING_IMG_URL', plugin_dir_url(__FILE__) . 'assets/images');

# Data
define('MRKV_UA_SHIPPING_NAME', $mrkv_ua_shipping_plug_data['Name']);
define('MRKV_UA_SHIPPING_PLUGIN_VERSION', $mrkv_ua_shipping_plug_data['Version']);
define('MRKV_UA_SHIPPING_PLUGIN_TEXT_DOMAIN', 'mrkv-ua-shipping');

# Allow tags 
define( 'MRKV_UA_SHIPPING_ALLOW_TAGS', array(
    'label' => array(
        'for'   => true,
        'class' => true,
    ),
    'input' => array(
        'type'        => true,
        'id'          => true,
        'name'        => true,
        'value'       => true,
        'placeholder' => true,
        'step'        => true,
        'min'         => true,
        'max'         => true,
        'checked'     => true,
        'disabled'    => true,
        'readonly'    => true,
        'multiple'    => true,
        'onwheel'     => true,
        'class'       => true,
    ),
    'select' => array(
        'id'       => true,
        'name'     => true,
        'multiple' => true,
        'disabled' => true,
        'class'    => true,
    ),
    'option' => array(
        'value'    => true,
        'selected' => true,
        'class'    => true,
    ),
    'textarea' => array(
        'id'          => true,
        'name'        => true,
        'placeholder' => true,
        'readonly'    => true,
        'class'       => true,
        'rows'        => true,
        'cols'        => true,
    ),
    'p' => array(
        'class' => true,
    ),
    'div' => array(
        'class' => true,
        'id'    => true,
    ),
    'span' => array(
        'class' => true,
    ),
) );