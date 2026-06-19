<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_AJAX_NOVA_GLOBAL'))
{
	/**
	 * Class for setup shipping methods ajax nova global
	 */
	class MRKV_UA_SHIPPING_AJAX_NOVA_GLOBAL
	{
		/**
		 * Constructor for plugin shipping methods ajax nova global
		 * */
		function __construct()
		{
			add_action( 'wp_ajax_mrkv_ua_ship_nova_global_warehouse', array($this, 'get_nova_global_warehouse') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_nova_global_warehouse', array($this, 'get_nova_global_warehouse') );
		}

		public function get_nova_global_warehouse()
		{
			if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field( wp_unslash($_POST['nonce'])), 'mrkv_ua_ship_nonce')) {
		        wp_send_json_error(__('Invalid nonce.', 'mrkv-ua-shipping'), 403);
		        wp_die();
		    }

		    require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-global/api/mrkv-ua-shipping-api-nova-global.php';
			$mrkv_object_nova_global = new MRKV_UA_SHIPPING_API_NOVA_GLOBAL(get_option('nova-global_m_ua_settings'));

			$warehouse_types = isset($_POST['warehouse_types']) && is_array($_POST['warehouse_types']) ? map_deep(wp_unslash($_POST['warehouse_types']), 'sanitize_text_field') : array();
			$method = isset($_POST['method']) ? sanitize_text_field(wp_unslash($_POST['method'])) : '';
			$language = isset($_POST['language']) ? sanitize_text_field(wp_unslash($_POST['language'])) : '';
			$country = isset($_POST['country']) ? sanitize_text_field(wp_unslash($_POST['country'])) : ''; 

			if($language && $country && $warehouse_types && is_array($warehouse_types) && $method && isset($warehouse_types[$method]))
			{
				$type = $warehouse_types[$method]; 

				$mrkv_ua_shipping_args = array(
					'country' => $country,
					'ext' => 0,
					'language' => $language,
					'type_info' => $type,
				);

				# Send request
	        	$obj = $mrkv_object_nova_global->send_post_request( $mrkv_ua_shipping_args, 'Dictionary/getWarehouses' );

	        	if(isset($obj['warehouse_list']) && !empty($obj['warehouse_list']))
	        	{
	        		$warehouses = array();

	        		foreach($obj['warehouse_list'] as $warehouse)
	        		{
	        			$warehouses[] = array(
		        			'value' => $warehouse['reference'],
		        			'label' => '(' . $warehouse['address']['name'] . ') ' . $warehouse['address']['state_province'] . ', ' . $warehouse['address']['address'],
		        			'area' => $warehouse['address']['state_province'],
		        			'city' => $warehouse['address']['city'],
		        			'address' => $warehouse['address']['address'],
		        			'zipcode' => $warehouse['address']['zipcode']
		        		);
	        		}

	        		# Return object
	        		echo wp_json_encode($warehouses);
	        	}
	        	else
	        	{
	        		echo wp_json_encode(array(array(
		        		'value' => 'none',
	        			'label' => __('No results for your request', 'mrkv-ua-shipping')
		        	)));
	        	}
			}

			wp_die();
		}
	}
}