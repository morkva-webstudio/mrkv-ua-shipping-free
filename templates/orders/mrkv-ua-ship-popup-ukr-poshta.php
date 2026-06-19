<?php 
	if ( ! defined( 'ABSPATH' ) ) exit;
	$mrkv_ua_shipping_shipping_slug = 'ukr-poshta';
	$mrkv_ua_shipping_shipping_slug_option = $mrkv_ua_shipping_shipping_slug . '_m_ua_settings';
	$mrkv_ua_shipping_ukr_settings = apply_filters('mrkv_ua_shipping_popup_settings', get_option($mrkv_ua_shipping_shipping_slug_option), $mrkv_ua_shipping_shipping_slug );
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
						$mrkv_ua_shipping_has_empty = false;	
						$mrkv_ua_shipping_sender_type = isset($mrkv_ua_shipping_ukr_settings['sender']['type']) ? $mrkv_ua_shipping_ukr_settings['sender']['type'] : '';
						$mrkv_ua_shipping_fullname = '';
						$mrkv_ua_shipping_middlename = '';
						$mrkv_ua_shipping_phone = '';
						$mrkv_ua_shipping_lastname = '';
						$mrkv_ua_shipping_name = '';

						if($mrkv_ua_shipping_sender_type == 'INDIVIDUAL')
						{
							$mrkv_ua_shipping_lastname = isset($mrkv_ua_shipping_ukr_settings['sender']['individual']['lastname']) ? $mrkv_ua_shipping_ukr_settings['sender']['individual']['lastname'] . ' ' : '';
							$mrkv_ua_shipping_name = isset($mrkv_ua_shipping_ukr_settings['sender']['individual']['name']) ? $mrkv_ua_shipping_ukr_settings['sender']['individual']['name'] . ' ' : '';
							$mrkv_ua_shipping_middlename = isset($mrkv_ua_shipping_ukr_settings['sender']['individual']['middlename']) ? $mrkv_ua_shipping_ukr_settings['sender']['individual']['middlename'] . ' ' : '';
							$mrkv_ua_shipping_phone = isset($mrkv_ua_shipping_ukr_settings['sender']['individual']['phone']) ? $mrkv_ua_shipping_ukr_settings['sender']['individual']['phone'] . ' ' : '';
						}
						elseif($mrkv_ua_shipping_sender_type && $mrkv_ua_shipping_sender_type != 'INDIVIDUAL')
						{
							$mrkv_ua_shipping_lastname = (isset($mrkv_ua_shipping_ukr_settings['sender']['company']['lastname']) && $mrkv_ua_shipping_ukr_settings['sender']['company']['lastname']) ? $mrkv_ua_shipping_ukr_settings['sender']['company']['lastname'] : $mrkv_ua_shipping_lastname;

							if(!$mrkv_ua_shipping_lastname){
								$mrkv_ua_shipping_lastname = (isset($mrkv_ua_shipping_ukr_settings['sender']['private']['lastname']) && $mrkv_ua_shipping_ukr_settings['sender']['private']['lastname']) ? $mrkv_ua_shipping_ukr_settings['sender']['private']['lastname'] : $mrkv_ua_shipping_lastname;
							}

							$mrkv_ua_shipping_name = (isset($mrkv_ua_shipping_ukr_settings['sender']['company']['name']) && $mrkv_ua_shipping_ukr_settings['sender']['company']['name']) ? $mrkv_ua_shipping_ukr_settings['sender']['company']['name'] : $mrkv_ua_shipping_name;

							if(!$mrkv_ua_shipping_name)
							{
								$mrkv_ua_shipping_name = (isset($mrkv_ua_shipping_ukr_settings['sender']['private']['name']) && $mrkv_ua_shipping_ukr_settings['sender']['private']['name']) ? $mrkv_ua_shipping_ukr_settings['sender']['private']['name'] : $mrkv_ua_shipping_name;
							}

							$mrkv_ua_shipping_middlename = (isset($mrkv_ua_shipping_ukr_settings['sender']['private']['middlename']) && $mrkv_ua_shipping_ukr_settings['sender']['private']['middlename']) ? $mrkv_ua_shipping_ukr_settings['sender']['private']['middlename'] : $mrkv_ua_shipping_middlename;

							$mrkv_ua_shipping_phone = (isset($mrkv_ua_shipping_ukr_settings['sender']['company']['phone']) && $mrkv_ua_shipping_ukr_settings['sender']['company']['phone']) ? $mrkv_ua_shipping_ukr_settings['sender']['company']['phone'] : $mrkv_ua_shipping_phone;

							if(!$mrkv_ua_shipping_phone)
							{
								$mrkv_ua_shipping_phone = (isset($mrkv_ua_shipping_ukr_settings['sender']['private']['phone']) && $mrkv_ua_shipping_ukr_settings['sender']['private']['phone']) ? $mrkv_ua_shipping_ukr_settings['sender']['private']['phone'] : $mrkv_ua_shipping_phone;
							}
						}
						else
						{
							$mrkv_ua_shipping_has_empty = true;	
						}

						$mrkv_ua_shipping_fullname = $mrkv_ua_shipping_lastname . $mrkv_ua_shipping_name . $mrkv_ua_shipping_middlename;

						if(!$mrkv_ua_shipping_lastname && !$mrkv_ua_shipping_name)
						{
							$mrkv_ua_shipping_has_empty = true;
						}

						if($mrkv_ua_shipping_has_empty)
						{
							?>
								<div class="admin_ua_ship_morkva__notification mrkv-notification-red"><?php echo esc_html__('Sender Data Incorrect', 'mrkv-ua-shipping'); ?></div>
							<?php
						}
					?>
				</label>
				<p><?php echo esc_attr($mrkv_ua_shipping_fullname); ?></p>
			</div>
			<div class="admin_ua_ship_morkva_settings_line">
				<label>
					<?php 
						echo esc_html__('Phone', 'mrkv-ua-shipping');

						if(!$mrkv_ua_shipping_phone)
						{
							?>
								<div class="admin_ua_ship_morkva__notification mrkv-notification-red"><?php echo esc_html__('Sender Phone Incorrect', 'mrkv-ua-shipping'); ?></div>
							<?php
						}
					?>
				</label>
				<p><?php echo esc_attr($mrkv_ua_shipping_phone); ?></p>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<label>
					<?php echo esc_html__('Address from', 'mrkv-ua-shipping'); ?>
					<?php
					$mrkv_ua_shipping_full_address = isset($mrkv_ua_shipping_ukr_settings['sender']['warehouse']['name']) ? $mrkv_ua_shipping_ukr_settings['sender']['warehouse']['name'] : '';
					$mrkv_ua_shipping_full_address_id = isset($mrkv_ua_shipping_ukr_settings['sender']['warehouse']['id']) ? $mrkv_ua_shipping_ukr_settings['sender']['warehouse']['id'] : '';

					if(!$mrkv_ua_shipping_full_address || !$mrkv_ua_shipping_full_address_id)
					{
						?>
							<div class="admin_ua_ship_morkva__notification mrkv-notification-red"><?php echo esc_html__('Address Ref Incorrect', 'mrkv-ua-shipping'); ?></div>
						<?php
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
						$mrkv_ua_shipping_data = isset($mrkv_ua_shipping_ukr_settings['shipment']['type']) ? $mrkv_ua_shipping_ukr_settings['shipment']['type'] : '';
						echo wp_kses($mrkv_global_option_generator->get_input_radio(__('STANDARD', 'mrkv-ua-shipping'), 'mrkv_ua_ship_invoice_shipment_type', 'STANDARD', $mrkv_ua_shipping_data, $mrkv_ua_shipping_shipping_slug_option . '_mrkv_ua_ship_invoice_shipment_type_standart', 'STANDARD'), MRKV_UA_SHIPPING_ALLOW_TAGS);
						echo wp_kses($mrkv_global_option_generator->get_input_radio(__('EXPRESS', 'mrkv-ua-shipping'), 'mrkv_ua_ship_invoice_shipment_type', 'EXPRESS', $mrkv_ua_shipping_data, $mrkv_ua_shipping_shipping_slug_option . '_mrkv_ua_ship_invoice_shipment_type_express', 'STANDARD'), MRKV_UA_SHIPPING_ALLOW_TAGS);
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
						$mrkv_ua_shipping_data = isset($mrkv_ua_shipping_ukr_settings['payer']['delivery']) ? $mrkv_ua_shipping_ukr_settings['payer']['delivery'] : '';
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
					<?php echo esc_html__('Weight of the shipment, g', 'mrkv-ua-shipping'); ?>
				</label>
				<?php 
					$mrkv_ua_shipping_data = isset($mrkv_ua_shipping_ukr_settings['shipment']['weight']) ? $mrkv_ua_shipping_ukr_settings['shipment']['weight'] : '';

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
							$mrkv_ua_shipping_data = isset($mrkv_ua_shipping_ukr_settings['shipment']['length']) ? $mrkv_ua_shipping_ukr_settings['shipment']['length'] : '';
							echo wp_kses($mrkv_global_option_generator->get_input_number('', 'mrkv_ua_ship_invoice_shipment_length', $mrkv_ua_shipping_data, $mrkv_ua_shipping_shipping_slug_option. '_mrkv_ua_ship_invoice_shipment_length' , '', '', ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
						?>
					</div>
					<span>
						<svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M8 18C5.17157 18 3.75736 18 2.87868 17.1213C2 16.2426 2 14.8284 2 12C2 9.17157 2 7.75736 2.87868 6.87868C3.75736 6 5.17157 6 8 6C10.8284 6 12.2426 6 13.1213 6.87868C14 7.75736 14 9.17157 14 12" stroke="#ed6230" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M10 12C10 14.8284 10 16.2426 10.8787 17.1213C11.7574 18 13.1716 18 16 18C18.8284 18 20.2426 18 21.1213 17.1213C21.4211 16.8215 21.6186 16.4594 21.7487 16M22 12C22 9.17157 22 7.75736 21.1213 6.87868C20.2426 6 18.8284 6 16 6" stroke="#ed6230" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
					</span>
					<div class="adm_morkva_row_size__col">
						<span><?php echo esc_html__('Width', 'mrkv-ua-shipping'); ?></span>
						<?php 
							$mrkv_ua_shipping_data = isset($mrkv_ua_shipping_ukr_settings['shipment']['width']) ? $mrkv_ua_shipping_ukr_settings['shipment']['width'] : '';
							echo wp_kses($mrkv_global_option_generator->get_input_number('', 'mrkv_ua_ship_invoice_shipment_width', $mrkv_ua_shipping_data, $mrkv_ua_shipping_shipping_slug_option. '_mrkv_ua_ship_invoice_shipment_width' , '', '', ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
						?>
					</div>
					<span>
						<svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M8 18C5.17157 18 3.75736 18 2.87868 17.1213C2 16.2426 2 14.8284 2 12C2 9.17157 2 7.75736 2.87868 6.87868C3.75736 6 5.17157 6 8 6C10.8284 6 12.2426 6 13.1213 6.87868C14 7.75736 14 9.17157 14 12" stroke="#ed6230" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M10 12C10 14.8284 10 16.2426 10.8787 17.1213C11.7574 18 13.1716 18 16 18C18.8284 18 20.2426 18 21.1213 17.1213C21.4211 16.8215 21.6186 16.4594 21.7487 16M22 12C22 9.17157 22 7.75736 21.1213 6.87868C20.2426 6 18.8284 6 16 6" stroke="#ed6230" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
					</span>
					<div class="adm_morkva_row_size__col">
						<span><?php echo esc_html__('Height', 'mrkv-ua-shipping'); ?></span>
						<?php 
							$mrkv_ua_shipping_data = isset($mrkv_ua_shipping_ukr_settings['shipment']['height']) ? $mrkv_ua_shipping_ukr_settings['shipment']['height'] : '';
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
					<?php echo esc_html__('Declared cost', 'mrkv-ua-shipping'); ?>
				</label>
				<?php 
					echo wp_kses($mrkv_global_option_generator->get_input_number('', 'mrkv_ua_ship_invoice_cost', 0, $mrkv_ua_shipping_shipping_slug_option . '_mrkv_ua_ship_invoice_cost' , '', '', ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<label>
					<?php echo esc_html__('Cash on delivery, UAH', 'mrkv-ua-shipping'); ?>
				</label>
				<?php 
					echo wp_kses($mrkv_global_option_generator->get_input_number('', 'mrkv_ua_ship_invoice_cost_back', 0, $mrkv_ua_shipping_shipping_slug_option . '_mrkv_ua_ship_invoice_cost_back' , '', '', ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_ttn_create_row', $mrkv_ua_shipping_shipping_slug, 'row_8'); ?>
	<div class="admin_ua_ship_morkva_settings_line">
		<?php
			$mrkv_ua_shipping_data = isset($mrkv_ua_shipping_ukr_settings['shipment']['description']) ? $mrkv_ua_shipping_ukr_settings['shipment']['description'] : '';
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
			<div class="admin_ua_ship_morkva_settings_line">
				<label>
					<?php echo esc_html__('In case of failure to deliver', 'mrkv-ua-shipping'); ?>
				</label>
				<div class="admin_ua_ship_morkva_settings_row admin_ua_ship_morkva_settings_row-col">
					<?php
						$mrkv_ua_shipping_data = 'RETURN';
						echo wp_kses($mrkv_global_option_generator->get_input_radio(__('Return to the sender in 14 calendar days', 'mrkv-ua-shipping'), 'mrkv_ua_ship_invoice_return', 'RETURN', $mrkv_ua_shipping_data, $mrkv_ua_shipping_shipping_slug_option . '_mrkv_ua_ship_invoice_return_return', 'RETURN_AFTER_7_DAYS'), MRKV_UA_SHIPPING_ALLOW_TAGS);
						/*echo wp_kses($mrkv_global_option_generator->get_input_radio(__('Return the shipment after the expiration of the free storage period (5 working days)', 'mrkv-ua-shipping'), 'mrkv_ua_ship_invoice_return', 'RETURN_AFTER_7_DAYS', $mrkv_ua_shipping_data, $mrkv_ua_shipping_shipping_slug_option . '_mrkv_ua_ship_invoice_return_seven', 'RETURN_AFTER_7_DAYS');*/
						echo wp_kses($mrkv_global_option_generator->get_input_radio(__('Destroy the shipment', 'mrkv-ua-shipping') . ' ' . __('(this feature is only available if the sender pays for delivery)', 'mrkv-ua-shipping'), 'mrkv_ua_ship_invoice_return', 'PROCESS_AS_REFUSAL', $mrkv_ua_shipping_data, $mrkv_ua_shipping_shipping_slug_option . '_mrkv_ua_ship_invoice_return_process', 'RETURN'), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
				</div>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_ttn_create_row', $mrkv_ua_shipping_shipping_slug, 'row_11'); ?>
</form>
<?php
	$mrkv_ua_shipping_order_id = filter_input(INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT);
        
	if (!$mrkv_ua_shipping_order_id) {
		$mrkv_ua_shipping_order_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
	}

	if ($mrkv_ua_shipping_order_id) 
    {
    	$mrkv_ua_shipping_order_id = $mrkv_ua_shipping_order_id;
        $mrkv_ua_shipping_order = wc_get_order($mrkv_ua_shipping_order_id);

        if($mrkv_ua_shipping_order)
        {
        	$mrkv_ua_ship_invoice = $mrkv_ua_shipping_order->get_meta('mrkv_ua_ship_invoice_number');

	        if($mrkv_ua_ship_invoice)
	        {
	        	$mrkv_ua_shipping_sticker_default = isset($mrkv_ua_shipping_ukr_settings['shipment']['sticker']) ? $mrkv_ua_shipping_ukr_settings['shipment']['sticker'] : '';
	        	$mrkv_ua_shipping_production_bearer_ecom = isset($mrkv_ua_shipping_ukr_settings['production_bearer_ecom']) ? $mrkv_ua_shipping_ukr_settings['production_bearer_ecom'] : '';
	        	$mrkv_ua_shipping_production_cp_token = isset($mrkv_ua_shipping_ukr_settings['production_cp_token']) ? $mrkv_ua_shipping_ukr_settings['production_cp_token'] : '';
	        	$mrkv_ua_shipping_sticker_default_inter = isset($mrkv_ua_shipping_ukr_settings['international']['sticker']) ? $mrkv_ua_shipping_ukr_settings['international']['sticker'] : '';
	        	?>
	        		<form class="form-ukr-poshta-ttn" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" target="_blank" style="display: none;">
						<input type="hidden" name="action" value="mrkv_ua_print_ukrposhta_pdf">
						<?php wp_nonce_field( 'mrkv_ua_print_pdf_action', 'mrkv_ua_shipping_nonce' ); ?>
						<input type="hidden" name="invoice_number" value="<?php echo esc_attr($mrkv_ua_ship_invoice); ?>">
						<input type="hidden" name="type" value="<?php echo esc_attr($mrkv_ua_shipping_sticker_default); ?>">
						<input type="hidden" name="bearer" value="<?php echo esc_attr($mrkv_ua_shipping_production_bearer_ecom); ?>">
						<input type="hidden" name="cp_token" value="<?php echo esc_attr($mrkv_ua_shipping_production_cp_token); ?>">
						<input type="submit">
					</form>
	        	<?php
	        }
        }
    }
	else{
		$mrkv_ua_shipping_sticker_default = isset($mrkv_ua_shipping_ukr_settings['shipment']['sticker']) ? $mrkv_ua_shipping_ukr_settings['shipment']['sticker'] : '';
		$mrkv_ua_shipping_production_bearer_ecom = isset($mrkv_ua_shipping_ukr_settings['production_bearer_ecom']) ? $mrkv_ua_shipping_ukr_settings['production_bearer_ecom'] : '';
		$mrkv_ua_shipping_production_cp_token = isset($mrkv_ua_shipping_ukr_settings['production_cp_token']) ? $mrkv_ua_shipping_ukr_settings['production_cp_token'] : '';
		$mrkv_ua_shipping_sticker_default_inter = isset($mrkv_ua_shipping_ukr_settings['international']['sticker']) ? $mrkv_ua_shipping_ukr_settings['international']['sticker'] : '';
		?>
			<form class="form-ukr-poshta-ttn form-ukr-poshta-ttn-orders" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" target="_blank" style="display: none;">
				<input type="hidden" name="action" value="mrkv_ua_print_ukrposhta_pdf">
				<?php wp_nonce_field( 'mrkv_ua_print_pdf_action', 'mrkv_ua_shipping_nonce' ); ?>
				<input type="hidden" name="invoice_number" value="">
				<input type="hidden" name="type" value="<?php echo esc_attr($mrkv_ua_shipping_sticker_default); ?>">
				<input type="hidden" name="bearer" value="<?php echo esc_attr($mrkv_ua_shipping_production_bearer_ecom); ?>">
				<input type="hidden" name="cp_token" value="<?php echo esc_attr($mrkv_ua_shipping_production_cp_token); ?>">
				<input type="submit">
			</form>
		<?php
	}
?>
