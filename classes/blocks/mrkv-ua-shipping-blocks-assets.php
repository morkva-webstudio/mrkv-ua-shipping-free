<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_BLOCKS_ASSETS'))
{
	/**
	 * Class for setup plugin 
	 */
	class MRKV_UA_SHIPPING_BLOCKS_ASSETS
	{
		/**
		 * Constructor for plugin setup
		 * */
		function __construct()
		{
            add_action( 'wp_enqueue_scripts', [$this, 'mrkv_enqueue_frontend_checkout_assets'] );
            add_action( 'enqueue_block_editor_assets', [ $this, 'mrkv_enqueue_admin_checkout_assets' ] );
        }

        private function is_poshtomat_active_in_any_instance() {
            $zones = \WC_Shipping_Zones::get_zones();
            
            $default_zone = new \WC_Shipping_Zone( 0 );
            $zones[]      = $default_zone->get_data();

            foreach ( $zones as $zone_data ) {
                $zone = new \WC_Shipping_Zone( $zone_data['id'] ?? 0 );
                $shipping_methods = $zone->get_shipping_methods( true ); 

                foreach ( $shipping_methods as $instance_id => $method ) {
                    if ( $method->id === 'mrkv_ua_shipping_nova-poshta' ) {
                        $settings = get_option( 'woocommerce_mrkv_ua_shipping_nova-poshta_' . $instance_id . '_settings' );
                        $exclude_poshtomat = isset( $settings['exclude_poshtomat'] ) && $settings['exclude_poshtomat'] === 'yes';

                        if ( $exclude_poshtomat ) {
                            return true;
                        }
                    }
                }
            }

            return false;
        }

        private static function is_cod_enabled() {
            if ( ! class_exists( 'WooCommerce' ) || ! WC()->payment_gateways ) {
                return false;
            }
            $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
            return isset( $available_gateways['cod'] );
        }

        private function get_free_methods() {
            $hide_free_methods = [];
            $zones = WC_Shipping_Zones::get_zones();

            foreach ($zones as $zone) {
                foreach ($zone['shipping_methods'] as $method) {
                    if (strpos($method->id, 'mrkv_ua_shipping') !== false) {
                        $enable_cost = $method->get_option('enable_cost', 'no');
                        $enable_fix_cost = $method->get_option('enable_fix_cost', 'no');
                        if ($enable_cost !== 'yes' && $enable_fix_cost !== 'yes') {
                            $hide_free_methods[] = $method->id . '_' . $method->instance_id;
                        }
                    } elseif (strpos($method->id, 'local_pickup') !== false) {
                        $cost = $method->get_option('cost', '');
                        if ($cost === '') {
                            $global_settings = get_option('woocommerce_local_pickup_settings', []);
                            $cost = isset($global_settings['cost']) ? $global_settings['cost'] : get_option('woocommerce_local_pickup_cost', 0);
                        }
                        if ((float)$cost == 0) {
                            if (!empty($method->instance_id)) {
                                $hide_free_methods[] = $method->id . '_' . $method->instance_id;
                            } else {
                                $hide_free_methods[] = $method->id;
                            }
                        }
                    }
                }
            }

            $zone_0 = WC_Shipping_Zones::get_zone(0);
            foreach ($zone_0->get_shipping_methods() as $method) {
                if (strpos($method->id, 'mrkv_ua_shipping') !== false) {
                    $enable_cost = $method->get_option('enable_cost', 'no');
                    $enable_fix_cost = $method->get_option('enable_fix_cost', 'no');
                    if ($enable_cost !== 'yes' && $enable_fix_cost !== 'yes') {
                        $hide_free_methods[] = $method->id . '_' . $method->instance_id;
                    }
                } elseif (strpos($method->id, 'local_pickup') !== false) {
                    $cost = $method->get_option('cost', '');
                    if ($cost === '') {
                        $global_settings = get_option('woocommerce_local_pickup_settings', []);
                        $cost = isset($global_settings['cost']) ? $global_settings['cost'] : get_option('woocommerce_local_pickup_cost', 0);
                    }
                    if ((float)$cost == 0) {
                        if (!empty($method->instance_id)) {
                            $hide_free_methods[] = $method->id . '_' . $method->instance_id;
                        } else {
                            $hide_free_methods[] = $method->id;
                        }
                    }
                }
            }

            return array_unique($hide_free_methods);
        }

        private function get_active_mrkv_providers() {
            $all_providers = array_keys( MRKV_UA_SHIPPING_LIST );
            $active        = array();

            $zones = WC_Shipping_Zones::get_zones();
            foreach ( $zones as $zone ) {
                foreach ( $zone['shipping_methods'] as $method ) {
                    foreach ( $all_providers as $provider ) {
                        if ( str_contains( $method->id, 'mrkv_ua_shipping_' . $provider ) && $method->is_enabled() ) {
                            $active[ $provider ] = true;
                        }
                    }
                }
            }

            $default_zone = new WC_Shipping_Zone( 0 );
            foreach ( $default_zone->get_shipping_methods() as $method ) {
                foreach ( $all_providers as $provider ) {
                    if ( str_contains( $method->id, 'mrkv_ua_shipping_' . $provider ) && $method->is_enabled() ) {
                        $active[ $provider ] = true;
                    }
                }
            }

            return array_keys( $active );
        }

        private function mrkv_load_checkout_resources($is_editor = false) {
            $active_methods = $this->get_active_mrkv_providers();

            if ( empty( $active_methods ) ) {
                return;
            }

            wp_register_script(
                'mrkv-ua-shipping-checkout-blocks-extension',
                MRKV_UA_SHIPPING_ASSETS_URL . '/js/blocks/mrkv-ua-shipping-checkout-blocks-extension.js',
                array(),
                MRKV_UA_SHIPPING_PLUGIN_VERSION,
                true
            );
            wp_enqueue_script( 'mrkv-ua-shipping-checkout-blocks-extension' );

            $providers_locations = [];
            $patranomic_positions = [];
            $mrkv_ua_shipping_method_list = MRKV_UA_SHIPPING_LIST;
            $is_under_methods = false;

            $settings_global = get_option( 'm_ua_active_plugins' );

            $is_under_methods = ( isset( $settings_global['checkout_block']['fields_under_methods'] ) && $settings_global['checkout_block']['fields_under_methods']  == 'on') ? true : false;            

            foreach ( $active_methods as $provider ) {
                $settings = get_option( $provider . '_m_ua_settings' );
                
                $providers_locations[ $provider ] = ( isset( $settings_global['checkout_block']['position'] ) && $settings_global['checkout_block']['position'] != '' ) ? $settings_global['checkout_block']['position'] : 'order';
                
                $patranomic_position = ( isset( $settings['checkout']['middlename']['position'] ) && $settings['checkout']['middlename']['position'] ) ? $settings['checkout']['middlename']['position'] : 'default';
                $patranomic_positions[ $provider ] = $patranomic_position;

                $is_enabled_patranomic = ( isset( $settings['checkout']['middlename']['enabled'] ) && $settings['checkout']['middlename']['enabled']  == 'on') ? true : false;
                $is_required_patranomic = ( isset( $settings['checkout']['middlename']['required'] ) && $settings['checkout']['middlename']['required']  == 'on') ? true : false;

                if($is_enabled_patranomic && $provider == 'ukr-poshta') {
                    $is_required_patranomic = true;
                }

                if ( isset( $mrkv_ua_shipping_method_list[ $provider ]['method'] ) ) {
                    foreach ( $mrkv_ua_shipping_method_list[ $provider ]['method'] as $method_slug => $method_data ) {
                        if ( isset( $method_data['checkout_fields']['_patronymic'] ) ) {
                            if ( ! $is_enabled_patranomic && !($provider == 'ukr-poshta' && $this->is_cod_enabled() )) {
                                unset( $mrkv_ua_shipping_method_list[ $provider ]['method'][ $method_slug ]['checkout_fields']['_patronymic'] );
                            } else {
                                $mrkv_ua_shipping_method_list[ $provider ]['method'][ $method_slug ]['checkout_fields']['_patronymic']['required'] = $is_required_patranomic;
                            }
                        }
                    }
                }
            }

            if($is_under_methods)
            {
                foreach($providers_locations as $provider){
                    $providers_locations[ $provider ] = 'order';
                }
            }

            if ( $this->is_poshtomat_active_in_any_instance() ) {
                if ( isset( $mrkv_ua_shipping_method_list['nova-poshta']['method']['mrkv_ua_shipping_nova-poshta']['checkout_fields']['_warehouse'] ) ) {
                    $mrkv_ua_shipping_method_list['nova-poshta']['method']['mrkv_ua_shipping_nova-poshta']['checkout_fields']['_warehouse']['label'] = __( 'Warehouse', 'mrkv-ua-shipping' );
                }
            }

            wp_localize_script( 'mrkv-ua-shipping-checkout-blocks-extension', 'mrkvShippingConfig', array(
                'list'                 => $mrkv_ua_shipping_method_list,
                'provider_location'    => $providers_locations,
                'patranomic_positions' => $patranomic_positions,
                'required_text'        => __( 'Field', 'mrkv-ua-shipping' ) . ' ' . __( 'is required', 'mrkv-ua-shipping' ) . ':',
                'is_editor'            => $is_editor,
                'is_under_methods'            => $is_under_methods,
                'hide_free_methods' => $this->get_free_methods()
            ) );

            wp_enqueue_style( 'front-mrkv-ua-shipping', MRKV_UA_SHIPPING_ASSETS_URL . '/css/blocks/blocks-mrkv-ua-shipping.css', array(), MRKV_UA_SHIPPING_PLUGIN_VERSION );
            if ( ! wp_style_is( 'select2', 'enqueued' ) && ! wp_style_is( 'selectWoo', 'enqueued' ) ) {
                wp_enqueue_style( 
                    'front-mrkv-ua-shipping-select2', 
                    MRKV_UA_SHIPPING_ASSETS_URL . '/css/global/select2.min.css', 
                    array(), 
                    MRKV_UA_SHIPPING_PLUGIN_VERSION 
                );
            }
            wp_enqueue_script( 'front-mrkv-ua-shipping-select2', MRKV_UA_SHIPPING_ASSETS_URL . '/js/global/select2.min.js', array( 'jquery' ), MRKV_UA_SHIPPING_PLUGIN_VERSION, true );

            $mrkv_ua_shipping_args = array(
                'ajax_url'      => admin_url( 'admin-ajax.php' ),
                'nonce'         => wp_create_nonce( 'mrkv_ua_ship_nonce' ),
                'select2_texts' => array(
                    'errorLoading'   => __('Error loading results.', 'mrkv-ua-shipping'),
                    // translators: %d: number of characters
                    'inputTooLong'   => __('Please delete %d character(s).', 'mrkv-ua-shipping'),
                    // translators: %d: number of characters
                    'inputTooShort'  => __('Please enter %d more character(s).', 'mrkv-ua-shipping'),
                    'loadingMore'    => __('Loading more results...', 'mrkv-ua-shipping'),
                    // translators: %d: number of items
                    'maximumSelected'=> __('You can only select %d item(s).', 'mrkv-ua-shipping'),
                    'noResults'      => __('No results found.', 'mrkv-ua-shipping'),
                    'searching'      => __('Searching...', 'mrkv-ua-shipping'),
                    'removeAllItems' => __('Remove all items', 'mrkv-ua-shipping'),
                )
            );

            foreach ( $active_methods as $method_key ) {
                include MRKV_UA_SHIPPING_PLUGIN_PATH_SHIP . '/' . $method_key . '/checkout/mrkv-ua-shipping-checkout.php';

                wp_enqueue_script( 'front-mrkv-ua-shipping-' . $method_key, MRKV_UA_SHIPPING_ASSETS_URL . '/js/blocks/mrkv-ua-shipping-blocks-' . $method_key . '.js', array( 'jquery', 'jquery-ui-autocomplete', 'front-mrkv-ua-shipping-select2' ), MRKV_UA_SHIPPING_PLUGIN_VERSION, true );
                wp_localize_script( 'front-mrkv-ua-shipping-' . $method_key, 'mrkv_ua_ship_helper', $mrkv_ua_shipping_args );
            }
        }

        private function mrkv_load_cart_resources() {
            wp_enqueue_style( 'front-mrkv-ua-shipping', MRKV_UA_SHIPPING_ASSETS_URL . '/css/blocks/blocks-mrkv-ua-shipping.css', array(), MRKV_UA_SHIPPING_PLUGIN_VERSION );
        }

        public function mrkv_enqueue_frontend_checkout_assets() {
            if ( is_checkout() && \Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils::is_checkout_block_default() ) {
                $this->mrkv_load_checkout_resources(false);
            }
            elseif ( is_cart() && \Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils::is_checkout_block_default() ) {
                $this->mrkv_load_cart_resources();
            }
        }

        public function mrkv_enqueue_admin_checkout_assets() {
            global $post;

            if ( $post && has_block( 'woocommerce/checkout', $post ) ) {
                $this->mrkv_load_checkout_resources(true);
            }
        }
    }
}