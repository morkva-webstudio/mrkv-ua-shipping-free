<?php 
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 
global $mrkv_global_option_generator; global $mrkv_global_shipping_object; ?>
<section id="basic_settings" class="mrkv_up_ship_shipping_tab_block active">
	<h2><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/settings-icon.svg'); ?>" alt="Basic settings" title="Basic settings"><?php echo esc_html__('Basic settings', 'mrkv-ua-shipping'); ?></h2>
	<p><?php echo esc_html__('There are currently no settings. The delivery method works without entering API keys.', 'mrkv-ua-shipping'); ?></p>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'rozetka-delivery', 'basic_first'); ?>	
</section>
<section id="checkout_settings" class="mrkv_up_ship_shipping_tab_block">
	<h2><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/clapperboard-edit-icon.svg'); ?>" alt="Sending email from TTN" title="Sending email from TTN"><?php echo esc_html__('Checkout settings', 'mrkv-ua-shipping'); ?></h2>
	<p><?php echo esc_html__('Customize the output of the plugin fields in relation to your theme', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'rozetka-delivery', 'checkout_first'); ?>	
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<?php 
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['checkout']['position']) ? MRKV_SHIPPING_SETTINGS['checkout']['position'] : '';
					$mrkv_ua_shipping_senders_type_list = array(
						'woocommerce_after_checkout_billing_form' => __('After Payment data', 'mrkv-ua-shipping'),
						'woocommerce_before_order_notes' => __('Before Notes to orders', 'mrkv-ua-shipping'),
					);

					$mrkv_ua_shipping_description = __('Select the position of the delivery method fields on the checkout page', 'mrkv-ua-shipping');

					echo wp_kses($mrkv_global_option_generator->get_select_simple(__('Position of plugin fields in Checkout', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[checkout][position]', $mrkv_ua_shipping_senders_type_list, $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_checkout_position' , __('Choose a position', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'rozetka-delivery', 'checkout_middle_1'); ?>	
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<?php
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['checkout']['hide_saving_data']) ? MRKV_SHIPPING_SETTINGS['checkout']['hide_saving_data'] : '';
					echo wp_kses($mrkv_global_option_generator->get_input_checkbox(__('Save customer selected fields', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[checkout][hide_saving_data]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_checkout_hide_saving_data', ), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
				<p class="mrkv-ua-ship-description">
					<?php echo esc_html__('Enable to store selected delivery city and warehouse/postamat in session cookies (may not work if privacy settings enabled in user’s browser)', 'mrkv-ua-shipping'); ?>
				</p>
			</div>
		</div>
		<div class="col-mrkv-5">
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'rozetka-delivery', 'checkout_last'); ?>	
</section>
<section id="log_settings" class="mrkv_up_ship_shipping_tab_block">
	<h2><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/clipboard-list-icon.svg'); ?>" alt="Debug Log" title="Debug Log"><?php echo esc_html__('Debug Log', 'mrkv-ua-shipping'); ?></h2>
	<p><?php echo esc_html__('To keep an error log, enable it in the settings', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'rozetka-delivery', 'log_first'); ?>	
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<?php
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['debug']['log']) ? MRKV_SHIPPING_SETTINGS['debug']['log'] : '';
					echo wp_kses($mrkv_global_option_generator->get_input_checkbox(__('Enable debug log', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[debug][log]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_debug_log', ), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
				<p class="mrkv-ua-ship-description">
				<?php echo esc_html__('Enable to receive request error logs', 'mrkv-ua-shipping'); ?>
				</p>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<p></p>
				<a href="<?php echo esc_url(admin_url( 'admin.php?page=wc-status&tab=logs' )); ?>"><?php echo esc_html__('Show Log files', 'mrkv-ua-shipping'); ?></a>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'rozetka-delivery', 'log_last'); ?>	
</section>