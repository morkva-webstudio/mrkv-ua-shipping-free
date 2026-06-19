<?php

$mrkv_ua_shipping_translate_labels = array(
	'nova-poshta' => array(
		'name' => __('Nova Poshta', 'mrkv-ua-shipping'),
		'description' => __('Add shipping method, calculate shipping costs, create and manage shipments, both manually and automatically. Using API 2.0 and connects directly to the Nova Poshta server for fast and secure user experience.', 'mrkv-ua-shipping'),
		'invoice_class' => 'MRKV_UA_SHIPPING_NOVA_POSHTA_INVOICE',
		'api_class' => 'MRKV_UA_SHIPPING_API_NOVA_POSHTA',
		'settings_class' => 'MRKV_UA_SHIPPING_SETTINGS_NOVA_POSHTA',
		'pages' => array(
			'invoices' => __('My shipments', 'mrkv-ua-shipping')
		),
		'invoice_links' => array(
			'invoice_pdf' => 'https://my.novaposhta.ua/orders/printDocument/orders[]/',
			'invoice_sticker' => 'https://my.novaposhta.ua/orders/printMarkings/orders[]/',
			'invoice_link_end' => '/type/pdf/apiKey/',
		),
		'old_slugs' => array(
			'mrkv_ua_shipping_nova-poshta' => 'nova_poshta_shipping_method',
			'mrkv_ua_shipping_nova-poshta_poshtamat' => 'nova_poshta_shipping_method_poshtomat',
			'mrkv_ua_shipping_nova-poshta_address'=> 'npttn_address_shipping_method'
		),
		'old_ttn_slug' => 'novaposhta_ttn',
		'method' => array(
			'mrkv_ua_shipping_nova-poshta' => array(
				'class' => 'MRKV_UA_SHIPPING_NOVA_POSHTA',
				'slug' => 'mrkv_ua_shipping_nova-poshta',
				'filename' => 'mrkv-ua-shipping-method-nova-poshta',
				'checkout_fields' => array(
					'_city' => array(
						'type' => 'select',
						'autocomplete' => 'off',
						'options' => array('' => __('Choose the city', 'mrkv-ua-shipping')),
						'required' => true,
						'label' => __('City (settlement)', 'mrkv-ua-shipping'),
						'replace' => '',
					),
					'_city_label' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_city'
					),
					'_city_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_city_ref',
						'old_slug' => 'np_city_ref'
					),
					'_area_name' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_state'
					),
					'_warehouse' => array(
						'type' => 'select',
						'autocomplete' => 'off',
						'options' => array('' => __('Choose the warehouse', 'mrkv-ua-shipping')),
						'required' => true,
						'label' => __('Warehouse/poshtomat', 'mrkv-ua-shipping'),
						'replace' => '_address_1'
					),
					'_warehouse_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_warehouse_ref',
						'old_slug' => 'np_warehouse_ref',
						'required' => true,
						'label' => __('Warehouse/poshtomat', 'mrkv-ua-shipping'),
					),
					'_warehouse_number' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_postcode'
					)
				)
			),
			'mrkv_ua_shipping_nova-poshta_poshtamat' => array(
				'class' => 'MRKV_UA_SHIPPING_NOVA_POSHTA_POSHTAMAT',
				'slug' => 'mrkv_ua_shipping_nova-poshta_poshtamat',
				'filename' => 'mrkv-ua-shipping-method-nova-poshta-poshtamat',
				'checkout_fields' => array(
					'_city' => array(
						'type' => 'select',
						'autocomplete' => 'off',
						'options' => array('' => __('Choose the city', 'mrkv-ua-shipping')),
						'required' => true,
						'label' => __('City (settlement)', 'mrkv-ua-shipping'),
						'replace' => '',
					),
					'_city_label' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_city'
					),
					'_city_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_city_ref',
						'old_slug' => 'np_city_ref'
					),
					'_area_name' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_state'
					),
					'_name' => array(
						'type' => 'select',
						'autocomplete' => 'off',
						'options' => array('' => __('Choose the poshtomat', 'mrkv-ua-shipping')),
						'required' => true,
						'label' => __('Poshtomat', 'mrkv-ua-shipping'),
						'replace' => '_address_1'
					),
					'_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_warehouse_ref',
						'old_slug' => 'np_warehouse_ref',
						'required' => true,
						'label' => __('Poshtomat', 'mrkv-ua-shipping'),
					),
					'_number' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_postcode'
					)
				)
			),
			'mrkv_ua_shipping_nova-poshta_address' => array(
				'class' => 'MRKV_UA_SHIPPING_NOVA_POSHTA_ADDRESS',
				'slug' => 'mrkv_ua_shipping_nova-poshta_address',
				'filename' => 'mrkv-ua-shipping-method-nova-poshta-address',
				'checkout_fields' => array(
					'_patronymic' => array(
						'type' => 'text',
						'required' => true,
						'label' => __('Patronymic', 'mrkv-ua-shipping'),
						'placeholder' => __('Enter the patronymic', 'mrkv-ua-shipping'),
						'replace' => '_patronymic',
						// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
						'exclude' => true,
						'order_edit' => true,
						'autocomplete' => 'off',
					),
					'_patronymic_enabled' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
					),
					'_city' => array(
						'type' => 'select',
						'autocomplete' => 'off',
						'options' => array('' => __('Choose the city', 'mrkv-ua-shipping')),
						'required' => true,
						'label' => __('City (settlement)', 'mrkv-ua-shipping'),
						'replace' => '',
					),
					'_city_label' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_city'
					),
					'_city_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_city_ref',
						'old_slug' => 'np_city_ref'
					),
					'_area_name' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_state'
					),
					'_street' => array(
						'type' => 'select',
						'autocomplete' => 'off',
						'options' => array('' => __('Choose the street', 'mrkv-ua-shipping')),
						'required' => true,
						'label' => __('Street', 'mrkv-ua-shipping'),
						'replace' => '_address_1',
					),
					'_street_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_street_ref'
					),
					'_house' => array(
						'type' => 'text',
						'required' => true,
						'label' => __('House', 'mrkv-ua-shipping'),
						'placeholder' => __('Number of house', 'mrkv-ua-shipping'),
						'replace' => '_address_2',
						'autocomplete' => 'off',
					),
					'_flat' => array(
						'type' => 'text',
						'required' => false,
						'label' => __('Flat', 'mrkv-ua-shipping'),
						'placeholder' => __('Number of flat', 'mrkv-ua-shipping'),
						'replace' => '_flat',
						'order_edit' => true,
						'autocomplete' => 'off',
					),
					'_address_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_address_ref'
					)
				)
			),
			'mrkv_ua_shipping_nova-poshta_international' => array(
				'class' => 'MRKV_UA_SHIPPING_NOVA_POSHTA_INTERNATIONAL',
				'slug' => 'mrkv_ua_shipping_nova-poshta_international',
				'filename' => 'mrkv-ua-shipping-method-nova-poshta-international',
				'validation_latin' => false,
				'checkout_fields' => array(
					'_warehouse' => array(
						'type' => 'text',
						'required' => true,
						'label' => __('Warehouse', 'mrkv-ua-shipping'),
						'placeholder' => __('Find nearest warehouse', 'mrkv-ua-shipping'),
						'replace' => '_address_1',
						'autocomplete' => 'new-password',
					),
					'_warehouse_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_warehouse_ref',
						'old_slug' => 'np_warehouse_ref',
						'required' => true,
						'label' => __('Warehouse/poshtomat', 'mrkv-ua-shipping'),
					),
					'_warehouse_number' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_postcode'
					)
				)
			),
			'mrkv_ua_shipping_nova-poshta_inter_address' => array(
				'class' => 'MRKV_UA_SHIPPING_NOVA_POSHTA_INTER_ADDRESS',
				'slug' => 'mrkv_ua_shipping_nova-poshta_inter_address',
				'filename' => 'mrkv-ua-shipping-method-nova-poshta-inter-address',
				'validation_latin' => false,
				'checkout_fields' => array(
					'_postcode' => array(
						'type' => 'text',
						'autocomplete' => 'new-password',
						'required' => true,
						'label' => __('Postal code', 'mrkv-ua-shipping'),
						'placeholder' => __('Enter the postal code', 'mrkv-ua-shipping'),
						'replace' => '_postcode'
					),
					'_region' => array(
						'type' => 'text',
						'autocomplete' => 'new-password',
						'required' => true,
						'label' => __('Region', 'mrkv-ua-shipping'),
						'placeholder' => __('Enter the region', 'mrkv-ua-shipping'),
						'replace' => '_state'
					),
					'_city' => array(
						'type' => 'text',
						'autocomplete' => 'new-password',
						'required' => true,
						'label' => __('City (settlement)', 'mrkv-ua-shipping'),
						'placeholder' => __('Enter the city', 'mrkv-ua-shipping'),
						'replace' => '_city'
					),
					'_street' => array(
						'type' => 'text',
						'autocomplete' => 'new-password',
						'required' => true,
						'label' => __('Street', 'mrkv-ua-shipping'),
						'placeholder' => __('Enter the street', 'mrkv-ua-shipping'),
						'replace' => '_address_1'
					),
					'_house' => array(
						'type' => 'text',
						'required' => true,
						'label' => __('House', 'mrkv-ua-shipping'),
						'placeholder' => __('Number of house', 'mrkv-ua-shipping'),
						'replace' => '_address_2',
						'autocomplete' => 'new-password',
					),
					'_flat' => array(
						'type' => 'text',
						'required' => false,
						'label' => __('Flat', 'mrkv-ua-shipping'),
						'placeholder' => __('Number of flat', 'mrkv-ua-shipping'),
						'replace' => '_flat',
						'order_edit' => true,
						'autocomplete' => 'new-password',
					),
				)
			)
		)
	),
	'ukr-poshta' => array(
		'name' => __('UkrPoshta', 'mrkv-ua-shipping'),
		'description' => __('Works with both domestic and international shipments. Add shipping method, calculate shipping costs, create and manage shipments, both manually and automatically. Get 10% off when creating shipments with our plugin.', 'mrkv-ua-shipping'),
		'api_class' => 'MRKV_UA_SHIPPING_API_UKR_POSHTA',
		'invoice_class' => 'MRKV_UA_SHIPPING_UKR_POSHTA_INVOICE',
		'settings_class' => 'MRKV_UA_SHIPPING_SETTINGS_UKR_POSHTA',
		'pages' => array(
			'invoices' => __('My shipments', 'mrkv-ua-shipping')
		),
		'invoice_links' => array(
			'invoice_pdf' => 'https://www.ukrposhta.ua/ecom/0.0.1/shipments/',
			'invoice_sticker' => '',
			'invoice_link_end' => '/sticker?token=',
		),
		'old_slugs' => array(
			'mrkv_ua_shipping_ukr-poshta' => 'ukrposhta_shippping',
			'mrkv_ua_shipping_ukr-poshta_address' => 'ukrposhta_address_shippping',
		),
		'old_ttn_slug' => 'ukrposhta_ttn',
		'method' => array(
			'mrkv_ua_shipping_ukr-poshta' => array(
				'class' => 'MRKV_UA_SHIPPING_UKR_POSHTA',
				'slug' => 'mrkv_ua_shipping_ukr-poshta',
				'filename' => 'mrkv-ua-shipping-method-ukr-poshta',
				'checkout_fields' => array(
					'_patronymic' => array(
						'type' => 'text',
						'required' => true,
						'label' => __('Patronymic', 'mrkv-ua-shipping'),
						'placeholder' => __('Enter the patronymic', 'mrkv-ua-shipping'),
						'replace' => '_patronymic',
						'order_edit' => true,
						// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
						'exclude' => true,
						'autocomplete' => 'off',
					),
					'_patronymic_enabled' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
					),
					'_city' => array(
						'type' => 'select',
						'autocomplete' => 'off',
						'options' => array('' => __('Choose the city', 'mrkv-ua-shipping')),
						'required' => true,
						'label' => __('City (settlement)', 'mrkv-ua-shipping'),
						'replace' => '_city',
					),
					'_city_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_city_ref'
					),
					'_area_name' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_state'
					),
					'_warehouse' => array(
						'type' => 'select',
						'autocomplete' => 'off',
						'options' => array('' => __('Choose the warehouse', 'mrkv-ua-shipping')),
						'required' => true,
						'label' => __('Warehouse', 'mrkv-ua-shipping'),
						'replace' => '_address_1'
					),
					'_warehouse_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_postcode',
						'required' => true,
						'label' => __('Warehouse', 'mrkv-ua-shipping'),
					),
					'_address_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_address_ref'
					)
				)
			),
			'mrkv_ua_shipping_ukr-poshta_address' => array(
				'class' => 'MRKV_UA_SHIPPING_UKR_POSHTA_ADDRESS',
				'slug' => 'mrkv_ua_shipping_ukr-poshta_address',
				'filename' => 'mrkv-ua-shipping-method-ukr-poshta-address',
				'checkout_fields' => array(
					'_patronymic' => array(
						'type' => 'text',
						'required' => true,
						'label' => __('Patronymic', 'mrkv-ua-shipping'),
						'placeholder' => __('Enter the patronymic', 'mrkv-ua-shipping'),
						'replace' => '_patronymic',
						'order_edit' => true,
						'autocomplete' => 'off',
					),
					'_city' => array(
						'type' => 'select',
						'autocomplete' => 'off',
						'options' => array('' => __('Choose the city', 'mrkv-ua-shipping')),
						'required' => true,
						'label' => __('City (settlement)', 'mrkv-ua-shipping'),
						'replace' => '_city',
					),
					'_city_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_city_ref'
					),
					'_area_name' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_state'
					),
					'_area_id' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_state_id'
					),
					'_district_id' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_district_id'
					),
					'_street' => array(
						'type' => 'select',
						'autocomplete' => 'off',
						'options' => array('' => __('Choose the street', 'mrkv-ua-shipping')),
						'required' => true,
						'label' => __('Street', 'mrkv-ua-shipping'),
						'replace' => '_address_1'
					),
					'_street_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_street_ref'
					),
					'_house' => array(
						'type' => 'select',
						'autocomplete' => 'off',
						'options' => array('' => __('Choose the house', 'mrkv-ua-shipping')),
						'required' => true,
						'label' => __('House', 'mrkv-ua-shipping'),
						'replace' => '_address_2'
					),
					'_house_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_postcode'
					),
					'_flat' => array(
						'type' => 'text',
						'required' => false,
						'label' => __('Flat', 'mrkv-ua-shipping'),
						'placeholder' => __('Number of flat', 'mrkv-ua-shipping'),
						'replace' => '_flat',
						'order_edit' => true,
						'autocomplete' => 'off',
					),
					'_address_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_address_ref'
					)
				)
			)
		)
	),
	'rozetka-delivery' => array(
		'name' => __('Rozetka Delivery', 'mrkv-ua-shipping'),
		'description' => __('Get the opportunity to deliver a parcel to any ROZETKA warehouse. You save your own time on order picking and shipping, as these processes are handled by ROZETKA', 'mrkv-ua-shipping'),
		'api_class' => 'MRKV_UA_SHIPPING_API_ROZETKA_DELIVERY',
		'invoice_class' => '',
		'settings_class' => 'MRKV_UA_SHIPPING_SETTINGS_ROZETKA_DELIVERY',
		'pages' => array(),
		'invoice_links' => array(),
		'old_slugs' => array(),
		'old_ttn_slug' => '',
		'method' => array(
			'mrkv_ua_shipping_rozetka-delivery' => array(
				'class' => 'MRKV_UA_SHIPPING_ROZETKA_DELIVERY',
				'slug' => 'mrkv_ua_shipping_rozetka-delivery',
				'filename' => 'mrkv-ua-shipping-method-rozetka-delivery',
				'checkout_fields' => array(
					'_city' => array(
						'type' => 'select',
						'autocomplete' => 'off',
						'options' => array('' => __('Choose the city', 'mrkv-ua-shipping')),
						'required' => true,
						'label' => __('City (settlement)', 'mrkv-ua-shipping'),
						'replace' => '',
					),
					'_city_label' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_city'
					),
					'_district' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_address_2'
					),
					'_district_id' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_district_id'
					),
					'_area_name' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_state'
					),
					'_area_id' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_area_id'
					),
					'_city_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_city_ref'
					),
					'_warehouse' => array(
						'type' => 'select',
						'autocomplete' => 'off',
						'options' => array('' => __('Choose the warehouse', 'mrkv-ua-shipping')),
						'required' => true,
						'label' => __('Warehouse', 'mrkv-ua-shipping'),
						'replace' => '_address_1'
					),
					'_warehouse_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_warehouse_ref',
						'required' => true,
						'label' => __('Warehouse', 'mrkv-ua-shipping'),
					)
				)
			)
		)
	),
	'nova-global' => array(
		'name' => __('Nova Global', 'mrkv-ua-shipping'),
		'description' => __('Nova Global is a logistics company specializing in cross-border delivery solutions for eCommerce businesses and marketplaces. Their services encompass customs clearance and international shipping across 243 countries and territories, facilitating seamless global trade for both businesses and individuals.', 'mrkv-ua-shipping'),
		'api_class' => 'MRKV_UA_SHIPPING_API_NOVA_GLOBAL',
		'invoice_class' => '',
		'settings_class' => 'MRKV_UA_SHIPPING_SETTINGS_NOVA_GLOBAL',
		'pages' => array(),
		'invoice_links' => array(),
		'old_slugs' => array(),
		'old_ttn_slug' => '',
		'method' => array(
			'mrkv_ua_shipping_nova-global' => array(
				'class' => 'MRKV_UA_SHIPPING_NOVA_GLOBAL',
				'slug' => 'mrkv_ua_shipping_nova-global',
				'filename' => 'mrkv-ua-shipping-method-nova-global',
				'checkout_fields' => array(
					'_warehouse' => array(
						'type' => 'select',
						'autocomplete' => 'off',
						'options' => array('' => __('Choose the warehouse', 'mrkv-ua-shipping')),
						'required' => true,
						'label' => __('Warehouse', 'mrkv-ua-shipping'),
						'replace' => '_address_1'
					),
					'_warehouse_ref' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_warehouse_ref',
						'required' => true,
						'label' => __('Warehouse', 'mrkv-ua-shipping'),
					),
					'_city_label' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_city'
					),
					'_address' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_address_2'
					),
					'_area_name' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_state'
					),
					'_zipcode' => array(
						'type' => 'hidden',
						'autocomplete' => 'off',
						'replace' => '_postcode'
					),
				)
			),
			'mrkv_ua_shipping_nova-global_address' => array(
				'class' => 'MRKV_UA_SHIPPING_NOVA_GLOBAL_ADDRESS',
				'slug' => 'mrkv_ua_shipping_nova-global_address',
				'filename' => 'mrkv-ua-shipping-method-nova-global-address',
				'checkout_fields' => array(
					'_postcode' => array(
						'type' => 'text',
						'autocomplete' => 'off',
						'required' => true,
						'label' => __('Postal code', 'mrkv-ua-shipping'),
						'placeholder' => __('Enter the postal code', 'mrkv-ua-shipping'),
						'replace' => '_postcode'
					),
					'_region' => array(
						'type' => 'text',
						'autocomplete' => 'off',
						'required' => true,
						'label' => __('Region', 'mrkv-ua-shipping'),
						'placeholder' => __('Enter the region', 'mrkv-ua-shipping'),
						'replace' => '_state'
					),
					'_city' => array(
						'type' => 'text',
						'autocomplete' => 'off',
						'required' => true,
						'label' => __('City (settlement)', 'mrkv-ua-shipping'),
						'placeholder' => __('Enter the city', 'mrkv-ua-shipping'),
						'replace' => '_city'
					),
					'_street' => array(
						'type' => 'text',
						'autocomplete' => 'off',
						'required' => true,
						'label' => __('Street', 'mrkv-ua-shipping'),
						'placeholder' => __('Enter the street', 'mrkv-ua-shipping'),
						'replace' => '_address_1'
					),
					'_house' => array(
						'type' => 'text',
						'required' => true,
						'label' => __('House', 'mrkv-ua-shipping'),
						'placeholder' => __('Number of house', 'mrkv-ua-shipping'),
						'replace' => '_address_2',
						'autocomplete' => 'off',
					),
					'_flat' => array(
						'type' => 'text',
						'required' => false,
						'label' => __('Flat', 'mrkv-ua-shipping'),
						'placeholder' => __('Number of flat', 'mrkv-ua-shipping'),
						'replace' => '_flat',
						'order_edit' => true,
						'autocomplete' => 'off',
					)
				)
			)
		)
	)
);