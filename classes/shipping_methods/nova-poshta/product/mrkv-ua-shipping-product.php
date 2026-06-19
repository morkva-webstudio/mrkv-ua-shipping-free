<?php 
    # Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) exit; 
    $mrkv_ua_shipping_classes_enabled = (isset($settings_shipping['shipment']['class']['enabled']) && $settings_shipping['shipment']['class']['enabled']) ? true : false;
    $mrkv_ua_shipping_global_cargo_type = (isset($settings_shipping['shipment']['type']) && $settings_shipping['shipment']['type']) ? $settings_shipping['shipment']['type'] : '';
    $mrkv_ua_shipping_tire_classes = (isset($settings_shipping['shipment']['class']['list']['TiresWheels']) && is_array($settings_shipping['shipment']['class']['list']['TiresWheels']) && !empty($settings_shipping['shipment']['class']['list']['TiresWheels'])) ? $settings_shipping['shipment']['class']['list']['TiresWheels'] : [];
    $mrkv_ua_shipping_document_classes = (isset($settings_shipping['shipment']['class']['list']['Documents']) && is_array($settings_shipping['shipment']['class']['list']['Documents']) && !empty($settings_shipping['shipment']['class']['list']['Documents'])) ? $settings_shipping['shipment']['class']['list']['Documents'] : [];
    $mrkv_ua_shipping_shipping_class_id = $product->get_shipping_class_id();

    $mrkv_ua_shipping_exists = false;

    if(isset($settings_shipping['shipment']['class']['list']) &&
    is_array($settings_shipping['shipment']['class']['list']) && !empty($settings_shipping['shipment']['class']['list']))
    {
        $mrkv_ua_shipping_ids = array_column($settings_shipping['shipment']['class']['list'], 0);

        if (in_array((int)$mrkv_ua_shipping_shipping_class_id, $mrkv_ua_shipping_ids, true)) {
            $mrkv_ua_shipping_exists = true;
        }
    }

    if(($mrkv_ua_shipping_classes_enabled && !empty($mrkv_ua_shipping_tire_classes) && in_array($mrkv_ua_shipping_shipping_class_id, $mrkv_ua_shipping_tire_classes)) || ($mrkv_ua_shipping_global_cargo_type == 'TiresWheels' && !$mrkv_ua_shipping_exists))
    {
        require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-poshta/api/mrkv-ua-shipping-api-nova-poshta.php';
        $mrkv_object_nova_poshta = new MRKV_UA_SHIPPING_API_NOVA_POSHTA($settings_shipping);
        require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-poshta/api/mrkv-ua-shipping-calculate-nova-poshta.php';
        $mrkv_calculate_nova_poshta = new MRKV_UA_SHIPPING_CALCULATE_NOVA_POSHTA($mrkv_object_nova_poshta);
        $mrkv_ua_shipping_nova_poshta_tires = $mrkv_calculate_nova_poshta->get_tiregroup_list();
        $mrkv_tire_type = get_post_meta($post->ID, '_mrkv_tire_type', true);
        ?>
            <hr>
            <h3 style="padding:0 15px;"><?php esc_html_e('Nova Poshta', 'mrkv-ua-shipping'); ?></h3>
            <p class="form-field">
            <label for="_mrkv_tire_type"><?php esc_html_e('Tire Type', 'mrkv-ua-shipping'); ?></label>
            <select id="_mrkv_tire_type" name="_mrkv_tire_type" class="select short">
                <option value=""><?php esc_html_e('Select Tire Type', 'mrkv-ua-shipping'); ?></option>
                <?php foreach ($mrkv_ua_shipping_nova_poshta_tires as $mrkv_ua_shipping_key => $mrkv_ua_shipping_label) : ?>
                    <option value="<?php echo esc_attr($mrkv_ua_shipping_key); ?>" <?php selected($mrkv_tire_type, $mrkv_ua_shipping_key); ?>>
                        <?php echo esc_html($mrkv_ua_shipping_label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="description"><?php esc_html_e('Select the type of tire for this product.', 'mrkv-ua-shipping'); ?></span>
        </p>
        <?php
    }

    if(($mrkv_ua_shipping_classes_enabled && !empty($mrkv_ua_shipping_document_classes) && in_array($mrkv_ua_shipping_shipping_class_id, $mrkv_ua_shipping_document_classes)) || ($mrkv_ua_shipping_global_cargo_type == 'Documents' && !$mrkv_ua_shipping_exists))
    {
        $mrkv_ua_shipping_nova_poshta_document_weights = [
            '0.1' => __('0.1 kg', 'mrkv-ua-shipping'),
            '0.5' => __('0.5 kg', 'mrkv-ua-shipping'),
            '1' => __('1 kg', 'mrkv-ua-shipping')

        ];
        $mrkv_document_weight = get_post_meta($post->ID, '_mrkv_document_weight', true);
        ?>
            <hr>
            <h3 style="padding:0 15px;"><?php esc_html_e('Nova Poshta', 'mrkv-ua-shipping'); ?></h3>
            <p class="form-field">
            <label for="_mrkv_document_weight"><?php esc_html_e('Document weight', 'mrkv-ua-shipping'); ?></label>
            <select id="_mrkv_document_weight" name="_mrkv_document_weight" class="select short">
                <option value=""><?php esc_html_e('Select Document Weight', 'mrkv-ua-shipping'); ?></option>
                <?php foreach ($mrkv_ua_shipping_nova_poshta_document_weights as $mrkv_ua_shipping_key => $mrkv_ua_shipping_label) : ?>
                    <option value="<?php echo esc_attr($mrkv_ua_shipping_key); ?>" <?php selected($mrkv_document_weight, $mrkv_ua_shipping_key); ?>>
                        <?php echo esc_html($mrkv_ua_shipping_label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="description"><?php esc_html_e('Select the weight of document for this product.', 'mrkv-ua-shipping'); ?></span>
        </p>
        <?php
    }
?>