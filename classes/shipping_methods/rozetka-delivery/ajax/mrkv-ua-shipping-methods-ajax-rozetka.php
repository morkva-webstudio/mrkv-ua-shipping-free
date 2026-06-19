<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_AJAX_RZTK'))
{
	/**
	 * Class for setup shipping methods ajax rozetka
	 */
	class MRKV_UA_SHIPPING_AJAX_RZTK
	{
		/**
		 * Constructor for plugin shipping methods ajax rozetka
		 * */
		function __construct()
		{
			add_action( 'wp_ajax_mrkv_ua_ship_rozetka_delivery_city', array($this, 'get_rozetka_city') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_rozetka_delivery_city', array($this, 'get_rozetka_city') );

			add_action( 'wp_ajax_mrkv_ua_ship_rozetka_delivery_warehouse', array($this, 'get_rozetka_warehouse') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_rozetka_delivery_warehouse', array($this, 'get_rozetka_warehouse') );
		}

		public function get_rozetka_city()
		{
			if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field( wp_unslash($_POST['nonce'])), 'mrkv_ua_ship_nonce')) {
		        wp_send_json_error(__('Invalid nonce.', 'mrkv-ua-shipping'), 403);
		        wp_die();
		    }

		    require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/rozetka-delivery/api/mrkv-ua-shipping-api-rozetka-delivery.php';
			$mrkv_object_rztk_delivery = new MRKV_UA_SHIPPING_API_ROZETKA_DELIVERY(get_option('rozetka-delivery_m_ua_settings'));

			$key_search = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';

			$page  = 1;
		    $limit = 50;
		    $cities = [];

		    do 
		    {
		        $obj = $mrkv_object_rztk_delivery->send_post_request(
		            'api/city?page=' . $page . '&limit=' . $limit . '&name=' . urlencode($key_search) . '&sort_by_population=desc',
		            'GET'
		        );

		        if (
		            empty($obj['data']) ||
		            !is_array($obj['data'])
		        ) {
		            break;
		        }

		        foreach ($obj['data'] as $entry) {
		            $cities[] = [
		                'value'       => $entry['id'],
		                'label'       => $entry['name'] . ', ' . $entry['district_name'] . ' ' . __('district', 'mrkv-ua-shipping'),
		                'area'        => $entry['region_name'],
		                'area_id'     => $entry['region_id'],
		                'city_label'  => $entry['name'],
		                'district'    => $entry['district_name'] . ' ' . __('district', 'mrkv-ua-shipping'),
		                'district_id' => $entry['district_id'],
		            ];
		        }

		        $total_pages = isset($obj['pagination']['page_count'])
		            ? (int) $obj['pagination']['page_count']
		            : 1;

		        $page++;

		    } while ($page <= $total_pages);

	        echo wp_json_encode($cities);
    		wp_die();
		}

		public function get_rozetka_warehouse()
		{
			if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field( wp_unslash($_POST['nonce'])), 'mrkv_ua_ship_nonce')) {
		        wp_send_json_error(__('Invalid nonce.', 'mrkv-ua-shipping'), 403);
		        wp_die();
		    }

			require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/rozetka-delivery/api/mrkv-ua-shipping-api-rozetka-delivery.php';
			$mrkv_object_rztk_delivery = new MRKV_UA_SHIPPING_API_ROZETKA_DELIVERY(get_option('rozetka-delivery_m_ua_settings'));

			$city_ref = isset($_POST['ref']) ? sanitize_text_field(wp_unslash($_POST['ref'])) : '';

			$all_departments = [];
			$page = 1;
			$limit = 100;
			$has_more = true;

			do {
			    $endpoint = 'api/department?page=' . $page . '&limit=' . $limit . '&city_id=' . $city_ref . '';
			    $response = $mrkv_object_rztk_delivery->send_post_request($endpoint, 'GET');

			    if (isset($response['data']) && !empty($response['data'])) 
			    {
			        $all_departments = array_merge($all_departments, $response['data']);

			        $total_pages = isset($response['pagination']['page_count']) ? (int)$response['pagination']['page_count'] : 1;
			        $page++;

			        if ($page > $total_pages) {
			            $has_more = false;
			        }
			    } else {
			        $has_more = false;
			    }

			} while ($has_more);

			# Send request
	        $obj = $mrkv_object_rztk_delivery->send_post_request('api/department?page=1&limit=100&city_id=' . $city_ref . '', 'GET');

	        if(!empty($all_departments))
       		{
       			$warehouse = array();

       			foreach($all_departments as $entry)
       			{
       				$warehouse[] = array(
	        			'value' => $entry['name'],
	        			'label' => $entry['name'],
	        		);
       			}

       			# Return object
	        	echo wp_json_encode($warehouse);
       		}
       		else
       		{
       			echo wp_json_encode(array());
       		}

       		wp_die();
		}
	}
}