<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_API_NOVA_POST'))
{
	/**
	 * Class for setup nova poshta api
	 */
	class MRKV_UA_SHIPPING_API_NOVA_POST
	{
		/**
		 * @var string API URL
		 * */
		private $api_url = 'https://api.novapost.com/v.1.0/';

		/**
		 * @var string API URL
		 * */
		private $sandbox_api_url = 'https://api-stage.novapost.pl/v.1.0/';

		/**
		 * @param array Settings
		 * */
		private $settings_method;

		private $jwt_key_key;

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

		public function get_current_api_url()
		{
			if(isset($this->settings_method['internal_api_server']) && $this->settings_method['internal_api_server'] == 'production')
	    	{
	    		return $this->api_url;
	    	}
	    	else
	    	{
	    		return $this->sandbox_api_url;
	    	}
		}

		/**
		 * Send general request
		 * @param array Params query
		 * 
		 * @return mixed Answer
		 * */
		public function send_post_request($mrkv_ua_shipping_args, $slug, $method) 
	    {
			# Save to log
			$this->debug_log->add_data_request(\wp_json_encode( $mrkv_ua_shipping_args ));

			if($method == 'POST')
			{	
				$response = wp_remote_post( $this->get_current_api_url() . $slug, array(
                    'method'      => 'POST',
                    'timeout'     => 30,
                    'headers'     => [
                        'Content-Type' => 'application/json',
                        'Authorization' => $this->jwt_key_key
                    ],
                    'body'        => wp_json_encode( $mrkv_ua_shipping_args ),
                    'data_format' => 'body',
                ) );
			}
			else
			{
				$response = wp_remote_get($this->get_current_api_url() . $slug, [
				    'headers' => [
				      'Authorization' => $this->jwt_key_key
				    ],
				  'timeout' => 30
				]);
			}

			if ( !is_wp_error( $response ) ) 
            {
                $body = wp_remote_retrieve_body( $response );
                return json_decode( $body, true );
            }
            else
            {
            	$error_message = $response->get_error_message();

            	# Save to log
				$this->debug_log->add_data_error($order_id . $error_message);
            	# Return error string
				return $error_message;
            }
	    }

	    public function get_jwt_key()
	    {
	    	$api_url = 
	    	$token_json = wp_remote_get($this->get_current_api_url() . 'clients/authorization?apiKey=' . $this->settings_method['internal_api_key'], [
			    'headers' => [
			      
			    ],
			  'timeout' => 30
			]);

			$token = json_decode($token_json['body'], true);

			if(is_array($token) && isset($token['jwt']))
			{
				$this->jwt_key_key = $token['jwt'];
				return $token['jwt'];
			}
			else
			{
				return false;
			}
	    }

	    /**
	     * Check api key correct
	     * @return mixed Api status
	     * */
		private function get_api_key_active()
	    {
	    	if(isset($this->settings_method['internal_api_key']) && $this->settings_method['internal_api_key'])
	    	{
	    		# Send request
	    		$obj = $this->get_jwt_key();

	    		if($obj)
	    		{
	    			return true;
	    		}
	    		else
	    		{
	    			# Return false
	    			return __('API key incorrect', 'mrkv-ua-shipping');
	    		}
	    	}
	    	else
	    	{
				# Return false
	    		return false;
	    	}
	    }

	    /**
	     * Get Api Key
	     * @return string API Key
	     * */
	    public function get_api_key()
	    {
	    	if(isset($this->settings_method['internal_api_key']) && $this->settings_method['internal_api_key'])
	    	{
	    		return $this->settings_method['internal_api_key'];	
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
	}
}