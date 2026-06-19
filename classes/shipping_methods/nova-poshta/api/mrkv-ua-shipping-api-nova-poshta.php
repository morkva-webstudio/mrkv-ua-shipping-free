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
		public function send_post_request($params) 
	    {
	    	# Create arguments
			$mrkv_ua_shipping_args = array(
				'timeout' => 30,
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
	    		# Set arguments
	    		$mrkv_ua_shipping_args = array(
		            "apiKey" => $this->settings_method['api_key'],
		            "modelName" => "AddressGeneral",
		            "calledMethod" => "getAreas",
		        );

	    		# Send request
	    		$obj = $this->send_post_request( $mrkv_ua_shipping_args );

	    		if(is_array($obj) && isset($obj['success']) && $obj['success'] == true)
	    		{
	    			update_option('mrkv_api_fixed_np', false);
	    			return true;
	    		}
	    		else
	    		{
	    			update_option('mrkv_api_fixed_np', true);
	    			if(is_array($obj) && isset($obj['errors'][0]) && $obj['errors'][0])
	    			{
	    				# Return false
		    			return $obj['errors'][0];
	    			}
	    			else
	    			{
	    				# Return false
		    			return __('API key incorrect', 'mrkv-ua-shipping');
	    			}
	    		}
	    	}
	    	else
	    	{
	    		update_option('mrkv_api_fixed_np', true);
				# Return false
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