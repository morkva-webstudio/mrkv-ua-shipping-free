<?php 
	# Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) exit; 
	if(MRKV_OPTION_TABS)
	{
		?>
			<div class="admin_mrkv_ua_shipping__tabs_main mrkv_block_rounded">
				<h2>
					<?php echo esc_attr(esc_html__('Settings', 'mrkv-ua-shipping') . ' ' . MRKV_UA_SHIPPING_LIST[MRKV_UA_SHIPPING_SETTINGS_SLUG]['name']); ?>
						<img src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/' . MRKV_UA_SHIPPING_SETTINGS_SLUG . '/logo-settings.svg'); ?>" alt="<?php echo esc_attr(MRKV_UA_SHIPPING_LIST[MRKV_UA_SHIPPING_SETTINGS_SLUG]['name']); ?>" title="<?php echo esc_attr(MRKV_UA_SHIPPING_LIST[MRKV_UA_SHIPPING_SETTINGS_SLUG]['name']); ?>">
					</h2>
				<div class="admin_mrkv_ua_shipping__tabs_main__inner">
					<?php 
						$mrkv_ua_shipping_counter = 0;
						foreach(MRKV_OPTION_TABS as $id => $mrkv_ua_shipping_name)
						{
							?>
								<a href="#<?php echo esc_attr($id); ?>-mrkv" data-tab="<?php echo esc_attr($id); ?>" class="mrkv_up_ship_tab_btn <?php if($mrkv_ua_shipping_counter == 0){echo esc_attr('active'); } ?>"><?php echo esc_attr($mrkv_ua_shipping_name); ?></a>
							<?php

							++$mrkv_ua_shipping_counter;
						}
					?>
				</div>
			</div>
		<?php
	}
?>