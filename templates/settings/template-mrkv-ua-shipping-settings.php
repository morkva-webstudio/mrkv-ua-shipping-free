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