<?php 
	# Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) exit; 
?>
<div class="admin_mrkv_ua_shipping__plugin-info mrkv_block_rounded">
	<h2><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/chart-icon.svg'); ?>" alt="Statistics of shipments" title="Statistics of shipments"><?php echo esc_html__('Statistics of shipments', 'mrkv-ua-shipping'); ?></h2>
	<ul class="mrkv_list_invoices_total">
		<li>
			<span><?php echo esc_html__('Total invoices', 'mrkv-ua-shipping'); ?></span>
			<span><?php echo esc_attr((isset($total_statistic)) ? $total_statistic : 0); ?></span>
		</li>
	</ul>
</div>