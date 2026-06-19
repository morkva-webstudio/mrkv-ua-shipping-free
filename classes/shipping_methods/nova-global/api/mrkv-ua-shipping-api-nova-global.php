<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_API_NOVA_GLOBAL'))
{
	/**
	 * Class for setup nova global api
	 */
	class MRKV_UA_SHIPPING_API_NOVA_GLOBAL
	{
		/**
		 * @var string API URL
		 * */
		private $api_url_prod = 'https://api.novaposhta.international:8243/npi/';

		/**
		 * @var string API URL
		 * */
		private $api_url_test = 'https://test-api.novaposhta.international:8243/npi/';

		/**
		 * @param array Settings
		 * */
		private $settings_method;

		/**
		 * @param object Log
		 * */
		public $debug_log;

		/**
		 * @var string Method Slug
		 * */
		public $slug_method = 'nova-global';

		/**
		 * Constructor for nova poshta api
		 * */
		function __construct($settings)
		{
			# Set data
			$this->settings_method = $settings;
			$this->debug_log = new MRKV_UA_SHIPPING_LOG($this->get_debug_enabled());
		}

		/**
		 * Send general request
		 * @param array Params query
		 * 
		 * @return mixed Answer
		 * */
		public function send_post_request($params, $url_path) 
	    {
	    	$url = '';
			$authorization = '';
			$username = '';
			$password = '';

			if(isset($this->settings_method['test_mode']) && $this->settings_method['test_mode'] == "on")
			{
				$url = $this->api_url_test;
				$username = isset($this->settings_method['test_username']) ? $this->settings_method['test_username'] : '';
				$password = isset($this->settings_method['test_password']) ? $this->settings_method['test_password'] : '';

			}
			else
			{
				$url = $this->api_url_prod;
				$username = isset($this->settings_method['production_username']) ? $this->settings_method['production_username'] : '';
				$password = isset($this->settings_method['production_password']) ? $this->settings_method['production_password'] : '';
			}

			$auth_string = base64_encode($username . ':' . $password);

	    	# Create arguments
			$mrkv_ua_shipping_args = array(
				'timeout' => 30,
				'redirection' => 10,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array( 
					"content-type" => "application/json",
					"Authorization" => 'Basic ' . $auth_string
				),
				'body' => \wp_json_encode( $params ),
				'cookies' => array(),
				'sslverify' => true,
			);

			# Save to log
			$this->debug_log->add_data_request(\wp_json_encode( $params ));

			# Send request
			$response = wp_remote_post( $url . $url_path, $mrkv_ua_shipping_args );

			# Check answer
			if ( is_wp_error( $response ) ) 
			{
				# Get error
				$error_message = $response->get_error_message();

				# Save to log
				$this->debug_log->add_data_error($error_message);

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
	}
}