<?php 
	# Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) exit; 
?>
<div class="admin_mrkv_ua_shipping_page">
	<div class="admin_mrkv_ua_shipping_page__header">
		<?php 
			include MRKV_UA_SHIPPING_PLUGIN_PATH_TEMP . '/elements/template-mrkv-ua-shipping-header.php';
		?>
	</div>
	<div class="admin_mrkv_ua_shipping_page__inner">
		<div class="admin_mrkv_ua_shipping__block col-mrkv-10">
			<div class="admin_mrkv_ua_shipping__info">
				<?php settings_errors(); ?>
			</div>
		</div>
		<div class="admin_mrkv_ua_shipping__block col-mrkv-7">
			<div class="admin_mrkv_ua_shipping__settings mrkv_block_rounded">
				<h2><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/settings-icon.svg'); ?>" alt="Shipping methods" title="Shipping methods"><?php echo esc_html__('Shipping methods', 'mrkv-ua-shipping'); ?></h2>
				<p><?php echo esc_html__('Activate a shipping method of your choice from the list below. Then go to settings and set them up.', 'mrkv-ua-shipping'); ?></p>
				<form method="post" action="options.php">
					<?php settings_fields('mrkv-ua-shipping-settings-group'); ?>

					<div class="admin_mrkv_ua_shipping__list">
						<?php

							$mrkv_ua_shipping_active_plugins = get_option('m_ua_active_plugins');

							foreach(MRKV_UA_SHIPPING_LIST as $mrkv_ua_shipping_slug => $mrkv_ua_shipping_shipping)
							{
								$mrkv_ua_shipping_enabled = '';

								if($mrkv_ua_shipping_active_plugins && isset($mrkv_ua_shipping_active_plugins[$mrkv_ua_shipping_slug]['enabled']) && $mrkv_ua_shipping_active_plugins[$mrkv_ua_shipping_slug]['enabled'] == 'on')
								{
									$mrkv_ua_shipping_enabled = 'checked';
								}

								?>
									<div class="admin_mrkv_ua_shipping__list__li">
										<input id="m_ua_active_plugins_<?php echo esc_attr($mrkv_ua_shipping_slug); ?>" type="checkbox" name="m_ua_active_plugins[<?php echo esc_attr($mrkv_ua_shipping_slug); ?>][enabled]" <?php echo esc_attr($mrkv_ua_shipping_enabled); ?>>
										<label for="m_ua_active_plugins_<?php echo esc_attr($mrkv_ua_shipping_slug); ?>">
											<div class="admin_mrkv_ua_shipping__checkbox__input">
				                                <span class="admin_mrkv_ua_shipping_slider"></span>
				                            </div>
										</label>
										<img src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/' . $mrkv_ua_shipping_slug . '/logo-settings.svg'); ?>" alt="<?php echo esc_attr($mrkv_ua_shipping_shipping['name']); ?>" title="<?php echo esc_attr($mrkv_ua_shipping_shipping['name']); ?>">
										<p>
											<span class="admin_mrkv_ua_shipping__list__li__name"><?php echo esc_attr($mrkv_ua_shipping_shipping['name']); ?></span>
											<span class="admin_mrkv_ua_shipping__list__li__desc"><?php echo esc_attr($mrkv_ua_shipping_shipping['description']); ?></span>
										</p>
									</div>
								<?php
							}
						?>
					</div>
					<h3><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/clapperboard-edit-icon.svg'); ?>" alt="Checkout Blocks" title="Checkout Blocks"><?php echo esc_html__('Checkout Blocks', 'mrkv-ua-shipping'); ?></h3>
					<p><?php echo esc_html__('Configure the block checkout page to work with the shipping fields', 'mrkv-ua-shipping'); ?></p>
					<hr class="mrkv-ua-ship__hr">
					<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'checkout_middle_4'); ?>	
					<div class="admin_ua_ship_morkva_settings_row">
						<div class="col-mrkv-5">
							<div class="admin_ua_ship_morkva_settings_line">
								<?php
									$mrkv_global_option_generator = new MRKV_UA_SHIPPING_OPTION_FIELDS();
									$mrkv_ua_shipping_data = isset($mrkv_ua_shipping_active_plugins['checkout_block']['position']) ? $mrkv_ua_shipping_active_plugins['checkout_block']['position'] : 'order';
									$mrkv_ua_shipping_senders_type_list = array(
										'order' => __('Order block', 'mrkv-ua-shipping'),
										'address' => __('Address block', 'mrkv-ua-shipping'),
										'contact' => __('Contact block', 'mrkv-ua-shipping')
									);

									$mrkv_ua_shipping_description = __('Select the position of the delivery method fields on the checkout page', 'mrkv-ua-shipping');

									echo wp_kses($mrkv_global_option_generator->get_select_simple(__('Position of plugin fields in Checkout', 'mrkv-ua-shipping') . ' ' . __('(Blocks)', 'mrkv-ua-shipping'), 'm_ua_active_plugins[checkout_block][position]', $mrkv_ua_shipping_senders_type_list, $mrkv_ua_shipping_data, 'm_ua_active_plugins_checkout_block_position' , __('Choose a position', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
								?>
							</div>
						</div>
						<div class="col-mrkv-5">
							<div class="admin_ua_ship_morkva_settings_line">
								<?php
									$mrkv_ua_shipping_data = isset($mrkv_ua_shipping_active_plugins['checkout_block']['fields_under_methods']) ? $mrkv_ua_shipping_active_plugins['checkout_block']['fields_under_methods'] : '';
									echo wp_kses($mrkv_global_option_generator->get_input_checkbox(__('Move fields under the method radio', 'mrkv-ua-shipping'), 'm_ua_active_plugins[checkout_block][fields_under_methods]', $mrkv_ua_shipping_data, 'm_ua_active_plugins_checkout_block_fields_under_methods', ), MRKV_UA_SHIPPING_ALLOW_TAGS);
								?>
								<p class="mrkv-ua-ship-description">
									<?php echo esc_html__('Include the display of shipping fields for the desired method in the list ', 'mrkv-ua-shipping'); ?>
								</p>
							</div>
						</div>
					</div>
					<?php submit_button(esc_html__('Save', 'mrkv-ua-shipping')); ?>
				</form>
			</div>
		</div>
		<div class="admin_mrkv_ua_shipping__block col-mrkv-3">
			<?php 
				include MRKV_UA_SHIPPING_PLUGIN_PATH_TEMP . '/elements/template-mrkv-ua-shipping-support.php';
			?>
		</div>
	</div>
</div>