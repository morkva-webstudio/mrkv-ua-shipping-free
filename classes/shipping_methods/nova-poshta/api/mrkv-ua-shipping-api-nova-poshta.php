<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_API_NOVA_POSHTA'))
{
	/**
	 * Class for setup nova poshta api
	 */
	class MRKV_UA_SHIPPING_API_NOVA_POSHTA
	{
		/**
		 * @var string API URL
		 * */
		private $api_url = 'https://api.novaposhta.ua/v2.0/json/';

		/**
		 * @param array Settings
		 * */
		private $settings_method;

		/**
		 * @param object Log
		 * */
		public $debug_log;

		/**
		 * @var mixed API Active
		 * */
		public $active_api;

		/**
		 * @var string Method Slug
		 * */
		public $slug_method = 'nova-poshta';

		/**
		 * @var int Timeout (s) for city/warehouse lookups: the proxy is the fallback, so do not wait long
		 * */
		const LOOKUP_TIMEOUT = 4;

		/**
		 * @var string Transient set while the API is unreachable
		 * */
		const API_DOWN_TRANSIENT = 'mrkv_np_api_down';

		/**
		 * @var string Transient counting failures in a row, to lengthen the down mark
		 * */
		const API_DOWN_STREAK_TRANSIENT = 'mrkv_np_api_down_streak';

		/**
		 * @var int[] Minutes the down mark lasts after the 1st, 2nd, 3rd, 4th+ failure in a row
		 * */
		const API_DOWN_MINUTES = array(2, 5, 15, 60);

		/**
		 * Is the API marked unreachable (connection error or 5xx within the last minutes)
		 * @return bool
		 * */
		public static function is_api_down()
		{
			return (bool) get_transient(self::API_DOWN_TRANSIENT);
		}

		/**
		 * Mark the API unreachable. Every failure in a row lengthens the mark (2, 5, 15, 60 min), so a long
		 * outage costs one probe an hour, not one every 2 minutes
		 * */
		private static function mark_api_down()
		{
			$streak = (int) get_transient(self::API_DOWN_STREAK_TRANSIENT) + 1;
			$minutes = self::API_DOWN_MINUTES[min($streak, count(self::API_DOWN_MINUTES)) - 1];
			$seconds = (int) apply_filters('mrkv_ua_shipping_np_api_down_ttl', $minutes * MINUTE_IN_SECONDS, $streak);

			set_transient(self::API_DOWN_STREAK_TRANSIENT, $streak, DAY_IN_SECONDS);
			set_transient(self::API_DOWN_TRANSIENT, 1, max(MINUTE_IN_SECONDS, $seconds));
		}

		/**
		 * The API answered: drop the down mark and the failure count
		 * */
		private static function mark_api_up()
		{
			if (get_transient(self::API_DOWN_STREAK_TRANSIENT)) {
				delete_transient(self::API_DOWN_STREAK_TRANSIENT);
				delete_transient(self::API_DOWN_TRANSIENT);
			}
		}

		/**
		 * Constructor for nova poshta api
		 * */
		function __construct($settings)
		{
			# Set data
			$this->settings_method = $settings;
			$this->debug_log = new MRKV_UA_SHIPPING_LOG($this->get_debug_enabled());
			$this->active_api = $this->get_api_key_active();
		}

		/**
		 * Send general request
		 * @param array Params query
		 * 
		 * @return mixed Answer
		 * */
		public function send_post_request($params, $timeout = 30) 
	    {
	    	# Create arguments
			$mrkv_ua_shipping_args = array(
				'timeout' => $timeout,
				'redirection' => 10,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array( 
					"content-type" => "application/json",
				),
				'body' => \wp_json_encode( $params ),
				'cookies' => array(),
				'sslverify' => true,
			);

			# Save to log
			$this->debug_log->add_data_request(\wp_json_encode( $params ));

			# Send request
			$response = wp_remote_post( $this->api_url, $mrkv_ua_shipping_args );

			# Remember an unreachable API so lookups skip it and use the proxy at once
			if ( is_wp_error( $response ) || (int) wp_remote_retrieve_response_code( $response ) >= 500 )
			{
				self::mark_api_down();
			}
			else
			{
				self::mark_api_up();
			}

			# Check answer
			if ( is_wp_error( $response ) ) 
			{
				# Get error
				$error_message = $response->get_error_message();

				$order_id = '';

				if(isset($params['InfoRegClientBarcodes']))
				{
					$order_id = $params['InfoRegClientBarcodes'] . ' ';
				}
				if(isset($params['order_id_ukr']))
				{
					$order_id = $params['order_id_ukr'] . ' ';
				}

				# Save to log
				$this->debug_log->add_data_error($order_id . $error_message);

				# Return error string
				return $error_message;
			} 
			else 
			{
				# Get body
				$body = wp_remote_retrieve_body( $response );

				# Decode json
				$obj = json_decode($body, true);

				# Return array
				return $obj;
			}
	    }

	    /**
	     * Check api key correct
	     * @return mixed Api status
	     * */
		private function get_api_key_active()
	    {
	    	if(isset($this->settings_method['api_key']) && $this->settings_method['api_key'])
	    	{
				$cache_key = 'mrkv_np_key_ok_' . md5($this->settings_method['api_key']);
				$cache_error_key = 'mrkv_np_key_err_' . md5($this->settings_method['api_key']);

	    		if(get_transient($cache_key))
	    		{
	    			return true;
	    		}

				$cached_error = get_transient($cache_error_key);
				if($cached_error !== false)
				{
					return $cached_error;
				}

	    		if(self::is_api_down())
	    		{
	    			return false;
	    		}

	    		$mrkv_ua_shipping_args = array(
		            "apiKey" => $this->settings_method['api_key'],
		            "modelName" => "AddressGeneral",
		            "calledMethod" => "getAreas",
		        );

	    		$obj = $this->send_post_request( $mrkv_ua_shipping_args, 3 );

	    		if(self::is_api_down())
	    		{
	    			return false;
	    		}

	    		if(is_array($obj) && isset($obj['success']) && $obj['success'] == true)
	    		{
					set_transient($cache_key, 1, HOUR_IN_SECONDS);
					delete_transient($cache_error_key);
	    			update_option('mrkv_api_fixed_np', false);
	    			return true;
	    		}
	    		else
	    		{
	    			update_option('mrkv_api_fixed_np', true);
	    			$error_message = __('API key incorrect', 'mrkv-ua-shipping');
	    			if(is_array($obj) && isset($obj['errors'][0]) && $obj['errors'][0])
	    			{
		    			$error_message = $obj['errors'][0];
	    			}
	    			set_transient($cache_error_key, $error_message, 5 * MINUTE_IN_SECONDS);
	    			return $error_message;
	    		}
	    	}
	    	else
	    	{
	    		update_option('mrkv_api_fixed_np', true);
	    		return false;
	    	}
	    }

	    /**
	     * Remove invoices from Nova Poshta platform
	     * @param array Invoices
	     * */
	    public function remove_invoice_data_platform($invoices_ref)
	    {
	    	if(isset($this->settings_method['api_key']) && $this->settings_method['api_key'])
	    	{
	    		if(is_array($invoices_ref) && !empty($invoices_ref))
	    		{
	    			# Set arguments
		    		$mrkv_ua_shipping_args = array(
			            "apiKey" => $this->settings_method['api_key'],
			            "modelName" => "InternetDocument",
			            "calledMethod" => "delete",
			            "methodProperties" => array(
				            "DocumentRefs" => $invoices_ref
				        )
			        );

			        # Send request
	    			$obj = $this->send_post_request( $mrkv_ua_shipping_args );
	    		}
	    	}

	    	return;
	    }

	    /**
	     * Get Api Key
	     * @return string API Key
	     * */
	    public function get_api_key()
	    {
	    	if(isset($this->settings_method['api_key']) && $this->settings_method['api_key'])
	    	{
	    		return $this->settings_method['api_key'];	
	    	}
	    	else
	    	{
	    		return '';
	    	}
	    }

	    /**
	     * Get Debug enabled
	     * @return boolean Debug
	     * */
	    public function get_debug_enabled()
	    {
	    	if(isset($this->settings_method['debug']['log']) && $this->settings_method['debug']['log'] == 'on')
	    	{
	    		return true;	
	    	}
	    	else
	    	{
	    		return false;	
	    	}
	    }

	    public function get_status_documents($invoices_data)
	    {
	    	if(isset($this->settings_method['api_key']) && $this->settings_method['api_key'])
	    	{
	    		$invoice_phone = (isset($this->settings_method['sender']['phones']) && $this->settings_method['sender']['phones']) ? $this->settings_method['sender']['phones'] : '';

	    		$invoice_list = array();

	    		foreach($invoices_data as $invoice)
	    		{
	    			$invoice_list[] = array(
	    				"DocumentNumber" => $invoice->invoice,
	    				"Phone" => $invoice_phone
	    			);
	    		}

	    		# Set arguments
	    		$mrkv_ua_shipping_args = array(
		            "apiKey" => $this->settings_method['api_key'],
		            "modelName" => "TrackingDocumentGeneral",
		            "calledMethod" => "getStatusDocuments",
		            "methodProperties" => array(
		            	"Documents" => $invoice_list
		            )
		        );

	    		# Send request
	    		$obj = $this->send_post_request( $mrkv_ua_shipping_args );

	    		if(isset($obj['success']) && $obj['success'] == true)
	    		{
	    			return isset($obj['data']) ? $obj['data'] : array();;
	    		}
	    	}
	    	
	    	# Return false
			return array();
	    }
	}
}