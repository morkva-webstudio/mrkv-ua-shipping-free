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
		<div class="admin_mrkv_ua_shipping__block col-mrkv-7">
			<div class="admin_mrkv_ua_shipping__settings mrkv_block_rounded">
				<h2><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/info-icon.svg'); ?>" alt="About us" title="About us"><?php echo esc_html__('About us', 'mrkv-ua-shipping'); ?></h2>
			</div>
		</div>
		<div class="admin_mrkv_ua_shipping__block col-mrkv-3">
			<?php 
				include MRKV_UA_SHIPPING_PLUGIN_PATH_TEMP . '/elements/template-mrkv-ua-shipping-support.php';
			?>
		</div>
	</div>
</div>