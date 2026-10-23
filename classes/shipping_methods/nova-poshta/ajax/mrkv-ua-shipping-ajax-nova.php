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
		# Nova Poshta warehouse types (getWarehouseTypes)
		const POSHTOMAT_TYPE = 'f9316480-5f2d-425d-bc2c-ac7cd29decf0';
		const BRANCH_TYPES = array('841339c7-591a-42e2-8233-7a0a00f0ed6f', '9a68df70-0267-42a8-bb5c-37f427e36ee4');

		# Every warehouse takes a parcel up to this weight (the lowest limit Nova Poshta has), so no filtering below it
		const WEIGHT_FILTER_FROM_KG = 5;
		# Poshtomat limit: TotalMaxWeightAllowed of practically every poshtomat
		const POSHTOMAT_MAX_KG = 20;

		# The city's branch list is refreshed once a day (Nova Poshta's advice), a stale copy is kept a week
		const BRANCHES_FRESH = DAY_IN_SECONDS;
		const BRANCHES_KEEP = 7 * DAY_IN_SECONDS;

		/**
		 * Constructor for plugin shipping methods ajax nova poshta
		 * */
		function __construct()
		{
			add_action( 'mrkv_ua_shipping_np_refresh_city_branches', array($this, 'refresh_city_branches') );

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

			add_action( 'wp_ajax_mrkv_ua_ship_nova_poshta_connect', array($this, 'get_sender_counterparties') );
			add_action( 'wp_ajax_mrkv_ua_ship_nova_poshta_contact_persons', array($this, 'get_sender_contact_persons') );

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
			$quote_symbols = ['‘', '’', '“', '”', '`', '´', '"', '‹', '›', '«', '»', 'ʼ', 'ʹ', 'ʺ', 'ʽ', 'ʻ', 'ʿ', 'ˈ', 'ˊ', 'ˋ', 'ˌ', 'ˍ', 'ˎ', 'ˏ', 'ˑ', '˒', '˓', '˘', '˙', '˚', '˜', '˝'];
			$key_search = str_replace($quote_symbols, "'", $key_search);

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


			# While the Nova Poshta API is known to be down go straight to the proxy instead of waiting for it again.
			$api_skipped = MRKV_UA_SHIPPING_API_NOVA_POSHTA::is_api_down();
			$obj = $api_skipped ? array() : $mrkv_object_nova_poshta->send_post_request($mrkv_ua_shipping_args, MRKV_UA_SHIPPING_API_NOVA_POSHTA::LOOKUP_TIMEOUT);

			if (is_array($obj) && isset($obj['success']) && !$obj['success'] && false !== stripos(implode(' ', (array) ($obj['errors'] ?? array())), 'many requests')) {
				usleep(400000);
				$obj = $mrkv_object_nova_poshta->send_post_request($mrkv_ua_shipping_args, MRKV_UA_SHIPPING_API_NOVA_POSHTA::LOOKUP_TIMEOUT);
			}

			$failed = !is_array($obj) || !isset($obj['data']) || (isset($obj['success']) && !$obj['success']);

			if (!is_array($obj)) {
				$obj = array();
			}

			if ($mrkv_object_nova_poshta->active_api === true && $failed && !$api_skipped) {
				delete_transient('mrkv_np_key_ok_' . md5($mrkv_object_nova_poshta->get_api_key()));
			}

			if ($mrkv_object_nova_poshta->active_api !== true || $failed) {
				if (!isset($obj['data']) || !isset($obj['data'][0]['Addresses'][0])) {
					$response = wp_remote_get(apply_filters('mrkv_ua_shipping_np_proxy_url', 'https://np.morkva.co.ua/api.php'), $this->proxy_request_args([
						'query_type' => 'city',
						'query_text' => $key_search,
					]));

					if (!is_wp_error($response)) {
						$city_array = json_decode(wp_remote_retrieve_body($response), true);
						$obj['data'][0]['Addresses'] = $city_array;
						$failed = !is_array($city_array);
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

			if (empty($areas)) {
				if ($failed) {
					wp_send_json_error(array('message' => __('Nova Poshta is not available, try again.', 'mrkv-ua-shipping')), 502);
				}
			}
			else {
				set_transient($transient_key, $areas, DAY_IN_SECONDS);
			}

			echo wp_json_encode($areas);
			wp_die();
		}

		/**
		 * Get Nova poshta Warehouse
		 * */
		public function get_nova_poshta_warehouse()
		{
			if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'mrkv_ua_ship_nonce')) {
				wp_send_json_error(__('Invalid nonce.', 'mrkv-ua-shipping'), 403);
			}

			$city_ref         = isset($_POST['ref']) ? sanitize_text_field(wp_unslash($_POST['ref'])) : '';
			$key_search       = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
			$quote_symbols = ['‘', '’', '“', '”', '`', '´', '"', '‹', '›', '«', '»', 'ʼ', 'ʹ', 'ʺ', 'ʽ', 'ʻ', 'ʿ', 'ˈ', 'ˊ', 'ˋ', 'ˌ', 'ˍ', 'ˎ', 'ˏ', 'ˑ', '˒', '˓', '˘', '˙', '˚', '˜', '˝'];
			$key_search = str_replace($quote_symbols, "'", $key_search);
			$warehouse_type   = isset($_POST['warehouse_type']) ? sanitize_text_field(wp_unslash($_POST['warehouse_type'])) : '';
			$source_query     = isset($_POST['source_query']) ? sanitize_text_field(wp_unslash($_POST['source_query'])) : '';
			$search_by        = isset($_POST['search_by']) ? sanitize_text_field(wp_unslash($_POST['search_by'])) : '';
			$default_type     = isset($_POST['default_content']) ? sanitize_text_field(wp_unslash($_POST['default_content'])) : '';
			$page  = isset($_POST['page']) ? absint($_POST['page']) : 1;
			$limit = 20; 
			$cart_weight = 0;
			if ($source_query === 'front' && function_exists('WC') && WC()->cart) {
				$volume_weight = 0;
				$dimension_unit = get_option( 'woocommerce_dimension_unit' );

				foreach (WC()->cart->get_cart() as $cart_item) {
					$p = $cart_item['data'];
					$length = wc_get_dimension((float) $p->get_length(), 'cm', $dimension_unit);
					$width  = wc_get_dimension((float) $p->get_width(), 'cm', $dimension_unit);
					$height = wc_get_dimension((float) $p->get_height(), 'cm', $dimension_unit);
					$volume_weight += (($length * $width * $height) / 4000) * $cart_item['quantity'];
				}

				$actual_weight_kg = wc_get_weight(WC()->cart->get_cart_contents_weight(), 'kg');
				$cart_weight = max($actual_weight_kg, $volume_weight);
			}

			$is_poshtomat_field = $warehouse_type && $warehouse_type != 'none';
			$label = $is_poshtomat_field ? __('Choose the poshtomat', 'mrkv-ua-shipping') : 
						__('Choose the warehouse', 'mrkv-ua-shipping');

			$placeholder_item = array('value' => '', 'label' => $label, 'number' => '', 'zipcode' => '');

			# A parcel over the poshtomat limit fits no poshtomat: say so instead of listing them
			if ($is_poshtomat_field && $cart_weight > self::POSHTOMAT_MAX_KG) {
				/* translators: 1: order weight, 2: poshtomat weight limit, both in kg */
				$placeholder_item['label'] = sprintf(__('Order weight %1$s kg exceeds the poshtomat limit of %2$s kg, choose another delivery method', 'mrkv-ua-shipping'), round($cart_weight, 2), self::POSHTOMAT_MAX_KG);
				echo wp_json_encode($page === 1 ? array($placeholder_item) : array());
				wp_die();
			}

			# "Show only branches" of the chosen method, or a parcel too heavy for a poshtomat
			$branches_only = !$is_poshtomat_field && ($warehouse_type == 'none' || $cart_weight > self::POSHTOMAT_MAX_KG || $this->chosen_method_excludes_poshtomat());
			$filter_weight = $cart_weight > self::WEIGHT_FILTER_FROM_KG ? $cart_weight : 0;

			$settings_method = get_option('nova-poshta_m_ua_settings');
			$mrkv_object_nova_poshta = new MRKV_UA_SHIPPING_API_NOVA_POSHTA($settings_method);

			# Nova Poshta filters neither by weight nor "all types but one": such a list is built here
			# from the city's branches (and the poshtomats after them), in full pages of $limit
			$filtered_failed = false;
			if (!$is_poshtomat_field && ($branches_only || $filter_weight)) {
				$areas = $this->get_filtered_warehouse_page($mrkv_object_nova_poshta, $city_ref, $key_search, $page, $limit, $filter_weight, $branches_only);

				if (null !== $areas) {
					if ($page === 1 && !empty($areas)) {
						array_unshift($areas, $placeholder_item);
					}
					echo wp_json_encode($areas);
					wp_die();
				}
				# Nova Poshta failed: straight to the proxy below, it gets the same filters
				$filtered_failed = true;
			}

			$mrkv_ua_shipping_args = array(
				'apiKey' => $mrkv_object_nova_poshta->get_api_key(),
				'modelName' => 'AddressGeneral',
				'calledMethod' => 'getWarehouses',
				'methodProperties' => array(
					'CityRef' => $city_ref,
					'Limit' => (string) $limit,
					'Page' => (string) $page,
					'FindByString' => '%' . $key_search . '%',
				)
			);

			if ($mrkv_object_nova_poshta->active_api !== true) {
				$mrkv_ua_shipping_args['modelName'] = 'Address';
				unset($mrkv_ua_shipping_args['apiKey']);
			}

			if ($is_poshtomat_field) {
				$mrkv_ua_shipping_args['methodProperties']['TypeOfWarehouseRef'] = $warehouse_type;
			}

			# Only the list opened without a search text is cached: every shopper of the city sees it,
			# while a typed text rarely repeats and would only fill the cache
			# (not the proxy's filtered answer: it would land under the key of the unfiltered list)
			$use_cache = '' === trim($key_search) && !$filtered_failed;
			$cache_properties = $mrkv_ua_shipping_args['methodProperties'];
			unset($cache_properties['FindByString']);
			$transient_key = 'mrkv_np_wh_' . md5(wp_json_encode($cache_properties));
			$areas = $use_cache ? get_transient($transient_key) : false;
			$failed = false;

			if (false === $areas) {
				# While the Nova Poshta API is known to be down go straight to the proxy instead of waiting for it again.
				$api_skipped = $filtered_failed || MRKV_UA_SHIPPING_API_NOVA_POSHTA::is_api_down();
				$obj = $api_skipped ? array() : $mrkv_object_nova_poshta->send_post_request($mrkv_ua_shipping_args, MRKV_UA_SHIPPING_API_NOVA_POSHTA::LOOKUP_TIMEOUT);

				if (is_array($obj) && isset($obj['success']) && !$obj['success'] && false !== stripos(implode(' ', (array) ($obj['errors'] ?? array())), 'many requests')) {
					usleep(400000);
					$obj = $mrkv_object_nova_poshta->send_post_request($mrkv_ua_shipping_args, MRKV_UA_SHIPPING_API_NOVA_POSHTA::LOOKUP_TIMEOUT);
				}

				$failed = !is_array($obj) || !isset($obj['data']) || (isset($obj['success']) && !$obj['success']);

				if (!is_array($obj)) {
					$obj = array();
				}

				if ($mrkv_object_nova_poshta->active_api === true && $failed && !$api_skipped) {
					delete_transient('mrkv_np_key_ok_' . md5($mrkv_object_nova_poshta->get_api_key()));
				}

				if ($mrkv_object_nova_poshta->active_api !== true || $failed) {
					if (!isset($obj['data']) || !isset($obj['data'][0])) {
						# the proxy filters and paginates like getWarehouses
						$proxy_query = [
							'query_type' => 'warehouse_poshtomat',
							'city_ref' => $city_ref,
							'page' => $page,
							'limit' => $limit,
							'find' => $key_search,
							'type' => $mrkv_ua_shipping_args['methodProperties']['TypeOfWarehouseRef'] ?? '',
						];
						# Filters getWarehouses does not have: a proxy that does not know them ignores them
						if ($filter_weight) {
							$proxy_query['weight'] = round($filter_weight, 2);
						}
						if ($branches_only) {
							$proxy_query['exclude_type'] = self::POSHTOMAT_TYPE;
						}
						$response = wp_remote_get(apply_filters('mrkv_ua_shipping_np_proxy_url', 'https://np.morkva.co.ua/api.php'), $this->proxy_request_args($proxy_query));

						if (!is_wp_error($response)) {
							# Already filtered and cut to the page by the proxy
							$proxy_rows = json_decode(wp_remote_retrieve_body($response), true);
							$obj['data'] = $proxy_rows;
							$failed = !is_array($proxy_rows);
						}
					}
				}

				$areas = array();
				if (isset($obj['data'][0])) {
					foreach ($obj['data'] as $area) {
						$areas[] = array(
							'value' => $area['Ref'],
							'label' => $area['Description'],
							'number' => $area['Number'],
							'zipcode' => $area['PostalCodeUA']
						);
					}
				}

				if ($use_cache && !empty($areas)) {
					set_transient($transient_key, $areas, apply_filters('mrkv_ua_shipping_np_warehouses_cache_ttl', 6 * HOUR_IN_SECONDS));
				}
			}

			if ($page === 1 && !empty($areas)) {
				array_unshift($areas, $placeholder_item);
			}

			if (empty($areas)) {
				if ($failed) {
					wp_send_json_error(array('message' => __('Nova Poshta is not available, try again.', 'mrkv-ua-shipping')), 502);
				}
			}

			echo wp_json_encode($areas);
			wp_die();
		}

		/**
		 * One page of the warehouse field when Nova Poshta cannot filter it: the city's branches that
		 * take the parcel, then (unless $branches_only) the poshtomats, cut into pages of $limit.
		 *
		 * @return array|null Page rows, null when Nova Poshta failed
		 */
		private function get_filtered_warehouse_page($api, $city_ref, $search, $page, $limit, $weight, $branches_only)
		{
			$branches = $this->get_city_branches($api, $city_ref);
			if (null === $branches) {
				return null;
			}

			$fitting = array();
			foreach ($branches as $branch) {
				if (self::warehouse_fits($branch['total'], $branch['place'], $weight, $weight) && ('' === trim($search) || false !== mb_stripos($branch['label'], trim($search)))) {
					$fitting[] = array('value' => $branch['value'], 'label' => $branch['label'], 'number' => $branch['number'], 'zipcode' => $branch['zipcode']);
				}
			}

			$offset = ($page - 1) * $limit;
			$rows = array_slice($fitting, $offset, $limit);

			if ($branches_only || count($rows) >= $limit) {
				return $rows;
			}

			# The poshtomats follow the branches: rows [$from, $from + $need) of their list, at most two of its pages
			$from = max(0, $offset - count($fitting));
			$need = $limit - count($rows);
			$first_page = intdiv($from, $limit) + 1;
			$last_page = intdiv($from + $need - 1, $limit) + 1;
			$poshtomats = array();

			for ($p = $first_page; $p <= $last_page; $p++) {
				$page_rows = $this->get_poshtomat_page($api, $city_ref, $search, $p, $limit);
				if (null === $page_rows) {
					return null;
				}
				foreach ($page_rows as $row) {
					if (self::warehouse_fits($row['total'], $row['place'], $weight, $weight)) {
						unset($row['total'], $row['place']);
						$poshtomats[] = $row;
					}
				}
				if (count($page_rows) < $limit) {
					break;
				}
			}

			return array_merge($rows, array_slice($poshtomats, $from - ($first_page - 1) * $limit, $need));
		}

		/**
		 * One page of the city's poshtomats with their weight limits
		 *
		 * @return array|null
		 */
		private function get_poshtomat_page($api, $city_ref, $search, $page, $limit)
		{
			# Own cache key: the poshtomat field caches the same request without the weight limits
			$use_cache = '' === trim($search);
			$transient_key = 'mrkv_np_pm_' . md5($city_ref . '|' . $page . '|' . $limit);

			$rows = $use_cache ? get_transient($transient_key) : false;
			if (is_array($rows)) {
				return $rows;
			}

			$data = $this->request_warehouses($api, array(
				'CityRef' => $city_ref,
				'Limit' => (string) $limit,
				'Page' => (string) $page,
				'FindByString' => '%' . $search . '%',
				'TypeOfWarehouseRef' => self::POSHTOMAT_TYPE,
			));
			if (null === $data) {
				return null;
			}

			$rows = array_map(array($this, 'warehouse_row'), $data);
			if ($use_cache && !empty($rows)) {
				set_transient($transient_key, $rows, apply_filters('mrkv_ua_shipping_np_warehouses_cache_ttl', 6 * HOUR_IN_SECONDS));
			}

			return $rows;
		}

		/**
		 * The city's branches (every type but poshtomats) with their weight limits, sorted like Nova Poshta does.
		 * A copy older than a day is still answered while a cron event refreshes it.
		 *
		 * @return array|null
		 */
		private function get_city_branches($api, $city_ref)
		{
			$cached = get_transient('mrkv_np_br_' . md5($city_ref));

			if (is_array($cached) && isset($cached['rows'], $cached['time'])) {
				if (time() - $cached['time'] > self::BRANCHES_FRESH && !wp_next_scheduled('mrkv_ua_shipping_np_refresh_city_branches', array($city_ref))) {
					wp_schedule_single_event(time(), 'mrkv_ua_shipping_np_refresh_city_branches', array($city_ref));
				}
				return $cached['rows'];
			}

			return $this->refresh_city_branches($city_ref, $api);
		}

		/**
		 * Load the city's branch list from Nova Poshta into the cache (also the cron event of get_city_branches)
		 *
		 * @return array|null
		 */
		public function refresh_city_branches($city_ref, $api = null)
		{
			if (!$api) {
				$api = new MRKV_UA_SHIPPING_API_NOVA_POSHTA(get_option('nova-poshta_m_ua_settings'));
			}

			# First page of each type, then all the remaining pages at once: Kyiv's ~2 MB come in two rounds
			$page_size = 200;
			$first = array();
			foreach (self::BRANCH_TYPES as $type) {
				$first[$type] = array('CityRef' => $city_ref, 'TypeOfWarehouseRef' => $type, 'Limit' => (string) $page_size, 'Page' => '1');
			}
			$answers = $this->request_warehouses_multi($api, $first);
			if (null === $answers) {
				return null;
			}

			$rest = array();
			foreach ($answers as $type => $answer) {
				$total = isset($answer['info']['totalCount']) ? (int) $answer['info']['totalCount'] : 0;
				# Without a total keep asking while the pages are full
				$pages = $total ? (int) ceil($total / $page_size) : (count($answer['data']) < $page_size ? 1 : 2);
				for ($page = 2; $page <= $pages; $page++) {
					$rest[$type . '|' . $page] = array('CityRef' => $city_ref, 'TypeOfWarehouseRef' => $type, 'Limit' => (string) $page_size, 'Page' => (string) $page);
				}
			}
			while ($rest) {
				$more = $this->request_warehouses_multi($api, $rest);
				if (null === $more) {
					return null;
				}
				$answers = array_merge($answers, $more);
				$next = array();
				foreach ($more as $key => $answer) {
					list($type, $page) = explode('|', $key);
					if (!isset($answer['info']['totalCount']) && count($answer['data']) >= $page_size) {
						$next[$type . '|' . ($page + 1)] = array('CityRef' => $city_ref, 'TypeOfWarehouseRef' => $type, 'Limit' => (string) $page_size, 'Page' => (string) ($page + 1));
					}
				}
				$rest = $next;
			}

			$rows = array();
			foreach ($answers as $answer) {
				$rows = array_merge($rows, array_map(array($this, 'warehouse_row'), $answer['data']));
			}

			usort($rows, function ($a, $b) {
				return (int) $a['number'] <=> (int) $b['number'];
			});

			set_transient('mrkv_np_br_' . md5($city_ref), array('time' => time(), 'rows' => $rows), self::BRANCHES_KEEP);

			return $rows;
		}

		/**
		 * getWarehouses with the key when it works, keyless otherwise
		 *
		 * @return array|null Rows, null on failure
		 */
		private function request_warehouses($api, $properties, $timeout = null)
		{
			$answer = $this->request_warehouses_answer($api, $properties, $timeout);

			return null === $answer ? null : $answer['data'];
		}

		/**
		 * getWarehouses answer (data + info), retried once when Nova Poshta asks to slow down
		 *
		 * @return array|null
		 */
		private function request_warehouses_answer($api, $properties, $timeout = null)
		{
			if (MRKV_UA_SHIPPING_API_NOVA_POSHTA::is_api_down()) {
				return null;
			}

			$args = $this->warehouses_args($api, $properties);
			$obj = $api->send_post_request($args, $timeout ?: MRKV_UA_SHIPPING_API_NOVA_POSHTA::LOOKUP_TIMEOUT);

			if (is_array($obj) && isset($obj['success']) && !$obj['success'] && false !== stripos(implode(' ', (array) ($obj['errors'] ?? array())), 'many requests')) {
				usleep(400000);
				$obj = $api->send_post_request($args, $timeout ?: MRKV_UA_SHIPPING_API_NOVA_POSHTA::LOOKUP_TIMEOUT);
			}

			return self::warehouses_answer($obj);
		}

		/**
		 * Several getWarehouses at once: in parallel when WordPress' Requests library is there, one by one otherwise.
		 * A request that failed in parallel is tried once more on its own.
		 *
		 * @return array|null Answers under the keys of $properties_list, null when one of them failed
		 */
		private function request_warehouses_multi($api, $properties_list)
		{
			$timeout = 15;
			$answers = array();
			$requests_class = class_exists('\WpOrg\Requests\Requests') ? '\WpOrg\Requests\Requests' : (class_exists('\Requests') ? '\Requests' : '');

			if ($requests_class && count($properties_list) > 1 && !MRKV_UA_SHIPPING_API_NOVA_POSHTA::is_api_down()) {
				$requests = array();
				foreach ($properties_list as $key => $properties) {
					$requests[$key] = array(
						'url' => 'https://api.novaposhta.ua/v2.0/json/',
						'type' => 'POST',
						'headers' => array('Content-Type' => 'application/json'),
						'data' => wp_json_encode($this->warehouses_args($api, $properties)),
					);
				}

				try {
					$responses = $requests_class::request_multiple($requests, array('timeout' => $timeout, 'connect_timeout' => 5));
				} catch (\Exception $e) {
					$responses = array();
				}

				foreach ($responses as $key => $response) {
					if (is_object($response) && !empty($response->success)) {
						$answer = self::warehouses_answer(json_decode($response->body, true));
						if (null !== $answer) {
							$answers[$key] = $answer;
						}
					}
				}
			}

			foreach ($properties_list as $key => $properties) {
				if (!isset($answers[$key])) {
					$answer = $this->request_warehouses_answer($api, $properties, $timeout);
					if (null === $answer) {
						return null;
					}
					$answers[$key] = $answer;
				}
			}

			return $answers;
		}

		/**
		 * getWarehouses request with the key when it works, keyless otherwise
		 */
		private function warehouses_args($api, $properties)
		{
			$args = array(
				'apiKey' => $api->get_api_key(),
				'modelName' => 'AddressGeneral',
				'calledMethod' => 'getWarehouses',
				'methodProperties' => $properties,
			);

			if ($api->active_api !== true) {
				$args['modelName'] = 'Address';
				unset($args['apiKey']);
			}

			return $args;
		}

		/**
		 * A usable getWarehouses answer, or null
		 */
		private static function warehouses_answer($obj)
		{
			if (!is_array($obj) || !isset($obj['data']) || !is_array($obj['data']) || (isset($obj['success']) && !$obj['success'])) {
				return null;
			}

			return $obj;
		}

		/**
		 * Warehouse field row with the weight limits
		 */
		private function warehouse_row($warehouse)
		{
			return array(
				'value' => $warehouse['Ref'],
				'label' => $warehouse['Description'],
				'number' => $warehouse['Number'],
				'zipcode' => $warehouse['PostalCodeUA'] ?? '',
				'total' => (float) ($warehouse['TotalMaxWeightAllowed'] ?? 0),
				'place' => (float) ($warehouse['PlaceMaxWeightAllowed'] ?? 0),
			);
		}

		/**
		 * Nova Poshta's rule: a warehouse takes the parcel when both limits allow it, a limit of 0 means none
		 */
		public static function warehouse_fits($total_limit, $place_limit, $total_weight, $place_weight)
		{
			return ((float) $total_limit == 0 || (float) $total_limit >= $total_weight)
				&& ((float) $place_limit == 0 || (float) $place_limit >= $place_weight);
		}

		/**
		 * "Show only branches" of the Nova Poshta warehouse method the customer has chosen
		 */
		private function chosen_method_excludes_poshtomat()
		{
			if (!function_exists('WC') || !isset(WC()->session) || !WC()->session) {
				return false;
			}

			foreach ((array) WC()->session->get('chosen_shipping_methods') as $chosen_method) {
				$parts = explode(':', (string) $chosen_method);
				if ($parts[0] === 'mrkv_ua_shipping_nova-poshta' && isset($parts[1])) {
					$settings = get_option('woocommerce_mrkv_ua_shipping_nova-poshta_' . absint($parts[1]) . '_settings');
					return isset($settings['exclude_poshtomat']) && $settings['exclude_poshtomat'] === 'yes';
				}
			}

			return false;
		}

		/**
		 * Arguments of a request to the np.morkva.co.ua proxy: tells it which plugin and version asks
		 */
		private function proxy_request_args($query)
		{
			$version = defined('MRKV_UA_SHIPPING_PLUGIN_VERSION') ? MRKV_UA_SHIPPING_PLUGIN_VERSION : '';
			$edition = defined('MRKV_UA_SHIPPING_EDITION') ? MRKV_UA_SHIPPING_EDITION : '';

			return array(
				'timeout' => 10,
				'headers' => array('X-Mrkv-Client' => 'mrkv-ua-shipping/' . $version . ' (' . $edition . ')'),
				'body' => $query,
			);
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

				array_unshift($areas, array(
					'value' => '',
					'label' => __('Choose the street', 'mrkv-ua-shipping')
				));

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

			$mrkv_object_nova_poshta = new MRKV_UA_SHIPPING_API_NOVA_POSHTA(get_option('nova-poshta_m_ua_settings'));

			$key_search = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
			$quote_symbols = ['‘', '’', '“', '”', '`', '´', '"', '‹', '›', '«', '»', 'ʼ', 'ʹ', 'ʺ', 'ʽ', 'ʻ', 'ʿ', 'ˈ', 'ˊ', 'ˋ', 'ˌ', 'ˍ', 'ˎ', 'ˏ', 'ˑ', '˒', '˓', '˘', '˙', '˚', '˜', '˝'];
			$key_search = str_replace($quote_symbols, "'", $key_search);
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
	     * Settings page, step 1: check the entered (maybe not saved yet) API key and load its sender counterparties
	     * */
	    public function get_sender_counterparties()
	    {
	    	$mrkv_sender_object_nova_poshta = $this->get_settings_sender_object();
	    	$force = !empty($_POST['force']);

	    	$counterparties = $mrkv_sender_object_nova_poshta->get_sender_counterparties($force);

	    	if(empty($counterparties))
	    	{
	    		wp_send_json_error($mrkv_sender_object_nova_poshta->get_last_error());
	    	}

	    	wp_send_json_success(array('counterparties' => $counterparties));
	    }

	    /**
	     * Settings page, step 2: contact persons of the chosen sender counterparty
	     * */
	    public function get_sender_contact_persons()
	    {
	    	$mrkv_sender_object_nova_poshta = $this->get_settings_sender_object();
	    	$force = !empty($_POST['force']);
	    	$counterparty_ref = isset($_POST['counterparty_ref']) ? sanitize_text_field(wp_unslash($_POST['counterparty_ref'])) : '';

	    	# Only a counterparty of this key: Nova Poshta would answer for any Ref
	    	$counterparties = $mrkv_sender_object_nova_poshta->get_sender_counterparties();

	    	if(!$counterparty_ref || !in_array($counterparty_ref, array_column($counterparties, 'ref'), true))
	    	{
	    		wp_send_json_error(__('Choose a sender', 'mrkv-ua-shipping'));
	    	}

	    	$contacts = $mrkv_sender_object_nova_poshta->get_contact_persons($counterparty_ref, $force);

	    	if(empty($contacts))
	    	{
	    		wp_send_json_error(__('Contact person list empty', 'mrkv-ua-shipping'));
	    	}

	    	wp_send_json_success(array('contacts' => MRKV_UA_SHIPPING_SENDER_NOVA_POSHTA::get_contact_persons_options($contacts, $counterparty_ref)));
	    }

	    /**
	     * Sender API object for the settings page: admins only, with the key from the form
	     * @return MRKV_UA_SHIPPING_SENDER_NOVA_POSHTA
	     * */
	    private function get_settings_sender_object()
	    {
	    	check_ajax_referer('mrkv_ua_ship_nonce', 'nonce');

	    	if(!current_user_can('manage_options'))
	    	{
	    		wp_send_json_error(__('You do not have permission to do this.', 'mrkv-ua-shipping'), 403);
	    	}

	    	$settings = get_option('nova-poshta_m_ua_settings');
	    	$settings = is_array($settings) ? $settings : array();
	    	$settings['api_key'] = isset($_POST['api_key']) ? sanitize_text_field(wp_unslash($_POST['api_key'])) : '';

	    	if(!$settings['api_key'])
	    	{
	    		wp_send_json_error(__('Enter the key...', 'mrkv-ua-shipping'));
	    	}

	    	# The key may be not saved yet: do not let its check mark the saved key broken or fixed
	    	return new MRKV_UA_SHIPPING_SENDER_NOVA_POSHTA(new MRKV_UA_SHIPPING_API_NOVA_POSHTA($settings, false));
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
		    $settings = get_option('nova-poshta_m_ua_settings');
		    $counterparty_ref = '';

		    # Settings page: the address belongs to the chosen counterparty of the entered key. Checkout keeps the saved key and the first counterparty
		    if(current_user_can('manage_options') && !empty($_POST['counterparty_ref']) && !empty($_POST['api_key']))
		    {
		    	$settings = is_array($settings) ? $settings : array();
		    	$settings['api_key'] = sanitize_text_field(wp_unslash($_POST['api_key']));
		    	$counterparty_ref = sanitize_text_field(wp_unslash($_POST['counterparty_ref']));
		    	$mrkv_object_nova_poshta = new MRKV_UA_SHIPPING_API_NOVA_POSHTA($settings, false);
		    }
		    else
		    {
				$mrkv_object_nova_poshta = new MRKV_UA_SHIPPING_API_NOVA_POSHTA($settings);
		    }
			$mrkv_sender_object_nova_poshta = new MRKV_UA_SHIPPING_SENDER_NOVA_POSHTA($mrkv_object_nova_poshta);

			$sender_street_ref = isset($_POST['sender_street_ref']) ? sanitize_text_field(wp_unslash($_POST['sender_street_ref'])) : '';
			$sender_building_number = isset($_POST['sender_building_number']) ? sanitize_text_field(wp_unslash($_POST['sender_building_number'])) : '';
			$sender_flat = isset($_POST['sender_flat']) ? sanitize_text_field(wp_unslash($_POST['sender_flat'])) : '';

	        # Send request
	        $ref = $mrkv_sender_object_nova_poshta->get_sender_address_ref($sender_street_ref, $sender_building_number, $sender_flat, $counterparty_ref);
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