<?php
	if ( ! defined( 'ABSPATH' ) ) exit;
	$mrkv_ua_shipping_shipping_slug = 'nova-poshta';
	$mrkv_ua_shipping_shipping_slug_option = $mrkv_ua_shipping_shipping_slug . '_m_ua_settings';
	$mrkv_ua_shipping_nova_settings = apply_filters('mrkv_ua_shipping_popup_settings', get_option($mrkv_ua_shipping_shipping_slug_option), $mrkv_ua_shipping_shipping_slug );
?>
<form data-ship="<?php echo esc_attr($mrkv_ua_shipping_shipping_slug); ?>">
	<input type="hidden" name="order_id" value="">
	<h3>
		<img src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/global'); ?>/user-icon.svg" alt="<?php echo esc_html__('Sender', 'mrkv-ua-shipping'); ?>" title="<?php echo esc_html__('Sender', 'mrkv-ua-shipping'); ?>">
		<span><?php echo esc_html__('Sender\'s data', 'mrkv-ua-shipping'); ?></span>
	</h3>
	<?php do_action('mrkv_ua_shipping_ttn_create_row', $mrkv_ua_shipping_shipping_slug, 'row_1'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<label>
					<?php echo esc_html__('Sender contact person', 'mrkv-ua-shipping'); ?>
					<?php
						$mrkv_ua_shipping_data = isset($mrkv_ua_shipping_nova_settings['sender']['ref']) ? $mrkv_ua_shipping_nova_settings['sender']['ref'] : '';

						if(!$mrkv_ua_shipping_data)
						{
							?>
								<div class="admin_ua_ship_morkva__notification mrkv-notification-red"><?php echo esc_html__('Sender Ref Incorrect', 'mrkv-ua-shipping'); ?></div>
							<?php
						}
					?>
				</label>
				<?php
					if(isset($mrkv_ua_shipping_nova_settings['sender']['list']))
					{
						$mrkv_ua_shipping_senders = json_decode(base64_decode($mrkv_ua_shipping_nova_settings['sender']['list']), true);

						echo wp_kses($mrkv_global_option_generator->get_select_tag('', 'mrkv_ua_ship_invoice_sender_ref', $mrkv_ua_shipping_senders, $mrkv_ua_shipping_data, $mrkv_ua_shipping_shipping_slug_option . '_mrkv_ua_ship_invoice_sender_ref' , __('Choose a sender', 'mrkv-ua-shipping')), MRKV_UA_SHIPPING_ALLOW_TAGS);

						if(isset($mrkv_ua_shipping_senders[0]['attr']) && !empty($mrkv_ua_shipping_senders))
						{
							foreach($mrkv_ua_shipping_senders[0]['attr'] as $mrkv_ua_shipping_sender_data_key => $mrkv_ua_shipping_sender_data_val)
							{
								if($mrkv_ua_shipping_sender_data_key == 'ref')
								{
									continue;
								}
								$mrkv_ua_shipping_data = isset($mrkv_ua_shipping_nova_settings['sender'][$mrkv_ua_shipping_sender_data_key]) ? $mrkv_ua_shipping_nova_settings['sender'][$mrkv_ua_shipping_sender_data_key] : '';
								echo wp_kses($mrkv_global_option_generator->get_input_hidden('mrkv_ua_ship_invoice_sender_' . $mrkv_ua_shipping_sender_data_key, $mrkv_ua_shipping_data, $mrkv_ua_shipping_shipping_slug_option . '_mrkv_ua_ship_invoice_sender_' . $mrkv_ua_shipping_sender_data_key), MRKV_UA_SHIPPING_ALLOW_TAGS);
							}
						}
					}
				?>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<label>
					<?php echo esc_html__('Address from', 'mrkv-ua-shipping'); ?>
					<?php
					$mrkv_ua_shipping_full_address = '';
					$mrkv_ua_shipping_full_address .= isset($mrkv_ua_shipping_nova_settings['sender']['city']['name']) ? $mrkv_ua_shipping_nova_settings['sender']['city']['name'] . ', ' : '';
					$mrkv_ua_shipping_data_error = '';

					$mrkv_ua_shipping_data = isset($mrkv_ua_shipping_nova_settings['sender']['address_type']) ? $mrkv_ua_shipping_nova_settings['sender']['address_type'] : '';

					if($mrkv_ua_shipping_data == 'W')
					{
						$mrkv_ua_shipping_full_address .= isset($mrkv_ua_shipping_nova_settings['sender']['warehouse']['name']) ? $mrkv_ua_shipping_nova_settings['sender']['warehouse']['name'] . ' ' : '';
						$mrkv_ua_shipping_data_error = isset($mrkv_ua_shipping_nova_settings['sender']['warehouse']['ref']) ? $mrkv_ua_shipping_nova_settings['sender']['warehouse']['ref'] : '';

						if(!$mrkv_ua_shipping_data_error)
						{
							?>
								<div class="admin_ua_ship_morkva__notification mrkv-notification-red"><?php echo esc_html__('Warehouse Ref Incorrect', 'mrkv-ua-shipping'); ?></div>
							<?php
						}
					}
					elseif($mrkv_ua_shipping_data == 'D')
					{
						$mrkv_ua_shipping_full_address .= (isset($mrkv_ua_shipping_nova_settings['sender']['street']['name']) && $mrkv_ua_shipping_nova_settings['sender']['street']['name']) ? $mrkv_ua_shipping_nova_settings['sender']['street']['name'] . ' ' : '';
						$mrkv_ua_shipping_full_address .= (isset($mrkv_ua_shipping_nova_settings['sender']['street']['house']) && $mrkv_ua_shipping_nova_settings['sender']['street']['house']) ? $mrkv_ua_shipping_nova_settings['sender']['street']['house'] . ', ' : '';
						$mrkv_ua_shipping_full_address .= (isset($mrkv_ua_shipping_nova_settings['sender']['street']['flat']) && $mrkv_ua_shipping_nova_settings['sender']['street']['flat']) ? __('flat/office', 'mrkv-ua-shipping') . ' ' . $mrkv_ua_shipping_nova_settings['sender']['street']['flat'] . ' ' : '';
					}
					else
					{
						$mrkv_ua_shipping_full_address .= __('Empty data', 'mrkv-ua-shipping');
					}
				?>
				</label>
				<p><?php echo esc_attr($mrkv_ua_shipping_full_address); ?></p>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_ttn_create_row', $mrkv_ua_shipping_shipping_slug, 'row_2'); ?>
	<hr class="mrkv-ua-ship__hr">
	<h3>
		<img src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/global'); ?>/user-icon.svg" alt="<?php echo esc_html__('Recipient', 'mrkv-ua-shipping'); ?>" title="<?php echo esc_html__('Recipient', 'mrkv-ua-shipping'); ?>">
		<span><?php echo esc_html__('Recipient\'s data', 'mrkv-ua-shipping'); ?></span>
	</h3>
	<?php do_action('mrkv_ua_shipping_ttn_create_row', $mrkv_ua_shipping_shipping_slug, 'row_3'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_row admin_ua_ship_morkva_settings_row_mb-0">
				<div class="col-mrkv-5">
					<div class="admin_ua_ship_morkva_settings_line">
						<?php
							echo wp_kses($mrkv_global_option_generator->get_input_text(__('Firstname', 'mrkv-ua-shipping'), 'mrkv_ua_ship_invoice_first_name', '', $mrkv_ua_shipping_shipping_slug_option . '_mrkv_ua_ship_invoice_first_name' , '', __('Enter the Firstname', 'mrkv-ua-shipping'), ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
						?>
					</div>	
				</div>
				<div class="col-mrkv-5">
					<div class="admin_ua_ship_morkva_settings_line">
						<?php
							echo wp_kses($mrkv_global_option_generator->get_input_text(__('Lastname', 'mrkv-ua-shipping'), 'mrkv_ua_ship_invoice_last_name', '', $mrkv_ua_shipping_shipping_slug_option . '_mrkv_ua_ship_invoice_last_name' , '', __('Enter the Lastname', 'mrkv-ua-shipping'), ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
						?>
					</div>	
				</div>
			</div>
			<div class="admin_ua_ship_morkva_settings_row admin_ua_ship_morkva_settings_row_mb-0">
				<div class="col-mrkv-5">
					<div class="admin_ua_ship_morkva_settings_line">
						<?php
							echo wp_kses($mrkv_global_option_generator->get_input_text(__('Patronymic', 'mrkv-ua-shipping'), 'mrkv_ua_ship_invoice_patronymic', '', $mrkv_ua_shipping_shipping_slug_option . '_mrkv_ua_ship_invoice_patronymic' , '', __('Enter the Patronymic', 'mrkv-ua-shipping'), ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
						?>
					</div>	
				</div>
				<div class="col-mrkv-5">
					<div class="admin_ua_ship_morkva_settings_line">
						<?php
							echo wp_kses($mrkv_global_option_generator->get_input_text(__('Phone', 'mrkv-ua-shipping'), 'mrkv_ua_ship_invoice_phone', '', $mrkv_ua_shipping_shipping_slug_option . '_mrkv_ua_ship_invoice_phone' , '', __('Enter the Phone', 'mrkv-ua-shipping'), ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
						?>
					</div>	
				</div>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<label>
					<?php echo esc_html__('Address to', 'mrkv-ua-shipping'); ?>
				</label>
				<p class="mrkv_ua_ship_invoice_address"></p>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_ttn_create_row', $mrkv_ua_shipping_shipping_slug, 'row_4'); ?>
	<hr class="mrkv-ua-ship__hr">
	<h3>
		<img src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/global'); ?>/tuning-icon.svg" alt="<?php echo esc_html__('Parameters of the shipment', 'mrkv-ua-shipping'); ?>" title="<?php echo esc_html__('Parameters of the shipment', 'mrkv-ua-shipping'); ?>">
		<span><?php echo esc_html__('Parameters of the shipment', 'mrkv-ua-shipping'); ?></span>
	</h3>
	<p><?php echo esc_html__('Check the correctness of the shipment data, or fill in if necessary', 'mrkv-ua-shipping'); ?></p>
	<?php do_action('mrkv_ua_shipping_ttn_create_row', $mrkv_ua_shipping_shipping_slug, 'row_5'); ?>
	<div class="admin_ua_ship_morkva_settings_row admin_ua_ship_morkva_settings_row_mb-0">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<label>
					<?php echo esc_html__('Type of shipment', 'mrkv-ua-shipping'); ?>
				</label>
				<div class="admin_ua_ship_morkva_settings_row">
					<?php
						$mrkv_ua_shipping_data = isset($mrkv_ua_shipping_nova_settings['shipment']['type']) ? $mrkv_ua_shipping_nova_settings['shipment']['type'] : '';
						echo wp_kses($mrkv_global_option_generator->get_input_radio(__('Parcel', 'mrkv-ua-shipping'), 'mrkv_ua_ship_invoice_shipment_type', 'Parcel', $mrkv_ua_shipping_data, $mrkv_ua_shipping_shipping_slug_option . '_mrkv_ua_ship_invoice_shipment_type_parcel', 'Parcel'), MRKV_UA_SHIPPING_ALLOW_TAGS);
						echo wp_kses($mrkv_global_option_generator->get_input_radio(__('Pallet', 'mrkv-ua-shipping'), 'mrkv_ua_ship_invoice_shipment_type', 'Pallet', $mrkv_ua_shipping_data, $mrkv_ua_shipping_shipping_slug_option . '_mrkv_ua_ship_invoice_shipment_type_pallet', 'Parcel'), MRKV_UA_SHIPPING_ALLOW_TAGS);
						echo wp_kses($mrkv_global_option_generator->get_input_radio(__('Documents', 'mrkv-ua-shipping'), 'mrkv_ua_ship_invoice_shipment_type', 'Documents', $mrkv_ua_shipping_data, $mrkv_ua_shipping_shipping_slug_option . '_mrkv_ua_ship_invoice_shipment_type_documents', 'Parcel'), MRKV_UA_SHIPPING_ALLOW_TAGS);
						echo wp_kses($mrkv_global_option_generator->get_input_radio(__('Tires', 'mrkv-ua-shipping'), 'mrkv_ua_ship_invoice_shipment_type', 'TiresWheels', $mrkv_ua_shipping_data, $mrkv_ua_shipping_shipping_slug_option . '_mrkv_ua_ship_invoice_shipment_type_tires', 'Parcel'), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
				</div>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<label>
					<?php echo esc_html__('Payer of delivery', 'mrkv-ua-shipping'); ?>
				</label>
				<div class="admin_ua_ship_morkva_settings_row">
					<?php
						$mrkv_ua_shipping_data = isset($mrkv_ua_shipping_nova_settings['payer']['delivery']) ? $mrkv_ua_shipping_nova_settings['payer']['delivery'] : '';
						echo wp_kses($mrkv_global_option_generator->get_input_radio(__('Recipient', 'mrkv-ua-shipping'), 'mrkv_ua_ship_invoice_payer_delivery', 'Recipient', $mrkv_ua_shipping_data, $mrkv_ua_shipping_shipping_slug_option . '_mrkv_ua_ship_invoice_payer_delivery_recipient', 'Recipient'), MRKV_UA_SHIPPING_ALLOW_TAGS);
						echo wp_kses($mrkv_global_option_generator->get_input_radio(__('Sender', 'mrkv-ua-shipping'), 'mrkv_ua_ship_invoice_payer_delivery', 'Sender', $mrkv_ua_shipping_data, $mrkv_ua_shipping_shipping_slug_option . '_mrkv_ua_ship_invoice_payer_delivery_sender', 'Recipient'), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
				</div>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_ttn_create_row', $mrkv_ua_shipping_shipping_slug, 'row_6'); ?>
	<div class="admin_ua_ship_morkva_settings_row admin_ua_ship_morkva_settings_row_mb-0">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<label>
					<?php echo esc_html__('Weight of the shipment, kg', 'mrkv-ua-shipping'); ?>
				</label>
				<?php 
					$mrkv_ua_shipping_data = isset($mrkv_ua_shipping_nova_settings['shipment']['weight']) ? $mrkv_ua_shipping_nova_settings['shipment']['weight'] : '';

					echo wp_kses($mrkv_global_option_generator->get_input_number('', 'mrkv_ua_ship_invoice_shipment_weight', $mrkv_ua_shipping_data, $mrkv_ua_shipping_shipping_slug_option . '_mrkv_ua_ship_invoice_shipment_weight' , '', '', ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<label>
					<?php echo esc_html__('Dimensions of the shipment, cm', 'mrkv-ua-shipping'); ?>
				</label>
				<div class="adm_morkva_row_size">
					<div class="adm_morkva_row_size__col">
						<span><?php echo esc_html__('Length', 'mrkv-ua-shipping'); ?></span>
						<?php 
							$mrkv_ua_shipping_data = isset($mrkv_ua_shipping_nova_settings['shipment']['length']) ? $mrkv_ua_shipping_nova_settings['shipment']['length'] : '';
							echo wp_kses($mrkv_global_option_generator->get_input_number('', 'mrkv_ua_ship_invoice_shipment_length', $mrkv_ua_shipping_data, $mrkv_ua_shipping_shipping_slug_option. '_mrkv_ua_ship_invoice_shipment_length' , '', '', ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
						?>
					</div>
					<span>
						<svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M8 18C5.17157 18 3.75736 18 2.87868 17.1213C2 16.2426 2 14.8284 2 12C2 9.17157 2 7.75736 2.87868 6.87868C3.75736 6 5.17157 6 8 6C10.8284 6 12.2426 6 13.1213 6.87868C14 7.75736 14 9.17157 14 12" stroke="#ed6230" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M10 12C10 14.8284 10 16.2426 10.8787 17.1213C11.7574 18 13.1716 18 16 18C18.8284 18 20.2426 18 21.1213 17.1213C21.4211 16.8215 21.6186 16.4594 21.7487 16M22 12C22 9.17157 22 7.75736 21.1213 6.87868C20.2426 6 18.8284 6 16 6" stroke="#ed6230" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
					</span>
					<div class="adm_morkva_row_size__col">
						<span><?php echo esc_html__('Width', 'mrkv-ua-shipping'); ?></span>
						<?php 
							$mrkv_ua_shipping_data = isset($mrkv_ua_shipping_nova_settings['shipment']['width']) ? $mrkv_ua_shipping_nova_settings['shipment']['width'] : '';
							echo wp_kses($mrkv_global_option_generator->get_input_number('', 'mrkv_ua_ship_invoice_shipment_width', $mrkv_ua_shipping_data, $mrkv_ua_shipping_shipping_slug_option. '_mrkv_ua_ship_invoice_shipment_width' , '', '', ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
						?>
					</div>
					<span>
						<svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M8 18C5.17157 18 3.75736 18 2.87868 17.1213C2 16.2426 2 14.8284 2 12C2 9.17157 2 7.75736 2.87868 6.87868C3.75736 6 5.17157 6 8 6C10.8284 6 12.2426 6 13.1213 6.87868C14 7.75736 14 9.17157 14 12" stroke="#ed6230" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M10 12C10 14.8284 10 16.2426 10.8787 17.1213C11.7574 18 13.1716 18 16 18C18.8284 18 20.2426 18 21.1213 17.1213C21.4211 16.8215 21.6186 16.4594 21.7487 16M22 12C22 9.17157 22 7.75736 21.1213 6.87868C20.2426 6 18.8284 6 16 6" stroke="#ed6230" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
					</span>
					<div class="adm_morkva_row_size__col">
						<span><?php echo esc_html__('Height', 'mrkv-ua-shipping'); ?></span>
						<?php 
							$mrkv_ua_shipping_data = isset($mrkv_ua_shipping_nova_settings['shipment']['height']) ? $mrkv_ua_shipping_nova_settings['shipment']['height'] : '';
							echo wp_kses($mrkv_global_option_generator->get_input_number('', 'mrkv_ua_ship_invoice_shipment_height', $mrkv_ua_shipping_data, $mrkv_ua_shipping_shipping_slug_option. '_mrkv_ua_ship_invoice_shipment_height' , '', '', ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_ttn_create_row', $mrkv_ua_shipping_shipping_slug, 'row_7'); ?>
	<div class="admin_ua_ship_morkva_settings_row admin_ua_ship_morkva_settings_row_mb-0">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<label>
					<?php echo esc_html__('The total weight of the shipment', 'mrkv-ua-shipping'); ?>
				</label>
				<?php 
					$mrkv_ua_shipping_data = isset($mrkv_ua_shipping_nova_settings['shipment']['volume']) ? $mrkv_ua_shipping_nova_settings['shipment']['volume'] : '';

					echo wp_kses($mrkv_global_option_generator->get_input_number('', 'mrkv_ua_ship_invoice_shipment_volume', $mrkv_ua_shipping_data, $mrkv_ua_shipping_shipping_slug_option . '_mrkv_ua_ship_invoice_shipment_volume' , '', '', '', 'readonly'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<label>
					<?php echo esc_html__('Number of seats', 'mrkv-ua-shipping'); ?>
				</label>
				<?php 
					echo wp_kses($mrkv_global_option_generator->get_input_number('', 'mrkv_ua_ship_invoice_shipment_seats', 1, $mrkv_ua_shipping_shipping_slug_option . '_mrkv_ua_ship_invoice_shipment_seats' , '', '', '', '', '1.00'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_ttn_create_row', $mrkv_ua_shipping_shipping_slug, 'row_8'); ?>
	<div class="admin_ua_ship_morkva_settings_line">
		<?php
			$mrkv_ua_shipping_data = isset($mrkv_ua_shipping_nova_settings['shipment']['description']) ? $mrkv_ua_shipping_nova_settings['shipment']['description'] : '';
			$mrkv_ua_shipping_description = __('Maximum number of characters:', 'mrkv-ua-shipping') . ' 100' . '<div class="mrkv-ua-shipping-desc-validation" data-success="' . __('Within acceptable limits.', 'mrkv-ua-shipping') . '" data-error="' . __('Reduce the number of characters.', 'mrkv-ua-shipping') . '">' . __('Number of symbols:', 'mrkv-ua-shipping') . ' <span class="mrkv-ua-ship-cout-symb"></span>. <span class="mrkv-ua-ship-message-symb"></span>' . '</div>';

			echo wp_kses($mrkv_global_option_generator->get_textarea(__('Description of the shipment', 'mrkv-ua-shipping'), 'mrkv_ua_ship_invoice_shipment_description', $mrkv_ua_shipping_data, $mrkv_ua_shipping_shipping_slug_option . 'mrkv_ua_ship_invoice_shipment_description' , '', __('For example, products for children...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
		?>
	</div>
	<?php do_action('mrkv_ua_shipping_ttn_create_row', $mrkv_ua_shipping_shipping_slug, 'row_9'); ?>
	<hr class="mrkv-ua-ship__hr">
	<h3>
		<img src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/global'); ?>/box-icon.svg" alt="<?php echo esc_html__('Additional services', 'mrkv-ua-shipping'); ?>" title="<?php echo esc_html__('Additional services', 'mrkv-ua-shipping'); ?>">
		<span><?php echo esc_html__('Additional services', 'mrkv-ua-shipping'); ?></span>
	</h3>
	<p><?php echo esc_html__('Use additional services as needed', 'mrkv-ua-shipping'); ?></p>
	<?php do_action('mrkv_ua_shipping_ttn_create_row', $mrkv_ua_shipping_shipping_slug, 'row_10'); ?>
	<div class="admin_ua_ship_morkva_settings_row mrkv-addittional-row">
		<div class="col-mrkv-10">
			<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
				<?php
					echo wp_kses($mrkv_global_option_generator->get_input_checkbox(__('Money transfer', 'mrkv-ua-shipping'), '', '', $mrkv_ua_shipping_shipping_slug_option . 'mrkv_ua_ship_invoice_money_transfer', '', 'disabled'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
				<span class="mrkv-ua-ship-only-pro"><?php echo esc_html__('Only in the Pro version', 'mrkv-ua-shipping'); ?></span>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_ttn_create_row', $mrkv_ua_shipping_shipping_slug, 'row_11'); ?>
</form>