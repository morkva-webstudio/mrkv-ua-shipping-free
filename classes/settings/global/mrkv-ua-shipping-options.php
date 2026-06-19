<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_OPTIONS'))
{
	/**
	 * Class for setup plugin global options
	 */
	class MRKV_UA_SHIPPING_OPTIONS
	{
		/**
		 * Constructor for plugin global options
		 * */
		function __construct()
		{
			# Register settings
			add_action('admin_init', array($this, 'mrkv_ua_shipping_register_settings'));
		}

		/**
		 * Recursively sanitize an array of settings
		 * @param mixed $value
		 * @return mixed
		 */
		public function mrkv_ua_shipping_sanitize_settings( $value ) {
			if ( is_array( $value ) ) {
                return array_map( [ $this, 'mrkv_ua_shipping_sanitize_settings' ], $value );
            }
            return sanitize_text_field( wp_unslash( (string) $value ) );
		}

		/**
		 * Register plugin options
		 * 
		 * */
	    public function mrkv_ua_shipping_register_settings()
	    {
	    	# List of plugin options
	        $options = array(
	            'm_ua_active_plugins',
	        );

	        # Loop of option
	        foreach ($options as $option) 
	        {
	        	# Register option
	            register_setting('mrkv-ua-shipping-settings-group', $option, array('sanitize_callback' => array($this, 'mrkv_ua_shipping_sanitize_settings')));
	        }

	        foreach(MRKV_UA_SHIPPING_LIST as $slug => $shipping)
			{
				# List of plugin options
		        $options = array(
		            $slug . '_m_ua_settings',
		        );

		        # Loop of option
		        foreach ($options as $option) 
		        {
		        	$option_name = $slug . '_m_ua_settings';
			        register_setting(
			            'mrkv-ua-shipping-' . $slug . '-group',
			            $option_name,
			            array(
			                'type'              => 'array',
			                'sanitize_callback' => array($this, 'sanitize_' . str_replace('-', '_', $slug) . '_settings'),
			                'default'           => array(),
			            )
			        );
		        }
			}
	    }

	    public function sanitize_nova_poshta_settings( $input ) 
		{
		    if ( ! current_user_can( 'manage_options' ) ) {
		        return [];
		    }

		    # Get current saved options to preserve untouched sections
		    $current = get_option( 'nova-poshta_m_ua_settings', [] );
		    $output = $current;

		    # --- API Key ---
		    $output['api_key'] = isset( $input['api_key'] ) ? sanitize_text_field( $input['api_key'] ) : '';

		    # --- Sender ---
		    if ( isset( $input['sender'] ) && is_array( $input['sender'] ) ) {
		        $sender = $input['sender'];
		        $output['sender'] = [
		            'ref'            => isset( $sender['ref'] ) ? sanitize_text_field( $sender['ref'] ) : '',
		            'description'    => isset( $sender['description'] ) ? sanitize_textarea_field( $sender['description'] ) : '',
		            'phones'         => isset( $sender['phones'] ) ? sanitize_text_field( $sender['phones'] ) : '',
		            'email'          => isset( $sender['email'] ) ? sanitize_email( $sender['email'] ) : '',
		            'firstname'      => isset( $sender['firstname'] ) ? sanitize_text_field( $sender['firstname'] ) : '',
		            'lastname'       => isset( $sender['lastname'] ) ? sanitize_text_field( $sender['lastname'] ) : '',
		            'middlename'     => isset( $sender['middlename'] ) ? sanitize_text_field( $sender['middlename'] ) : '',
		            'counterparty_ref' => isset( $sender['counterparty_ref'] ) ? sanitize_text_field( $sender['counterparty_ref'] ) : '',
		            'list'           => isset( $sender['list'] ) ? wp_kses_post( $sender['list'] ) : '',
		            'address_type'   => isset( $sender['address_type'] ) ? sanitize_text_field( $sender['address_type'] ) : 'W',
		            'city'           => isset( $sender['city'] ) && is_array( $sender['city'] ) ? [
		                'name' => isset( $sender['city']['name'] ) ? sanitize_text_field( $sender['city']['name'] ) : '',
		                'ref'  => isset( $sender['city']['ref'] ) ? sanitize_text_field( $sender['city']['ref'] ) : '',
		            ] : [],
		            'warehouse'      => isset( $sender['warehouse'] ) && is_array( $sender['warehouse'] ) ? [
		                'name'   => isset( $sender['warehouse']['name'] ) ? sanitize_text_field( $sender['warehouse']['name'] ) : '',
		                'ref'    => isset( $sender['warehouse']['ref'] ) ? sanitize_text_field( $sender['warehouse']['ref'] ) : '',
		                'number' => isset( $sender['warehouse']['number'] ) ? sanitize_text_field( $sender['warehouse']['number'] ) : '',
		            ] : [],
		            'street'         => isset( $sender['street'] ) && is_array( $sender['street'] ) ? [
		                'name'  => isset( $sender['street']['name'] ) ? sanitize_text_field( $sender['street']['name'] ) : '',
		                'ref'   => isset( $sender['street']['ref'] ) ? sanitize_text_field( $sender['street']['ref'] ) : '',
		                'house' => isset( $sender['street']['house'] ) ? sanitize_text_field( $sender['street']['house'] ) : '',
		                'flat'  => isset( $sender['street']['flat'] ) ? sanitize_text_field( $sender['street']['flat'] ) : '',
		            ] : [],
		            'address'        => isset( $sender['address'] ) && is_array( $sender['address'] ) ? [
		                'ref' => isset( $sender['address']['ref'] ) ? sanitize_text_field( $sender['address']['ref'] ) : '',
		            ] : [],
		        ];
		    }

		    # --- Payer (Default Settings) --- NEW SECTION
		    if ( isset( $input['payer'] ) && is_array( $input['payer'] ) ) {
		        $payer = $input['payer'];
		        $output['payer'] = [
		            'delivery' => isset( $payer['delivery'] ) ? sanitize_text_field( $payer['delivery'] ) : 'Recipient',
		            'cash'     => isset( $payer['cash'] ) ? sanitize_text_field( $payer['cash'] ) : 'Recipient',
		        ];
		    } elseif ( ! isset( $output['payer'] ) ) {
		        $output['payer'] = [ 'delivery' => 'Recipient', 'cash' => 'Recipient' ];
		    }

		    # --- Default Addresses (legacy) ---
		    $output['default_addresses'] = isset( $input['default_addresses'] ) ? sanitize_text_field( $input['default_addresses'] ) : '0';

		    # --- Shipment ---
		    if ( isset( $input['shipment'] ) && is_array( $input['shipment'] ) ) { 
		        $shipment = $input['shipment'];

		        $shipment_class = [
			        'enabled' => isset( $shipment['class']['enabled'] )
			            ? sanitize_text_field( $shipment['class']['enabled'] )
			            : 'off',
			        'list' => [],
			    ];

			    if ( isset( $shipment['class']['list'] ) && is_array( $shipment['class']['list'] ) ) {

			        foreach ( $shipment['class']['list'] as $cargo_type => $class_ids ) {

			            if ( ! is_array( $class_ids ) ) {
			                continue;
			            }

			            $shipment_class['list'][ sanitize_text_field( $cargo_type ) ] =
			                array_map( 'intval', $class_ids );
			        }
			    }

		        $output['shipment'] = [
		            'type'            => isset( $shipment['type'] ) ? sanitize_text_field( $shipment['type'] ) : 'Parcel',
		            'payment'         => isset( $shipment['payment'] ) ? sanitize_text_field( $shipment['payment'] ) : 'Cash',
		            'weight'          => isset( $shipment['weight'] ) ? floatval( $shipment['weight'] ) : 0,
		            'length'          => isset( $shipment['length'] ) ? floatval( $shipment['length'] ) : 0,
		            'width'           => isset( $shipment['width'] ) ? floatval( $shipment['width'] ) : 0,
		            'height'          => isset( $shipment['height'] ) ? floatval( $shipment['height'] ) : 0,
		            'volume'          => isset( $shipment['volume'] ) ? floatval( $shipment['volume'] ) : 0,
		            'description'     => isset( $shipment['description'] ) ? sanitize_textarea_field( $shipment['description'] ) : '',
		            'cart_total'      => isset( $shipment['cart_total'] ) ? sanitize_text_field( $shipment['cart_total'] ) : '',
		            'prepayment_type' => isset( $shipment['prepayment_type'] ) ? sanitize_text_field( $shipment['prepayment_type'] ) : 'value_total',
		            'prepayment'      => isset( $shipment['prepayment'] ) ? sanitize_text_field( $shipment['prepayment'] ) : '',
		            'class'           => $shipment_class,
		        ];
		    }

		    # --- International (inter) ---
		    if ( isset( $input['inter'] ) && is_array( $input['inter'] ) ) {
		        $inter = $input['inter'];
		        $output['inter'] = [
		            'payer'            => isset( $inter['payer'] ) ? sanitize_text_field( $inter['payer'] ) : '',
		            'cart_total'       => isset( $inter['cart_total'] ) ? sanitize_text_field( $inter['cart_total'] ) : '',
		            'division_address' => isset( $inter['division_address'] ) ? sanitize_text_field( $inter['division_address'] ) : '',
		            'division_id'      => isset( $inter['division_id'] ) ? sanitize_text_field( $inter['division_id'] ) : '',
		            'division_number'  => isset( $inter['division_number'] ) ? sanitize_text_field( $inter['division_number'] ) : '',
		            'shipment_type'    => isset( $inter['shipment_type'] ) ? sanitize_text_field( $inter['shipment_type'] ) : 'Parcel',
		            'weight'           => isset( $inter['weight'] ) ? floatval( $inter['weight'] ) : 0,
		            'length'           => isset( $inter['length'] ) ? floatval( $inter['length'] ) : 0,
		            'width'            => isset( $inter['width'] ) ? floatval( $inter['width'] ) : 0,
		            'height'           => isset( $inter['height'] ) ? floatval( $inter['height'] ) : 0,
		            'volume'           => isset( $inter['volume'] ) ? floatval( $inter['volume'] ) : 0,
		        ];
		    }

		    # --- Automation --- (FIXED structure to match form)
		    if ( isset( $input['automation'] ) && is_array( $input['automation'] ) ) {
		        $automation = $input['automation'];
		        $output['automation'] = [
		            'payment_control' => isset( $automation['payment_control'] ) ? sanitize_text_field( $automation['payment_control'] ) : 'off',
		            'autocreate'      => [
		                'enabled' => isset( $automation['autocreate']['enabled'] ) ? sanitize_text_field( $automation['autocreate']['enabled'] ) : 'off',
		            ],
		            'status'          => [
		                'enabled' => isset( $automation['status']['enabled'] ) ? sanitize_text_field( $automation['status']['enabled'] ) : 'off',
		            ],
		        ];
		    } elseif ( ! isset( $output['automation'] ) ) {
		        # Default automation structure if never saved
		        $output['automation'] = [
		            'payment_control' => 'off',
		            'autocreate'      => [ 'enabled' => 'off' ],
		            'status'          => [ 'enabled' => 'off' ],
		        ];
		    }

		    # --- Checkout --- (improved middlename sub‑keys)
		    if ( isset( $input['checkout'] ) && is_array( $input['checkout'] ) ) {
		        $checkout = $input['checkout'];
		        $middlename = isset( $checkout['middlename'] ) && is_array( $checkout['middlename'] ) ? $checkout['middlename'] : [];
		        $output['checkout'] = [
		            'position'         => isset( $checkout['position'] ) ? sanitize_text_field( $checkout['position'] ) : '',
		            'middlename'       => [
		                'enabled'  => isset( $middlename['enabled'] ) ? sanitize_text_field( $middlename['enabled'] ) : 'off',
		                'required' => isset( $middlename['required'] ) ? sanitize_text_field( $middlename['required'] ) : 'off',
		                'position' => isset( $middlename['position'] ) ? sanitize_text_field( $middlename['position'] ) : '',
		            ],
		            'hide_saving_data' => isset( $checkout['hide_saving_data'] ) ? sanitize_text_field( $checkout['hide_saving_data'] ) : 'off',
		        ];
		    }

		    # --- Debug ---
		    if ( isset( $input['debug'] ) && is_array( $input['debug'] ) ) {
		        $debug = $input['debug'];
		        $output['debug'] = [
		            'log'   => isset( $debug['log'] ) ? sanitize_text_field( $debug['log'] ) : 'off',
		            'query' => isset( $debug['query'] ) ? sanitize_text_field( $debug['query'] ) : 'off',
		        ];
		    }

		    # --- Internal (international API) ---
		    $output['internal_api_server'] = isset( $input['internal_api_server'] ) ? sanitize_text_field( $input['internal_api_server'] ) : 'production';
		    $output['internal_api_key']    = isset( $input['internal_api_key'] ) ? sanitize_text_field( $input['internal_api_key'] ) : '';

		    # --- Email --- (added 'auto' checkbox)
		    if ( isset( $input['email'] ) && is_array( $input['email'] ) ) {
		        $email = $input['email'];
		        $output['email'] = [
		            'subject' => isset( $email['subject'] ) ? sanitize_text_field( $email['subject'] ) : '',
		            'content' => isset( $email['content'] ) ? sanitize_textarea_field( $email['content'] ) : '',
		            'auto'    => isset( $email['auto'] ) ? sanitize_text_field( $email['auto'] ) : 'off', // NEW
		        ];
		    }

		    $output = apply_filters('mrkv_ua_shipping_option_serialize', $output, 'nova-poshta', $input );

		    return $output;
		}


	    public function sanitize_ukr_poshta_settings( $input ) 
	    {
		    if ( ! current_user_can( 'manage_options' ) ) {
		        return [];
		    }

		    # Get current saved options to preserve unsent sections
		    $current = get_option( 'ukr-poshta_m_ua_settings', [] );
		    $output = $current;

		    # --- API keys & test mode ---
		    $output['production_bearer_ecom']               = isset( $input['production_bearer_ecom'] ) ? sanitize_text_field( $input['production_bearer_ecom'] ) : '';
		    $output['production_bearer_status_tracking']    = isset( $input['production_bearer_status_tracking'] ) ? sanitize_text_field( $input['production_bearer_status_tracking'] ) : '';
		    $output['production_cp_token']                  = isset( $input['production_cp_token'] ) ? sanitize_text_field( $input['production_cp_token'] ) : '';

		    # Test mode checkbox (explicit default)
		    $output['test_mode'] = isset( $input['test_mode'] ) ? sanitize_text_field( $input['test_mode'] ) : 'off';

		    # Test (sandbox) keys
		    $output['test_production_bearer_ecom']          = isset( $input['test_production_bearer_ecom'] ) ? sanitize_text_field( $input['test_production_bearer_ecom'] ) : '';
		    $output['test_production_bearer_status_tracking'] = isset( $input['test_production_bearer_status_tracking'] ) ? sanitize_text_field( $input['test_production_bearer_status_tracking'] ) : '';
		    $output['test_production_cp_token']             = isset( $input['test_production_cp_token'] ) ? sanitize_text_field( $input['test_production_cp_token'] ) : '';

		    # --- Sender settings ---
		    if ( isset( $input['sender'] ) && is_array( $input['sender'] ) ) {
		        $sender = $input['sender'];
		        $output['sender'] = [
		            'type'      => isset( $sender['type'] ) ? sanitize_text_field( $sender['type'] ) : '',
		            'warehouse' => isset( $sender['warehouse'] ) && is_array( $sender['warehouse'] ) ? [
		                'name' => isset( $sender['warehouse']['name'] ) ? sanitize_text_field( $sender['warehouse']['name'] ) : '',
		                'id'   => isset( $sender['warehouse']['id'] ) ? sanitize_text_field( $sender['warehouse']['id'] ) : '',
		            ] : [],
		            'individual' => isset( $sender['individual'] ) && is_array( $sender['individual'] ) ? [
		                'lastname'   => isset( $sender['individual']['lastname'] ) ? sanitize_text_field( $sender['individual']['lastname'] ) : '',
		                'name'       => isset( $sender['individual']['name'] ) ? sanitize_text_field( $sender['individual']['name'] ) : '',
		                'middlename' => isset( $sender['individual']['middlename'] ) ? sanitize_text_field( $sender['individual']['middlename'] ) : '',
		                'phone'      => isset( $sender['individual']['phone'] ) ? sanitize_text_field( $sender['individual']['phone'] ) : '',
		            ] : [],
		        ];
		    }

		    # --- Payer (domestic) ---
		    if ( isset( $input['payer'] ) && is_array( $input['payer'] ) ) {
		        $payer = $input['payer'];
		        $output['payer'] = [
		            'delivery' => isset( $payer['delivery'] ) ? sanitize_text_field( $payer['delivery'] ) : 'Recipient',
		            'cash'     => isset( $payer['cash'] ) ? sanitize_text_field( $payer['cash'] ) : 'Recipient',
		        ];
		    } elseif ( ! isset( $output['payer'] ) ) {
		        $output['payer'] = [ 'delivery' => 'Recipient', 'cash' => 'Recipient' ];
		    }

		    # --- Shipment (domestic) ---
		    if ( isset( $input['shipment'] ) && is_array( $input['shipment'] ) ) {
		        $shipment = $input['shipment'];
		        $output['shipment'] = [
		            'type'        => isset( $shipment['type'] ) ? sanitize_text_field( $shipment['type'] ) : 'STANDARD',
		            'weight'      => isset( $shipment['weight'] ) ? floatval( $shipment['weight'] ) : 0,
		            'length'      => isset( $shipment['length'] ) ? floatval( $shipment['length'] ) : 0,
		            'width'       => isset( $shipment['width'] ) ? floatval( $shipment['width'] ) : 0,
		            'height'      => isset( $shipment['height'] ) ? floatval( $shipment['height'] ) : 0,
		            'sticker'     => isset( $shipment['sticker'] ) ? sanitize_text_field( $shipment['sticker'] ) : '100*100',
		            'cart_total'  => isset( $shipment['cart_total'] ) ? sanitize_text_field( $shipment['cart_total'] ) : '',
		            'description' => isset( $shipment['description'] ) ? sanitize_textarea_field( $shipment['description'] ) : '',
		        ];
		    }

		    # --- International (Pro only – but sanitize anyway) ---
		    if ( isset( $input['international'] ) && is_array( $input['international'] ) ) {
		        $inter = $input['international'];
		        $output['international'] = [
		            // Sender contact
		            'name'          => isset( $inter['name'] ) ? sanitize_text_field( $inter['name'] ) : '',
		            'lastname'      => isset( $inter['lastname'] ) ? sanitize_text_field( $inter['lastname'] ) : '',
		            'city'          => isset( $inter['city'] ) ? sanitize_text_field( $inter['city'] ) : '',
		            'street'        => isset( $inter['street'] ) ? sanitize_text_field( $inter['street'] ) : '',
		            'house'         => isset( $inter['house'] ) ? sanitize_text_field( $inter['house'] ) : '',
		            'index'         => isset( $inter['index'] ) ? sanitize_text_field( $inter['index'] ) : '',
		            'phone'         => isset( $inter['phone'] ) ? sanitize_text_field( $inter['phone'] ) : '',
		            // Payer
		            'payer'         => isset( $inter['payer'] ) && is_array( $inter['payer'] ) ? [
		                'delivery' => isset( $inter['payer']['delivery'] ) ? sanitize_text_field( $inter['payer']['delivery'] ) : 'Recipient',
		                'cash'     => isset( $inter['payer']['cash'] ) ? sanitize_text_field( $inter['payer']['cash'] ) : 'Recipient',
		            ] : [ 'delivery' => 'Recipient', 'cash' => 'Recipient' ],
		            // Shipment params
		            'type'          => isset( $inter['type'] ) ? sanitize_text_field( $inter['type'] ) : '',
		            'category'      => isset( $inter['category'] ) ? sanitize_text_field( $inter['category'] ) : '',
		            'weight'        => isset( $inter['weight'] ) ? floatval( $inter['weight'] ) : 0,
		            'length'        => isset( $inter['length'] ) ? floatval( $inter['length'] ) : 0,
		            'global_hscode' => isset( $inter['global_hscode'] ) ? sanitize_text_field( $inter['global_hscode'] ) : '',
		            'hscode_attr'   => isset( $inter['hscode_attr'] ) ? sanitize_text_field( $inter['hscode_attr'] ) : '',
		            'track'         => isset( $inter['track'] ) ? sanitize_text_field( $inter['track'] ) : 'off',
		            'bulky'         => isset( $inter['bulky'] ) ? sanitize_text_field( $inter['bulky'] ) : 'off',
		            'air'           => isset( $inter['air'] ) ? sanitize_text_field( $inter['air'] ) : 'off',
		            'courier'       => isset( $inter['courier'] ) ? sanitize_text_field( $inter['courier'] ) : 'off',
		            'sms'           => isset( $inter['sms'] ) ? sanitize_text_field( $inter['sms'] ) : 'off',
		            'sticker'       => isset( $inter['sticker'] ) ? sanitize_text_field( $inter['sticker'] ) : 'cp71',
		            'description'   => isset( $inter['description'] ) ? sanitize_textarea_field( $inter['description'] ) : '',
		        ];
		    }

		    # --- Email ---
		    if ( isset( $input['email'] ) && is_array( $input['email'] ) ) {
		        $email = $input['email'];
		        $output['email'] = [
		            'subject' => isset( $email['subject'] ) ? sanitize_text_field( $email['subject'] ) : '',
		            'auto'    => isset( $email['auto'] ) ? sanitize_text_field( $email['auto'] ) : 'off',
		            'content' => isset( $email['content'] ) ? sanitize_textarea_field( $email['content'] ) : '',
		        ];
		    }

		    # --- Automation ---
		    if ( isset( $input['automation'] ) && is_array( $input['automation'] ) ) {
		        $automation = $input['automation'];
		        $output['automation'] = [
		            'autocreate' => [
		                'enabled' => isset( $automation['autocreate']['enabled'] ) ? sanitize_text_field( $automation['autocreate']['enabled'] ) : 'off',
		            ],
		        ];
		    } elseif ( ! isset( $output['automation'] ) ) {
		        $output['automation'] = [ 'autocreate' => [ 'enabled' => 'off' ] ];
		    }

		    # --- Checkout ---
		    if ( isset( $input['checkout'] ) && is_array( $input['checkout'] ) ) {
		        $checkout = $input['checkout'];
		        $middlename = isset( $checkout['middlename'] ) && is_array( $checkout['middlename'] ) ? $checkout['middlename'] : [];
		        $output['checkout'] = [
		            'position'         => isset( $checkout['position'] ) ? sanitize_text_field( $checkout['position'] ) : '',
		            'middlename'       => [
		                'enabled'  => isset( $middlename['enabled'] ) ? sanitize_text_field( $middlename['enabled'] ) : 'off',
		                'required' => isset( $middlename['required'] ) ? sanitize_text_field( $middlename['required'] ) : 'off',
		                'position' => isset( $middlename['position'] ) ? sanitize_text_field( $middlename['position'] ) : '',
		            ],
		            'hide_saving_data' => isset( $checkout['hide_saving_data'] ) ? sanitize_text_field( $checkout['hide_saving_data'] ) : 'off',
		        ];
		    }

		    # --- Debug ---
		    if ( isset( $input['debug'] ) && is_array( $input['debug'] ) ) {
		        $debug = $input['debug'];
		        $output['debug'] = [
		            'log'   => isset( $debug['log'] ) ? sanitize_text_field( $debug['log'] ) : 'off',
		            'query' => isset( $debug['query'] ) ? sanitize_text_field( $debug['query'] ) : 'off',
		        ];
		    }

		    $output = apply_filters('mrkv_ua_shipping_option_serialize', $output, 'ukr-poshta', $input );

		    return $output;
		}

	    public function sanitize_rozetka_delivery_settings( $input ) 
	    {
		    if ( ! current_user_can( 'manage_options' ) ) {
		        return [];
		    }

		    # Preserve existing settings for any undeclared sections
		    $current = get_option( 'rozetka-delivery_m_ua_settings', [] );
		    $output = $current;

		    # --- Checkout settings ---
		    if ( isset( $input['checkout'] ) && is_array( $input['checkout'] ) ) {
		        $checkout = $input['checkout'];
		        $output['checkout'] = [
		            'position'          => isset( $checkout['position'] ) ? sanitize_text_field( $checkout['position'] ) : '',
		            'hide_saving_data'  => isset( $checkout['hide_saving_data'] ) ? sanitize_text_field( $checkout['hide_saving_data'] ) : 'off',
		        ];
		    }

		    # --- Debug settings ---
		    if ( isset( $input['debug'] ) && is_array( $input['debug'] ) ) {
		        $debug = $input['debug'];
		        $output['debug'] = [
		            'log'   => isset( $debug['log'] ) ? sanitize_text_field( $debug['log'] ) : 'off',
		            'query' => isset( $debug['query'] ) ? sanitize_text_field( $debug['query'] ) : 'off',
		        ];
		    }

		    $output = apply_filters('mrkv_ua_shipping_option_serialize', $output, 'rozetka-delivery', $input );

		    return $output;
		}


	    public function sanitize_nova_global_settings( $input ) {
	        if ( ! current_user_can( 'manage_options' ) ) {
	            return [];
	        }

	        // Preserve existing settings for any undeclared sections
	        $current = get_option( 'nova-global_m_ua_settings', [] );
	        $output = $current;

	        // --- Basic settings (API credentials) ---
	        $output['production_username'] = isset( $input['production_username'] ) ? sanitize_text_field( $input['production_username'] ) : '';
	        $output['production_password'] = isset( $input['production_password'] ) ? sanitize_text_field( $input['production_password'] ) : '';

	        // Test mode checkbox
	        $output['test_mode'] = isset( $input['test_mode'] ) ? sanitize_text_field( $input['test_mode'] ) : 'off';

	        // Sandbox credentials
	        $output['test_username'] = isset( $input['test_username'] ) ? sanitize_text_field( $input['test_username'] ) : '';
	        $output['test_password'] = isset( $input['test_password'] ) ? sanitize_text_field( $input['test_password'] ) : '';

	        // --- Shipment default values ---
	        if ( isset( $input['shipment'] ) && is_array( $input['shipment'] ) ) {
	            $shipment = $input['shipment'];
	            $output['shipment'] = [
	                'weight' => isset( $shipment['weight'] ) ? floatval( $shipment['weight'] ) : 0,
	                'length' => isset( $shipment['length'] ) ? floatval( $shipment['length'] ) : 0,
	                'width'  => isset( $shipment['width'] ) ? floatval( $shipment['width'] ) : 0,
	                'height' => isset( $shipment['height'] ) ? floatval( $shipment['height'] ) : 0,
	                'volume' => isset( $shipment['volume'] ) ? floatval( $shipment['volume'] ) : 0,
	            ];
	        }

	        // --- Checkout settings ---
	        if ( isset( $input['checkout'] ) && is_array( $input['checkout'] ) ) {
	            $checkout = $input['checkout'];
	            $output['checkout'] = [
	                'position'         => isset( $checkout['position'] ) ? sanitize_text_field( $checkout['position'] ) : '',
	                'hide_saving_data' => isset( $checkout['hide_saving_data'] ) ? sanitize_text_field( $checkout['hide_saving_data'] ) : 'off',
	            ];
	        }

	        // --- Debug settings ---
	        if ( isset( $input['debug'] ) && is_array( $input['debug'] ) ) {
	            $debug = $input['debug'];
	            $output['debug'] = [
	                'log'   => isset( $debug['log'] ) ? sanitize_text_field( $debug['log'] ) : 'off',
	                'query' => isset( $debug['query'] ) ? sanitize_text_field( $debug['query'] ) : 'off',
	            ];
	        }

	        $output = apply_filters('mrkv_ua_shipping_option_serialize', $output, 'nova-global', $input );

	        return $output;
	    }
	}
}