<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_BLOCKS_ORDER'))
{
	/**
	 * Class for setup plugin 
	 */
	class MRKV_UA_SHIPPING_BLOCKS_ORDER
	{
		/**
		 * Constructor for plugin setup
		 * */
		function __construct()
		{
            add_action( 'woocommerce_store_api_checkout_order_processed', [ $this, 'mrkv_clean_active_blocks_meta' ], 10, 1 );
            add_action( 'woocommerce_store_api_checkout_update_order_from_request', [ $this, 'mrkv_blocks_update_order_from_request' ], 10, 2 );

        }

        private function mrkv_get_chosen_shipping_group() {
            $group = '';

            if ( ! empty( $_SERVER['HTTP_X_MRKV_SHIPPING_GROUP'] ) ) {
                $group = sanitize_text_field( $_SERVER['HTTP_X_MRKV_SHIPPING_GROUP'] );
            }

            if ( ! in_array( $group, [ 'shipping', 'billing' ], true ) ) {
                $group = 'shipping';
            }

            return $group;
        }

        public function mrkv_clean_active_blocks_meta( $order ) {
            if ( ! $order instanceof \WC_Order ) {
                return;
            }

            if ( empty( MRKV_UA_SHIPPING_LIST ) || ! is_array( MRKV_UA_SHIPPING_LIST ) ) {
                return;
            }

            $customer_id = $order->get_customer_id();

            $block_prefixes = [
                '_wc_shipping/',
                '_wc_billing/', 
                '_wc_other/',   
                ''              
            ];

            $has_changes = false;

            foreach ( MRKV_UA_SHIPPING_LIST as $group_key => $group_data ) {
                if ( empty( $group_data['method'] ) || ! is_array( $group_data['method'] ) ) {
                    continue;
                }

                $settings = get_option($group_key . '_m_ua_settings');

                $is_enabled_saved_user_data = (isset($settings['checkout']['hide_saving_data']) && $settings['checkout']['hide_saving_data'] == 'on') ? true : false;

                foreach ( $group_data['method'] as $method_slug => $method_data ) {
                    if ( empty( $method_data['checkout_fields'] ) || ! is_array( $method_data['checkout_fields'] ) ) {
                        continue;
                    }

                    foreach ( $method_data['checkout_fields'] as $field_id => $field_val ) {
                        
                        $block_field_id       = 'mrkv-ua-shipping/' . $method_slug . $field_id;
                        $hidden_block_field_id = $block_field_id . '_hidden_val';
                        $legacy_hidden_id      = $method_slug . $field_id . '_hidden_val';
                        $normalized_field_id   = ltrim( $field_id, '_' );
                        $block_field_id_dash   = 'mrkv-ua-shipping/' . $method_slug . '-' . $normalized_field_id;

                        if ( $is_enabled_saved_user_data && $customer_id ) {
                            $val_to_save = '';
                            foreach ( $block_prefixes as $prefix ) {
                                $val_to_save = $order->get_meta( $prefix . $block_field_id );
                                if ( ! empty( $val_to_save ) ) {
                                    break;
                                }
                            }
                            if ( ! empty( $val_to_save ) ) {
                                update_user_meta( $customer_id, 'mrkv_last_' . $method_slug . $field_id, $val_to_save );
                            }

                            $hidden_val_to_save = '';
                            foreach ( $block_prefixes as $prefix ) {
                                $hidden_val_to_save = $order->get_meta( $prefix . $hidden_block_field_id );
                                if ( ! empty( $hidden_val_to_save ) ) {
                                    break;
                                }
                            }
                            if ( ! empty( $hidden_val_to_save ) ) {
                                update_user_meta( $customer_id, 'mrkv_last_' . $method_slug . $field_id . '_hidden_val', $hidden_val_to_save );
                            }
                        }

                        foreach ( $block_prefixes as $prefix ) {
                            $order->delete_meta_data( $prefix . $block_field_id );
                            $order->delete_meta_data( $prefix . $hidden_block_field_id );
                            $order->delete_meta_data( $prefix . $block_field_id_dash );
                            $order->delete_meta_data( $prefix . $block_field_id_dash . '_hidden_val' );
                        }

                        $order->delete_meta_data( $legacy_hidden_id );
                        $has_changes = true;
                    }
                }
            }

            if ( $has_changes ) {
                $order->save();
            }
        }

        public function mrkv_blocks_update_order_from_request( $order, $request ) {
            $billing_address   = $request->get_param( 'billing_address' ) ?? [];
            $shipping_address  = $request->get_param( 'shipping_address' ) ?? [];
            $additional_fields = $request->get_param( 'additional_fields' ) ?? [];

            $chosen_methods = $order->get_shipping_methods();
            $shipping_method_post = '';
            
            if ( ! empty( $chosen_methods ) ) {
                $shipping_method = current( $chosen_methods );
                $shipping_method_post = $shipping_method->get_method_id();
            } else {
                $session_methods = WC()->session ? WC()->session->get( 'chosen_shipping_methods' ) : array();
                $shipping_method_post = ! empty( $session_methods ) ? $session_methods[0] : '';
            }

            if ( empty( $shipping_method_post ) ) {
                return;
            }

            $keys_shipping = array_keys( MRKV_UA_SHIPPING_LIST );
            $key = '';
            $current_shipping = '';
            $has_mrkv_ua_ship = false;

            foreach ( $keys_shipping as $key_ship ) {
                if ( str_contains( $shipping_method_post, $key_ship ) ) {
                    $key = $key_ship;
                    break;
                }
            }

            if ( $key ) {
                foreach ( MRKV_UA_SHIPPING_LIST[ $key ]['method'] as $method_slug => $method_data ) {
                    $clean_shipping_method = preg_replace( '/_\d+$/', '', $shipping_method_post );
                    
                    if ( $clean_shipping_method == $method_slug ) {
                        $current_shipping = $method_slug;
                        $has_mrkv_ua_ship = true; 
                        break;
                    }
                }
            }

            $is_under_methods = false;

            $settings_global = get_option( 'm_ua_active_plugins' );
            if( isset( $settings_global['checkout_block']['fields_under_methods'] ) && $settings_global['checkout_block']['fields_under_methods']  == 'on'){
                $is_under_methods = true;
                break;
            }

            $active_prefix = $has_mrkv_ua_ship ? 'mrkv-ua-shipping/' . $current_shipping : '###';
            
            $all_request_fields = array_unique( array_merge( 
                array_keys( $shipping_address ), 
                array_keys( $billing_address ),
                array_keys( $additional_fields )
            ) );

            foreach ( $all_request_fields as $field_key ) {
                if ( str_starts_with( $field_key, 'mrkv-ua-shipping/' ) ) {
                    if ( ! str_starts_with( $field_key, $active_prefix ) ) {
                        $clean_meta_key = str_replace( 'mrkv-ua-shipping/', '', $field_key );
                        
                        $order->delete_meta_data( $clean_meta_key );
                        $order->delete_meta_data( $clean_meta_key . '_hidden_val' );

                        $order->delete_meta_data( '_wc_shipping/' . $field_key );
                        $order->delete_meta_data( '_wc_billing/' . $field_key );
                        $order->delete_meta_data( '_wc_shipping/' . $field_key . '_hidden_val' );
                        $order->delete_meta_data( '_wc_billing/' . $field_key . '_hidden_val' );
                        
                        $order->delete_meta_data( '_wc_other/' . $field_key );
                        $order->delete_meta_data( '_wc_other/' . $field_key . '_hidden_val' );
                    }
                }
            }

            if ( ! $has_mrkv_ua_ship || ! $current_shipping ) {
                $order->save();
                return;
            }

            $checkout_block_position = ( isset( $settings_global['checkout_block']['position'] ) && $settings_global['checkout_block']['position'] != '' ) ? $settings_global['checkout_block']['position'] : 'order';

            

            if ( $checkout_block_position === 'address' && !$is_under_methods) {
                $group = $this->mrkv_get_chosen_shipping_group();
                if ( $group === 'billing' ) {
                    $search_pool = ! empty( $billing_address ) ? $billing_address : $shipping_address;
                } else {
                    $search_pool = ! empty( $shipping_address ) ? $shipping_address : $billing_address;
                }
            } else {
                $search_pool = $additional_fields;
            }

            $order->set_billing_city( '' );
            $order->set_shipping_city( '' );
            $order->set_billing_state( '' );
            $order->set_shipping_state( '' );
            $order->set_billing_address_1( '' );
            $order->set_shipping_address_1( '' );
            $order->set_billing_address_2( '' );
            $order->set_shipping_address_2( '' );
            $order->set_billing_postcode( '' );
            $order->set_shipping_postcode( '' );

            foreach ( MRKV_UA_SHIPPING_LIST[ $key ]['method'][ $current_shipping ]['checkout_fields'] as $field_id => $field_config ) {
                $block_field_id        = 'mrkv-ua-shipping/' . $current_shipping . $field_id;
                $hidden_block_field_id = $block_field_id . '_hidden_val';

                $field_raw_data = isset( $search_pool[ $block_field_id ] ) ? $search_pool[ $block_field_id ] : null;
                $hidden_raw_data = null;

                if ( isset( $field_config['type'] ) && $field_config['type'] === 'select' ) {
                    $hidden_raw_data = isset( $search_pool[ $hidden_block_field_id ] ) ? $search_pool[ $hidden_block_field_id ] : null;
                }

                $clean_val = '';
                if ( $field_raw_data !== null ) {
                    $field_raw_data = sanitize_text_field( wp_unslash( $field_raw_data ) );
                    $clean_val = str_replace( '\"', '', stripslashes( $field_raw_data ) );
                    $clean_val = str_replace( "\\'", "'", $clean_val );
                }

                $clean_hidden_val = '';
                if ( $hidden_raw_data !== null ) {
                    $hidden_raw_data = sanitize_text_field( wp_unslash( $hidden_raw_data ) );
                    $clean_hidden_val = str_replace( '\"', '', stripslashes( $hidden_raw_data ) );
                    $clean_hidden_val = str_replace( "\\'", "'", $clean_hidden_val );
                }

                if ( $field_raw_data !== null || ! empty( $clean_hidden_val ) ) {
                    
                    $value_for_wc = ( ! empty( $clean_hidden_val ) ) ? $clean_hidden_val : $clean_val;

                    if ( isset( $field_config['replace'] ) && ! empty( $field_config['replace'] ) ) {
                        if ( $field_config['replace'] == '_city' ) {
                            $order->set_billing_city( $value_for_wc );
                            $order->set_shipping_city( $value_for_wc );
                        } 
                        elseif ( $field_config['replace'] == '_state' ) {
                            $order->set_billing_state( esc_attr( $value_for_wc ) );
                            $order->set_shipping_state( esc_attr( $value_for_wc ) );
                        } 
                        elseif ( $field_config['replace'] == '_address_1' ) {
                            $order->set_billing_address_1( esc_attr( $value_for_wc ) );
                            $order->set_shipping_address_1( esc_attr( $value_for_wc ) );
                        } 
                        elseif ( $field_config['replace'] == '_postcode' ) {
                            $order->set_billing_postcode( esc_attr( $value_for_wc ) );
                            $order->set_shipping_postcode( esc_attr( $value_for_wc ) );
                        } 
                        elseif ( $field_config['replace'] == '_address_2' ) {
                            $order->set_billing_address_2( esc_attr( $value_for_wc ) );
                            $order->set_shipping_address_2( esc_attr( $value_for_wc ) );
                        }
                    }

                    if ( $field_raw_data !== null ) {
                        $order->update_meta_data( $current_shipping . $field_id, esc_attr( $clean_val ) );
                    }
                }

                if ( ! empty( $clean_hidden_val ) ) {
                    $order->update_meta_data( $current_shipping . $field_id . '_hidden_val', esc_attr( $clean_hidden_val ) );
                }
            }
            
            $order->save();
        }
    }
}