<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$mrkv_ua_shipping_up_warehouse_middlename_exclude = 'no';
$mrkv_ua_shipping_up_warehouse_middlename_required = 'no';

if(isset($this->active_shipping['ukr-poshta']['methods']['mrkv_ua_shipping_ukr-poshta']))
{
	if(isset($this->active_shipping['ukr-poshta']['settings']))
	{
		if(!isset($this->active_shipping['ukr-poshta']['settings']['checkout']['middlename']['enabled']) || $this->active_shipping['ukr-poshta']['settings']['checkout']['middlename']['enabled'] != 'on')
		{
			$mrkv_ua_shipping_up_warehouse_middlename_exclude = 'yes';
		}
		else{
			if(isset($this->active_shipping['ukr-poshta']['settings']['checkout']['middlename']['required']) && $this->active_shipping['ukr-poshta']['settings']['checkout']['middlename']['required'] == 'on')
			{
				$mrkv_ua_shipping_up_warehouse_middlename_required = 'yes';
			}
		}
	}
}

$mrkv_ua_shipping_args['up_middlename_exclude'] = $mrkv_ua_shipping_up_warehouse_middlename_exclude;
$mrkv_ua_shipping_args['up_middlename_required'] = $mrkv_ua_shipping_up_warehouse_middlename_required;

$mrkv_ua_shipping_args['ukr_city_area'] = array(
	array('label' => __('Vinnytsia, Vinnytsia district', 'mrkv-ua-shipping'), 'value' => '1057'),
	array('label' => __('Dnipro, Dniprovskyi district', 'mrkv-ua-shipping'), 'value' => '3641'),
	array('label' => __('Zhytomyr, Zhytomyr district', 'mrkv-ua-shipping'), 'value' => '6708'),
	array('label' => __('Zaporizhzhia, Zaporizhzhya district', 'mrkv-ua-shipping'), 'value' => '8968'),
	array('label' => __('Ivano-Frankivsk, Ivano-Frankivsk district', 'mrkv-ua-shipping'), 'value' => '9826'),
	array('label' => __('Kyiv, Kyiv district', 'mrkv-ua-shipping'), 'value' => '29713'),
	array('label' => __('Kropyvnytskyi, Kropyvnytskyi district', 'mrkv-ua-shipping'), 'value' => '12069'),
	array('label' => __('Lutsk, Lutsk district', 'mrkv-ua-shipping'), 'value' => '3477'),
	array('label' => __('Lviv, Lviv district', 'mrkv-ua-shipping'), 'value' => '14288'),
	array('label' => __('Mykolaiv, Mykolaiv district', 'mrkv-ua-shipping'), 'value' => '16169'),
	array('label' => __('Odesa, Odesa district', 'mrkv-ua-shipping'), 'value' => '17069'),
	array('label' => __('Poltava, Poltava district', 'mrkv-ua-shipping'), 'value' => '19234'),
	array('label' => __('Rivne, Rivne district', 'mrkv-ua-shipping'), 'value' => '20296'),
	array('label' => __('Sumy, Sumy district', 'mrkv-ua-shipping'), 'value' => '21680'),
	array('label' => __('Ternopil, Ternopil district', 'mrkv-ua-shipping'), 'value' => '22662'),
	array('label' => __('Uzhhorod, Uzhhorod district', 'mrkv-ua-shipping'), 'value' => '8553'),
	array('label' => __('Kharkiv, Kharkiv district', 'mrkv-ua-shipping'), 'value' => '24550'),
	array('label' => __('Kherson, Kherson district', 'mrkv-ua-shipping'), 'value' => '25448'),
	array('label' => __('Khmelnytskyi, Khmelnytsky district', 'mrkv-ua-shipping'), 'value' => '26481'),
	array('label' => __('Cherkasy, Cherkasy district', 'mrkv-ua-shipping'), 'value' => '27760'),
	array('label' => __('Chernivtsi, Chernivtsi district', 'mrkv-ua-shipping'), 'value' => '28188'),
	array('label' => __('Chernihiv, Chernihiv district', 'mrkv-ua-shipping'), 'value' => '29712'),
);
$mrkv_ua_shipping_args['city_placeholder'] = __('Enter the first 3 letters', 'mrkv-ua-shipping');