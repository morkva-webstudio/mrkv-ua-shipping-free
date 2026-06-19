<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<section id="basic_settings" class="mrkv_up_ship_shipping_tab_block active">
	<h2><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/settings-icon.svg'); ?>" alt="Basic settings" title="Basic settings"><?php echo esc_html__('Basic settings', 'mrkv-ua-shipping'); ?></h2>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'basic_first'); ?>
	<div class="admin_ua_ship_morkva_settings_line">
		<?php 
			$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['api_key']) ? MRKV_SHIPPING_SETTINGS['api_key'] : '';
			$mrkv_ua_shipping_label = __('API Key', 'mrkv-ua-shipping');
			global $mrkv_global_option_generator;
			global $mrkv_global_shipping_object;

			if(is_string($mrkv_global_shipping_object->active_api))
			{
				$mrkv_ua_shipping_label .= '<div class="admin_ua_ship_morkva__notification mrkv-notification-red">' . $mrkv_global_shipping_object->active_api . '</div>';
			}
			elseif($mrkv_global_shipping_object->active_api)
			{
				$mrkv_ua_shipping_label .= '<div class="admin_ua_ship_morkva__notification mrkv-notification-green">' . __('API key correct','mrkv-ua-shipping') . '</div>';
			}

			$mrkv_ua_shipping_description = __('Not sure where to get the key? Take a look', 'mrkv-ua-shipping') . ' <a target="blanc" href="http://my.novaposhta.ua/settings/index#apikeys">' . __('this video', 'mrkv-ua-shipping') . '</a>';

			echo wp_kses( $mrkv_global_option_generator->get_input_text($mrkv_ua_shipping_label, MRKV_OPTION_OBJECT_NAME . '[api_key]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_api_key' , '', __('Enter the key...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
		?>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'basic_last'); ?>
</section>
<section id="sender_settings" class="mrkv_up_ship_shipping_tab_block">
	<h2><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/user-icon.svg'); ?>" alt="Sender settings" title="Sender settings"><?php echo esc_html__('Sender Settings', 'mrkv-ua-shipping'); ?></h2>
	<p><?php echo esc_html__('Fill in the data for the sender, which will be used to create shipment', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'sender_first'); ?>
	<div class="admin_ua_ship_morkva_settings_line">
		<?php
			$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['sender']['ref']) ? MRKV_SHIPPING_SETTINGS['sender']['ref'] : '';
			$mrkv_ua_shipping_label = __('Sender', 'mrkv-ua-shipping');

			if(empty(MRKV_OPTION_NOVA_SENDER))
			{
				$mrkv_ua_shipping_label .= '<div class="admin_ua_ship_morkva__notification mrkv-notification-red">' . __('Sender list empty', 'mrkv-ua-shipping') . '</div>';
			}
			elseif(isset(MRKV_OPTION_NOVA_SENDER[0]))
			{
				$mrkv_ua_shipping_label .= '<div class="admin_ua_ship_morkva__notification mrkv-notification-green">' . __('Sender list loaded','mrkv-ua-shipping') . '</div>';
			}

			echo wp_kses( $mrkv_global_option_generator->get_select_tag($mrkv_ua_shipping_label, MRKV_OPTION_OBJECT_NAME . '[sender][ref]', MRKV_OPTION_NOVA_SENDER, $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_sender_ref' , __('Choose a sender', 'mrkv-ua-shipping')), MRKV_UA_SHIPPING_ALLOW_TAGS);

			if(isset(MRKV_OPTION_NOVA_SENDER[0]['attr']) && !empty(MRKV_OPTION_NOVA_SENDER))
			{
				foreach(MRKV_OPTION_NOVA_SENDER[0]['attr'] as $mrkv_ua_shipping_sender_data_key => $mrkv_ua_shipping_sender_data_val)
				{
					if($mrkv_ua_shipping_sender_data_key == 'ref')
					{
						continue;
					}
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['sender'][$mrkv_ua_shipping_sender_data_key]) && MRKV_SHIPPING_SETTINGS['sender'][$mrkv_ua_shipping_sender_data_key] ? MRKV_SHIPPING_SETTINGS['sender'][$mrkv_ua_shipping_sender_data_key] : $mrkv_ua_shipping_sender_data_val;
					echo wp_kses( $mrkv_global_option_generator->get_input_hidden(MRKV_OPTION_OBJECT_NAME . '[sender][' . $mrkv_ua_shipping_sender_data_key . ']', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_sender_' . $mrkv_ua_shipping_sender_data_key), MRKV_UA_SHIPPING_ALLOW_TAGS);
				}
			}
			$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['sender']['list']) ? MRKV_SHIPPING_SETTINGS['sender']['list'] : '';
			echo wp_kses( $mrkv_global_option_generator->get_input_hidden(MRKV_OPTION_OBJECT_NAME . '[sender][list]', base64_encode(wp_json_encode(MRKV_OPTION_NOVA_SENDER)), MRKV_OPTION_OBJECT_NAME . '_sender_list'), MRKV_UA_SHIPPING_ALLOW_TAGS);
		?>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'sender_middle_1'); ?>
	<h3><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/routing-icon.svg'); ?>" alt="Sender address" title="Sender address"><?php echo esc_html__('Sender address', 'mrkv-ua-shipping'); ?></h3>
	<p><?php echo esc_html__('Specify the sender\'s address from which the goods will be sent', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'sender_middle_2'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="admin_ua_ship_morkva_settings_line col-mrkv-5">
			<?php 
				$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['sender']['city']['name']) ? MRKV_SHIPPING_SETTINGS['sender']['city']['name'] : '';
				$mrkv_ua_shipping_description = __('Enter the first 2-3 letters and wait for the data to load', 'mrkv-ua-shipping');

				echo wp_kses( $mrkv_global_option_generator->get_input_text(__('City', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[sender][city][name]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_sender_city_name' , '', __('Enter the city...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
				$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['sender']['city']['ref']) ? MRKV_SHIPPING_SETTINGS['sender']['city']['ref'] : '';
				echo wp_kses( $mrkv_global_option_generator->get_input_hidden(MRKV_OPTION_OBJECT_NAME . '[sender][city][ref]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_sender_city_ref'), MRKV_UA_SHIPPING_ALLOW_TAGS);
			?>
		</div>
		<div class="admin_ua_ship_morkva_settings_line col-mrkv-5">
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'sender_middle_3'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="admin_ua_ship_morkva_settings_line col-mrkv-5">
			<?php
				$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['sender']['address_type']) ? MRKV_SHIPPING_SETTINGS['sender']['address_type'] : '';
				echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('Sending from a branch', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[sender][address_type]', 'W', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_sender_address_type_w', 'W'), MRKV_UA_SHIPPING_ALLOW_TAGS);
			?>
			<div class="admin_ua_ship_morkva_settings_line__inner">
				<?php 
				$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['sender']['warehouse']['name']) ? esc_attr(MRKV_SHIPPING_SETTINGS['sender']['warehouse']['name']) : '';
				$mrkv_ua_shipping_description = __('Enter the first 2-3 letters and wait for the data to load', 'mrkv-ua-shipping');

				echo wp_kses( $mrkv_global_option_generator->get_input_text(__('Warehouse', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[sender][warehouse][name]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_sender_warehouse_name' , '', __('Enter the warehouse...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
				$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['sender']['warehouse']['ref']) ? MRKV_SHIPPING_SETTINGS['sender']['warehouse']['ref'] : '';
				echo wp_kses( $mrkv_global_option_generator->get_input_hidden(MRKV_OPTION_OBJECT_NAME . '[sender][warehouse][ref]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_sender_warehouse_ref'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['sender']['warehouse']['number']) ? MRKV_SHIPPING_SETTINGS['sender']['warehouse']['number'] : '';
				echo wp_kses( $mrkv_global_option_generator->get_input_hidden(MRKV_OPTION_OBJECT_NAME . '[sender][warehouse][number]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_sender_warehouse_number'), MRKV_UA_SHIPPING_ALLOW_TAGS);
			?>
			</div>
		</div>
		<div class="admin_ua_ship_morkva_settings_line col-mrkv-5">
			<?php
				$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['sender']['address_type']) ? MRKV_SHIPPING_SETTINGS['sender']['address_type'] : '';
				echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('Sending from the address', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[sender][address_type]', 'D', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_sender_address_type_d', 'W'), MRKV_UA_SHIPPING_ALLOW_TAGS);
			?>
			<div class="admin_ua_ship_morkva_settings_line__inner">
				<?php 
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['sender']['street']['name']) ? MRKV_SHIPPING_SETTINGS['sender']['street']['name'] : '';
					echo wp_kses( $mrkv_global_option_generator->get_input_text(__('Street', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[sender][street][name]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_sender_street_name' , '', __('Enter the street...', 'mrkv-ua-shipping')), MRKV_UA_SHIPPING_ALLOW_TAGS);
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['sender']['street']['ref']) ? MRKV_SHIPPING_SETTINGS['sender']['street']['ref'] : '';
					echo wp_kses( $mrkv_global_option_generator->get_input_hidden(MRKV_OPTION_OBJECT_NAME . '[sender][street][ref]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_sender_street_ref'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
				<p class="mrkv-ua-ship-description"><?php echo esc_html__('Enter the first 2-3 letters and wait for the data to load', 'mrkv-ua-shipping'); ?></p>
				<div class="admin_ua_ship_morkva_settings_row">
					<div class="admin_ua_ship_morkva_settings_line col-mrkv-5">
						<?php 
							$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['sender']['street']['house']) ? MRKV_SHIPPING_SETTINGS['sender']['street']['house'] : '';
							echo wp_kses( $mrkv_global_option_generator->get_input_text('', MRKV_OPTION_OBJECT_NAME . '[sender][street][house]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_sender_street_house' , '', __('Enter the house...', 'mrkv-ua-shipping')), MRKV_UA_SHIPPING_ALLOW_TAGS);
						?>
					</div>
					<div class="admin_ua_ship_morkva_settings_line col-mrkv-5">
						<?php
							$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['sender']['street']['flat']) ? MRKV_SHIPPING_SETTINGS['sender']['street']['flat'] : '';
							echo wp_kses( $mrkv_global_option_generator->get_input_text('', MRKV_OPTION_OBJECT_NAME . '[sender][street][flat]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_sender_street_flat' , '', __('Apartment / Office', 'mrkv-ua-shipping')), MRKV_UA_SHIPPING_ALLOW_TAGS);
						?>
					</div>
				</div>
				<?php 
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['sender']['address']['ref']) ? MRKV_SHIPPING_SETTINGS['sender']['address']['ref'] : '';
					echo wp_kses( $mrkv_global_option_generator->get_input_hidden(MRKV_OPTION_OBJECT_NAME . '[sender][address][ref]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_sender_address_ref'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'sender_last'); ?>
</section>
<section id="default_settings" class="mrkv_up_ship_shipping_tab_block">
	<h2><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/box-icon.svg'); ?>" alt="Default values" title="Default values"><?php echo esc_html__('Default values', 'mrkv-ua-shipping'); ?></h2>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'default_first'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<h4><?php echo esc_html__('Payer of delivery', 'mrkv-ua-shipping'); ?></h4>
				<div class="admin_ua_ship_morkva_settings_row">
					<?php
						$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['payer']['delivery']) ? MRKV_SHIPPING_SETTINGS['payer']['delivery'] : '';
						echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('Recipient', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[payer][delivery]', 'Recipient', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_payer_delivery_recipient', 'Recipient'), MRKV_UA_SHIPPING_ALLOW_TAGS);
						echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('Sender', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[payer][delivery]', 'Sender', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_payer_delivery_sender', 'Recipient'), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
				</div>
			</div>
		</div>
		<div class="col-mrkv-5">
		</div>
	</div>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
				<h4><?php echo esc_html__('Payer for the cash on delivery function', 'mrkv-ua-shipping'); ?></h4>
				<p class="mrkv-ua-ship-only-pro"><?php echo esc_html__('Only in the Pro version', 'mrkv-ua-shipping'); ?></p>
				<div class="admin_ua_ship_morkva_settings_row">
					<?php
						$mrkv_ua_shipping_data = '';
						echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('Recipient', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[payer][cash]', 'Recipient', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_payer_cash_recipient', 'Recipient', 'disabled'), MRKV_UA_SHIPPING_ALLOW_TAGS);
						echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('Sender', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[payer][cash]', 'Sender', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_payer_cash_sender', 'Recipient', 'disabled'), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
				</div>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
				<h4><?php echo esc_html__('Payer for the cash on delivery (Free shipping)', 'mrkv-ua-shipping'); ?></h4>
				<p class="mrkv-ua-ship-only-pro"><?php echo esc_html__('Only in the Pro version', 'mrkv-ua-shipping'); ?></p>
				<div class="admin_ua_ship_morkva_settings_row">
					<?php
						$mrkv_ua_shipping_data = '';
						echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('Recipient', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[payer][cash_free]', 'Recipient', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_payer_cash_recipient', 'Recipient', 'disabled'), MRKV_UA_SHIPPING_ALLOW_TAGS);
						echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('Sender', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[payer][cash_free]', 'Sender', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_payer_cash_sender', 'Recipient', 'disabled'), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
				</div>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'default_middle_1'); ?>
	<div class="admin_ua_ship_morkva_settings_line admin_ua_ship_morkva_one_data mrkv-field-disabled">
		<?php 
			$mrkv_ua_shipping_data = '';
			$mrkv_ua_shipping_description = '<span class="mrkv-ua-ship-only-pro">' . __('Only in the Pro version', 'mrkv-ua-shipping') . '</span>' . __('If filled in, for cash on delivery, this amount will be deducted from the shipment value', 'mrkv-ua-shipping');

			echo wp_kses( $mrkv_global_option_generator->get_input_number(__('Prepayment', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[shipment][prepayment]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_shipment_prepayment' , '', '', $mrkv_ua_shipping_description, 'readonly'), MRKV_UA_SHIPPING_ALLOW_TAGS);
		?>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'default_middle_2'); ?>
	<h3><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/tuning-icon.svg'); ?>" alt="Shipment" title="Shipment"><?php echo esc_html__('Shipment', 'mrkv-ua-shipping'); ?></h3>
	<p><?php echo esc_html__('Fill in the default shipping data for the shipment', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'default_middle_3'); ?>
	<div class="admin_ua_ship_morkva_settings_line">
		<h4><?php echo esc_html__('Global Cargo Type', 'mrkv-ua-shipping'); ?></h4>
		<div class="admin_ua_ship_morkva_settings_row">
			<?php
				$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['shipment']['type']) ? MRKV_SHIPPING_SETTINGS['shipment']['type'] : '';
				echo wp_kses($mrkv_global_option_generator->get_input_radio(__('Parcel', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[shipment][type]', 'Parcel', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_shipment_type_parcel', 'Parcel'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				echo wp_kses($mrkv_global_option_generator->get_input_radio(__('Pallet', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[shipment][type]', 'Pallet', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_shipment_type_pallet', 'Parcel'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				echo wp_kses($mrkv_global_option_generator->get_input_radio(__('Documents', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[shipment][type]', 'Documents', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_shipment_type_documents', 'Parcel'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				echo wp_kses($mrkv_global_option_generator->get_input_radio(__('Tires', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[shipment][type]', 'TiresWheels', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_shipment_type_tires', 'Parcel'), MRKV_UA_SHIPPING_ALLOW_TAGS);
			?>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'default_middle_4'); ?>
	<div class="admin_ua_ship_morkva_settings_line admin_ua_shipping_classes">
		<?php
			$mrkv_ua_shipping_classes_enabled = isset(MRKV_SHIPPING_SETTINGS['shipment']['class']['enabled']) ? MRKV_SHIPPING_SETTINGS['shipment']['class']['enabled'] : '';
			echo wp_kses($mrkv_global_option_generator->get_input_checkbox(__('Enabled classes support', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[shipment][class][enabled]', $mrkv_ua_shipping_classes_enabled, MRKV_OPTION_OBJECT_NAME . '_shipment_class_enabled', ), MRKV_UA_SHIPPING_ALLOW_TAGS);

			$mrkv_ua_shipping_shipping_classes = [];

			foreach ( WC()->shipping()->get_shipping_classes() as $mrkv_ua_shipping_class ) {
			    $mrkv_ua_shipping_shipping_classes[ (int) $mrkv_ua_shipping_class->term_id ] = $mrkv_ua_shipping_class->name;
			}
		?>
		<p class="mrkv-ua-ship-description">
			<?php echo esc_html__('Enable if you need to use cargo types for product classes', 'mrkv-ua-shipping'); ?>
		</p>
		<div class="admin_ua_ship_morkva_settings_line__inner inner-align">
			<div class="admin_ua_ship_morkva_settings_row">
				<div class="col-mrkv-5">
					<h4><?php echo esc_html__('Cargo Type', 'mrkv-ua-shipping'); ?></h4>
				</div>
				<div class="col-mrkv-5">
					<h4><?php echo esc_html__('Product classes', 'mrkv-ua-shipping'); ?></h4>
				</div>
			</div>
			<div class="admin_ua_ship_morkva_settings_row">
				<div class="col-mrkv-5">
					<p><?php echo esc_html__('Parcel', 'mrkv-ua-shipping'); ?></p>
				</div>
				<div class="col-mrkv-5">
					<?php 
						$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['shipment']['class']['list']['Parcel']) ? MRKV_SHIPPING_SETTINGS['shipment']['class']['list']['Parcel'] : '';
						echo wp_kses($mrkv_global_option_generator->get_select_multiple('', MRKV_OPTION_OBJECT_NAME . '[shipment][class][list][Parcel][]', $mrkv_ua_shipping_shipping_classes, $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_shipment_class_list_parcel', '' , __('Choose classes', 'mrkv-ua-shipping'),  'multiple'), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
				</div>
			</div>
			<div class="admin_ua_ship_morkva_settings_row">
				<div class="col-mrkv-5">
					<p><?php echo esc_html__('Pallet', 'mrkv-ua-shipping'); ?></p>
				</div>
				<div class="col-mrkv-5">
					<?php 
						$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['shipment']['class']['list']['Pallet']) ? MRKV_SHIPPING_SETTINGS['shipment']['class']['list']['Pallet'] : '';
						echo wp_kses($mrkv_global_option_generator->get_select_multiple('', MRKV_OPTION_OBJECT_NAME . '[shipment][class][list][Pallet][]', $mrkv_ua_shipping_shipping_classes, $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_shipment_class_list_pallet', '' , __('Choose classes', 'mrkv-ua-shipping'),  'multiple'), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
				</div>
			</div>
			<div class="admin_ua_ship_morkva_settings_row">
				<div class="col-mrkv-5">
					<p><?php echo esc_html__('Documents', 'mrkv-ua-shipping'); ?></p>
				</div>
				<div class="col-mrkv-5">
					<?php 
						$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['shipment']['class']['list']['Documents']) ? MRKV_SHIPPING_SETTINGS['shipment']['class']['list']['Documents'] : '';
						echo wp_kses($mrkv_global_option_generator->get_select_multiple('', MRKV_OPTION_OBJECT_NAME . '[shipment][class][list][Documents][]', $mrkv_ua_shipping_shipping_classes, $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_shipment_class_list_documents', '' , __('Choose classes', 'mrkv-ua-shipping'),  'multiple'), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
				</div>
			</div>
			<div class="admin_ua_ship_morkva_settings_row">
				<div class="col-mrkv-5">
					<p><?php echo esc_html__('TiresWheels', 'mrkv-ua-shipping'); ?></p>
				</div>
				<div class="col-mrkv-5">
					<?php 
						$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['shipment']['class']['list']['TiresWheels']) ? MRKV_SHIPPING_SETTINGS['shipment']['class']['list']['TiresWheels'] : '';
						echo wp_kses($mrkv_global_option_generator->get_select_multiple('', MRKV_OPTION_OBJECT_NAME . '[shipment][class][list][TiresWheels][]', $mrkv_ua_shipping_shipping_classes, $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_shipment_class_list_tire', '' , __('Choose classes', 'mrkv-ua-shipping'),  'multiple'), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
				</div>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'default_middle_5'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<h4><?php echo esc_html__('Payment type (Sender-related)', 'mrkv-ua-shipping'); ?></h4>
				<div class="admin_ua_ship_morkva_settings_row">
					<?php
						$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['shipment']['payment']) ? MRKV_SHIPPING_SETTINGS['shipment']['payment'] : '';
						echo wp_kses($mrkv_global_option_generator->get_input_radio(__('Cash', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[shipment][payment]', 'Cash', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_shipment_payment_cash', 'Cash'), MRKV_UA_SHIPPING_ALLOW_TAGS);

						$mrkv_ua_shipping_label = __('NonCash', 'mrkv-ua-shipping') . '<span class="mrkv-up-ship-tooltip"><img src="' . MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/info-icon.svg' .'" ><div class="mrkv-up-ship-tooltip__data">' . __('Cashless payment for the sender is available only if the contract is signed.', 'mrkv-ua-shipping') . '</div></span>';
						echo wp_kses($mrkv_global_option_generator->get_input_radio($mrkv_ua_shipping_label, MRKV_OPTION_OBJECT_NAME . '[shipment][payment]', 'NonCash', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_shipment_payment_cashless', 'Cash'), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
				</div>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'default_middle_6'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<h4><?php echo esc_html__('Weight, kg', 'mrkv-ua-shipping'); ?></h4>
				<?php 
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['shipment']['weight']) ? MRKV_SHIPPING_SETTINGS['shipment']['weight'] : '';

					echo wp_kses( $mrkv_global_option_generator->get_input_number('', MRKV_OPTION_OBJECT_NAME . '[shipment][weight]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_shipment_weight' , '', '', ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<h4><?php echo esc_html__('Dimensions, cm', 'mrkv-ua-shipping'); ?></h4>
				<div class="adm_morkva_row_size">
					<div class="adm_morkva_row_size__col">
						<span><?php echo esc_html__('Length', 'mrkv-ua-shipping'); ?></span>
						<?php 
							$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['shipment']['length']) ? MRKV_SHIPPING_SETTINGS['shipment']['length'] : '';
							echo wp_kses( $mrkv_global_option_generator->get_input_number('', MRKV_OPTION_OBJECT_NAME . '[shipment][length]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_shipment_length' , '', '', ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
						?>
					</div>
					<span>
						<svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M8 18C5.17157 18 3.75736 18 2.87868 17.1213C2 16.2426 2 14.8284 2 12C2 9.17157 2 7.75736 2.87868 6.87868C3.75736 6 5.17157 6 8 6C10.8284 6 12.2426 6 13.1213 6.87868C14 7.75736 14 9.17157 14 12" stroke="#ed6230" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M10 12C10 14.8284 10 16.2426 10.8787 17.1213C11.7574 18 13.1716 18 16 18C18.8284 18 20.2426 18 21.1213 17.1213C21.4211 16.8215 21.6186 16.4594 21.7487 16M22 12C22 9.17157 22 7.75736 21.1213 6.87868C20.2426 6 18.8284 6 16 6" stroke="#ed6230" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
					</span>
					<div class="adm_morkva_row_size__col">
						<span><?php echo esc_html__('Width', 'mrkv-ua-shipping'); ?></span>
						<?php 
							$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['shipment']['width']) ? MRKV_SHIPPING_SETTINGS['shipment']['width'] : '';
							echo wp_kses( $mrkv_global_option_generator->get_input_number('', MRKV_OPTION_OBJECT_NAME . '[shipment][width]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_shipment_width' , '', '', ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
						?>
					</div>
					<span>
						<svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M8 18C5.17157 18 3.75736 18 2.87868 17.1213C2 16.2426 2 14.8284 2 12C2 9.17157 2 7.75736 2.87868 6.87868C3.75736 6 5.17157 6 8 6C10.8284 6 12.2426 6 13.1213 6.87868C14 7.75736 14 9.17157 14 12" stroke="#ed6230" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M10 12C10 14.8284 10 16.2426 10.8787 17.1213C11.7574 18 13.1716 18 16 18C18.8284 18 20.2426 18 21.1213 17.1213C21.4211 16.8215 21.6186 16.4594 21.7487 16M22 12C22 9.17157 22 7.75736 21.1213 6.87868C20.2426 6 18.8284 6 16 6" stroke="#ed6230" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
					</span>
					<div class="adm_morkva_row_size__col">
						<span><?php echo esc_html__('Height', 'mrkv-ua-shipping'); ?></span>
						<?php 
							$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['shipment']['height']) ? MRKV_SHIPPING_SETTINGS['shipment']['height'] : '';
							echo wp_kses( $mrkv_global_option_generator->get_input_number('', MRKV_OPTION_OBJECT_NAME . '[shipment][height]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_shipment_height' , '', '', ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'default_middle_7'); ?>
	<div class="admin_ua_ship_morkva_settings_line admin_ua_ship_morkva_one_data">
		<?php 
			$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['shipment']['volume']) ? MRKV_SHIPPING_SETTINGS['shipment']['volume'] : '';
			$mrkv_ua_shipping_description = __('It is calculated automatically according to the dimensions in the settings.', 'mrkv-ua-shipping');

			echo wp_kses( $mrkv_global_option_generator->get_input_number(__('Volumetric weight', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[shipment][volume]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_shipment_volume' , '', '', $mrkv_ua_shipping_description, 'readonly'), MRKV_UA_SHIPPING_ALLOW_TAGS);
		?>
		<p><strong><?php echo esc_html__('These standard weight and dimensions apply when products do not have ones of their own', 'mrkv-ua-shipping'); ?></strong></p>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'default_middle_8'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<?php 
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['shipment']['cart_total']) ? MRKV_SHIPPING_SETTINGS['shipment']['cart_total'] : '';
					$mrkv_ua_shipping_cart_total = array(
						'subtotal' => __('From the intermediate cost of the order (excluding promotional codes)', 'mrkv-ua-shipping'),
						'total' => __('Of the total cost of the order (including promotional codes)', 'mrkv-ua-shipping'),
					);

					$mrkv_ua_shipping_description = __('Choose how much the shipping cost will be calculated', 'mrkv-ua-shipping');

					echo wp_kses( $mrkv_global_option_generator->get_select_simple(__('Free shipping calculation', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[shipment][cart_total]', $mrkv_ua_shipping_cart_total, $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_shipment_cart_total' , __('Choose a cart cost', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
		<div class="col-mrkv-5">
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'default_middle_9'); ?>
	<div class="admin_ua_ship_morkva_settings_line">
		<?php
			$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['shipment']['description']) ? MRKV_SHIPPING_SETTINGS['shipment']['description'] : '';
			$mrkv_ua_shipping_description = __('Maximum number of characters:', 'mrkv-ua-shipping') . ' 100';

			echo wp_kses( $mrkv_global_option_generator->get_textarea(__('Description of the shipment', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[shipment][description]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_shipment_description' , '', __('For example, products for children...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
		?>
		<div class="admin_ua_ship_morkva_settings_row admin_ua_ship_morkva_settings_row_btns">
			<h4><?php echo esc_html__('Click to insert the shortcode:', 'mrkv-ua-shipping'); ?></h4>
			<div data-added="[order_id]" class="button button-primary adm-textarea-btn"><?php echo esc_html__('Order number', 'mrkv-ua-shipping'); ?></div>
			<div data-added="[product_list]" class="button button-primary adm-textarea-btn"><?php echo esc_html__('List of products', 'mrkv-ua-shipping'); ?></div>
			<div data-added="[product_list_qa]" class="button button-primary adm-textarea-btn"><?php echo esc_html__('List of goods (with articles and quantity)', 'mrkv-ua-shipping'); ?></div>
			<div data-added="[product_list_a]" class="button button-primary adm-textarea-btn"><?php echo esc_html__('List of articles with quantity', 'mrkv-ua-shipping'); ?></div>
			<div data-added="[quantity]" class="button button-primary adm-textarea-btn"><?php echo esc_html__('Quantity of goods', 'mrkv-ua-shipping'); ?></div>
			<div data-added="[quantity_p]" class="button button-primary adm-textarea-btn"><?php echo esc_html__(' Number of positions', 'mrkv-ua-shipping'); ?></div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'default_last'); ?>
</section>
<section id="international_settings" class="mrkv_up_ship_shipping_tab_block">
	<h2><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/map-icon.svg'); ?>" alt="International shipping settings" title="International shipping settings"><?php echo esc_html__('International shipping settings', 'mrkv-ua-shipping'); ?></h2>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'international_first'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<h4><?php echo esc_html__('Payer of delivery', 'mrkv-ua-shipping'); ?></h4>
				<div class="admin_ua_ship_morkva_settings_row">
					<?php
						$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['internal_api_server']) ? MRKV_SHIPPING_SETTINGS['internal_api_server'] : 'sandbox';
						echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('Production', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[internal_api_server]', 'production', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_internal_api_server_production', 'sandbox'), MRKV_UA_SHIPPING_ALLOW_TAGS);
						echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('Sandbox', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[internal_api_server]', 'sandbox', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_internal_api_server_sandbox', 'sandbox'), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
				</div>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'international_middle_1'); ?>
	<div class="admin_ua_ship_morkva_settings_line">
		<?php 
			$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['internal_api_key']) ? MRKV_SHIPPING_SETTINGS['internal_api_key'] : '';
			$mrkv_ua_shipping_label = __('API Key', 'mrkv-ua-shipping');

			require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/nova-poshta/api/mrkv-ua-shipping-api-nova-post.php';
			$mrkv_ua_shipping_api_internal = new MRKV_UA_SHIPPING_API_NOVA_POST(MRKV_SHIPPING_SETTINGS);

			if(is_string($mrkv_ua_shipping_api_internal->active_api))
			{
				$mrkv_ua_shipping_label .= '<div class="admin_ua_ship_morkva__notification mrkv-notification-red">' . $mrkv_ua_shipping_api_internal->active_api . '</div>';
			}
			elseif($mrkv_ua_shipping_api_internal->active_api)
			{
				$mrkv_ua_shipping_label .= '<div class="admin_ua_ship_morkva__notification mrkv-notification-green">' . __('API key correct','mrkv-ua-shipping') . '</div>';
			}

			$mrkv_ua_shipping_description = __('During the registration process, you will receive an API access key. Make sure to store this information in a secure place.', 'mrkv-ua-shipping');

			echo wp_kses( $mrkv_global_option_generator->get_input_text($mrkv_ua_shipping_label, MRKV_OPTION_OBJECT_NAME . '[internal_api_key]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_internal_api_key' , '', __('Enter the key...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
		?>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'international_middle_2'); ?>
	<h3><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/user-icon.svg'); ?>" alt="Sender settings" title="Sender settings"><?php echo esc_html__('Sender settings', 'mrkv-ua-shipping'); ?></h3>
	<p><?php echo esc_html__('Fill in the default shipping data sender', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'international_middle_3'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<h4><?php echo esc_html__('Payer of delivery', 'mrkv-ua-shipping'); ?></h4>
				<div class="admin_ua_ship_morkva_settings_row">
					<?php
						$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['inter']['payer']) ? MRKV_SHIPPING_SETTINGS['inter']['payer'] : '';
						echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('Recipient', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[inter][payer]', 'Recipient', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_inter_payer_recipient', 'Recipient'), MRKV_UA_SHIPPING_ALLOW_TAGS);
						echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('Sender', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[inter][payer]', 'Sender', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_inter_payer_sender', 'Recipient'), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
				</div>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<?php 
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['inter']['cart_total']) ? MRKV_SHIPPING_SETTINGS['inter']['cart_total'] : '';

					$mrkv_ua_shipping_description = __('Choose how much the shipping cost will be calculated', 'mrkv-ua-shipping');

					echo wp_kses( $mrkv_global_option_generator->get_select_simple(__('Free shipping calculation', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[inter][cart_total]', $mrkv_ua_shipping_cart_total, $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_inter_cart_total' , __('Choose a cart cost', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'international_middle_4'); ?>
	<div class="admin_ua_ship_morkva_settings_line">
		<?php 
			$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['inter']['division_address']) ? MRKV_SHIPPING_SETTINGS['inter']['division_address'] : '';
			$mrkv_ua_shipping_label = __('Sender division', 'mrkv-ua-shipping');

			$mrkv_ua_shipping_description = __('Currently, only shipping from the branch is supported.', 'mrkv-ua-shipping');

			echo wp_kses( $mrkv_global_option_generator->get_input_text($mrkv_ua_shipping_label, MRKV_OPTION_OBJECT_NAME . '[inter][division_address]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_inter_division_address' , '', __('Enter the address...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
		
			$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['inter']['division_id']) ? MRKV_SHIPPING_SETTINGS['inter']['division_id'] : '';
			echo wp_kses( $mrkv_global_option_generator->get_input_hidden(MRKV_OPTION_OBJECT_NAME . '[inter][division_id]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_inter_division_id'), MRKV_UA_SHIPPING_ALLOW_TAGS);

			$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['inter']['division_number']) ? MRKV_SHIPPING_SETTINGS['inter']['division_number'] : '';
			echo wp_kses( $mrkv_global_option_generator->get_input_hidden(MRKV_OPTION_OBJECT_NAME . '[inter][division_number]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_inter_division_number'), MRKV_UA_SHIPPING_ALLOW_TAGS);
		?>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'international_middle_5'); ?>
	<h3><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/tuning-icon.svg'); ?>" alt="Shipment" title="Shipment"><?php echo esc_html__('Shipment', 'mrkv-ua-shipping'); ?></h3>
	<p><?php echo esc_html__('Fill in the default shipping data for the shipment', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'international_middle_6'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<h4><?php echo esc_html__('Type', 'mrkv-ua-shipping'); ?></h4>
				<div class="admin_ua_ship_morkva_settings_row">
					<?php
						$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['inter']['shipment_type']) ? MRKV_SHIPPING_SETTINGS['inter']['shipment_type'] : '';
						echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('Parcel', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[inter][shipment_type]', 'Parcel', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_inter_shipment_type_parcel', 'Parcel'), MRKV_UA_SHIPPING_ALLOW_TAGS);
						/*echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('Pallet', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[inter][shipment_type]', 'Pallet', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_inter_shipment_type_pallet', 'Parcel'), MRKV_UA_SHIPPING_ALLOW_TAGS);*/
						echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('Documents', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[inter][shipment_type]', 'Documents', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_inter_shipment_type_documents', 'Parcel'), MRKV_UA_SHIPPING_ALLOW_TAGS);
						echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('TiresWheels', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[inter][shipment_type]', 'TiresWheels', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_inter_shipment_type_tirewheels', 'Parcel', true), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
				</div>
			</div>
		</div>
		<div class="col-mrkv-5"></div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'international_middle_7'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<h4><?php echo esc_html__('Weight, kg', 'mrkv-ua-shipping'); ?></h4>
				<?php 
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['inter']['weight']) ? MRKV_SHIPPING_SETTINGS['inter']['weight'] : '';

					echo wp_kses( $mrkv_global_option_generator->get_input_number('', MRKV_OPTION_OBJECT_NAME . '[inter][weight]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_inter_weight' , '', '', ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<h4><?php echo esc_html__('Dimensions, cm', 'mrkv-ua-shipping'); ?></h4>
				<div class="adm_morkva_row_size">
					<div class="adm_morkva_row_size__col">
						<span><?php echo esc_html__('Length', 'mrkv-ua-shipping'); ?></span>
						<?php 
							$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['inter']['length']) ? MRKV_SHIPPING_SETTINGS['inter']['length'] : '';
							echo wp_kses( $mrkv_global_option_generator->get_input_number('', MRKV_OPTION_OBJECT_NAME . '[inter][length]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_inter_length' , '', '', ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
						?>
					</div>
					<span>
						<svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M8 18C5.17157 18 3.75736 18 2.87868 17.1213C2 16.2426 2 14.8284 2 12C2 9.17157 2 7.75736 2.87868 6.87868C3.75736 6 5.17157 6 8 6C10.8284 6 12.2426 6 13.1213 6.87868C14 7.75736 14 9.17157 14 12" stroke="#ed6230" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M10 12C10 14.8284 10 16.2426 10.8787 17.1213C11.7574 18 13.1716 18 16 18C18.8284 18 20.2426 18 21.1213 17.1213C21.4211 16.8215 21.6186 16.4594 21.7487 16M22 12C22 9.17157 22 7.75736 21.1213 6.87868C20.2426 6 18.8284 6 16 6" stroke="#ed6230" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
					</span>
					<div class="adm_morkva_row_size__col">
						<span><?php echo esc_html__('Width', 'mrkv-ua-shipping'); ?></span>
						<?php 
							$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['inter']['width']) ? MRKV_SHIPPING_SETTINGS['inter']['width'] : '';
							echo wp_kses( $mrkv_global_option_generator->get_input_number('', MRKV_OPTION_OBJECT_NAME . '[inter][width]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_inter_width' , '', '', ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
						?>
					</div>
					<span>
						<svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M8 18C5.17157 18 3.75736 18 2.87868 17.1213C2 16.2426 2 14.8284 2 12C2 9.17157 2 7.75736 2.87868 6.87868C3.75736 6 5.17157 6 8 6C10.8284 6 12.2426 6 13.1213 6.87868C14 7.75736 14 9.17157 14 12" stroke="#ed6230" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M10 12C10 14.8284 10 16.2426 10.8787 17.1213C11.7574 18 13.1716 18 16 18C18.8284 18 20.2426 18 21.1213 17.1213C21.4211 16.8215 21.6186 16.4594 21.7487 16M22 12C22 9.17157 22 7.75736 21.1213 6.87868C20.2426 6 18.8284 6 16 6" stroke="#ed6230" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
					</span>
					<div class="adm_morkva_row_size__col">
						<span><?php echo esc_html__('Height', 'mrkv-ua-shipping'); ?></span>
						<?php 
							$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['inter']['height']) ? MRKV_SHIPPING_SETTINGS['inter']['height'] : '';
							echo wp_kses( $mrkv_global_option_generator->get_input_number('', MRKV_OPTION_OBJECT_NAME . '[inter][height]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_inter_height' , '', '', ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'international_middle_8'); ?>
	<div class="admin_ua_ship_morkva_settings_line admin_ua_ship_morkva_one_data">
		<?php 
			$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['inter']['volume']) ? MRKV_SHIPPING_SETTINGS['inter']['volume'] : '';
			$mrkv_ua_shipping_description = __('It is calculated automatically according to the dimensions in the settings.', 'mrkv-ua-shipping');

			echo wp_kses( $mrkv_global_option_generator->get_input_number(__('Volumetric weight', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[inter][volume]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_inter_volume' , '', '', $mrkv_ua_shipping_description, 'readonly'), MRKV_UA_SHIPPING_ALLOW_TAGS);
		?>
		<p><strong><?php echo esc_html__('These standard weight and dimensions apply when products do not have ones of their own', 'mrkv-ua-shipping'); ?></strong></p>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'international_last'); ?>
</section>
<section id="email_settings" class="mrkv_up_ship_shipping_tab_block">
	<h2><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/mention-square-icon.svg'); ?>" alt="Sending email from TTN" title="Sending email from TTN"><?php echo esc_html__('Email settings', 'mrkv-ua-shipping'); ?></h2>
	<p><?php echo esc_html__('Create a custom message that will be sent after creating the shipment', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'email_first'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
				<?php 
					$mrkv_ua_shipping_data = '';
					$mrkv_ua_shipping_description = '<span class="mrkv-ua-ship-only-pro">' . __('Only in the Pro version', 'mrkv-ua-shipping') . '</span>';

					echo wp_kses( $mrkv_global_option_generator->get_input_text(__('Email subject', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[email][subject]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_email_subject' , '', __('Enter the subject...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description, 'readonly'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
				<?php
					$mrkv_ua_shipping_data = '';
					echo wp_kses( $mrkv_global_option_generator->get_input_checkbox(__('Send automatically', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[email][auto]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_email_auto', '', 'disabled'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
				<span class="mrkv-ua-ship-only-pro"><?php echo esc_html__('Only in the Pro version', 'mrkv-ua-shipping'); ?></span>
				<p class="mrkv-ua-ship-description">
					<?php echo esc_html__('Enable if you want to send an email automatically after a shipment is created', 'mrkv-ua-shipping'); ?>
				</p>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'email_middle_1'); ?>
	<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
		<?php
			$mrkv_ua_shipping_data = '';
			$mrkv_ua_shipping_description = '<span class="mrkv-ua-ship-only-pro">' . __('Only in the Pro version', 'mrkv-ua-shipping') . '</span>';

			echo wp_kses( $mrkv_global_option_generator->get_textarea(__('Email text', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[email][content]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_email_content' , '', __('Enter the email...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description, 'readonly'), MRKV_UA_SHIPPING_ALLOW_TAGS);
		?>
		<div class="admin_ua_ship_morkva_settings_row admin_ua_ship_morkva_settings_row_btns">
			<div class="button button-primary adm-textarea-btn"><?php echo esc_html__('Default email template', 'mrkv-ua-shipping'); ?></div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'email_last'); ?>
</section>
<section id="automation_settings" class="mrkv_up_ship_shipping_tab_block">
	<h2><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/automation-icon.svg'); ?>" alt="Automation Settings" title="Automation Settings"><?php echo esc_html__('Automation Settings', 'mrkv-ua-shipping'); ?></h2>
	<p><?php echo esc_html__('Connect automation when working with shipments', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'automation_first'); ?>
	<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
		<?php
			$mrkv_ua_shipping_data = '';
			echo wp_kses( $mrkv_global_option_generator->get_input_checkbox(__('Payment control', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[automation][payment_control]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_automation_payment_control', '', 'disabled' ), MRKV_UA_SHIPPING_ALLOW_TAGS);
		?>
		<span class="mrkv-ua-ship-only-pro"><?php echo esc_html__('Only in the Pro version', 'mrkv-ua-shipping'); ?></span>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'automation_middle_1'); ?>
	<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
		<?php
			$mrkv_ua_shipping_all_order_statuses = wc_get_order_statuses();
			$mrkv_ua_shipping_enabled_gateways = array_filter(WC()->payment_gateways->payment_gateways(), function ($mrkv_ua_shipping_gateway) {
	            return 'yes' === $mrkv_ua_shipping_gateway->enabled;
	        });
	        $mrkv_ua_shipping_payment_methods = array();

	        foreach($mrkv_ua_shipping_enabled_gateways as $mrkv_ua_shipping_id => $mrkv_ua_shipping_gateway)
	        {
	        	$mrkv_ua_shipping_payment_methods[$mrkv_ua_shipping_id] = $mrkv_ua_shipping_gateway->get_title();
	        }

			$mrkv_ua_shipping_data = '';
			echo wp_kses( $mrkv_global_option_generator->get_input_checkbox(__('Automatically create shipments', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[automation][autocreate][enabled]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_automation_autocreate_enabled', '', 'disabled' ), MRKV_UA_SHIPPING_ALLOW_TAGS);
		?>
		<span class="mrkv-ua-ship-only-pro"><?php echo esc_html__('Only in the Pro version', 'mrkv-ua-shipping'); ?></span>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'automation_middle_2'); ?>
	<h3><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/routing-icon.svg'); ?>" alt="Auto" title="Auto"><?php echo esc_html__('Cron Automation settings', 'mrkv-ua-shipping'); ?></h3>
	<p><?php echo esc_html__('Set up a cron task to change order statuses by TTN status', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'automation_middle_3'); ?>
	<div class="admin_ua_ship_morkva_settings_line">
		<?php
			$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['automation']['status']['enabled']) ? MRKV_SHIPPING_SETTINGS['automation']['status']['enabled'] : '';
			echo wp_kses($mrkv_global_option_generator->get_input_checkbox(__('Automatically change order status', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[automation][status][enabled]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_automation_status_enabled', ), MRKV_UA_SHIPPING_ALLOW_TAGS);
		?>
		<div class="admin_ua_ship_morkva_settings_line__inner inner-align">
			<div class="admin_ua_ship_morkva_settings_row">
				<div class="col-mrkv-5">
					<h4><?php echo esc_html__('Tracking status of Nova Poshta', 'mrkv-ua-shipping'); ?></h4>
				</div>
				<div class="col-mrkv-5">
					<h4><?php echo esc_html__('WooCommerce order status', 'mrkv-ua-shipping'); ?></h4>
				</div>
			</div>
			<div class="admin_ua_ship_morkva_settings_row">
				<div class="col-mrkv-5">
					<p><?php echo esc_html__('The shipment has been received', 'mrkv-ua-shipping'); ?></p>
				</div>
				<div class="col-mrkv-5">
					<?php 
						$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['automation']['status']['received']) ? MRKV_SHIPPING_SETTINGS['automation']['status']['received'] : '';
						echo wp_kses($mrkv_global_option_generator->get_select_simple('', MRKV_OPTION_OBJECT_NAME . '[automation][status][received]', $mrkv_ua_shipping_all_order_statuses, $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_automation_status_received' , __('Choose a status', 'mrkv-ua-shipping')), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
				</div>
			</div>
			<div class="admin_ua_ship_morkva_settings_row">
				<div class="col-mrkv-5">
					<p><?php echo esc_html__('The transfer has been received. Within a day you will receive an SMS message about the receipt of the money transfer.', 'mrkv-ua-shipping'); ?></p>
				</div>
				<div class="col-mrkv-5">
					<?php 
						$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['automation']['status']['moneysms']) ? MRKV_SHIPPING_SETTINGS['automation']['status']['moneysms'] : '';
						echo wp_kses($mrkv_global_option_generator->get_select_simple('', MRKV_OPTION_OBJECT_NAME . '[automation][status][moneysms]', $mrkv_ua_shipping_all_order_statuses, $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_automation_status_moneysms' , __('Choose a status', 'mrkv-ua-shipping')), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
				</div>
			</div>
			<div class="admin_ua_ship_morkva_settings_row">
				<div class="col-mrkv-5">
					<p><?php echo esc_html__('The money transfer is issued to the recipient', 'mrkv-ua-shipping'); ?></p>
				</div>
				<div class="col-mrkv-5">
					<?php 
						$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['automation']['status']['money']) ? MRKV_SHIPPING_SETTINGS['automation']['status']['money'] : '';
						echo wp_kses($mrkv_global_option_generator->get_select_simple('', MRKV_OPTION_OBJECT_NAME . '[automation][status][money]', $mrkv_ua_shipping_all_order_statuses, $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_automation_status_money' , __('Choose a status', 'mrkv-ua-shipping')), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
				</div>
			</div>
			<div class="admin_ua_ship_morkva_settings_row">
				<div class="col-mrkv-5">
					<p><?php echo esc_html__('Recipient\'s refusal to receive', 'mrkv-ua-shipping'); ?></p>
				</div>
				<div class="col-mrkv-5">
					<?php 
						$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['automation']['status']['refused']) ? MRKV_SHIPPING_SETTINGS['automation']['status']['refused'] : '';
						echo wp_kses($mrkv_global_option_generator->get_select_simple('', MRKV_OPTION_OBJECT_NAME . '[automation][status][refused]', $mrkv_ua_shipping_all_order_statuses, $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_automation_status_refused' , __('Choose a status', 'mrkv-ua-shipping')), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
				</div>
			</div>
			<div class="admin_ua_ship_morkva_settings_row">
				<div class="col-mrkv-5">
					<p><?php echo esc_html__('Unsuccessful delivery attempt due to the Recipient\'s absence at the address or lack of communication with him/her', 'mrkv-ua-shipping'); ?></p>
				</div>
				<div class="col-mrkv-5">
					<?php 
						$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['automation']['status']['canceled']) ? MRKV_SHIPPING_SETTINGS['automation']['status']['canceled'] : '';
						echo wp_kses($mrkv_global_option_generator->get_select_simple('', MRKV_OPTION_OBJECT_NAME . '[automation][status][canceled]', $mrkv_ua_shipping_all_order_statuses, $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_automation_status_canceled' , __('Choose a status', 'mrkv-ua-shipping')), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>					
				</div>
			</div>
			<div class="admin_ua_ship_morkva_settings_row">
				<div class="col-mrkv-5">
					<p><?php echo esc_html__('The shipment is sent to..', 'mrkv-ua-shipping'); ?></p>
				</div>
				<div class="col-mrkv-5">
					<?php 
						$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['automation']['status']['shipping']) ? MRKV_SHIPPING_SETTINGS['automation']['status']['shipping'] : '';
						echo wp_kses($mrkv_global_option_generator->get_select_simple('', MRKV_OPTION_OBJECT_NAME . '[automation][status][shipping]', $mrkv_ua_shipping_all_order_statuses, $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_automation_status_shipping' , __('Choose a status', 'mrkv-ua-shipping')), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>					
				</div>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'automation_middle_4'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<h4 style="margin-top: 0;"><?php echo esc_html__('Cron type', 'mrkv-ua-shipping'); ?></h4>
				<div class="admin_ua_ship_morkva_settings_row">
					<?php
						$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['automation']['cron']['type']) ? MRKV_SHIPPING_SETTINGS['automation']['cron']['type'] : 'wp_cron';
						echo wp_kses($mrkv_global_option_generator->get_input_radio(__('WP Cron', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[automation][cron][type]', 'wp_cron', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_automation_cron_type_wp_cron', 'wp_cron'), MRKV_UA_SHIPPING_ALLOW_TAGS);
						echo wp_kses($mrkv_global_option_generator->get_input_radio(__('Server Cron', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[automation][cron][type]', 'server_cron', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_automation_cron_type_server_cron', 'wp_cron'), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
				</div>
				<p>
					<?php echo esc_html__('Select the type of crown that you will use to update order statuses', 'mrkv-ua-shipping'); ?>
				</p>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line mrkv-ua-shipping-server">
				<label for="nova-poshta_m_ua_settings_api_key"><?php echo esc_html__('Server Cron URL', 'mrkv-ua-shipping'); ?></label>
				<input style="width: 100%; max-width: 100%;" type="text" value="<?php echo esc_url(rest_url('mrkv_ua_shipping/v1/check_ttn')); ?>" readonly="">
				<p><?php echo esc_html__('Create a cron on the server to this link and set it to execute at your discretion. Recommended for every 2 minutes', 'mrkv-ua-shipping'); ?></p>
			</div>
			<div class="admin_ua_ship_morkva_settings_line mrkv-ua-shipping-wpcron">
				<p><?php echo esc_html__('The script checks the last 100 orders from the past 30 days. Need more granular settings? Use a server-side cron job.', 'mrkv-ua-shipping'); ?></p>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'automation_middle_5'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line mrkv-ua-shipping-server">
				<?php 
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['automation']['cron']['status']) ? MRKV_SHIPPING_SETTINGS['automation']['cron']['status'] : 'wc-shipped';
					$mrkv_ua_shipping_description = __('Select the order status for which the bill of lading will be checked', 'mrkv-ua-shipping');

					echo wp_kses($mrkv_global_option_generator->get_select_simple(__('Cron order status check', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[automation][cron][status]', $mrkv_ua_shipping_all_order_statuses, $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_automation_cron_status' , __('Choose a order status', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line mrkv-ua-shipping-server">
				<?php 
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['automation']['cron']['max_count']) ? MRKV_SHIPPING_SETTINGS['automation']['cron']['max_count'] : '10000';

					echo wp_kses($mrkv_global_option_generator->get_input_number(__('Number of orders to check', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[automation][cron][max_count]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_automation_cron_max_count' , '', '', ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'automation_middle_6'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line mrkv-ua-shipping-server">
				<?php 
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['automation']['cron']['frequency']) ? MRKV_SHIPPING_SETTINGS['automation']['cron']['frequency'] : '1440';

					echo wp_kses($mrkv_global_option_generator->get_input_number(__('Request frequency (minutes)', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[automation][cron][frequency]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_automation_cron_frequency' , '', '', ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line mrkv-ua-shipping-server">
				<?php 
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['automation']['cron']['count_step']) ? MRKV_SHIPPING_SETTINGS['automation']['cron']['count_step'] : '300';

					echo wp_kses($mrkv_global_option_generator->get_input_number(__('Number of processing of the CTD in one step', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[automation][cron][count_step]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_automation_cron_count_step' , '', '', ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'automation_middle_7'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5 mrkv-ua-shipping-server">
			<div class="admin_ua_ship_morkva_settings_line ">
				<?php 
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['automation']['cron']['days']) ? MRKV_SHIPPING_SETTINGS['automation']['cron']['days'] : '30';

					echo wp_kses($mrkv_global_option_generator->get_input_number(__('Verification period (days)', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[automation][cron][days]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_automation_cron_days' , '', '', ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
		<div class="col-mrkv-5 mrkv-ua-shipping-wpcron">
			<div class="admin_ua_ship_morkva_settings_line">
				<?php 
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['automation']['cron']['wp_frequency']) ? MRKV_SHIPPING_SETTINGS['automation']['cron']['wp_frequency'] : 'hourly';
					$mrkv_ua_shipping_description = __('Choose the frequency of execution wp cron event', 'mrkv-ua-shipping');
					$mrkv_ua_shipping_schedules = wp_get_schedules();
					$mrkv_ua_shipping_cron_list = [];
					foreach ( $mrkv_ua_shipping_schedules as $mrkv_ua_shipping_slug => $mrkv_ua_shipping_schedule ) {
					    $mrkv_ua_shipping_cron_list[$mrkv_ua_shipping_slug] = $mrkv_ua_shipping_schedule['display'];
					}

					echo wp_kses($mrkv_global_option_generator->get_select_simple(__('Frequency of execution (WP Cron)', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[automation][cron][wp_frequency]', $mrkv_ua_shipping_cron_list, $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_automation_cron_wp_frequency' , __('Choose a period', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'automation_last'); ?>
</section>
<section id="checkout_settings" class="mrkv_up_ship_shipping_tab_block">
	<h2><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/clapperboard-edit-icon.svg'); ?>" alt="Sending email from TTN" title="Sending email from TTN"><?php echo esc_html__('Checkout settings', 'mrkv-ua-shipping'); ?></h2>
	<p><?php echo esc_html__('Customize the output of the plugin fields in relation to your theme', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'checkout_first'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<?php 
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['checkout']['position']) ? MRKV_SHIPPING_SETTINGS['checkout']['position'] : '';
					$mrkv_ua_shipping_senders_type_list = array(
						'woocommerce_after_checkout_billing_form' => __('After Payment data', 'mrkv-ua-shipping'),
						'woocommerce_before_order_notes' => __('Before Notes to orders', 'mrkv-ua-shipping'),
					);

					$mrkv_ua_shipping_description = __('Select the position of the delivery method fields on the checkout page', 'mrkv-ua-shipping');

					echo wp_kses( $mrkv_global_option_generator->get_select_simple(__('Position of plugin fields in Checkout', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[checkout][position]', $mrkv_ua_shipping_senders_type_list, $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_checkout_position' , __('Choose a position', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'checkout_middle_1'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<?php
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['checkout']['middlename']['enabled']) ? MRKV_SHIPPING_SETTINGS['checkout']['middlename']['enabled'] : '';
					echo wp_kses( $mrkv_global_option_generator->get_input_checkbox(__('Enable patronymic', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[checkout][middlename][enabled]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_checkout_middlename_enabled', ), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
				<p class="mrkv-ua-ship-description">
					<?php echo esc_html__('Remove the patronymic field from the checkout page', 'mrkv-ua-shipping'); ?>
				</p>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<?php
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['checkout']['middlename']['required']) ? MRKV_SHIPPING_SETTINGS['checkout']['middlename']['required'] : '';
					echo wp_kses( $mrkv_global_option_generator->get_input_checkbox(__('Patronymic is required', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[checkout][middlename][required]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_checkout_middlename_required', ), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
				<p class="mrkv-ua-ship-description">
					<?php echo esc_html__('Make the middle name field mandatory', 'mrkv-ua-shipping'); ?>
				</p>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'checkout_middle_2'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<?php 
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['checkout']['middlename']['position']) ? MRKV_SHIPPING_SETTINGS['checkout']['middlename']['position'] : '';
					$mrkv_ua_shipping_middlename_position = array(
						'default' => __('Default', 'mrkv-ua-shipping'),
						'billing_last_name' => __('After the last name', 'mrkv-ua-shipping'),
						'billing_first_name' => __('After the first name', 'mrkv-ua-shipping'),
					);

					$mrkv_ua_shipping_description = __('Select the middlename field position on the checkout page', 'mrkv-ua-shipping');

					echo wp_kses( $mrkv_global_option_generator->get_select_simple(__('Position of middlename in Checkout', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[checkout][middlename][position]', $mrkv_ua_shipping_middlename_position, $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_checkout_middlename_position' , __('Choose a position', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<?php
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['checkout']['hide_saving_data']) ? MRKV_SHIPPING_SETTINGS['checkout']['hide_saving_data'] : '';
					echo wp_kses( $mrkv_global_option_generator->get_input_checkbox(__('Save customer selected fields', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[checkout][hide_saving_data]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_checkout_hide_saving_data', ), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
				<p class="mrkv-ua-ship-description">
					<?php echo esc_html__('Enable to store selected delivery city and warehouse/postamat in session cookies (may not work if privacy settings enabled in user’s browser)', 'mrkv-ua-shipping'); ?>
				</p>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'checkout_last'); ?>
</section>
<section id="log_settings" class="mrkv_up_ship_shipping_tab_block">
	<h2><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/clipboard-list-icon.svg'); ?>" alt="Debug Log" title="Debug Log"><?php echo esc_html__('Debug Log', 'mrkv-ua-shipping'); ?></h2>
	<p><?php echo esc_html__('To keep an error log, enable it in the settings', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'log_first'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<?php
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['debug']['log']) ? MRKV_SHIPPING_SETTINGS['debug']['log'] : '';
					echo wp_kses($mrkv_global_option_generator->get_input_checkbox(__('Enable debug log', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[debug][log]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_debug_log', ), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
				<p class="mrkv-ua-ship-description">
					<?php echo esc_html__('Enable to receive request error logs', 'mrkv-ua-shipping'); ?>
				</p>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<p></p>
				<a href="<?php echo esc_url(admin_url( 'admin.php?page=wc-status&tab=logs' )); ?>"><?php echo esc_html__('Show Log files', 'mrkv-ua-shipping'); ?></a>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-poshta', 'log_last'); ?>	
</section>