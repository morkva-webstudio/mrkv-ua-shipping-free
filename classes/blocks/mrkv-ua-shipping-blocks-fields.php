<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_BLOCKS_FIELDS'))
{
	/**
	 * Class for setup plugin 
	 */
	class MRKV_UA_SHIPPING_BLOCKS_FIELDS
	{
		/**
		 * Constructor for plugin setup
		 * */
		function __construct()
		{
            add_action( 'woocommerce_init', [$this, 'mrkv_blocks_register_dynamic_fields'] );
        }

        private static function is_cod_enabled() {
            if ( ! class_exists( 'WooCommerce' ) || ! WC()->payment_gateways ) {
                return false;
            }
            $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
            return isset( $available_gateways['cod'] );
        }

        private function get_active_mrkv_shipping()
        {
            $active_methods = array();
            
            if ( isset( WC()->shipping ) && WC()->shipping ) {
                $shipping_methods = WC()->shipping()->get_shipping_methods();
                foreach ( $shipping_methods as $method ) {
                    if ( isset( $method->enabled ) && $method->enabled === 'yes' ) {
                        $active_methods[] = $method->id;
                    }
                }
            }

            $zones = WC_Shipping_Zones::get_zones();
            $zones[] = array( 'shipping_methods' => ( new WC_Shipping_Zone( 0 ) )->get_shipping_methods() );
            
            foreach ( $zones as $zone ) {
                if ( ! empty( $zone['shipping_methods'] ) ) {
                    foreach ( $zone['shipping_methods'] as $method ) {
                        if ( $method->enabled === 'yes' ) {
                            $active_methods[] = $method->id; 
                        }
                    }
                }
            }
            
            return array_unique( $active_methods );
        }

        public function mrkv_blocks_register_dynamic_fields() {
            if (!empty( $_SERVER['REQUEST_URI'] ) && url_to_postid( $_SERVER['REQUEST_URI'] ) > 0 && wc_get_page_id( 'myaccount' ) == url_to_postid( $_SERVER['REQUEST_URI'] ) ) {
                return;
            }
            if ( is_admin() ) {
                global $pagenow;
                if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) ) {
                    $post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0;
                    if ( $post_id ) {
                        $post = get_post( $post_id );
                        if ( $post && has_block( 'woocommerce/checkout', $post->post_content ) ) {
                            woocommerce_register_additional_checkout_field( array(
                                'id'       => 'mrkv-ua-shipping/timeline',
                                'label'    => __( 'morkva Order Location', 'mrkv-ua-shipping' ),
                                'location' => 'order',
                                'type'     => 'text',
                                'required' => false,
                            ) );
                            return;
                        }
                    }
                }
            }
            if ( ! function_exists( 'woocommerce_register_additional_checkout_field' ) ) return;

            $active_methods = $this->get_active_mrkv_shipping();
            $registered = array();
            $is_under_methods = false;

            $settings_global = get_option( 'm_ua_active_plugins' );

            $is_under_methods = ( isset( $settings_global['checkout_block']['fields_under_methods'] ) && $settings_global['checkout_block']['fields_under_methods']  == 'on') ? true : false;
            $checkout_block_position = (isset($settings_global['checkout_block']['position']) && $settings_global['checkout_block']['position'] != '') ? $settings_global['checkout_block']['position'] : 'order';

            foreach ( MRKV_UA_SHIPPING_LIST as $provider => $provider_data ) {
                $settings = get_option($provider . '_m_ua_settings');
                $is_enabled_patranomic = (isset($settings['checkout']['middlename']['enabled']) && $settings['checkout']['middlename']['enabled'] == 'on') ? true : false;
                $patranomic_position = (isset($settings['checkout']['middlename']['position']) && $settings['checkout']['middlename']['position']) ? $settings['checkout']['middlename']['position'] : 'default';
                
                $is_enabled_saved_user_data = ( isset( $settings['checkout']['hide_saving_data'] ) && $settings['checkout']['hide_saving_data'] == 'on' ) ? true : false;
                
                foreach ( $provider_data['method'] as $method_slug => $method_data ) {
                    
                    if ( ! in_array( $method_slug, $active_methods, true ) ) {
                        continue;
                    }

                    if ( empty( $method_data['checkout_fields'] ) ) continue;

                    foreach ( $method_data['checkout_fields'] as $field_key => $field_data ) {
                        if($provider == 'ukr-poshta' && ! $is_enabled_patranomic && $field_key === '_patronymic' && $method_slug == 'mrkv_ua_shipping_ukr-poshta' && $this->is_cod_enabled())
                        {
                            $is_enabled_patranomic = true;
                        }

                        if ( $field_key === '_patronymic' && ! $is_enabled_patranomic ) {
                            continue;
                        }

                        $block_field_id = 'mrkv-ua-shipping/' . $method_slug . $field_key;

                        if ( in_array( $block_field_id, $registered ) ) continue;

                        if($is_under_methods){
                            $field_location = 'order';
                        }

                        $field_location = $checkout_block_position;
                        if ( $field_key === '_patronymic' && $patranomic_position !== 'default' ) {
                            $field_location = 'address';
                        }

                        $type = ( isset( $field_data['type'] ) && $field_data['type'] === 'select' ) ? 'select' : 'text';
                        
                        $label = isset( $field_data['label'] ) ? $field_data['label'] : '';
                        if ( isset( $field_data['type'] ) && $field_data['type'] === 'hidden' ) {
                            $label = 'hidden'; 
                        }

                        $options = null;
                        if ( $type === 'select' ) {
                            $options = array();
                            if ( ! empty( $field_data['options'] ) ) {
                                foreach ( $field_data['options'] as $opt_val => $opt_label ) {
                                    $options[] = array( 'value' => (string) $opt_val, 'label' => $opt_label );
                                }
                            } else {
                                $options[] = array( 'value' => '', 'label' => '' );
                            }
                        }

                        woocommerce_register_additional_checkout_field( array(
                            'id'       => $block_field_id,
                            'label'    => $label,
                            'location' => $field_location,
                            'type'     => $type,
                            'options'  => $options,
                            'required' => false,
                        ) );

                        add_filter( "woocommerce_get_default_value_for_" . $block_field_id, function( $value, $location, $customer ) use ( $is_enabled_saved_user_data, $method_slug, $field_key ) {
                            if ( ! $customer instanceof \WC_Customer ) {
                                return $value;
                            }
                            $user_id = $customer->get_id();
                            if ( ! $user_id ) {
                                return $value;
                            }
                            
                            if ( $is_enabled_saved_user_data ) {
                                $saved_meta = get_user_meta( $user_id, 'mrkv_last_' . $method_slug . $field_key, true );
                                if ( ! empty( $saved_meta ) ) {
                                    return $saved_meta;
                                }
                            }
                            return $value;
                        }, 10, 3 );

                        $registered[] = $block_field_id;

                        if ( $type === 'select' ) {
                            $hidden_block_field_id = $block_field_id . '_hidden_val';
                            
                            woocommerce_register_additional_checkout_field( array(
                                'id'       => $hidden_block_field_id,
                                'label'    => 'hidden',
                                'location' => $field_location,
                                'type'     => 'text',
                                'required' => false,
                            ) );

                            add_filter( "woocommerce_get_default_value_for_" . $hidden_block_field_id, function( $value, $location, $customer ) use ( $is_enabled_saved_user_data, $method_slug, $field_key ) {
                                if ( ! $customer instanceof \WC_Customer ) {
                                    return $value;
                                }
                                $user_id = $customer->get_id();
                                if ( ! $user_id ) {
                                    return $value;
                                }
                                
                                if ( $is_enabled_saved_user_data ) {
                                    $saved_meta = get_user_meta( $user_id, 'mrkv_last_' . $method_slug . $field_key . '_hidden_val', true );
                                    if ( ! empty( $saved_meta ) ) {
                                        return $saved_meta;
                                    }
                                }
                                return $value;
                            }, 10, 3 );

                            $registered[] = $hidden_block_field_id;
                        }
                    }
                }
            }
        }
    }
}