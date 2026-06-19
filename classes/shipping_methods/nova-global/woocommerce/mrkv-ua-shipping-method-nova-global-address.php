<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_NOVA_GLOBAL_ADDRESS'))
{
    /**
     * Class for setup rozetka delivery method
     */
    class MRKV_UA_SHIPPING_NOVA_GLOBAL_ADDRESS extends WC_Shipping_Method
    {
        public function __construct($mrkv_ua_shipping_instance_id = 0)
        {
            # Set instance id
            $this->instance_id = absint( $mrkv_ua_shipping_instance_id );
            parent::__construct( $mrkv_ua_shipping_instance_id );

            # Set main fields
            $this->id = 'mrkv_ua_shipping_nova-global_address';
            $this->method_title = __( 'Nova Global Address', 'mrkv-ua-shipping' );
            $this->method_description = $this->get_description();

            # Set supports
            $this->supports = array(
                    'shipping-zones',
                    'instance-settings',
                    'instance-settings-modal',
                );

            $this->init();

            # Get setting values
            $this->title = $this->get_option( 'title' );
            $this->enabled = $this->get_option('enabled');
            if (empty($this->enabled)) {
                $this->enabled = 'yes';
            }
        }

        /**
         * Init your settings
         *
         * @access public
         * @return void
         */
        function init()
        {
            $this->init_form_fields();
            $this->init_settings();
            
            # Save settings in admin if you have any defined
            add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
        }

        /**
         * Initialise Gateway Settings Form Fields
         */
        public function init_form_fields()
        {
            $this->instance_form_fields = array(
                'title' => array(
                    'title' => __('This controls the title which the user sees during checkout', 'mrkv-ua-shipping'),
                    'type' => 'text',
                    'description' => '',
                    'default' => __('Nova Global Address', 'mrkv-ua-shipping')
                ),
                'shipping_type' => array(
                    'title'       => __('Shipping type', 'mrkv-ua-shipping'),
                    'type'        => 'select',
                    'description' => '',
                    'default'     => 'Parcel',
                    'options'     => array(
                        'Parcel' => __('Parcel', 'mrkv-ua-shipping'),
                        'Document'  => __('Document', 'mrkv-ua-shipping'),
                    ),
                ),
                'enable_cost' => array(
                    'title' => __('Enable Price for Delivery', 'mrkv-ua-shipping'),
                    'label' => __('If checked, shipping price will be add for delivery', 'mrkv-ua-shipping'),
                    'type' => 'checkbox',
                    'default' => 'no',
                    'description' => '',
                ),
                'enable_fix_cost' => array(
                    'title' => __('Enable Fixed Price for Delivery', 'mrkv-ua-shipping'),
                    'label' => __('If checked, fixed price will be set for delivery', 'mrkv-ua-shipping'),
                    'type' => 'checkbox',
                    'default' => 'no',
                    'description' => '',
                ),
                'fix_cost_total' => array(
                    'title' => __('Fixed shipping price', 'mrkv-ua-shipping'),
                    'type' => 'text',
                    'placeholder' => __('Enter the amount in numbers', 'mrkv-ua-shipping'),
                    'description' => '',
                    'default' => 0.00
                ),
                'enable_minimum_cost' => array(
                    'title' => __('Enable Minimum amount for free shipping', 'mrkv-ua-shipping'),
                    'label' => __('If checked, Minimum amount for free shipping will be set for delivery', 'mrkv-ua-shipping'),
                    'type' => 'checkbox',
                    'default' => 'no',
                    'description' => '',
                ),
                'minimum_cost_total' => array(
                    'title' => __('Minimum amount for free shipping', 'mrkv-ua-shipping'),
                    'type' => 'text',
                    'placeholder' => __('Enter the amount in numbers', 'mrkv-ua-shipping'),
                    'description' => '',
                    'default' => 0.00
                ),
                'free_shipping_text' => array(
                    'title' => __('Text with free delivery', 'mrkv-ua-shipping'),
                    'type' => 'text',
                    'placeholder' => __('FREE to Nova Global Address', 'mrkv-ua-shipping'),
                    'description' => '',
                ),
                'method_description' => array(
                    'title' => __('Description', 'mrkv-ua-shipping'),
                    'type' => 'textarea',
                    'description' => __('Enter a description for this shipping method to display on the cart and checkout pages.', 'mrkv-ua-shipping'),
                    'default' => '',
                    'css' => 'width: 100%; max-width: 100%; box-sizing: border-box;'
                )
            );
        }

        /**
         * calculate_shipping function.
         *
         * @access public
         *
         * @param array $package
         */
        public function calculate_shipping($package = array())
        {
            # Create rate
            $rate = array(
                'id' => $this->id . '_' . $this->instance_id,
                'label' => $this->title,
                'cost' => 0.00,
                'calc_tax' => 'per_item'
            );

            $should_calculate = true;
    
            if (WC()->session) {
                $is_on_product_page = WC()->session->get('mrkv_is_on_product_page');
                
                if ($is_on_product_page === true) {
                    $should_calculate = false;
                }
            }

            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $action = isset( $_REQUEST['action'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) : '';

            if ( 'woocommerce_update_order_review' === $action ) {
                $should_calculate = true;
            }

            $should_calculate = apply_filters('mrkv_ua_shipping_calculation_condtion', $should_calculate, $package);

            if (!$should_calculate) {
                $this->add_rate($rate);
                return;
            }

            if($this->get_option('enable_cost') && $this->get_option('enable_cost') == 'yes' && $this->get_option('enable_fix_cost') != 'yes')
            {
                $country = 'PL';
                $weight = 0.00;
                $volume_weight = 0.00;
                $cost = 0.00;
                $shipping_type = 'parcel';
                
                // phpcs:ignore WordPress.Security.NonceVerification.Missing
                if(isset( $_POST['post_data'] ))
                {
                    // phpcs:ignore WordPress.Security.NonceVerification.Missing
                    $raw_post_data = sanitize_text_field( wp_unslash( $_POST['post_data'] ) );
                    $post_data = array();
                    parse_str( $raw_post_data, $post_data );
                    
                    if(isset($post_data['billing_country']) && $post_data['billing_country'])
                    {
                        $country = sanitize_text_field( $post_data['billing_country'] );
                    }
                }

                if(is_cart() && !$country)
                {
                    $country = WC()->customer->get_shipping_country();
                }

                $dimension_unit = get_option( 'woocommerce_dimension_unit' );

                foreach(WC()->cart->get_cart() as $cart_item => $cart_value)
                {
                    $quantity    = $cart_value['quantity'];
                    
                    $item_length = ( null !== $cart_value['data']->get_length() && $cart_value['data']->get_length()) ? wc_get_dimension( $cart_value['data']->get_length(), 'cm', $dimension_unit ) : 0.00;
                    $item_width = ( null !== $cart_value['data']->get_width() && $cart_value['data']->get_width()) ? wc_get_dimension( $cart_value['data']->get_width(), 'cm', $dimension_unit ) : 0.00;
                    $item_height = ( null !== $cart_value['data']->get_height() && $cart_value['data']->get_height()) ? wc_get_dimension( $cart_value['data']->get_height(), 'cm', $dimension_unit ) : 0.00;

                    $volume_weight += ($item_length * $item_width * $item_height / 5000) * $quantity;
                }

                $settings_method = get_option('nova-global_m_ua_settings');

                if((!$volume_weight) && isset($settings_method['shipment']['volume']) && $settings_method['shipment']['volume'])
                {
                    $volume_weight = floatval($settings_method['shipment']['volume']);
                }

                $weight_coef = $this->convert_weight_unit();
                $weight = ( WC()->cart->cart_contents_weight > 0 ) ? WC()->cart->cart_contents_weight * $weight_coef : 0.00;

                if((!$weight) && isset($settings_method['shipment']['weight']) && $settings_method['shipment']['weight'])
                {
                    $weight = floatval($settings_method['shipment']['weight']);
                }

                if(($weight && $weight > 5) || !$weight)
                {
                    $weight = $volume_weight;
                }

                if($this->get_option('shipping_type'))
                {
                    $shipping_type = strtolower($this->get_option('shipping_type'));
                }

                require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-global/constants/mrkv-ua-shipping-nova-global-ship-constant.php';

                $new_cost = 0.00;

                if(isset(MRKV_UA_SHIPPING_GLOBAL_EXCLUDE[$country]))
                {
                    if(isset(MRKV_UA_SHIPPING_GLOBAL_EXCLUDE[$country]['costs'][$shipping_type]))
                    {
                        $price_range = MRKV_UA_SHIPPING_GLOBAL_EXCLUDE[$country]['costs'][$shipping_type];
                        $new_cost = intval($this->getShippingCost($weight, $price_range));
                    }
                }
                else
                {
                    $zoneId = $this->getShippingZoneId($country, MRKV_UA_SHIPPING_GLOBAL_SHIPPING_ZONE);

                    if($zoneId)
                    {
                        $price_range = MRKV_UA_SHIPPING_GLOBAL_SHIPPING_ZONE[$zoneId]['costs'][$shipping_type];
                        $new_cost = intval($this->getShippingCost($weight, $price_range));
                    }
                }

                if (in_array($country, MRKV_UA_SHIPPING_GLOBAL_ADDRESS['countries']))
                {
                    if($weight > 30)
                    {
                        $range = ceil($weight / 100);
                        $new_cost += $range * intval(MRKV_UA_SHIPPING_GLOBAL_ADDRESS['costs']['30+']);

                    }
                    else
                    {
                        $new_cost += intval(MRKV_UA_SHIPPING_GLOBAL_ADDRESS['costs']['30']);
                    }
                }

                if($new_cost && $new_cost > 0)
                {
                    $rate['cost'] = $new_cost;
                }
                else
                {
                    $rate['cost'] = $cost;
                }
            }

            if($this->get_option('enable_fix_cost') && $this->get_option('enable_fix_cost') == 'yes')
            {
                $rate['cost'] = $this->get_option('fix_cost_total');
            }

            if($this->get_option('enable_minimum_cost') && $this->get_option('enable_minimum_cost') == 'yes')
            {
                $woo_cart_total = WC()->cart->get_subtotal();

                if($woo_cart_total >= $this->get_option('minimum_cost_total'))
                {
                    $rate['cost'] = 0.00;

                    if($this->get_option('free_shipping_text'))
                    {
                        $rate['label'] = $this->get_option('free_shipping_text');
                    }
                }
            }

            # Set rate
            $this->add_rate($rate);
        }

        /**
         * Is this method available?
         * @param array $package
         * @return bool
         */
        public function is_available($package)
        {
            return $this->is_enabled();
        }

        /**
         * @return string
         */
        private function get_description()
        {
            return '';
        }

        public function convert_weight_unit() {

            $weight_unit = get_option('woocommerce_weight_unit');

            if ( 'g' == $weight_unit ) return 0.001;
            if ( 'kg' == $weight_unit ) return 1;
            if ( 'lbs' == $weight_unit ) return 0.45359;
            if ( 'oz' == $weight_unit ) return 0.02834;
        }

        public function getShippingCost($weight, $rates) {
            $numericKeys = [];
            foreach ($rates as $key => $value) {
                if ($key === '30+') {
                    $over30 = $value;
                } else {
                    $numericKeys[$key] = $value;
                }
            }
            ksort($numericKeys);
            foreach ($numericKeys as $key => $value) {
                if ($weight <= $key) {
                    return $value;
                }
            }
            return $over30 ?? '';
        }

        public function getShippingZoneId($country, $mrkv_ua_shipping_shipping_zones) {
            foreach ($mrkv_ua_shipping_shipping_zones as $zoneId => $zoneData) {
                if (in_array($country, $zoneData['countries'])) {
                    return $zoneId;
                }
            }
            return '';
        }
    }
}