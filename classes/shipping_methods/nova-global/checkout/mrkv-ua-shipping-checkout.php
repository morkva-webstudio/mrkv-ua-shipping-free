<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if(isset($this->active_shipping['nova-global']['methods']['mrkv_ua_shipping_nova-global']))
{
	$mrkv_ua_shipping_instance_id = $this->active_shipping['nova-global']['methods']['mrkv_ua_shipping_nova-global']['instance_id'];
	$mrkv_ua_shipping_shipping_settings = get_option('woocommerce_mrkv_ua_shipping_nova-global_' . $mrkv_ua_shipping_instance_id . '_settings');

	$mrkv_ua_shipping_args['nova_global_type']['mrkv_ua_shipping_nova-global_' . $mrkv_ua_shipping_instance_id] = '';

	if(isset($mrkv_ua_shipping_shipping_settings['warehouse_type']) && $mrkv_ua_shipping_shipping_settings['warehouse_type'])
    {
    	$mrkv_ua_shipping_args['nova_global_type']['mrkv_ua_shipping_nova-global_' . $mrkv_ua_shipping_instance_id] = $mrkv_ua_shipping_shipping_settings['warehouse_type'];
    }
}