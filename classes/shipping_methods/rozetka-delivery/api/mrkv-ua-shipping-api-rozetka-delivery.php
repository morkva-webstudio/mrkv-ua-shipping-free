<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_API_ROZETKA_DELIVERY'))
{
	/**
	 * Class for shipping rozetka delivery api
	 */
	class MRKV_UA_SHIPPING_API_ROZETKA_DELIVERY
	{
		/**
		 * @var string API URL
		 * */
		private $api_url = 'https://rz-delivery.rozetka.ua/';

	    /**
	     * @var string $responseTime waiting for response from server, sec.
	     */
	    private $response_time = '30';

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
		public $slug_method = 'rozetka-delivery';

		/**
		 * Constructor for shipping ukr poshta api
		 * */
		function __construct($settings)
		{
			# Set data
			$this->settings_method = $settings;
			$this->debug_log = new MRKV_UA_SHIPPING_LOG($this->get_debug_enabled());
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

	    public function get_status_documents($data)
	    {
	    	return '';
	    }

	    /**
		 * Send general request
		 * @param array Params query
		 * 
		 * @return mixed Answer
		 * */
		public function send_post_request($model, $method = 'POST', $params = array(), $add = '') 
	    {
	    	# Get required URL
	        $url = $this->api_url . $model . $add;

	        $mrkv_ua_shipping_args = array(
	        	'timeout' => 30,
				'redirection' => 10,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array( 
					"Сontent-type" => "application/json, charset=utf-8",
					'Accept' => 'application/json'
				),
				'cookies' => array(),
				'sslverify' => true,
	        );

	        if(!empty($params))
	        {
	        	$mrkv_ua_shipping_args['body'] = \wp_json_encode( $params );
	        	
	        	# Save to log
				$this->debug_log->add_data_request(\wp_json_encode( $params ));
	        }

	        if($method == 'POST')
	        {
	        	# Send request
				$response = wp_remote_post( $url, $mrkv_ua_shipping_args );
	        }
	        else
	        {
	        	# Send request
				$response = wp_remote_get( $url, $mrkv_ua_shipping_args );
	        }

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
	}
}