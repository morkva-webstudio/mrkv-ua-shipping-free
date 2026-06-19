<?php
	if ( ! defined( 'ABSPATH' ) ) exit; 
	$mrkv_ua_shipping_header_pre_link = '/wp-admin/admin.php?page=';

	$mrkv_ua_shipping_screen = get_current_screen();

	if ( ! $mrkv_ua_shipping_screen ) {
		return;
	}

	$mrkv_ua_shipping_current_page = $mrkv_ua_shipping_screen->id;
?>
<div class="admin_mrkv_ua_shipping__header mrkv_block_rounded">
	<div class="admin_mrkv_ua_shipping__header__content">
		<a class="admin_mrkv_ua_shipping__header_img" href="<?php echo esc_url($mrkv_ua_shipping_header_pre_link); ?>mrkv_ua_shipping_settings">
			<img src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/global/delivery-icon.svg'); ?>" alt="morkva UA Shipping" title="morkva UA Shipping">
		</a>
		<a class="<?php if($mrkv_ua_shipping_current_page == 'mrkv_ua_shipping_settings'){ echo 'active'; } ?>" href="<?php echo esc_url($mrkv_ua_shipping_header_pre_link); ?>mrkv_ua_shipping_settings"><?php echo esc_html__('Global', 'mrkv-ua-shipping'); ?></a>
		<?php 
			foreach(MRKV_UA_SHIPPING_LIST as $mrkv_ua_shipping_slug => $mrkv_ua_shipping_shipping)
			{
				?>
					<a class="<?php if(defined('MRKV_UA_SHIPPING_SETTINGS_SLUG') && MRKV_UA_SHIPPING_SETTINGS_SLUG == $mrkv_ua_shipping_slug){ echo 'active'; } ?>" href="<?php echo esc_url($mrkv_ua_shipping_header_pre_link . 'mrkv_ua_shipping_' . $mrkv_ua_shipping_slug); ?>"><?php echo esc_html($mrkv_ua_shipping_shipping['name']); ?></a>
				<?php
			}
		?>
		<a class="<?php if($mrkv_ua_shipping_current_page == 'mrkv_ua_shipping_about_us'){ echo 'active'; } ?>" href="<?php echo esc_url($mrkv_ua_shipping_header_pre_link); ?>mrkv_ua_shipping_about_us"><?php echo esc_html__('About us', 'mrkv-ua-shipping'); ?></a>
		<a class="admin_mrkv_ua_shipping_morkva-logo" href="https://morkva.co.ua/" target="blanc">
			<img src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/global/morkva-logo.svg'); ?>" alt="morkva" title="morkva">
		</a>
	</div>
</div>