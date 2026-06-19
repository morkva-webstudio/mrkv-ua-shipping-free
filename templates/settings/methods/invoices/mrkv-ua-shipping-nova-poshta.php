<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="admin_mrkv_ua_shipping__invoices mrkv_block_rounded">
	<h2>
		<img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/invoices-icon.svg'); ?>" alt="My invoices" title="My invoices">
		<?php echo esc_html__('My shipments', 'mrkv-ua-shipping'); ?>
		<img class="mrkv-shipping-logo" src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/' . MRKV_UA_SHIPPING_SETTINGS_SLUG . '/logo-settings.svg'); ?>" alt="<?php echo esc_attr(MRKV_UA_SHIPPING_LIST[MRKV_UA_SHIPPING_SETTINGS_SLUG]['name']); ?>" title="<?php echo esc_attr(MRKV_UA_SHIPPING_LIST[MRKV_UA_SHIPPING_SETTINGS_SLUG]['name']); ?>">
	</h2>
	<p><?php echo esc_html__('Use the list of invoices to get the status, print your orders', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<div class="admin_mrkv_ua_shipping__invoices__table__actions">
		<div class="admin_ua_ship_morkva_settings_row">
			<div class="admin_ua_ship_morkva_settings_line col-mrkv-7">
				<label><?php echo esc_html__('Group actions', 'mrkv-ua-shipping'); $mrkv_ua_ship_invoice = 'morkva'; ?></label>
				<div class="admin_mrkv_ua_shipping_groups">
					<div class="mrkv_invoices__table_data_val_all">
						<input id="mrkv_ua_main_checkbox_invoice" type="checkbox" disabled>
						<label class="mrkv-checkbox-line" for="mrkv_ua_main_checkbox_invoice">
							<div class="admin_mrkv_ua_shipping__checkbox__input">
			                    <span class="admin_mrkv_ua_shipping_slider"></span>
			                </div>
			            </label>
					</div>
					<a class="mrkv_ua_ship_print" target="blanc">
						<img src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/global'); ?>/printer-icon.svg" alt="<?php echo esc_html__('Print invoice', 'mrkv-ua-shipping'); ?>" title="<?php echo esc_html__('Print', 'mrkv-ua-shipping'); ?>">
						<?php echo esc_html__('Print invoice', 'mrkv-ua-shipping'); ?>
					</a>
					<a class="mrkv_ua_ship_print" target="blanc">
						<img src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/global'); ?>/sticker-icon.svg" alt="<?php echo esc_html__('Print sticker', 'mrkv-ua-shipping'); ?>" title="<?php echo esc_html__('Print sticker', 'mrkv-ua-shipping'); ?>">
						<?php echo esc_html__('Print sticker', 'mrkv-ua-shipping'); ?>
					</a>
					<a class="mrkv_ua_ship_send_invoice_email_all">
						<span class="mrkv_ua_shipping_email_notify"><?php echo esc_html__('Email sent', 'mrkv-ua-shipping'); ?></span>
						<img src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/global'); ?>/letter-icon.svg" alt="<?php echo esc_html__('Send email', 'mrkv-ua-shipping'); ?>" title="<?php echo esc_html__('Send email', 'mrkv-ua-shipping'); ?>">
						<?php echo esc_html__('Send email', 'mrkv-ua-shipping'); ?>
						<div class="mrkv_ua_ship_create_invoice__loader"></div>
					</a>
					<a class="mrkv_ua_ship_send_remove_ttn_all">
						<img src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/global'); ?>/trash-icon.svg" alt="<?php echo esc_html__('Remove ttn', 'mrkv-ua-shipping'); ?>" title="<?php echo esc_html__('Remove ttn', 'mrkv-ua-shipping'); ?>">
						<?php echo esc_html__('Remove', 'mrkv-ua-shipping'); ?>
						<div class="mrkv_ua_ship_create_invoice__loader"></div>
					</a>
				</div>
			</div>
			<div class="admin_ua_ship_morkva_settings_line col-mrkv-3">
				<label><?php echo esc_html__('Search', 'mrkv-ua-shipping'); ?></label>
				<div class="mrkv_ua_ship_search_form">
					<input type="text" placeholder="<?php echo esc_html__('invoice number', 'mrkv-ua-shipping'); ?>" readonly>
					<button><img src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/global'); ?>/magnifer-icon.svg" alt="<?php echo esc_html__('Search', 'mrkv-ua-shipping'); ?>" title="<?php echo esc_html__('Search', 'mrkv-ua-shipping'); ?>" disabled></button>
				</div>
			</div>
		</div>
	</div>
	<div class="admin_mrkv_ua_shipping__invoices__table">
		<div class="mrkv_invoices__table__body mrkv-field-disabled">
			<?php
				$mrkv_ua_shipping_orders = array();
				if(is_array($mrkv_ua_shipping_orders) && !empty($mrkv_ua_shipping_orders))
				{
					
				}
				else{
					?>
						<span class="mrkv-ua-ship-only-pro"><?php echo esc_html__('Only in the Pro version', 'mrkv-ua-shipping'); ?></span>
					<?php
				}
			?>
		</div>		
	</div>
	<div class="mrkv_invoices__table__footer">
		
	</div>
</div>