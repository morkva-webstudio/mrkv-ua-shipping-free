<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_RECIPIENT_NOVA_POSHTA'))
{
	/**
	 * Class for setup nova poshta api recipient
	 */
	class MRKV_UA_SHIPPING_RECIPIENT_NOVA_POSHTA
	{
		/**
		 * @param object MRKV_UA_SHIPPING_API_NOVA_POSHTA
		 * */
		private $nova_poshta_api;

		/**
		 * @var var Contact ref
		 * */
		public $contact_recipient_ref;

		/**
		 * Constructor for nova poshta api recipient
		 * */
		function __construct($nova_poshta_api)
		{
			# Set fields
			$this->nova_poshta_api = $nova_poshta_api;
			$this->contact_recipient_ref = '';
		}

	    /**
	     * Get Sender Counterparties Ref
	     * */
	    public function get_recipient_ref($recipient_first_name, $recipient_middle_name, $recipient_last_name, $recipient_phone)
	    {
	    	$mrkv_ua_shipping_args = array(
	            "apiKey" => $this->nova_poshta_api->get_api_key(),
	            "modelName" => "Counterparty",
            	"calledMethod" => "save",
	            "methodProperties" => array(
	                "FirstName" => stripslashes($recipient_first_name),
		            "MiddleName" => stripslashes($recipient_middle_name),
		            "LastName" => stripslashes($recipient_last_name),
		            "Phone" => $recipient_phone,
		            "Email" => "",
		            "CounterpartyType" => "PrivatePerson",
		            "CounterpartyProperty" => "Recipient"
	            ),
	        );

	        # Send request
	        $obj = $this->nova_poshta_api->send_post_request( $mrkv_ua_shipping_args );

	        if(isset($obj['data'][0]['ContactPerson']['data'][0]['Ref']))
	        {
	        	$this->contact_recipient_ref = $obj['data'][0]['ContactPerson']['data'][0]['Ref'];
	        }

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
	        		$this->nova_poshta_api->debug_log->add_data_error(__('Error with Recipient Ref','mrkv-ua-shipping'));
	        	}
	        	
	        	return '';
	        }
	    }

	    public function get_recipient_address_ref($recipient_ref, $city_ref, $order, $is_new_shipping = false)
	    {
	    	$flat_number = $this->get_recipient_flat_number( $order, $is_new_shipping );
	    	if($flat_number == '-')
	    	{
	    		$flat_number = '';
	    	}
	    	
	    	$mrkv_ua_shipping_args = array(
	            "apiKey" => $this->nova_poshta_api->get_api_key(),
	            "modelName" => "Address",
	            "calledMethod" => "save",
	            "methodProperties" => array(
	                "CounterpartyRef" => $recipient_ref,
	                "StreetRef" => $this->get_recipient_street_ref( $city_ref, $order, $is_new_shipping),
	                "BuildingNumber" => $this->get_recipient_building_number($order, $is_new_shipping),
	                "Flat" => $flat_number,
	                "Note" => ""
	            )
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
	        		$this->nova_poshta_api->debug_log->add_data_error(__('Error with Recipient Street Ref','mrkv-ua-shipping'));
	        	}
	        	
	        	return '';
	        }
	    }

	    private function get_recipient_flat_number($order, $is_new_shipping)
	    {
	        $order_data = $order->get_data();

	        if($is_new_shipping)
	        {
	        	return $order->get_meta('mrkv_ua_shipping_nova-poshta_address_flat');
	        }

	        if ( isset( $order_data['shipping']['address_2'] ) &&  !empty( $order_data['shipping']['address_2'] ) ) 
	        {
	            return $order_data['shipping']['address_2'];
	        }
	        elseif ( isset( $order_data['billing']['address_2'] ) && !empty( $order_data['billing']['address_2'] ) ) 
	        {
	            return $order_data['billing']['address_2'];
	        }  
	        else 
	        {
	            return '';
	        }
	    }

	    private function get_recipient_building_number($order, $is_new_shipping)
	    {
	        $order_data = $order->get_data();

	        if($is_new_shipping)
	        {
	        	return $order->get_billing_address_2();
	        }

	        if ( isset( $order_data['shipping']['address_1'] ) && !empty( $order_data['shipping']['address_1'] ) ) 
	        {
	            $street_house_string = $order_data['shipping']['address_1'];
	        }
	        elseif ( isset( $order_data['billing']['address_1'] ) &&  !empty( $order_data['billing']['address_1'] ) ) 
	        {
	            $street_house_string = $order_data['billing']['address_1'];
	        }  
	        else 
	        {
	            return '';
	        }

	        $street_house = \trim( $street_house_string );
	        $pos_comma = strpos( $street_house, ',' );

	        return substr( $street_house, $pos_comma + 1 );
	    }

	    private function get_recipient_street_ref($city_ref, $order, $is_new_shipping)
	    {
	    	if($is_new_shipping)
	    	{
	    		return $order->get_meta('mrkv_ua_shipping_nova-poshta_address_street_ref');
	    	}

	        $mrkv_ua_shipping_args = array(
	            "apiKey" => $this->nova_poshta_api->get_api_key(),
	            "modelName" => "Address",
	            "calledMethod" => "getStreet",
	            "methodProperties" => array(
	                "CityRef" => $city_ref,
	                "FindByString" => $this->get_recipient_address_full( $order )
	            )
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
	        		$this->nova_poshta_api->debug_log->add_data_error(__('Error with Recipient Street Ref','mrkv-ua-shipping'));
	        	}
	        	
	        	return '';
	        }
	    }

	    private function get_recipient_address_full($order)
	    {
	        $street_house_arr = array();
	        $order_data = $order->get_data();

	        if ( isset( $order_data['shipping']['address_1'] ) && !empty( $order_data['shipping']['address_1'] ) ) 
	        {
	            $street_name = $order_data['shipping']['address_1'];
	        }
	        elseif ( isset( $order_data['billing']['address_1'] ) && !empty( $order_data['billing']['address_1'] ) ) 
	        {
	            $street_name = $order_data['billing']['address_1'];
	        }  
	        else {}

	        $pos_blank = strpos( $street_name, ' ');
	        $pos_comma = strpos( $street_name, ',' );
	        $street_len   = $pos_comma - $pos_blank - 1;
	        return substr( $street_name, $pos_blank + 1, $street_len );
	        $street_house = \trim( $street_name );
	        $pos_blank = strpos( $street_house, ' ');
	        $pos_comma = strpos( $street_house, ',' );
	        $street_house_arr = explode( " ", $street_house );
	        $street_len   = $pos_comma - $pos_blank - 1;
	        return substr( $street_house, $pos_blank + 1, $street_len );
	    }


	    public function get_recipient_warehouse_ref($warehouse_name, $city_ref)
	    {
	    	$mrkv_ua_shipping_args = array(
	            "apiKey" => $this->nova_poshta_api->get_api_key(),
	            "modelName" => "Address",
            	"calledMethod" => "getWarehouses",
	            "methodProperties" => array(
	                "CityRef" => $city_ref,
					"FindByString" => (string) $warehouse_name
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
	        		$this->nova_poshta_api->debug_log->add_data_error(__('Error with Recipient Warehouse Ref','mrkv-ua-shipping'));
	        	}
	        	
	        	return '';
	        }
	    }
	}
}