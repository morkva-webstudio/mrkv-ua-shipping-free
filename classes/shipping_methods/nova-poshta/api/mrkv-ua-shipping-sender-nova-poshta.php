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
		 * @param object MRKV_UA_SHIPPING_API_NOVA_POSHTA
		 * */
		private $nova_poshta_api;

		/**
		 * @var string Sender Counterparties Ref
		 * */
		private $sender_counterparties_ref;

		/**
		 * Constructor for nova poshta api sender
		 * */
		function __construct($nova_poshta_api)
		{
			# Set fields
			$this->nova_poshta_api = $nova_poshta_api;
			$this->sender_counterparties_ref = $this->get_sender_counterparties_ref();
		}

		/**
		 * Get all sender contacts list
		 * @return 
		 * */
		public function get_senders_contacts_ref() 
	    {
	    	if(is_object($this->nova_poshta_api))
	    	{
	    		# Set arguments
		        $mrkv_ua_shipping_args = array(
		            "apiKey" => $this->nova_poshta_api->get_api_key(),
		            "modelName" => "Counterparty",
		            "calledMethod" => "getCounterpartyContactPersons",
		            "methodProperties" => array(
		                "Ref" => $this->sender_counterparties_ref,
		                "Page" => 1
		            ),
		        );

		        # Send request
		        $obj = $this->nova_poshta_api->send_post_request( $mrkv_ua_shipping_args );

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
	    	return $this->sender_counterparties_ref;
	    }

	    /**
	     * Get Sender Counterparties Ref
	     * */
	    public function get_sender_counterparties_ref()
	    {
	    	if(is_object($this->nova_poshta_api))
	    	{
	    		$mrkv_ua_shipping_args = array(
		            "apiKey" => $this->nova_poshta_api->get_api_key(),
		            "modelName" => "Counterparty",
		            "calledMethod" => "getCounterparties",
		            "methodProperties" => array(
		                "CounterpartyProperty" => "Sender",
		                "Page" => "1"
		            ),
		        );

		        # Send request
		        $obj = $this->nova_poshta_api->send_post_request( $mrkv_ua_shipping_args );

		        if(isset($obj['data'][0]['Ref']))
		        {
		        	# Return object
		        	return $obj['data'][0]['Ref'];
		        }
		        else
		        {
		        	if(isset($obj['errors'][0]))
		        	{
		        		$this->nova_poshta_api->debug_log->add_data_error($obj['errors'][0]);
		        	}
		        	else{
		        		$this->nova_poshta_api->debug_log->add_data_error(__('Error with Sender Counterparties Ref','mrkv-ua-shipping'));
		        	}
		        }
	    	}
	    	return '';
	    }

	    /**
	     * Get Sender Counterparties Ref
	     * */
	    public function get_sender_address_ref($sender_street_ref, $sender_building_number, $sender_flat)
	    {
	    	if(is_object($this->nova_poshta_api))
	    	{
	    		$mrkv_ua_shipping_args = array(
		            "apiKey" => $this->nova_poshta_api->get_api_key(),
		            "modelName" => "Address",
		            "calledMethod" => "save",
		            "methodProperties" => array(
		                "CounterpartyRef" => $this->sender_counterparties_ref,
	                    "StreetRef" => $sender_street_ref,
	                    "BuildingNumber" => $sender_building_number,
	                    "Flat" => $sender_flat,
	                    "Note" => ''
		            ),
		        );

		        # Send request
		        $obj = $this->nova_poshta_api->send_post_request( $mrkv_ua_shipping_args );

		        if(isset($obj['data'][0]['Ref']))
		        {
		        	# Return object
		        	return $obj['data'][0]['Ref'];
		        }
		        else
		        {
		        	if(isset($obj['errors'][0]))
		        	{
		        		$this->nova_poshta_api->debug_log->add_data_error($obj['errors'][0]);
		        	}
		        	else{
		        		$this->nova_poshta_api->debug_log->add_data_error(__('Error with Sender Counterparties Ref','mrkv-ua-shipping'));
		        	}
		        	
		        	
		        }
	    	}
	    	return '';
	    }
	}
}