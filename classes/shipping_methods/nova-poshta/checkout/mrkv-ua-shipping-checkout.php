<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
$mrkv_ua_shipping_nova_poshtomat_exclude = '';
$mrkv_ua_shipping_nova_poshtomat_middlename_exclude = 'no';
$mrkv_ua_shipping_nova_poshtomat_middlename_required = 'no';
$mrkv_ua_shipping_nova_poshta_search_by_number = 'no';
$mrkv_ua_shipping_nova_warehouse_text = '';

if(isset($this->active_shipping['nova-poshta']['methods']['mrkv_ua_shipping_nova-poshta']))
{
	$mrkv_ua_shipping_instance_id = $this->active_shipping['nova-poshta']['methods']['mrkv_ua_shipping_nova-poshta']['instance_id'];
	$mrkv_ua_shipping_shipping_settings = get_option('woocommerce_mrkv_ua_shipping_nova-poshta_' . $mrkv_ua_shipping_instance_id . '_settings');

	if(isset($mrkv_ua_shipping_shipping_settings['exclude_poshtomat']) && $mrkv_ua_shipping_shipping_settings['exclude_poshtomat'] == 'yes')
    {
        $mrkv_ua_shipping_nova_poshtomat_exclude = 'none';
        $mrkv_ua_shipping_nova_warehouse_text = __('Warehouse', 'mrkv-ua-shipping');
    }

    if(isset($mrkv_ua_shipping_shipping_settings['search_by_number']) && $mrkv_ua_shipping_shipping_settings['search_by_number'] == 'yes')
    {
        $mrkv_ua_shipping_nova_poshta_search_by_number = 'yes';
    }
}
if(isset($this->active_shipping['nova-poshta']['methods']['mrkv_ua_shipping_nova-poshta_address']))
{
	if(isset($this->active_shipping['nova-poshta']['settings']))
	{
		if(!isset($this->active_shipping['nova-poshta']['settings']['checkout']['middlename']['enabled']) || $this->active_shipping['nova-poshta']['settings']['checkout']['middlename']['enabled'] != 'on')
		{
			$mrkv_ua_shipping_nova_poshtomat_middlename_exclude = 'yes';
		}
		else{
			if(isset($this->active_shipping['nova-poshta']['settings']['checkout']['middlename']['required']) && $this->active_shipping['nova-poshta']['settings']['checkout']['middlename']['required'] == 'on')
			{
				$mrkv_ua_shipping_nova_poshtomat_middlename_required = 'yes';
			}
		}
	}
}

$mrkv_ua_shipping_args['nova_middlename_exclude'] = $mrkv_ua_shipping_nova_poshtomat_middlename_exclude;
$mrkv_ua_shipping_args['nova_middlename_required'] = $mrkv_ua_shipping_nova_poshtomat_middlename_required;
$mrkv_ua_shipping_args['nova_search_by_number'] = $mrkv_ua_shipping_nova_poshta_search_by_number;
$mrkv_ua_shipping_args['enter_search_text'] = __('Please enter warehouse number', 'mrkv-ua-shipping');
$mrkv_ua_shipping_args['nova_warehouse_type'] = $mrkv_ua_shipping_nova_poshtomat_exclude;
$mrkv_ua_shipping_args['nova_warehouse_text'] = $mrkv_ua_shipping_nova_warehouse_text;
$mrkv_ua_shipping_args['nova_poshtamat_type'] = 'f9316480-5f2d-425d-bc2c-ac7cd29decf0';
$mrkv_ua_shipping_args['city_placeholder'] = __('Enter the first 3 letters', 'mrkv-ua-shipping');
$mrkv_ua_shipping_args['city_text_weight'] = __('Order products don\'t match weight and dimensions criteria, try another method', 'mrkv-ua-shipping');

$mrkv_ua_shipping_args['nova_city_area'] = array(
	array('label' => __('Vinnytsia', 'mrkv-ua-shipping'), 'area' => __('Vinnytsia', 'mrkv-ua-shipping'), 'value' => 'db5c88de-391c-11dd-90d9-001a92567626'),
	array('label' => __('Dnipro', 'mrkv-ua-shipping'), 'area' => __('Dnipro', 'mrkv-ua-shipping'), 'value' => 'db5c88f0-391c-11dd-90d9-001a92567626'),
	array('label' => __('Zhytomyr', 'mrkv-ua-shipping'), 'area' => __('Zhytomyr', 'mrkv-ua-shipping'), 'value' => 'db5c88c4-391c-11dd-90d9-001a92567626'),
	array('label' => __('Zaporizhzhia', 'mrkv-ua-shipping'), 'area' => __('Zaporizhzhia', 'mrkv-ua-shipping'), 'value' => 'db5c88c6-391c-11dd-90d9-001a92567626'),
	array('label' => __('Ivano-Frankivsk', 'mrkv-ua-shipping'), 'area' => __('Ivano-Frankivsk', 'mrkv-ua-shipping'), 'value' => 'db5c8904-391c-11dd-90d9-001a92567626'),
	array('label' => __('Kyiv', 'mrkv-ua-shipping'), 'area' => __('Kyiv', 'mrkv-ua-shipping'), 'value' => '8d5a980d-391c-11dd-90d9-001a92567626'),
	array('label' => __('Kropyvnytskyi', 'mrkv-ua-shipping'), 'area' => __('Kropyvnytskyi', 'mrkv-ua-shipping'), 'value' => 'db5c891b-391c-11dd-90d9-001a92567626'),
	array('label' => __('Lutsk', 'mrkv-ua-shipping'), 'area' => __('Lutsk', 'mrkv-ua-shipping'), 'value' => 'db5c893b-391c-11dd-90d9-001a92567626'),
	array('label' => __('Lviv', 'mrkv-ua-shipping'), 'area' => __('Lviv', 'mrkv-ua-shipping'), 'value' => 'db5c88f5-391c-11dd-90d9-001a92567626'),
	array('label' => __('Mykolaiv', 'mrkv-ua-shipping'), 'area' => __('Mykolaiv', 'mrkv-ua-shipping'), 'value' => 'db5c888c-391c-11dd-90d9-001a92567626'),
	array('label' => __('Odesa', 'mrkv-ua-shipping'), 'area' => __('Odesa', 'mrkv-ua-shipping'), 'value' => 'db5c88d0-391c-11dd-90d9-001a92567626'),
	array('label' => __('Poltava', 'mrkv-ua-shipping'), 'area' => __('Poltava', 'mrkv-ua-shipping'), 'value' => 'db5c8892-391c-11dd-90d9-001a92567626'),
	array('label' => __('Rivne', 'mrkv-ua-shipping'), 'area' => __('Rivne', 'mrkv-ua-shipping'), 'value' => 'db5c896a-391c-11dd-90d9-001a92567626'),
	array('label' => __('Sumy', 'mrkv-ua-shipping'), 'area' => __('Sumy', 'mrkv-ua-shipping'), 'value' => 'db5c88e5-391c-11dd-90d9-001a92567626'),
	array('label' => __('Ternopil', 'mrkv-ua-shipping'), 'area' => __('Ternopil', 'mrkv-ua-shipping'), 'value' => 'db5c8900-391c-11dd-90d9-001a92567626'),
	array('label' => __('Uzhhorod', 'mrkv-ua-shipping'), 'area' => __('Uzhhorod', 'mrkv-ua-shipping'), 'value' => 'e221d627-391c-11dd-90d9-001a92567626'),
	array('label' => __('Kharkiv', 'mrkv-ua-shipping'), 'area' => __('Kharkiv', 'mrkv-ua-shipping'), 'value' => 'db5c88e0-391c-11dd-90d9-001a92567626'),
	array('label' => __('Kherson', 'mrkv-ua-shipping'), 'area' => __('Kherson', 'mrkv-ua-shipping'), 'value' => 'db5c88cc-391c-11dd-90d9-001a92567626'),
	array('label' => __('Khmelnytskyi', 'mrkv-ua-shipping'), 'area' => __('Khmelnytskyi', 'mrkv-ua-shipping'), 'value' => 'db5c88ac-391c-11dd-90d9-001a92567626'),
	array('label' => __('Cherkasy', 'mrkv-ua-shipping'), 'area' => __('Cherkasy', 'mrkv-ua-shipping'), 'value' => 'db5c8902-391c-11dd-90d9-001a92567626'),
	array('label' => __('Chernivtsi', 'mrkv-ua-shipping'), 'area' => __('Chernivtsi', 'mrkv-ua-shipping'), 'value' => 'e221d642-391c-11dd-90d9-001a92567626'),
	array('label' => __('Chernihiv', 'mrkv-ua-shipping'), 'area' => __('Chernihiv', 'mrkv-ua-shipping'), 'value' => 'db5c897c-391c-11dd-90d9-001a92567626'),
);