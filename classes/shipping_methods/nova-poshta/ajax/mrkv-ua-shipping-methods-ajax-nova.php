<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_AJAX_NOVA'))
{
	/**
	 * Class for setup shipping methods ajax nova poshta
	 */
	class MRKV_UA_SHIPPING_AJAX_NOVA
	{
		/**
		 * Constructor for plugin shipping methods ajax nova poshta
		 * */
		function __construct()
		{
			add_action( 'wp_ajax_mrkv_ua_ship_nova_poshta_area', array($this, 'get_nova_poshta_area') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_nova_poshta_area', array($this, 'get_nova_poshta_area') );

			add_action( 'wp_ajax_mrkv_ua_ship_nova_poshta_city', array($this, 'get_nova_poshta_city') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_nova_poshta_city', array($this, 'get_nova_poshta_city') );

			add_action( 'wp_ajax_mrkv_ua_ship_nova_poshta_warehouse', array($this, 'get_nova_poshta_warehouse') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_nova_poshta_warehouse', array($this, 'get_nova_poshta_warehouse') );

			add_action( 'wp_ajax_mrkv_ua_ship_nova_poshta_street', array($this, 'get_nova_poshta_street') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_nova_poshta_street', array($this, 'get_nova_poshta_street') );

			add_action( 'wp_ajax_mrkv_ua_ship_nova_poshta_street_default', array($this, 'get_nova_poshta_street_default') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_nova_poshta_street_default', array($this, 'get_nova_poshta_street_default') );

			add_action( 'wp_ajax_mrkv_ua_ship_nova_poshta_sender_get_address_ref', array($this, 'get_sender_get_address_ref') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_nova_poshta_sender_get_address_ref', array($this, 'get_sender_get_address_ref') );

			add_action( 'wp_ajax_mrkv_ua_ship_novapost_divisions', array($this, 'get_mrkv_ua_ship_novapost_divisions') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_novapost_divisions', array($this, 'get_mrkv_ua_ship_novapost_divisions') );
		}

		public function get_mrkv_ua_ship_novapost_divisions()
		{
			if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field( wp_unslash($_POST['nonce'])), 'mrkv_ua_ship_nonce')) {
		        wp_send_json_error(__('Invalid nonce.', 'mrkv-ua-shipping'), 403);
		        wp_die();
		    }

		    $novapost_term_suggestion = isset($_POST['term']) ? sanitize_text_field(wp_unslash($_POST['term'])) : '';
		    $mrkvup_country_suggestion = isset($_POST['mrkvup_country_suggestion']) ? sanitize_text_field(wp_unslash($_POST['mrkvup_country_suggestion'])) : '';

		    require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-poshta/api/mrkv-ua-shipping-api-nova-post.php';
			$mrkv_object_nova_post = new MRKV_UA_SHIPPING_API_NOVA_POST(get_option('nova-poshta_m_ua_settings'));

			$city_body = $mrkv_object_nova_post->send_post_request([], 'divisions?countryCodes[]=' . $mrkvup_country_suggestion . '&limit=100&textSearch=' . $novapost_term_suggestion, 'GET');

			$city_output = array();

			if(isset($city_body['items']))
			{
				foreach($city_body['items'] as $city){

					$label = $city['address'];

					if($mrkvup_country_suggestion == 'UA')
					{
						$label = $city['shortName'];
					}

					$city_output['response'][] = array(
						"label" => $label,
						"value" => $city['id'],
						"number" => $city['number']
					);
				}
			}

			echo wp_json_encode( $city_output );
			wp_die();
		}

		/**
		 * Get Nova poshta Area
		 * */
		public function get_nova_poshta_area()
		{
			if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field( wp_unslash($_POST['nonce'])), 'mrkv_ua_ship_nonce')) {
		        wp_send_json_error(__('Invalid nonce.', 'mrkv-ua-shipping'), 403);
		        wp_die();
		    }

			require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-poshta/api/mrkv-ua-shipping-api-nova-poshta.php';
			$mrkv_object_nova_poshta = new MRKV_UA_SHIPPING_API_NOVA_POSHTA(get_option('nova-poshta_m_ua_settings'));

			$key_search = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';

			$mrkv_ua_shipping_args = array(
	            'apiKey' => $mrkv_object_nova_poshta->get_api_key(),
	            'modelName' => 'AddressGeneral',
	            'calledMethod' => 'getAreas',
            	'methodProperties' => array(
            		'FindByString' => $key_search .'%',
            		'Limit' => '10'
            	)
	        );

	        if ($mrkv_object_nova_poshta->active_api !== true) {
	        	$mrkv_ua_shipping_args['modelName'] = 'Address';
	        	unset($mrkv_ua_shipping_args['apiKey']);
	        }

	        # Send request
	        $obj = $mrkv_object_nova_poshta->send_post_request( $mrkv_ua_shipping_args );

	        if(isset($obj['data'][0]))
	        {
	        	$areas = array();

	        	foreach($obj['data'] as $area)
	        	{
	        		$areas[] = array(
	        			'value' => $area['Ref'],
	        			'label' => $area['Description']
	        		);
	        	}

	        	# Return object
	        	echo wp_json_encode($areas);
	        }
	        else
	        {
	        	echo wp_json_encode(array());
	        }

			wp_die();
		}

		/**
		 * Get Nova poshta City
		 * */
		public function get_nova_poshta_city()
		{
			if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'mrkv_ua_ship_nonce')) {
				wp_send_json_error(__('Invalid nonce.', 'mrkv-ua-shipping'), 403);
			}

			$key_search = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';

			if (mb_strlen($key_search) < 2) {
				echo wp_json_encode(array());
				wp_die();
			}

			$transient_key = 'mrkv_np_city_' . md5($key_search);
			$cached_data = get_transient($transient_key);

			if (false !== $cached_data) {
				echo wp_json_encode($cached_data);
				wp_die();
			}

			require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-poshta/api/mrkv-ua-shipping-api-nova-poshta.php';
			$mrkv_object_nova_poshta = new MRKV_UA_SHIPPING_API_NOVA_POSHTA(get_option('nova-poshta_m_ua_settings'));

			$mrkv_ua_shipping_args = array(
				'apiKey' => $mrkv_object_nova_poshta->get_api_key(),
				'modelName' => 'AddressGeneral',
				'calledMethod' => 'searchSettlements',
				'methodProperties' => array(
					'CityName' => $key_search,
					'Limit' => '50'
				)
			);

			if ($mrkv_object_nova_poshta->active_api !== true) {
				$mrkv_ua_shipping_args['modelName'] = 'Address';
				unset($mrkv_ua_shipping_args['apiKey']);
			}

			$obj = $mrkv_object_nova_poshta->send_post_request($mrkv_ua_shipping_args);

			if ($mrkv_object_nova_poshta->active_api !== true) {
				if (!isset($obj['data']) || !isset($obj['data'][0]['Addresses'][0])) {
					$response = wp_remote_get('https://np.morkva.co.ua/api.php', [
						'timeout' => 10,
						'body' => [
							'query_type' => 'city',
							'query_text' => $key_search,
						]
					]);

					if (!is_wp_error($response)) {
						$city_array = json_decode(wp_remote_retrieve_body($response), true);
						$obj['data'][0]['Addresses'] = $city_array;
					}
				}
			}

			$areas = array();
			if (isset($obj['data'][0]['Addresses'][0])) {
				foreach ($obj['data'][0]['Addresses'] as $area) {
					$areas[] = array(
						'value' => $area['DeliveryCity'] ?? '',
						'label' => $area['Present'] ?? '',
						'area' => $area['Area'] ?? '',
						'label_simple' => $area['MainDescription'] ?? '',
						'zipcode' => $area['Index1'] ?? ''
					);
				}
			}

			set_transient($transient_key, $areas, DAY_IN_SECONDS);

			echo wp_json_encode($areas);
			wp_die();
		}

		/**
		 * Get Nova poshta Warehouse
		 * */
		public function get_nova_poshta_warehouse()
        {
            $start_time = microtime(true);

            if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'mrkv_ua_ship_nonce')) {
                wp_send_json_error(__('Invalid nonce.', 'mrkv-ua-shipping'), 403);
            }

            $city_ref         = isset($_POST['ref']) ? sanitize_text_field(wp_unslash($_POST['ref'])) : '';
            $key_search       = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
            $warehouse_type   = isset($_POST['warehouse_type']) ? sanitize_text_field(wp_unslash($_POST['warehouse_type'])) : '';
            $source_query     = isset($_POST['source_query']) ? sanitize_text_field(wp_unslash($_POST['source_query'])) : '';
            $search_by        = isset($_POST['search_by']) ? sanitize_text_field(wp_unslash($_POST['search_by'])) : '';
            $default_type     = isset($_POST['default_content']) ? sanitize_text_field(wp_unslash($_POST['default_content'])) : '';
            $search_by_number = isset($_POST['search_by_number']) ? sanitize_text_field(wp_unslash($_POST['search_by_number'])) : '';
            $page  = isset($_POST['page']) ? absint($_POST['page']) : 1;
            $limit = 20; 

            $label = ($warehouse_type && $warehouse_type != 'none') ? __('Choose the poshtomat', 'mrkv-ua-shipping') : 
                        (($search_by_number == 'yes') ? __('Please enter warehouse number', 'mrkv-ua-shipping') : __('Choose the warehouse', 'mrkv-ua-shipping'));

            $placeholder_item = array('value' => '', 'label' => $label, 'number' => '', 'zipcode' => '');

            $settings_method = get_option('nova-poshta_m_ua_settings');
            require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-poshta/api/mrkv-ua-shipping-api-nova-poshta.php';
            $mrkv_object_nova_poshta = new MRKV_UA_SHIPPING_API_NOVA_POSHTA($settings_method);

            $mrkv_ua_shipping_args = array(
                'apiKey' => $mrkv_object_nova_poshta->get_api_key(),
                'modelName' => 'AddressGeneral',
                'calledMethod' => 'getWarehouses',
                'methodProperties' => array(
                    'CityRef' => $city_ref,
                    'Limit' => (string) $limit,
                    'Page' => (string) $page,
                    'FindByString' => $search_by ? '' : '%' . $key_search . '%',
                )
            );

            if ($mrkv_object_nova_poshta->active_api !== true) {
                $mrkv_ua_shipping_args['modelName'] = 'Address';
                unset($mrkv_ua_shipping_args['apiKey']);
            }

            $exclude_post = false;
            if ($warehouse_type == 'none') {
                $exclude_post = true;
            } elseif ($warehouse_type) {
                $mrkv_ua_shipping_args['methodProperties']['TypeOfWarehouseRef'] = $warehouse_type;
            }

            $obj = $mrkv_object_nova_poshta->send_post_request($mrkv_ua_shipping_args);

            if ($mrkv_object_nova_poshta->active_api !== true) {
                if (!isset($obj['data']) || !isset($obj['data'][0])) {
                    $response = wp_remote_get('https://np.morkva.co.ua/api.php', [
                        'timeout' => 10,
                        'body' => [
                            'query_type' => 'warehouse_poshtomat',
                            'city_ref' => $city_ref,
                        ]
                    ]);

                    if (!is_wp_error($response)) {
                        $obj['data'] = json_decode(wp_remote_retrieve_body($response), true);
                    }
                }
            }

            $areas = array();
            if (isset($obj['data'][0])) {
                $cart_weight = 0;
                if ($source_query == 'front') {
                    $volume_weight = 0;
                    $dimension_unit = get_option('woocommerce_dimension_unit');
                    foreach (WC()->cart->get_cart() as $cart_item) {
                        $p = $cart_item['data'];
                        $volume_weight += (($p->get_length() ?: 0) * ($p->get_width() ?: 0) * ($p->get_height() ?: 0) / 4000) * $cart_item['quantity'];
                    }
                    $weight_unit = get_option('woocommerce_weight_unit');
                    $weight_coef = array('g' => 0.001, 'kg' => 1, 'lbs' => 0.45359, 'oz' => 0.02834)[$weight_unit] ?? 1;
                    $cart_weight = max((WC()->cart->cart_contents_weight * $weight_coef), $volume_weight);
                }

                foreach ($obj['data'] as $area) {
                    if ($cart_weight > 0) {
                        if (intval($area['TotalMaxWeightAllowed']) > 0 && $cart_weight > $area['TotalMaxWeightAllowed']) continue;
                        if (intval($area['PlaceMaxWeightAllowed']) > 0 && $cart_weight > $area['PlaceMaxWeightAllowed']) continue;
                    }

                    if ($exclude_post && $area['TypeOfWarehouse'] == 'f9316480-5f2d-425d-bc2c-ac7cd29decf0') continue;

                    $areas[] = array(
                        'value' => $area['Ref'],
                        'label' => $area['Description'],
                        'number' => $area['Number'],
                        'zipcode' => $area['PostalCodeUA']
                    );
                }
            }

            if(!$key_search) {
                set_transient($transient_key, $areas, WEEK_IN_SECONDS);
            }

            if ($page === 1 && !empty($areas)) {
                array_unshift($areas, $placeholder_item);
            }

            echo wp_json_encode($areas);
            wp_die();
        }

		/**
		 * Get Nova poshta Street
		 * */
		public function get_nova_poshta_street_default()
		{
			if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field( wp_unslash($_POST['nonce'])), 'mrkv_ua_ship_nonce')) {
		        wp_send_json_error(__('Invalid nonce.', 'mrkv-ua-shipping'), 403);
		        wp_die();
		    }

			require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-poshta/api/mrkv-ua-shipping-api-nova-poshta.php';
			$mrkv_object_nova_poshta = new MRKV_UA_SHIPPING_API_NOVA_POSHTA(get_option('nova-poshta_m_ua_settings'));

			$city_ref = isset($_POST['ref']) ? sanitize_text_field(wp_unslash($_POST['ref'])) : '';

			$mrkv_ua_shipping_args = array(
	            'apiKey' => $mrkv_object_nova_poshta->get_api_key(),
	            'modelName' => 'AddressGeneral',
	            'calledMethod' => 'getStreet',
            	'methodProperties' => array(
            		'FindByString' => '',
            		'CityRef' => $city_ref,
            		'Limit' => '100'
            	)
	        );

	        if ($mrkv_object_nova_poshta->active_api !== true) {
	        	$mrkv_ua_shipping_args['modelName'] = 'Address';
	        	unset($mrkv_ua_shipping_args['apiKey']);
	        }

	        # Send request
	        $obj = $mrkv_object_nova_poshta->send_post_request( $mrkv_ua_shipping_args );

	        if(isset($obj['data'][0]))
	        {
	        	$areas = array();

	        	foreach($obj['data'] as $area)
	        	{
	        		$areas[] = array(
	        			'value' => $area['Ref'],
	        			'label' => $area['StreetsType'] . ' ' . $area['Description']
	        		);
	        	}

	        	# Return object
	        	echo wp_json_encode($areas);
	        }
	        else
	        {
	        	echo wp_json_encode(array());
	        }

			wp_die();
		}

		/**
		 * Get Nova poshta Street
		 * */
		public function get_nova_poshta_street()
		{
			if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field( wp_unslash($_POST['nonce'])), 'mrkv_ua_ship_nonce')) {
		        wp_send_json_error(__('Invalid nonce.', 'mrkv-ua-shipping'), 403);
		        wp_die();
		    }

			require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-poshta/api/mrkv-ua-shipping-api-nova-poshta.php';
			$mrkv_object_nova_poshta = new MRKV_UA_SHIPPING_API_NOVA_POSHTA(get_option('nova-poshta_m_ua_settings'));

			$key_search = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
			$city_ref = isset($_POST['ref']) ? sanitize_text_field(wp_unslash($_POST['ref'])) : '';

			$mrkv_ua_shipping_args = array(
	            'apiKey' => $mrkv_object_nova_poshta->get_api_key(),
	            'modelName' => 'AddressGeneral',
	            'calledMethod' => 'getStreet',
            	'methodProperties' => array(
            		'FindByString' => $key_search .'%',
            		'CityRef' => $city_ref,
            		'Limit' => '10'
            	)
	        );

	        if ($mrkv_object_nova_poshta->active_api !== true) {
	        	$mrkv_ua_shipping_args['modelName'] = 'Address';
	        	unset($mrkv_ua_shipping_args['apiKey']);
	        }

	        # Send request
	        $obj = $mrkv_object_nova_poshta->send_post_request( $mrkv_ua_shipping_args );

	        if(isset($obj['data'][0]))
	        {
	        	$areas = array();

	        	foreach($obj['data'] as $area)
	        	{
	        		$areas[] = array(
	        			'value' => $area['Ref'],
	        			'label' => $area['StreetsType'] . ' ' . $area['Description']
	        		);
	        	}

	        	# Return object
	        	echo wp_json_encode($areas);
	        }
	        else
	        {
	        	echo wp_json_encode(array());
	        }

			wp_die();
		}

		/**
	     * Get Sender Address Ref
	     * */
	    public function get_sender_get_address_ref()
	    {
	    	if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field( wp_unslash($_POST['nonce'])), 'mrkv_ua_ship_nonce')) {
		        wp_send_json_error(__('Invalid nonce.', 'mrkv-ua-shipping'), 403);
		        wp_die();
		    }

	    	require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-poshta/api/mrkv-ua-shipping-api-nova-poshta.php';
			$mrkv_object_nova_poshta = new MRKV_UA_SHIPPING_API_NOVA_POSHTA(get_option('nova-poshta_m_ua_settings'));
			require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-poshta/api/mrkv-ua-shipping-sender-nova-poshta.php';
			$mrkv_sender_object_nova_poshta = new MRKV_UA_SHIPPING_SENDER_NOVA_POSHTA($mrkv_object_nova_poshta);

			$sender_street_ref = isset($_POST['sender_street_ref']) ? sanitize_text_field(wp_unslash($_POST['sender_street_ref'])) : '';
			$sender_building_number = isset($_POST['sender_building_number']) ? sanitize_text_field(wp_unslash($_POST['sender_building_number'])) : '';
			$sender_flat = isset($_POST['sender_flat']) ? sanitize_text_field(wp_unslash($_POST['sender_flat'])) : '';

	        # Send request
	        $ref = $mrkv_sender_object_nova_poshta->get_sender_address_ref($sender_street_ref, $sender_building_number, $sender_flat);
	        $ref = str_replace('"', "", $ref);

	        if($ref)
	        {
	        	# Return object
	        	echo wp_json_encode($ref);
	        }

	        wp_die();
	    }
	}
}