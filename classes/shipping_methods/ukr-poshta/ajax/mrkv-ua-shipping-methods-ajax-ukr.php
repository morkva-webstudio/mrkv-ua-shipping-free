<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_AJAX_UKR'))
{
	/**
	 * Class for setup shipping methods ajax ukr poshta
	 */
	class MRKV_UA_SHIPPING_AJAX_UKR
	{
		/**
		 * Constructor for plugin shipping methods ajax ukr poshta
		 * */
		function __construct()
		{
			add_action( 'admin_post_mrkv_ua_print_ukrposhta_pdf', [$this, 'mrkv_ua_handle_pdf_printing'] );

			add_action( 'wp_ajax_mrkv_ua_ship_ukr_poshta_city', array($this, 'get_ukr_poshta_city') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_ukr_poshta_city', array($this, 'get_ukr_poshta_city') );

			add_action( 'wp_ajax_mrkv_ua_ship_ukr_poshta_warehouse', array($this, 'get_ukr_poshta_warehouse') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_ukr_poshta_warehouse', array($this, 'get_ukr_poshta_warehouse') );

			add_action( 'wp_ajax_mrkv_ua_ship_ukr_poshta_street', array($this, 'get_ukr_poshta_street') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_ukr_poshta_street', array($this, 'get_ukr_poshta_street') );

			add_action( 'wp_ajax_mrkv_ua_ship_ukr_poshta_house', array($this, 'get_ukr_poshta_house') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_ukr_poshta_house', array($this, 'get_ukr_poshta_house') );

			add_action( 'wp_ajax_mrkv_ua_ship_ukr_poshta_warehouse_id', array($this, 'get_warehouse_id') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_ukr_poshta_warehouse_id', array($this, 'get_warehouse_id') );

			add_action( 'wp_ajax_mrkv_ua_ship_ukr_poshta_address_id', array($this, 'get_address_id') );
			add_action( 'wp_ajax_nopriv_mrkv_ua_ship_ukr_poshta_address_id', array($this, 'get_address_id') );
		}

		public function get_ukr_poshta_city()
		{
			if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field( wp_unslash($_POST['nonce'])), 'mrkv_ua_ship_nonce')) {
		        wp_send_json_error(__('Invalid nonce.', 'mrkv-ua-shipping'), 403);
		        wp_die();
		    }

			require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/ukr-poshta/api/mrkv-ua-shipping-api-ukr-poshta.php';
			$mrkv_object_ukr_poshta = new MRKV_UA_SHIPPING_API_UKR_POSHTA(get_option('ukr-poshta_m_ua_settings'));

			$key_search = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';

	        # Send request
	        $obj = $mrkv_object_ukr_poshta->send_post_request('address-classifier-ws/get_city_by_region_id_and_district_id_and_city_ua?region_id=&district_id=&city_ua=' . $key_search . '&fuzzy=1', 'GET');

	        if(isset($obj['Entries']['Entry']) && is_array($obj['Entries']['Entry']) && !empty($obj['Entries']['Entry']))
       		{
       			$cities = array();

       			foreach($obj['Entries']['Entry'] as $entry)
       			{
       				$cities[] = array(
	        			'value' => $entry['CITY_ID'],
	        			'label' => $entry['CITY_UA'] . ', ' . $entry['DISTRICT_UA'] . ' ' . __('district', 'mrkv-ua-shipping'),
	        			'area' => $entry['REGION_UA'],
	        			'area_id' => $entry['REGION_ID'],
	        			'district_id' => $entry['DISTRICT_ID'],
	        		);
       			}

       			# Return object
	        	echo wp_json_encode($cities);
       		}
       		else
       		{
       			echo wp_json_encode(array());
       		}

			wp_die();
		}

		public function get_ukr_poshta_warehouse()
		{
			if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field( wp_unslash($_POST['nonce'])), 'mrkv_ua_ship_nonce')) {
		        wp_send_json_error(__('Invalid nonce.', 'mrkv-ua-shipping'), 403);
		        wp_die();
		    }

			require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/ukr-poshta/api/mrkv-ua-shipping-api-ukr-poshta.php';
			$mrkv_object_ukr_poshta = new MRKV_UA_SHIPPING_API_UKR_POSHTA(get_option('ukr-poshta_m_ua_settings'));

			$city_ref = isset($_POST['ref']) ? sanitize_text_field(wp_unslash($_POST['ref'])) : '';

			# Send request
	        $obj = $mrkv_object_ukr_poshta->send_post_request('address-classifier-ws/get_postoffices_by_postcode_cityid_cityvpzid?city_id=' . $city_ref, 'GET');

	        if(isset($obj['Entries']['Entry']) && is_array($obj['Entries']['Entry']) && !empty($obj['Entries']['Entry']))
       		{
       			$warehouse = array();

       			$warehouse[] = array(
        			'value' => '',
        			'label' => __('Choose the warehouse', 'mrkv-ua-shipping'),
        		);

       			foreach($obj['Entries']['Entry'] as $entry)
       			{
       				$warehouse[] = array(
	        			'value' => $entry['POSTCODE'],
	        			'label' => $entry['POSTOFFICE_UA'] . ', ' . $entry['STREET_UA_VPZ'],
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

		public function get_ukr_poshta_street()
		{
			if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field( wp_unslash($_POST['nonce'])), 'mrkv_ua_ship_nonce')) {
		        wp_send_json_error(__('Invalid nonce.', 'mrkv-ua-shipping'), 403);
		        wp_die();
		    }

			require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/ukr-poshta/api/mrkv-ua-shipping-api-ukr-poshta.php';
			$mrkv_object_ukr_poshta = new MRKV_UA_SHIPPING_API_UKR_POSHTA(get_option('ukr-poshta_m_ua_settings'));

			$city_ref = isset($_POST['ref']) ? sanitize_text_field(wp_unslash($_POST['ref'])) : '';

			# Send request
	        $obj = $mrkv_object_ukr_poshta->send_post_request('address-classifier-ws/get_street_by_region_id_and_district_id_and_city_id_and_street_ua?city_id=' . $city_ref, 'GET');

	        if(isset($obj['Entries']['Entry']) && is_array($obj['Entries']['Entry']) && !empty($obj['Entries']['Entry']))
       		{
       			$warehouse = array();

       			foreach($obj['Entries']['Entry'] as $entry)
       			{
       				$warehouse[] = array(
	        			'value' => $entry['STREET_ID'],
	        			'label' => $entry['SHORTSTREETTYPE_UA'] . $entry['STREET_UA'] . ', ' . $entry['DISTRICT_UA'] . ' ' . __('district', 'mrkv-ua-shipping'),
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

		public function get_ukr_poshta_house()
		{
			if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field( wp_unslash($_POST['nonce'])), 'mrkv_ua_ship_nonce')) {
		        wp_send_json_error(__('Invalid nonce.', 'mrkv-ua-shipping'), 403);
		        wp_die();
		    }

			require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/ukr-poshta/api/mrkv-ua-shipping-api-ukr-poshta.php';
			$mrkv_object_ukr_poshta = new MRKV_UA_SHIPPING_API_UKR_POSHTA(get_option('ukr-poshta_m_ua_settings'));

			$street_ref = isset($_POST['ref']) ? sanitize_text_field(wp_unslash($_POST['ref'])) : '';
			$house = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';

			# Send request
	        $obj = $mrkv_object_ukr_poshta->send_post_request('address-classifier-ws/get_addr_house_by_street_id?street_id=' . $street_ref . '&housenumber=' . $house, 'GET');

	        if(isset($obj['Entries']['Entry']) && is_array($obj['Entries']['Entry']) && !empty($obj['Entries']['Entry']))
       		{
       			$houses = array();

       			foreach($obj['Entries']['Entry'] as $entry)
       			{
       				$houses[] = array(
	        			'value' => $entry['POSTCODE'],
	        			'label' => $entry['HOUSENUMBER_UA'],
	        		);
       			}

       			# Return object
	        	echo wp_json_encode($houses);
       		}
       		else
       		{
       			echo wp_json_encode(array());
       		}

       		wp_die();
		}

		public function get_warehouse_id()
		{
			if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field( wp_unslash($_POST['nonce'])), 'mrkv_ua_ship_nonce')) {
		        wp_send_json_error(__('Invalid nonce.', 'mrkv-ua-shipping'), 403);
		        wp_die();
		    }

			require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/ukr-poshta/api/mrkv-ua-shipping-api-ukr-poshta.php';
			$mrkv_object_ukr_poshta = new MRKV_UA_SHIPPING_API_UKR_POSHTA(get_option('ukr-poshta_m_ua_settings'));

			$warehouse_name = isset($_POST['warehouse_name']) ? sanitize_text_field(wp_unslash($_POST['warehouse_name'])) : '';

			# Send request
	        $obj = $mrkv_object_ukr_poshta->send_post_request_curl('ecom/0.0.1/addresses', 'POST', array( "postcode" => $warehouse_name  ));

	        if(isset($obj['id']))
       		{
       			echo esc_html($obj['id']);
       		}

       		wp_die();
		}

		public function get_address_id()
		{
			if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field( wp_unslash($_POST['nonce'])), 'mrkv_ua_ship_nonce')) {
		        wp_send_json_error(__('Invalid nonce.', 'mrkv-ua-shipping'), 403);
		        wp_die();
		    }

			require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/ukr-poshta/api/mrkv-ua-shipping-api-ukr-poshta.php';
			$mrkv_object_ukr_poshta = new MRKV_UA_SHIPPING_API_UKR_POSHTA(get_option('ukr-poshta_m_ua_settings'));

			$postcode = isset($_POST['postcode']) ? sanitize_text_field(wp_unslash($_POST['postcode'])) : '';
			$country = isset($_POST['country']) ? sanitize_text_field(wp_unslash($_POST['country'])) : '';
			$region = isset($_POST['region']) ? sanitize_text_field(wp_unslash($_POST['region'])) : '';
			$city = isset($_POST['city']) ? sanitize_text_field(wp_unslash($_POST['city'])) : '';
			$street = isset($_POST['street']) ? sanitize_text_field(wp_unslash($_POST['street'])) : '';
			$apartment_number = isset($_POST['apartment_number']) ? sanitize_text_field(wp_unslash($_POST['apartment_number'])) : '';

			# Send request
	        $obj = $mrkv_object_ukr_poshta->send_post_request_curl('ecom/0.0.1/addresses', 'POST', array(
				"postcode" => $postcode,
				"country" => $country,
				"region" => $region,
				"city" => $city,
				"street" => $street,
				"apartmentNumber" => $apartment_number,
			));

	        if(isset($obj['id']))
       		{
       			echo esc_html($obj['id']);
       		}

       		wp_die();
		}

		public function mrkv_ua_handle_pdf_printing() {
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				wp_die( esc_html__( 'You do not have permission to access this page.', 'mrkv-ua-shipping' ) );
			}

			$nonce_value = isset( $_POST['mrkv_ua_shipping_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['mrkv_ua_shipping_nonce'] ) ) : '';
			if ( empty( $nonce_value ) || ! wp_verify_nonce( $nonce_value, 'mrkv_ua_print_pdf_action' ) ) {
				wp_die( esc_html__( 'Security check failed. Please refresh the page and try again.', 'mrkv-ua-shipping' ) );
			}

			$template_path = MRKV_UA_SHIPPING_PLUGIN_PATH . 'templates/orders/mrkv-ua-ship-ukr-poshta-pdf.php';

			if ( file_exists( $template_path ) ) {
				include $template_path;
			}
			
			exit;
		}
	}
}