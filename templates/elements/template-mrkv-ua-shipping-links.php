<?php 
	# Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) exit; 
?>
<div class="admin_mrkv_ua_shipping__links_main mrkv_block_rounded">
	<div class="mrkv_ua_shipping__links_main__menu">
		<div class="mrkv-links-head">
			<img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/library-icon.svg'); ?>" alt="morkva menu shipping" title="morkva menu shipping">
			<?php echo esc_html__('Pages:', 'mrkv-ua-shipping'); ?>
		</div>
		<a class="<?php echo esc_attr(!defined('MRKV_UA_SHIPPING_SETTINGS_PAGE_SLUG') ? 'active' : ''); ?>" href="/wp-admin/admin.php?page=mrkv_ua_shipping_<?php echo esc_attr(MRKV_UA_SHIPPING_SETTINGS_SLUG);?>"><?php echo esc_html__('Settings', 'mrkv-ua-shipping'); ?></a>
		<?php 
			if(MRKV_UA_SHIPPING_LIST[MRKV_UA_SHIPPING_SETTINGS_SLUG]['pages'] && is_array(MRKV_UA_SHIPPING_LIST[MRKV_UA_SHIPPING_SETTINGS_SLUG]['pages']))
			{
				foreach(MRKV_UA_SHIPPING_LIST[MRKV_UA_SHIPPING_SETTINGS_SLUG]['pages'] as $mrkv_ua_shipping_page_slug => $mrkv_ua_shipping_page_name)
				{
					$mrkv_ua_shipping_is_active = (defined('MRKV_UA_SHIPPING_SETTINGS_PAGE_SLUG') && MRKV_UA_SHIPPING_SETTINGS_PAGE_SLUG == $mrkv_ua_shipping_page_slug) ? 'active' : '';

					?>
						<a class="<?php echo esc_attr($mrkv_ua_shipping_is_active); ?>" href="/wp-admin/admin.php?page=mrkv_ua_shipping_<?php echo esc_attr(MRKV_UA_SHIPPING_SETTINGS_SLUG . '_' . $mrkv_ua_shipping_page_slug); ?>"><?php echo esc_attr($mrkv_ua_shipping_page_name); ?></a>
					<?php
				}
			}
		?>
	</div>
</div>
