<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_BLOCKS_VALIDATION'))
{
	/**
	 * Class for setup plugin 
	 */
	class MRKV_UA_SHIPPING_BLOCKS_VALIDATION
	{
		/**
		 * Constructor for plugin setup
		 * */
		function __construct()
		{
            add_filter( 'rest_pre_dispatch', [$this, 'mrkv_strip_empty_fields_before_validation'], 10, 3 );
            add_action( "woocommerce_blocks_validate_location_address_fields", [$this, 'mrkv_validate_custom_checkout_fields'], 9999, 3 );
            add_action( 'woocommerce_store_api_validate_additional_checkout_field', [ $this, 'mrkv_validate_non_address_additional_fields' ], 10, 3 );  
            add_filter( 'woocommerce_store_api_cart_update_customer_from_request', [ $this, 'mrkv_change_customer_country' ], 10, 2 );
            add_filter( 'woocommerce_cart_shipping_packages', [ $this, 'mrkv_shipping_cost_recalculate' ]);
            add_filter( 'woocommerce_validate_postcode', '__return_true', 9999 );
        }

        private function mrkv_get_chosen_shipping_method() {
            $method = '';

            if ( ! empty( $_SERVER['HTTP_X_MRKV_SHIPPING_METHOD'] ) ) {
                $method = sanitize_text_field( $_SERVER['HTTP_X_MRKV_SHIPPING_METHOD'] );
            } elseif ( isset( WC()->session ) ) {
                $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
                $method = ! empty( $chosen_methods ) ? $chosen_methods[0] : '';
            }

            if ( ! empty( $method ) && strpos( $method, ':' ) !== false ) {
                $method_parts = explode( ':', $method );
                $method = $method_parts[0];
            }

            return $method;
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

        private function mrkv_get_all_fields_map() {
            $all_fields = array();
            if ( ! defined( 'MRKV_UA_SHIPPING_LIST' ) || ! is_array( MRKV_UA_SHIPPING_LIST ) ) {
                return $all_fields;
            }
            foreach ( MRKV_UA_SHIPPING_LIST as $provider => $provider_data ) {
                if ( empty( $provider_data['method'] ) || ! is_array( $provider_data['method'] ) ) {
                    continue;
                }
                foreach ( $provider_data['method'] as $method_slug => $method_data ) {
                    if ( empty( $method_data['checkout_fields'] ) || ! is_array( $method_data['checkout_fields'] ) ) {
                        continue;
                    }
                    foreach ( $method_data['checkout_fields'] as $field_key => $field_data ) {
                        $block_field_id = 'mrkv-ua-shipping/' . $method_slug . $field_key;
                        
                        $all_fields[$block_field_id] = array(
                            'method'        => $method_slug,
                            'real_required' => isset( $field_data['required'] ) ? (bool) $field_data['required'] : true,
                            'label'         => isset( $field_data['label'] ) ? $field_data['label'] : $field_key
                        );
                    }
                }
            }
            return $all_fields;
        }

        private function extract_mrkv_fields_from_payload( $data ) {
            $found = array();

            if ( isset( $data['additional_fields'] ) && is_array( $data['additional_fields'] ) ) {
                foreach ( $data['additional_fields'] as $key => $val ) {
                    if ( strpos( $key, 'mrkv-ua-shipping/' ) === 0 ) {
                        $found[ $key ] = sanitize_text_field( $val );
                    }
                }
            }

            $address_keys = array( 'billing_address', 'shipping_address' );
            foreach ( $address_keys as $a_key ) {
                if ( isset( $data[ $a_key ] ) && is_array( $data[ $a_key ] ) ) {
                    foreach ( $data[ $a_key ] as $key => $val ) {
                        if ( strpos( $key, 'mrkv-ua-shipping/' ) === 0 ) {
                            $found[ $key ] = sanitize_text_field( $val );
                        }
                    }
                }
            }

            if ( isset( $data['requests'] ) && is_array( $data['requests'] ) ) {
                foreach ( $data['requests'] as $request ) {
                    if ( isset( $request['body'] ) && is_array( $request['body'] ) ) {
                        $body = $request['body'];
                        $sub_keys = array( 'billing_address', 'shipping_address', 'additional_fields' );
                        foreach ( $sub_keys as $s_key ) {
                            if ( isset( $body[ $s_key ] ) && is_array( $body[ $s_key ] ) ) {
                                foreach ( $body[ $s_key ] as $key => $val ) {
                                    if ( strpos( $key, 'mrkv-ua-shipping/' ) === 0 ) {
                                            $found[ $key ] = sanitize_text_field( $val );
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return $found;
        }

        public function mrkv_shipping_cost_recalculate( $packages ) {
            $is_block_checkout = false;
            if ( defined( 'REST_REQUEST' ) && REST_REQUEST && isset( $_SERVER['REQUEST_URI'] ) && strpos( $_SERVER['REQUEST_URI'], 'wc/store' ) !== false ) {
                $is_block_checkout = true;
            }

            $stored_fields = array();
            if ( WC()->session ) {
                $stored_fields = WC()->session->get( 'mrkv_checkout_block_fields', array() );
            }

            if ( $is_block_checkout ) {
                $raw_body = file_get_contents( 'php://input' );
                if ( ! empty( $raw_body ) ) {
                    $parsed = json_decode( $raw_body, true );
                    if ( is_array( $parsed ) ) {
                        $extracted = $this->extract_mrkv_fields_from_payload( $parsed );
                        if ( ! empty( $extracted ) ) {
                            $stored_fields = array_merge( $stored_fields, $extracted );
                            if ( WC()->session ) {
                                WC()->session->set( 'mrkv_checkout_block_fields', $stored_fields );
                            }
                        }
                    }
                }
            }

            if ( ! empty( $stored_fields ) ) {
                foreach ( $packages as $key => $package ) {
                    foreach ( $stored_fields as $field_key => $field_value ) {
                        $clean_key = str_replace( 'mrkv-ua-shipping/', '', $field_key );
                        $packages[ $key ]['destination'][ $clean_key ] = $field_value;
                    }
                }
            }

            return $packages;
        }

        public function mrkv_change_customer_country($customer_data, $request){
            $extensions = $request->get_param('extension_data');

            if (!is_array($extensions)) {
                $extensions = [];
            }

            $override_country = $extensions['mrkv_override_country'] ?? null;

            if ($override_country) {
                $customer_data->set_shipping_country($override_country);
                $customer_data->set_billing_country($override_country);
                $customer_data->set_calculated_shipping(false);
            }

            return $customer_data;
        }

        public function mrkv_validate_custom_checkout_fields( $errors, $fields, $group ) {
            if(empty($fields) || !is_array($fields)) {
                return;
            }
            $chosen_method = $this->mrkv_get_chosen_shipping_method();
            if ( empty( $chosen_method ) || ! str_starts_with( $chosen_method, 'mrkv_ua_shipping' ) ) {
                return;
            }

            $active_group = $this->mrkv_get_chosen_shipping_group();

            if ( $group !== $active_group ) {
                return; 
            }

            $provider = '';
            $method_config = null;
            foreach ( MRKV_UA_SHIPPING_LIST as $key => $provider_data ) {
                if ( isset( $provider_data['method'][ $chosen_method ] ) ) {
                    $provider = $key;
                    $method_config = $provider_data['method'][ $chosen_method ];
                    break;
                }
            }

            if ( ! $method_config || ! isset( $method_config['checkout_fields'] ) ) {
                return;
            }

            $checkout_fields = $method_config['checkout_fields'];
            $settings = get_option( $provider . '_m_ua_settings' );
            $settings_global = get_option( 'm_ua_active_plugins' );

            $checkout_block_position = (isset($settings_global['checkout_block']['position']) && $settings_global['checkout_block']['position'] != '') ? $settings_global['checkout_block']['position'] : 'order';

            if ( $checkout_block_position !== 'address' ) {
                return;
            }
            $payment_method = WC()->session ? WC()->session->get( 'chosen_payment_method' ) : '';

            foreach ( $checkout_fields as $field_id => $field_config ) {
                if ( isset( $field_config['type'] ) && $field_config['type'] === 'hidden' ) {
                    continue;
                }

                $key = 'mrkv-ua-shipping/' . $chosen_method . $field_id;
                
                if ( isset( $field_config['type'] ) && $field_config['type'] === 'select' ) {
                    $hidden_key = $key . '_hidden_val';
                    $field_value = isset( $fields[ $hidden_key ] ) ? $fields[ $hidden_key ] : null;
                } else {
                    $field_value = isset( $fields[ $key ] ) ? $fields[ $key ] : null;
                }

                if (
                    $payment_method === 'cod' &&
                    '_patronymic' === $field_id &&
                    ! empty( $field_config['cod_validation'] ) &&
                    ( is_null( $field_value ) || trim( $field_value ) === '' || $field_value === 'off' )
                ) {
                    $errors->add(
                        'mrkv_required_patronymic',
                        sprintf(
                            '%s %s %s',
                            __( 'Field', 'mrkv-ua-shipping' ),
                            $field_config['label'] ?? $field_id,
                            __( 'is required', 'mrkv-ua-shipping' )
                        )
                    );
                    continue;
                }

                $placeholder = '';
                if ( isset( $field_config['options'] ) && is_array( $field_config['options'] ) ) {
                    $placeholder = (string) key( $field_config['options'] );
                }

                $is_empty = is_null( $field_value ) || trim( $field_value ) === '' || $field_value === 'off';
                if ( ! $is_empty && $placeholder !== '' && $field_value === $placeholder ) {
                    $is_empty = true;
                }

                if ( ! $is_empty ) {
                    continue;
                }

                if (
                    '_patronymic' === $field_id && 
                    ((isset( $settings['checkout']['middlename']['required'] ) && 'on' === $settings['checkout']['middlename']['required']) || 
                    ($provider == 'ukr-poshta' && isset( $settings['checkout']['middlename']['enabled'] ) && 'on' === $settings['checkout']['middlename']['enabled']))
                ) {
                    $errors->add(
                        'mrkv_required_patronymic',
                        sprintf(
                            '%s %s %s',
                            __( 'Field', 'mrkv-ua-shipping' ),
                            $field_config['label'] ?? $field_id,
                            __( 'is required', 'mrkv-ua-shipping' )
                        )
                    );
                    continue;
                }

                if ( isset( $field_config['required'] ) && ! $field_config['required'] ) {
                    continue;
                }

                $errors->add(
                    'mrkv_required_' . sanitize_key( $field_id ),
                    sprintf(
                        '%s %s %s',
                        __( 'Field', 'mrkv-ua-shipping' ),
                        $field_config['label'] ?? $field_id,
                        __( 'is required', 'mrkv-ua-shipping' )
                    )
                );
            }
        }

        public function mrkv_strip_empty_fields_before_validation( $result, $server, $request ) {
            if ( strpos( $request->get_route(), '/wc/store/' ) === false ) {
                return $result;
            }

            $chosen_method = $request->get_header( 'X-MRKV-Shipping-Method' );
            if ( $chosen_method && ( strpos( $chosen_method, 'mrkv_ua_shipping' ) === 0 || strpos( $chosen_method, 'local_pickup' ) === 0 ) ) {
                $shipping_address = $request->get_param( 'shipping_address' );
                $billing_address  = $request->get_param( 'billing_address' );

                add_filter( 'woocommerce_get_country_locale_default', function( $fields ) {
                    $fields_to_bypass = array( 'address_1', 'address_2', 'city', 'state', 'postcode' );
                    foreach ( $fields_to_bypass as $field_key ) {
                        if ( isset( $fields[$field_key] ) ) {
                            $fields[$field_key]['required'] = false;
                        }
                    }
                    return $fields;
                }, 9999 );

                add_filter( 'woocommerce_get_country_locale', function( $locales ) {
                    $fields_to_bypass = array( 'address_1', 'city', 'state', 'postcode' );
                    foreach ( $locales as $country => $fields ) {
                        foreach ( $fields_to_bypass as $field_key ) {
                            if ( isset( $locales[$country][$field_key] ) ) {
                                if ( is_array( $locales[$country][$field_key] ) ) {
                                    $locales[$country][$field_key]['required'] = false;
                                }
                            }
                        }
                    }
                    return $locales;
                }, 9999 );
                
                add_filter( 'woocommerce_validate_additional_field', function( $errors, $field_id, $field_value ) use ( $fields_to_remove ) {
                    if ( in_array( $field_id, $fields_to_remove ) ) {
                        $errors->remove( 'woocommerce_required_checkout_field' );
                        $errors->remove( 'woocommerce_invalid_checkout_field' );
                    }
                    return $errors;
                }, 9999, 3 );
            }

            return $result;
        }

        public function mrkv_validate_non_address_additional_fields( $error, $field, $value ) {
            if ( ! str_starts_with( $field['id'], 'mrkv-ua-shipping/' ) ) {
                return;
            }

            $chosen_method = $this->mrkv_get_chosen_shipping_method();
            if ( empty( $chosen_method ) || ! str_starts_with( $chosen_method, 'mrkv_ua_shipping' ) ) {
                return;
            }

            $keys_shipping = array_keys( MRKV_UA_SHIPPING_LIST );
            $key = '';
            $current_shipping = '';
            $provider = '';
            $has_mrkv_ua_ship = false;

            foreach ( $keys_shipping as $key_ship ) {
                if ( str_contains( $chosen_method, $key_ship ) ) {
                    $key = $key_ship;
                    break;
                }
            }

            if ( $key ) {
                foreach ( MRKV_UA_SHIPPING_LIST[ $key ]['method'] as $method_slug => $method_data ) {
                    $clean_shipping_method = preg_replace( '/_\d+$/', '', $chosen_method );
                    if ( $clean_shipping_method == $method_slug ) {
                        $current_shipping = $method_slug;
                        $has_mrkv_ua_ship = true;
                        $provider = $key;
                        break;
                    }
                }
            }

            if ( ! $has_mrkv_ua_ship || ! $current_shipping ) {
                return;
            }

            $method_config = MRKV_UA_SHIPPING_LIST[ $key ]['method'][ $current_shipping ];

            $settings = get_option($provider . '_m_ua_settings');
            $settings_global = get_option( 'm_ua_active_plugins' );
            $checkout_block_position = (isset($settings_global['checkout_block']['position']) && $settings_global['checkout_block']['position'] != '') ? $settings_global['checkout_block']['position'] : 'order';

            if ( $checkout_block_position === 'address' ) {
                return;
            }

            $prefix_to_remove = 'mrkv-ua-shipping/' . $chosen_method;
            $field_suffix = str_replace( $prefix_to_remove, '', $field['id'] );

            $base_field_id = $field_suffix;
            if ( str_ends_with( $field_suffix, '_hidden_val' ) ) {
                $base_field_id = str_replace( '_hidden_val', '', $field_suffix );
            }

            if ( ! isset( $method_config['checkout_fields'][ $base_field_id ] ) ) {
                return;
            }

            $field_config = $method_config['checkout_fields'][ $base_field_id ];
            if ( isset( $field_config['type'] ) && $field_config['type'] === 'hidden' ) {
                return;
            }

            $is_empty = is_null( $value ) || trim( (string) $value ) === '' || $value === 'off';
    
            $placeholder = '';
            if ( isset( $field_config['options'] ) && is_array( $field_config['options'] ) ) {
                $placeholder = (string) key( $field_config['options'] );
            }
            if ( ! $is_empty && $placeholder !== '' && $value === $placeholder ) {
                $is_empty = true;
            }

            $payment_method = WC()->session ? WC()->session->get( 'chosen_payment_method' ) : '';

            if (
                $payment_method === 'cod' &&
                '_patronymic' === $base_field_id &&
                ! empty( $field_config['cod_validation'] ) &&
                $is_empty
            ) {
                $errors->add(
                    'additional_field_invalid',
                    sprintf( '%s %s %s', __( 'Field', 'mrkv-ua-shipping' ), $field_config['label'] ?? $base_field_id, __( 'is required', 'mrkv-ua-shipping' ) )
                );
                return;
            }

            if ( ! $is_empty ) {
                return;
            }

            if (
                '_patronymic' === $base_field_id &&
                ((isset( $settings['checkout']['middlename']['required'] ) &&
                'on' === $settings['checkout']['middlename']['required']) || 
                    ($provider == 'ukr-poshta' && isset( $settings['checkout']['middlename']['enabled'] ) && 'on' === $settings['checkout']['middlename']['enabled']))
            ) {
                $errors->add(
                    'additional_field_invalid',
                    sprintf( '%s %s %s', __( 'Field', 'mrkv-ua-shipping' ), $field_config['label'] ?? $base_field_id, __( 'is required', 'mrkv-ua-shipping' ) )
                );
                return;
            }

            if ( isset( $field_config['required'] ) && ! $field_config['required'] ) {
                return;
            }

            $errors->add(
                'additional_field_invalid',
                sprintf(
                    '%s %s %s',
                    __( 'Field', 'mrkv-ua-shipping' ),
                    $field_config['label'] ?? $base_field_id,
                    __( 'is required', 'mrkv-ua-shipping' )
                )
            );
        }
    }
}