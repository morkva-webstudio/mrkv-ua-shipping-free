<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_SENDER_NOVA_POSHTA'))
{
	/**
	 * Class for setup nova poshta api sender
	 */
	class MRKV_UA_SHIPPING_SENDER_NOVA_POSHTA
	{
		/**
		 * @var int Cache lifetime (s) of the counterparty and contact person lists
		 * */
		const CACHE_TTL = 12 * HOUR_IN_SECONDS;

		/**
		 * @param object MRKV_UA_SHIPPING_API_NOVA_POSHTA
		 * */
		private $mrkv_ua_shipping_nova_poshta_api;

		/**
		 * @var string|null Sender Counterparties Ref, loaded on first use
		 * */
		private $sender_counterparties_ref = null;

		/**
		 * @var string Error of the last counterparty list request
		 * */
		private $last_error = '';

		/**
		 * Constructor for nova poshta api sender
		 * */
		function __construct($mrkv_ua_shipping_nova_poshta_api)
		{
			# Set fields
			$this->mrkv_ua_shipping_nova_poshta_api = $mrkv_ua_shipping_nova_poshta_api;
		}

		/**
		 * Get all sender contacts list
		 * @param string Counterparty Ref, the first sender counterparty of the key when empty
		 * @return 
		 * */
		public function get_senders_contacts_ref($counterparty_ref = '') 
	    {
	    	if(is_object($this->mrkv_ua_shipping_nova_poshta_api))
	    	{
	    		# Set arguments
		        $mrkv_ua_shipping_args = array(
		            "apiKey" => $this->mrkv_ua_shipping_nova_poshta_api->get_api_key(),
		            "modelName" => "Counterparty",
		            "calledMethod" => "getCounterpartyContactPersons",
		            "methodProperties" => array(
		                "Ref" => $counterparty_ref ? $counterparty_ref : $this->get_counterparties_ref(),
		                "Page" => 1
		            ),
		        );

		        # Send request
		        $obj = $this->mrkv_ua_shipping_nova_poshta_api->send_post_request( $mrkv_ua_shipping_args );

		        if(isset($obj['data']))
		        {
		        	# Return object
		        	return $obj['data'];
		        }
	    	}
	    	return '';
	    }

	    public function get_counterparties_ref()
	    {
	    	if($this->sender_counterparties_ref === null)
	    	{
	    		$this->sender_counterparties_ref = $this->get_sender_counterparties_ref();
	    	}

	    	return $this->sender_counterparties_ref;
	    }

	    /**
	     * Get Sender Counterparties Ref
	     * */
	    public function get_sender_counterparties_ref()
	    {
	    	if(is_object($this->mrkv_ua_shipping_nova_poshta_api))
	    	{
	    		$mrkv_ua_shipping_args = array(
		            "apiKey" => $this->mrkv_ua_shipping_nova_poshta_api->get_api_key(),
		            "modelName" => "Counterparty",
		            "calledMethod" => "getCounterparties",
		            "methodProperties" => array(
		                "CounterpartyProperty" => "Sender",
		                "Page" => "1"
		            ),
		        );

		        # Send request
		        $obj = $this->mrkv_ua_shipping_nova_poshta_api->send_post_request( $mrkv_ua_shipping_args );

		        if(isset($obj['data'][0]['Ref']))
		        {
		        	# Return object
		        	return $obj['data'][0]['Ref'];
		        }
		        else
		        {
		        	if(isset($obj['errors'][0]))
		        	{
		        		$this->mrkv_ua_shipping_nova_poshta_api->debug_log->add_data_error($obj['errors'][0]);
		        	}
		        	else{
		        		$this->mrkv_ua_shipping_nova_poshta_api->debug_log->add_data_error(__('Error with Sender Counterparties Ref','mrkv-ua-shipping'));
		        	}
		        }
	    	}
	    	return '';
	    }

	    /**
	     * All sender counterparties of the key (private person, ФОП, ТОВ...), in the order Nova Poshta returns them
	     * @param bool Skip the cache and ask Nova Poshta
	     *
	     * @return array List of array('ref', 'description', 'type', 'ownership', 'edrpou'), empty on error (see get_last_error())
	     * */
	    public function get_sender_counterparties($force = false)
	    {
	    	$this->last_error = '';
	    	$api_key = is_object($this->mrkv_ua_shipping_nova_poshta_api) ? $this->mrkv_ua_shipping_nova_poshta_api->get_api_key() : '';

	    	if(!$api_key)
	    	{
	    		return array();
	    	}

	    	$cache_key = 'mrkv_np_counterparties_' . md5($api_key);

	    	if(!$force)
	    	{
	    		$cached = get_transient($cache_key);

	    		if(is_array($cached))
	    		{
	    			return $cached;
	    		}
	    	}

	    	$mrkv_ua_shipping_args = array(
	            "apiKey" => $api_key,
	            "modelName" => "Counterparty",
	            "calledMethod" => "getCounterparties",
	            "methodProperties" => array(
	                "CounterpartyProperty" => "Sender",
	                "Page" => "1"
	            ),
	        );

	        $obj = $this->mrkv_ua_shipping_nova_poshta_api->send_post_request( $mrkv_ua_shipping_args );
	        $counterparties = array();

	        if(isset($obj['data']) && is_array($obj['data']))
	        {
	        	foreach($obj['data'] as $counterparty)
	        	{
	        		if(empty($counterparty['Ref']))
	        		{
	        			continue;
	        		}

	        		$counterparties[] = array(
	        			'ref'         => $counterparty['Ref'],
	        			'description' => isset($counterparty['Description']) ? $counterparty['Description'] : '',
	        			'type'        => (isset($counterparty['CounterpartyType']) && $counterparty['CounterpartyType'] == 'Organization') ? 'Organization' : 'PrivatePerson',
	        			'ownership'   => isset($counterparty['OwnershipFormDescription']) ? $counterparty['OwnershipFormDescription'] : '',
	        			'edrpou'      => isset($counterparty['EDRPOU']) ? $counterparty['EDRPOU'] : '',
	        		);
	        	}
	        }

	        if(empty($counterparties))
	        {
	        	if(isset($obj['errors'][0]) && $obj['errors'][0])
	        	{
	        		$this->last_error = $obj['errors'][0];
	        	}
	        	elseif(is_array($obj) && !empty($obj['success']))
	        	{
	        		$this->last_error = __('Sender list empty', 'mrkv-ua-shipping');
	        	}
	        	else
	        	{
	        		$this->last_error = __('Nova Poshta did not answer, try again later', 'mrkv-ua-shipping');
	        	}

	        	$this->mrkv_ua_shipping_nova_poshta_api->debug_log->add_data_error($this->last_error);

	        	# Errors are not cached: a fixed key must work at once
	        	return array();
	        }

	        set_transient($cache_key, $counterparties, self::CACHE_TTL);

	        # The first counterparty is the default the plugin always used
	        $this->sender_counterparties_ref = $counterparties[0]['ref'];

	        return $counterparties;
	    }

	    /**
	     * Contact persons of a sender counterparty
	     * @param string Counterparty Ref
	     * @param bool Skip the cache and ask Nova Poshta
	     *
	     * @return array Contact persons as Nova Poshta returns them, empty on error
	     * */
	    public function get_contact_persons($counterparty_ref, $force = false)
	    {
	    	$api_key = is_object($this->mrkv_ua_shipping_nova_poshta_api) ? $this->mrkv_ua_shipping_nova_poshta_api->get_api_key() : '';

	    	if(!$api_key || !$counterparty_ref)
	    	{
	    		return array();
	    	}

	    	$cache_key = 'mrkv_np_contact_persons_' . md5($api_key . '|' . $counterparty_ref);

	    	if(!$force)
	    	{
	    		$cached = get_transient($cache_key);

	    		if(is_array($cached))
	    		{
	    			return $cached;
	    		}
	    	}

	    	$contacts = $this->get_senders_contacts_ref($counterparty_ref);

	    	if(!is_array($contacts) || empty($contacts))
	    	{
	    		return array();
	    	}

	    	set_transient($cache_key, $contacts, self::CACHE_TTL);

	    	return $contacts;
	    }

	    /**
	     * Contact persons in the format of the "Sender" select: data (Ref), description, attr (data-* of the option)
	     * @param array Contact persons as Nova Poshta returns them
	     * @param string Counterparty Ref, saved with the chosen person
	     *
	     * @return array Options
	     * */
	    public static function get_contact_persons_options($contacts, $counterparty_ref)
	    {
	    	$options_sender = array();

	    	if(is_array($contacts) && !empty($contacts))
			{
				foreach($contacts as $counter => $sender)
				{
					foreach($sender as $sender_data_key => $sender_data_val)
					{
						if($sender_data_key == 'Description')
						{
							$options_sender[$counter]['description'] = $sender_data_val;
						}
						if($sender_data_key == 'Ref')
						{
							$options_sender[$counter]['data'] = $sender_data_val;
							continue;
						}

						$options_sender[$counter]['attr'][strtolower($sender_data_key)] = $sender_data_val;
					}
					$options_sender[$counter]['attr']['counterparty_ref'] = $counterparty_ref;
				}
			}

			return $options_sender;
	    }

	    /**
	     * Error of the last get_sender_counterparties() call
	     * @return string
	     * */
	    public function get_last_error()
	    {
	    	return $this->last_error;
	    }

	    /**
	     * Get Sender Counterparties Ref
	     * @param string Counterparty Ref the address belongs to, the first sender counterparty of the key when empty
	     * */
	    public function get_sender_address_ref($sender_street_ref, $sender_building_number, $sender_flat, $counterparty_ref = '')
	    {
	    	if(is_object($this->mrkv_ua_shipping_nova_poshta_api))
	    	{
	    		$mrkv_ua_shipping_args = array(
		            "apiKey" => $this->mrkv_ua_shipping_nova_poshta_api->get_api_key(),
		            "modelName" => "Address",
		            "calledMethod" => "save",
		            "methodProperties" => array(
		                "CounterpartyRef" => $counterparty_ref ? $counterparty_ref : $this->get_counterparties_ref(),
	                    "StreetRef" => $sender_street_ref,
	                    "BuildingNumber" => $sender_building_number,
	                    "Flat" => $sender_flat,
	                    "Note" => ''
		            ),
		        );

		        # Send request
		        $obj = $this->mrkv_ua_shipping_nova_poshta_api->send_post_request( $mrkv_ua_shipping_args );

		        if(isset($obj['data'][0]['Ref']))
		        {
		        	# Return object
		        	return $obj['data'][0]['Ref'];
		        }
		        else
		        {
		        	if(isset($obj['errors'][0]))
		        	{
		        		$this->mrkv_ua_shipping_nova_poshta_api->debug_log->add_data_error($obj['errors'][0]);
		        	}
		        	else{
		        		$this->mrkv_ua_shipping_nova_poshta_api->debug_log->add_data_error(__('Error with Sender Counterparties Ref','mrkv-ua-shipping'));
		        	}


		        }
	    	}
	    	return '';
	    }
	}
}
