<?php 
	# Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) exit; 
?>
<section id="basic_settings" class="mrkv_up_ship_shipping_tab_block active">
	<h2><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/settings-icon.svg'); ?>" alt="Basic settings" title="Basic settings"><?php echo esc_html__('Basic settings', 'mrkv-ua-shipping'); ?></h2>
	<p><?php echo esc_html__('For the shipping method to work, you need to get API keys from Ukrposhta (conclude an agreement at the branch). Details:', 'mrkv-ua-shipping'); ?> <a target="blanc" href="https://ukrposhta.ua/ukrposhta-dlya-biznesu.html">ukrposhta.ua</a></p>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'basic_first'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<?php 
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['production_bearer_ecom']) ? MRKV_SHIPPING_SETTINGS['production_bearer_ecom'] : '';
					$mrkv_ua_shipping_description = __('Find it in an email from your Ukrposhta customer manager', 'mrkv-ua-shipping');
					$mrkv_ua_shipping_label = __('PRODUCTION BEARER eCom', 'mrkv-ua-shipping');
					global $mrkv_global_option_generator;
					global $mrkv_global_shipping_object;

					echo wp_kses( $mrkv_global_option_generator->get_input_text($mrkv_ua_shipping_label, MRKV_OPTION_OBJECT_NAME . '[production_bearer_ecom]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_production_bearer_ecom' , '', __('Enter the key...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<?php 
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['production_bearer_status_tracking']) ? MRKV_SHIPPING_SETTINGS['production_bearer_status_tracking'] : '';
					$mrkv_ua_shipping_description = __('Find it in an email from your Ukrposhta customer manager', 'mrkv-ua-shipping');
					echo wp_kses( $mrkv_global_option_generator->get_input_text(__('PRODUCTION BEARER StatusTracking', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[production_bearer_status_tracking]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_production_bearer_status_tracking' , '', __('Enter the key...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'basic_middle_1'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<?php 
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['production_cp_token']) ? MRKV_SHIPPING_SETTINGS['production_cp_token'] : '';
					$mrkv_ua_shipping_description = __('Find it in an email from your Ukrposhta customer manager', 'mrkv-ua-shipping');
					echo wp_kses( $mrkv_global_option_generator->get_input_text(__('PROD_COUNTERPARTY TOKEN', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[production_cp_token]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_production_cp_token' , '', __('Enter the key...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
		<div class="col-mrkv-5"></div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'basic_middle_2'); ?>
	<div class="admin_ua_ship_morkva_settings_line">
		<?php
			$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['test_mode']) ? MRKV_SHIPPING_SETTINGS['test_mode'] : '';
			echo wp_kses( $mrkv_global_option_generator->get_input_checkbox(__('Test mode', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[test_mode]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_test_mode', ), MRKV_UA_SHIPPING_ALLOW_TAGS);
		?>
		<div class="admin_ua_ship_morkva_settings_line__inner">
			<div class="admin_ua_ship_morkva_settings_row">
				<div class="col-mrkv-5">
					<?php 
						$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['test_production_bearer_ecom']) ? MRKV_SHIPPING_SETTINGS['test_production_bearer_ecom'] : '';
						$mrkv_ua_shipping_description = __('Find it in an email from your Ukrposhta customer manager', 'mrkv-ua-shipping');
						$mrkv_ua_shipping_label = __('SANDBOX BEARER eCom', 'mrkv-ua-shipping');

						echo wp_kses( $mrkv_global_option_generator->get_input_text($mrkv_ua_shipping_label, MRKV_OPTION_OBJECT_NAME . '[test_production_bearer_ecom]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_test_production_bearer_ecom' , '', __('Enter the key...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
				</div>
				<div class="col-mrkv-5">
					<?php 
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['test_production_bearer_status_tracking']) ? MRKV_SHIPPING_SETTINGS['test_production_bearer_status_tracking'] : '';
					$mrkv_ua_shipping_description = __('Find it in an email from your Ukrposhta customer manager', 'mrkv-ua-shipping');
					echo wp_kses( $mrkv_global_option_generator->get_input_text(__('SANDBOX BEARER StatusTracking', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[test_production_bearer_status_tracking]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_test_production_bearer_status_tracking' , '', __('Enter the key...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
				</div>
			</div>
			<div class="admin_ua_ship_morkva_settings_row">
				<div class="col-mrkv-5">
					<?php 
						$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['test_production_cp_token']) ? MRKV_SHIPPING_SETTINGS['test_production_cp_token'] : '';
						$mrkv_ua_shipping_description = __('Find it in an email from your Ukrposhta customer manager', 'mrkv-ua-shipping');
						echo wp_kses( $mrkv_global_option_generator->get_input_text(__('SAND_COUNTERPARTY TOKEN', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[test_production_cp_token]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_test_production_cp_token' , '', __('Enter the key...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
				</div>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'basic_last'); ?>
</section>
<section id="domestic_settings" class="mrkv_up_ship_shipping_tab_block">
	<h2><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/box-icon.svg'); ?>" alt="Domestic settings" title="Domestic settings"><?php echo esc_html__('Domestic settings', 'mrkv-ua-shipping'); ?></h2>
	<p><?php echo esc_html__('Data is used in case of missing values when creating a shipment', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<h3><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/user-icon.svg'); ?>" alt="Sender settings" title="Sender settings"><?php echo esc_html__('Sender settings', 'mrkv-ua-shipping'); ?></h3>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'domestic_first'); ?>
	<div class="admin_ua_ship_morkva_settings_line">
		<div class="admin_ua_ship_morkva_settings_row mrkv_row_reverse">
			<div class="col-mrkv-5">
				<?php 
					$mrkv_ua_shipping_data_sender_type = isset(MRKV_SHIPPING_SETTINGS['sender']['type']) ? MRKV_SHIPPING_SETTINGS['sender']['type'] : '';
					$mrkv_ua_shipping_sender_lastname = isset(MRKV_SHIPPING_SETTINGS['sender']['individual']['lastname']) ? MRKV_SHIPPING_SETTINGS['sender']['individual']['lastname'] : '';
					$mrkv_ua_shipping_sender_firstname = isset(MRKV_SHIPPING_SETTINGS['sender']['individual']['name']) ? MRKV_SHIPPING_SETTINGS['sender']['individual']['name'] : '';
					$mrkv_ua_shipping_sender_middlename = isset(MRKV_SHIPPING_SETTINGS['sender']['individual']['middlename']) ? MRKV_SHIPPING_SETTINGS['sender']['individual']['middlename'] : '';
					$mrkv_ua_shipping_sender_phone = isset(MRKV_SHIPPING_SETTINGS['sender']['individual']['phone']) ? MRKV_SHIPPING_SETTINGS['sender']['individual']['phone'] : '';

					if($mrkv_ua_shipping_data_sender_type && $mrkv_ua_shipping_data_sender_type != 'INDIVIDUAL')
					{
						$mrkv_ua_shipping_data_sender_type = 'INDIVIDUAL';
						$mrkv_ua_shipping_sender_lastname = (isset(MRKV_SHIPPING_SETTINGS['sender']['company']['lastname']) && MRKV_SHIPPING_SETTINGS['sender']['company']['lastname']) ? MRKV_SHIPPING_SETTINGS['sender']['company']['lastname'] : $mrkv_ua_shipping_sender_lastname;

						if(!$mrkv_ua_shipping_sender_lastname){
							$mrkv_ua_shipping_sender_lastname = (isset(MRKV_SHIPPING_SETTINGS['sender']['private']['lastname']) && MRKV_SHIPPING_SETTINGS['sender']['private']['lastname']) ? MRKV_SHIPPING_SETTINGS['sender']['private']['lastname'] : $mrkv_ua_shipping_sender_lastname;
						}

						$mrkv_ua_shipping_sender_firstname = (isset(MRKV_SHIPPING_SETTINGS['sender']['company']['name']) && MRKV_SHIPPING_SETTINGS['sender']['company']['name']) ? MRKV_SHIPPING_SETTINGS['sender']['company']['name'] : $mrkv_ua_shipping_sender_firstname;

						if(!$mrkv_ua_shipping_sender_firstname)
						{
							$mrkv_ua_shipping_sender_firstname = (isset(MRKV_SHIPPING_SETTINGS['sender']['private']['name']) && MRKV_SHIPPING_SETTINGS['sender']['private']['name']) ? MRKV_SHIPPING_SETTINGS['sender']['private']['name'] : $mrkv_ua_shipping_sender_firstname;
						}

						$mrkv_ua_shipping_sender_middlename = (isset(MRKV_SHIPPING_SETTINGS['sender']['private']['middlename']) && MRKV_SHIPPING_SETTINGS['sender']['private']['middlename']) ? MRKV_SHIPPING_SETTINGS['sender']['private']['middlename'] : $mrkv_ua_shipping_sender_middlename;

						$mrkv_ua_shipping_sender_phone = (isset(MRKV_SHIPPING_SETTINGS['sender']['company']['phone']) && MRKV_SHIPPING_SETTINGS['sender']['company']['phone']) ? MRKV_SHIPPING_SETTINGS['sender']['company']['phone'] : $mrkv_ua_shipping_sender_phone;

						if(!$mrkv_ua_shipping_sender_phone)
						{
							$mrkv_ua_shipping_sender_phone = (isset(MRKV_SHIPPING_SETTINGS['sender']['private']['phone']) && MRKV_SHIPPING_SETTINGS['sender']['private']['phone']) ? MRKV_SHIPPING_SETTINGS['sender']['private']['phone'] : $mrkv_ua_shipping_sender_phone;
						}
					}

					$mrkv_ua_shipping_senders_type_list = array(
						'INDIVIDUAL' => __('An individual', 'mrkv-ua-shipping')
					);

					$mrkv_ua_shipping_description = __('The choice affects the fields that need to be filled in in the setup', 'mrkv-ua-shipping');

					echo wp_kses( $mrkv_global_option_generator->get_select_simple(__('The sender represents', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[sender][type]', $mrkv_ua_shipping_senders_type_list, $mrkv_ua_shipping_data_sender_type, MRKV_OPTION_OBJECT_NAME . '_sender_type' , __('Choose a type', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
			<div class="col-mrkv-5">
				<?php 
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['sender']['warehouse']['name']) ? MRKV_SHIPPING_SETTINGS['sender']['warehouse']['name'] : '';
					$mrkv_ua_shipping_description = __('The index consists of five numbers', 'mrkv-ua-shipping');
					echo wp_kses( $mrkv_global_option_generator->get_input_text(__('Postal code', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[sender][warehouse][name]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_sender_warehouse_name' , '', __('Enter the postal index...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['sender']['warehouse']['id']) ? MRKV_SHIPPING_SETTINGS['sender']['warehouse']['id'] : '';
					echo wp_kses( $mrkv_global_option_generator->get_input_hidden(MRKV_OPTION_OBJECT_NAME . '[sender][warehouse][id]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_sender_warehouse_id'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
		<div class="admin_ua_ship_morkva_settings_line__inner active" id="<?php echo esc_attr(MRKV_OPTION_OBJECT_NAME); ?>_sender_type_list">
			<div data-sender="DEFAULT" class="<?php echo esc_attr(MRKV_OPTION_OBJECT_NAME); ?>_sender_type_block <?php if(!$mrkv_ua_shipping_data_sender_type){ echo esc_attr('active'); } ?>">
				<div class="admin_ua_ship_morkva_settings_row">
					<div class="col-mrkv-5">
						<p style="display: flex; align-items: center; gap: 7px;">
							<svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<g id="SVGRepo_bgCarrier" stroke-width="0"/>
							<g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
							<g id="SVGRepo_iconCarrier"> <path d="M12 4.5L7 9.5M12 4.5L17 9.5M12 4.5L12 11M12 14.5C12 16.1667 13 19.5 17 19.5" stroke="#ed6230" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </g>
							</svg>
							<?php echo esc_html__('Select Sender represents to continue filling in the data', 'mrkv-ua-shipping'); ?>
						</p>
					</div>
				</div>
			</div>
			<div data-sender="INDIVIDUAL" class="<?php echo esc_attr(MRKV_OPTION_OBJECT_NAME); ?>_sender_type_block <?php if($mrkv_ua_shipping_data_sender_type == 'INDIVIDUAL'){ echo esc_attr('active'); } ?>">
				<div class="admin_ua_ship_morkva_settings_row">
					<div class="col-mrkv-5">
						<?php 
							$mrkv_ua_shipping_data = $mrkv_ua_shipping_sender_lastname;
							echo wp_kses( $mrkv_global_option_generator->get_input_text(__('Lastname', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[sender][individual][lastname]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_sender_individual_lastname' , '', __('Enter the lastname...', 'mrkv-ua-shipping'), ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
						?>
						<p></p>
					</div>
					<div class="col-mrkv-5">
						<?php 
							$mrkv_ua_shipping_data = $mrkv_ua_shipping_sender_firstname;
							echo wp_kses( $mrkv_global_option_generator->get_input_text(__('Name', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[sender][individual][name]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_sender_individual_name' , '', __('Enter the name...', 'mrkv-ua-shipping'), ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
						?>
						<p></p>
					</div>
				</div>
				<div class="admin_ua_ship_morkva_settings_row">
					<div class="col-mrkv-5">
						<?php 
							$mrkv_ua_shipping_data = $mrkv_ua_shipping_sender_middlename;
							echo wp_kses( $mrkv_global_option_generator->get_input_text(__('Middlename', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[sender][individual][middlename]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_sender_individual_middlename' , '', __('Enter the middlename...', 'mrkv-ua-shipping'), ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
						?>
						<p></p>
					</div>
					<div class="col-mrkv-5">
						<?php 
							$mrkv_ua_shipping_data = $mrkv_ua_shipping_sender_phone;
							$mrkv_ua_shipping_description = __('Hint: the main format is 0987654321 (without +38)', 'mrkv-ua-shipping');
							echo wp_kses( $mrkv_global_option_generator->get_input_text(__('Phone', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[sender][individual][phone]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_sender_individual_phone' , '', __('Enter the phone...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'domestic_middle_1'); ?>
	<h3><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/wallet-icon.svg'); ?>" alt="Shipment" title="Shipment"><?php echo esc_html__('Payer', 'mrkv-ua-shipping'); ?></h3>
	<p><?php echo esc_html__('Check the default shipping payer for the shipment', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'domestic_middle_2'); ?>
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
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'domestic_middle_2'); ?>
	<h3><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/tuning-icon.svg'); ?>" alt="Shipment" title="Shipment"><?php echo esc_html__('Shipment', 'mrkv-ua-shipping'); ?></h3>
	<p><?php echo esc_html__('Fill in the default shipping data for the shipment', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'domestic_middle_3'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<label><?php echo esc_html__('Type of shipment (Default value)', 'mrkv-ua-shipping'); ?></label>
				<div class="admin_ua_ship_morkva_settings_row">
					<?php
						$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['shipment']['type']) ? MRKV_SHIPPING_SETTINGS['shipment']['type'] : '';
						echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('STANDARD', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[shipment][type]', 'STANDARD', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_shipment_type_standart', 'STANDARD'), MRKV_UA_SHIPPING_ALLOW_TAGS);
						echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('EXPRESS', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[shipment][type]', 'EXPRESS', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_shipment_type_express', 'STANDARD'), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
				</div>
			</div>
		</div>
		<div class="col-mrkv-5">
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'domestic_middle_4'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<h4><?php echo esc_html__('Weight of the shipment, g', 'mrkv-ua-shipping'); ?></h4>
				<?php 
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['shipment']['weight']) ? MRKV_SHIPPING_SETTINGS['shipment']['weight'] : '';
					echo wp_kses( $mrkv_global_option_generator->get_input_number('', MRKV_OPTION_OBJECT_NAME . '[shipment][weight]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_shipment_weight' , '', '', ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
					<p class="mrkv-ua-ship-description">
						<?php
						echo esc_html__('The value entered here will be used to calculate the cost of all shipments. The weight must be specified in grams.', 'mrkv-ua-shipping');
						?>
					</p>
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
							echo wp_kses( $mrkv_global_option_generator->get_input_number('', MRKV_OPTION_OBJECT_NAME . '[shipment][length]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_shipment_length' , '', '', '', '', '0.01', '120'), MRKV_UA_SHIPPING_ALLOW_TAGS);
						?>
					</div>
					<span>
						<svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M8 18C5.17157 18 3.75736 18 2.87868 17.1213C2 16.2426 2 14.8284 2 12C2 9.17157 2 7.75736 2.87868 6.87868C3.75736 6 5.17157 6 8 6C10.8284 6 12.2426 6 13.1213 6.87868C14 7.75736 14 9.17157 14 12" stroke="#ed6230" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M10 12C10 14.8284 10 16.2426 10.8787 17.1213C11.7574 18 13.1716 18 16 18C18.8284 18 20.2426 18 21.1213 17.1213C21.4211 16.8215 21.6186 16.4594 21.7487 16M22 12C22 9.17157 22 7.75736 21.1213 6.87868C20.2426 6 18.8284 6 16 6" stroke="#ed6230" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
					</span>
					<div class="adm_morkva_row_size__col">
						<span><?php echo esc_html__('Width', 'mrkv-ua-shipping'); ?></span>
						<?php 
							$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['shipment']['width']) ? MRKV_SHIPPING_SETTINGS['shipment']['width'] : '';
							echo wp_kses( $mrkv_global_option_generator->get_input_number('', MRKV_OPTION_OBJECT_NAME . '[shipment][width]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_shipment_width' , '', '', '', '', '0.01', '70'), MRKV_UA_SHIPPING_ALLOW_TAGS);
						?>
					</div>
					<span>
						<svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M8 18C5.17157 18 3.75736 18 2.87868 17.1213C2 16.2426 2 14.8284 2 12C2 9.17157 2 7.75736 2.87868 6.87868C3.75736 6 5.17157 6 8 6C10.8284 6 12.2426 6 13.1213 6.87868C14 7.75736 14 9.17157 14 12" stroke="#ed6230" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M10 12C10 14.8284 10 16.2426 10.8787 17.1213C11.7574 18 13.1716 18 16 18C18.8284 18 20.2426 18 21.1213 17.1213C21.4211 16.8215 21.6186 16.4594 21.7487 16M22 12C22 9.17157 22 7.75736 21.1213 6.87868C20.2426 6 18.8284 6 16 6" stroke="#ed6230" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
					</span>
					<div class="adm_morkva_row_size__col">
						<span><?php echo esc_html__('Height', 'mrkv-ua-shipping'); ?></span>
						<?php 
							$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['shipment']['height']) ? MRKV_SHIPPING_SETTINGS['shipment']['height'] : '';
							echo wp_kses( $mrkv_global_option_generator->get_input_number('', MRKV_OPTION_OBJECT_NAME . '[shipment][height]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_shipment_height' , '', '', '', '', '0.01', '70'), MRKV_UA_SHIPPING_ALLOW_TAGS);
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'domestic_middle_5'); ?>
	<div class="admin_ua_ship_morkva_settings_line">
		<h4><?php echo esc_html__('Sticker format', 'mrkv-ua-shipping'); ?></h4>
		<div class="admin_ua_ship_morkva_settings_row">
			<?php
				$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['shipment']['sticker']) ? MRKV_SHIPPING_SETTINGS['shipment']['sticker'] : '';
				echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('100*100 mm', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[shipment][sticker]', '100*100', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_shipment_sticker_hxh', '100*100'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('100*100 mm for printing on A4 size', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[shipment][sticker]', '100*100A4', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_shipment_sticker_standart', ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
				echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('100*100 mm for printing on A5 size', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[shipment][sticker]', '100*100A5', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_shipment_sticker_standart', ''), MRKV_UA_SHIPPING_ALLOW_TAGS);
			?>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'domestic_middle_6'); ?>
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
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'domestic_middle_7'); ?>
	<div class="admin_ua_ship_morkva_settings_line">
		<?php
			$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['shipment']['description']) ? MRKV_SHIPPING_SETTINGS['shipment']['description'] : '';
			$mrkv_ua_shipping_description = '';

			echo wp_kses( $mrkv_global_option_generator->get_textarea(__('Description of the shipment', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[shipment][description]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_shipment_description' , '', __('For example, products for children...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
		?>
		<p class="mrkv-ua-ship-description">
			<?php echo esc_html__('Values of shortcodes that are added to the end of the text field when the buttons are pressed', 'mrkv-ua-shipping'); ?>
		</p>
		<div class="admin_ua_ship_morkva_settings_row admin_ua_ship_morkva_settings_row_btns">
			<h4><?php echo esc_html__('Insert the shortcode:', 'mrkv-ua-shipping'); ?></h4>
			<div data-added="[order_id]" class="button button-primary adm-textarea-btn"><?php echo esc_html__('Order number', 'mrkv-ua-shipping'); ?></div>
			<div data-added="[product_list]" class="button button-primary adm-textarea-btn"><?php echo esc_html__('List of products', 'mrkv-ua-shipping'); ?></div>
			<div data-added="[product_list_qa]" class="button button-primary adm-textarea-btn"><?php echo esc_html__('List of goods (with articles and quantity)', 'mrkv-ua-shipping'); ?></div>
			<div data-added="[product_list_a]" class="button button-primary adm-textarea-btn"><?php echo esc_html__('List of articles with quantity', 'mrkv-ua-shipping'); ?></div>
			<div data-added="[quantity]" class="button button-primary adm-textarea-btn"><?php echo esc_html__('Quantity of goods', 'mrkv-ua-shipping'); ?></div>
			<div data-added="[quantity_p]" class="button button-primary adm-textarea-btn"><?php echo esc_html__('Number of positions', 'mrkv-ua-shipping'); ?></div>
			<div data-added="[cost]" class="button button-primary adm-textarea-btn"><?php echo esc_html__('Cost', 'mrkv-ua-shipping'); ?></div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'domestic_last'); ?>
</section>
<section id="international_settings" class="mrkv_up_ship_shipping_tab_block">
	<h2><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/map-icon.svg'); ?>" alt="International shipping settings" title="International shipping settings"><?php echo esc_html__('International shipping settings', 'mrkv-ua-shipping'); ?></h2>
	<p><?php echo esc_html__('Customize international shipping to your needs', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<h3><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/user-icon.svg'); ?>" alt="Sender settings" title="Sender settings"><?php echo esc_html__('Sender settings', 'mrkv-ua-shipping'); ?></h3>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'sender_first'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
				<?php 
					$mrkv_ua_shipping_data = '';
					$mrkv_ua_shipping_description = '<span class="mrkv-ua-ship-only-pro">' . __('Only in the Pro version', 'mrkv-ua-shipping') . '</span>' . __('The field must be filled in in Latin', 'mrkv-ua-shipping');
					echo wp_kses( $mrkv_global_option_generator->get_input_text(__('Name', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][name]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_international_name' , '', __('Enter the name...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description, 'readonly'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
				<?php 
					$mrkv_ua_shipping_data = '';
					$mrkv_ua_shipping_description = '<span class="mrkv-ua-ship-only-pro">' . __('Only in the Pro version', 'mrkv-ua-shipping') . '</span>' . __('The field must be filled in in Latin', 'mrkv-ua-shipping');
					echo wp_kses( $mrkv_global_option_generator->get_input_text(__('Lastname', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][lastname]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_international_lastname' , '', __('Enter the lastname...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description, 'readonly'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'sender_middle_1'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
				<?php 
					$mrkv_ua_shipping_data = '';
					$mrkv_ua_shipping_description = '<span class="mrkv-ua-ship-only-pro">' . __('Only in the Pro version', 'mrkv-ua-shipping') . '</span>' . __('The field must be filled in in Latin', 'mrkv-ua-shipping');
					echo wp_kses( $mrkv_global_option_generator->get_input_text(__('City', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][city]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_international_city' , '', __('Enter the city...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description, 'readonly'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
				<?php 
					$mrkv_ua_shipping_data = '';
					$mrkv_ua_shipping_description = '<span class="mrkv-ua-ship-only-pro">' . __('Only in the Pro version', 'mrkv-ua-shipping') . '</span>' . __('The field must be filled in in Latin', 'mrkv-ua-shipping');
					echo wp_kses( $mrkv_global_option_generator->get_input_text(__('Street', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][street]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_international_street' , '', __('Enter the street...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description, 'readonly'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'sender_middle_2'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
				<?php 
					$mrkv_ua_shipping_data = '';
					$mrkv_ua_shipping_description = '<span class="mrkv-ua-ship-only-pro">' . __('Only in the Pro version', 'mrkv-ua-shipping') . '</span>' . __('The field must be filled in in Latin', 'mrkv-ua-shipping');
					echo wp_kses( $mrkv_global_option_generator->get_input_text(__('House number', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][house]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_international_house' , '', __('Enter the house number...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description, 'readonly'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
				<?php 
					$mrkv_ua_shipping_data = '';
					$mrkv_ua_shipping_description = '<span class="mrkv-ua-ship-only-pro">' . __('Only in the Pro version', 'mrkv-ua-shipping') . '</span>';
					echo wp_kses( $mrkv_global_option_generator->get_input_text(__('Index', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][index]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_international_index' , '', __('Enter the index...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description, 'readonly'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'sender_middle_3'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
				<?php 
					$mrkv_ua_shipping_data = '';
					$mrkv_ua_shipping_description = '<span class="mrkv-ua-ship-only-pro">' . __('Only in the Pro version', 'mrkv-ua-shipping') . '</span>';
					echo wp_kses( $mrkv_global_option_generator->get_input_text(__('Phone', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][phone]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_international_phone' , '', __('Enter the phone...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description, 'readonly'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
		<div class="col-mrkv-5">
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'sender_middle_4'); ?>
	<h3><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/wallet-icon.svg'); ?>" alt="Shipment" title="Shipment"><?php echo esc_html__('Payer', 'mrkv-ua-shipping'); ?></h3>
	<p><?php echo esc_html__('Check the default shipping payer for the shipment', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'sender_middle_5'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
				<h4><?php echo esc_html__('Payer of delivery', 'mrkv-ua-shipping'); ?></h4>
				<p class="mrkv-ua-ship-only-pro"><?php echo esc_html__('Only in the Pro version', 'mrkv-ua-shipping'); ?></p>
				<div class="admin_ua_ship_morkva_settings_row">
					<?php
						$mrkv_ua_shipping_data = '';
						echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('Recipient', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][payer][delivery]', 'Recipient', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_international_payer_delivery_recipient', 'Recipient', 'disabled'), MRKV_UA_SHIPPING_ALLOW_TAGS);
						echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('Sender', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][payer][delivery]', 'Sender', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_international_payer_delivery_sender', 'Recipient', 'disabled'), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
				</div>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
				<h4><?php echo esc_html__('Payer for the cash on delivery function', 'mrkv-ua-shipping'); ?></h4>
				<p class="mrkv-ua-ship-only-pro"><?php echo esc_html__('Only in the Pro version', 'mrkv-ua-shipping'); ?></p>
				<div class="admin_ua_ship_morkva_settings_row">
					<?php
						$mrkv_ua_shipping_data = '';
						echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('Recipient', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][payer][cash]', 'Recipient', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_international_payer_cash_recipient', 'Recipient', 'disabled'), MRKV_UA_SHIPPING_ALLOW_TAGS);
						echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('Sender', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][payer][cash]', 'Sender', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_international_payer_cash_sender', 'Recipient', 'disabled'), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
				</div>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'sender_middle_6'); ?>
	<h3><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/tuning-icon.svg'); ?>" alt="Shipment" title="Shipment"><?php echo esc_html__('Shipment', 'mrkv-ua-shipping'); ?></h3>
	<p><?php echo esc_html__('Fill in the default shipping data for the international invoice', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'sender_middle_7'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
				<?php 
					$mrkv_ua_shipping_data = '';
					$mrkv_ua_shipping_senders_type_list = array(
					);

					$mrkv_ua_shipping_description = '<span class="mrkv-ua-ship-only-pro">' . __('Only in the Pro version', 'mrkv-ua-shipping') . '</span>';

					echo wp_kses( $mrkv_global_option_generator->get_select_simple(__('Type of international shipment', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][type]', $mrkv_ua_shipping_senders_type_list, $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_international_type' , __('Choose a type', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description, '', 'disabled'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
				<?php 
					$mrkv_ua_shipping_data = '';
					$mrkv_ua_shipping_senders_type_list = array(
					);

					$mrkv_ua_shipping_description = '<span class="mrkv-ua-ship-only-pro">' . __('Only in the Pro version', 'mrkv-ua-shipping') . '</span>';

					echo wp_kses( $mrkv_global_option_generator->get_select_simple(__('Category of shipment', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][category]', $mrkv_ua_shipping_senders_type_list, $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_international_category' , __('Choose a category', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description, '', 'disabled'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'sender_middle_8'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
				<h4><?php echo esc_html__('Weight of the shipment, g', 'mrkv-ua-shipping'); ?></h4>
				<p class="mrkv-ua-ship-only-pro"><?php echo esc_html__('Only in the Pro version', 'mrkv-ua-shipping'); ?></p>
				<?php 
					$mrkv_ua_shipping_data = '';
					echo wp_kses( $mrkv_global_option_generator->get_input_number('', MRKV_OPTION_OBJECT_NAME . '[international][weight]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_international_weight' , '', '', '', 'readonly'), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
					<p class="mrkv-ua-ship-description">
						<?php
							echo esc_html__('The value entered here will be used to calculate the cost of all shipments. The weight must be specified in grams.', 'mrkv-ua-shipping');
						?>
					</p>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
				<h4><?php echo esc_html__('Length of shipment, cm', 'mrkv-ua-shipping'); ?></h4>
				<p class="mrkv-ua-ship-only-pro"><?php echo esc_html__('Only in the Pro version', 'mrkv-ua-shipping'); ?></p>
				<?php 
					$mrkv_ua_shipping_data = '';
					echo wp_kses( $mrkv_global_option_generator->get_input_number('', MRKV_OPTION_OBJECT_NAME . '[international][length]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_international_length' , '', '', '', 'readonly'), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
					<p class="mrkv-ua-ship-description">
						<?php
						echo esc_html__('The value entered here will be used to calculate the cost of all shipments. The length must be specified in centimeters.', 'mrkv-ua-shipping');
						?>
					</p>
				?>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'sender_middle_9'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
				<?php 
					$mrkv_ua_shipping_data = '';
					$mrkv_ua_shipping_description = '<span class="mrkv-ua-ship-only-pro">' . __('Only in the Pro version', 'mrkv-ua-shipping') . '</span>';
					echo wp_kses( $mrkv_global_option_generator->get_input_text(__('Global HS code', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][global_hscode]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_international_global_hscode' , '', __('Enter the HS code for all products...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description, 'readonly'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
				<p><?php echo esc_html__('The code of the commodity invoice of foreign economic activity (CN VED) must contain only numbers (from 6 to 10 digits).', 'mrkv-ua-shipping'); ?></p>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
				<?php 
					$mrkv_ua_shipping_data = '';

					$mrkv_ua_shipping_attributes = array();

					$mrkv_ua_shipping_description = '<span class="mrkv-ua-ship-only-pro">' . __('Only in the Pro version', 'mrkv-ua-shipping') . '</span>';

					echo wp_kses( $mrkv_global_option_generator->get_select_simple(__('HS codes in attributes', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][hscode_attr]', $mrkv_ua_shipping_attributes, $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_international_hscode_attr' , __('Choose an attribute', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description, 'disabled'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'sender_middle_10'); ?>
	<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
		<?php
			$mrkv_ua_shipping_data = '';
			echo wp_kses( $mrkv_global_option_generator->get_input_checkbox(__('Track a Banderole?', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][track]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_international_track', '', 'disabled'), MRKV_UA_SHIPPING_ALLOW_TAGS);
		?>
		<span class="mrkv-ua-ship-only-pro"><?php echo esc_html__('Only in the Pro version', 'mrkv-ua-shipping'); ?></span>
		<p class="mrkv-ua-ship-description"><?php echo esc_html__('Turn on if you need to track a Banderole in international delivery', 'mrkv-ua-shipping'); ?></p>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'sender_middle_11'); ?>
	<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
		<h4><?php echo esc_html__('Parameters of the shipment', 'mrkv-ua-shipping'); ?></h4>
		<p class="mrkv-ua-ship-only-pro"><?php echo esc_html__('Only in the Pro version', 'mrkv-ua-shipping'); ?></p>
		<div class="admin_ua_ship_morkva_settings_row">
			<div class="col-mrkv-5">
				<?php
					$mrkv_ua_shipping_data = '';
					echo wp_kses( $mrkv_global_option_generator->get_input_checkbox(__('Bulky parcel', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][bulky]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_international_bulky', '', 'disabled' ), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
			<div class="col-mrkv-5">
				<?php
					$mrkv_ua_shipping_data = '';
					echo wp_kses( $mrkv_global_option_generator->get_input_checkbox(__('Air delivery', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][air]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_international_air', '', 'disabled' ), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
		<div class="admin_ua_ship_morkva_settings_row">
			<div class="col-mrkv-5">
				<?php
					$mrkv_ua_shipping_data = '';
					echo wp_kses( $mrkv_global_option_generator->get_input_checkbox(__('Courier delivery', 'mrkv-ua-shipping') . ' <span class="mrkv-checkbox-cancel">' . __('(temporarily unavailable)', 'mrkv-ua-shipping') .'</span>', MRKV_OPTION_OBJECT_NAME . '[international][courier]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_international_courier', '', 'disabled' ), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
			<div class="col-mrkv-5">
				<?php
					$mrkv_ua_shipping_data = '';
					echo wp_kses( $mrkv_global_option_generator->get_input_checkbox(__('SMS notification upon arrival', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][sms]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_international_sms', '', 'disabled' ), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
		<p class="mrkv-ua-ship-description"><?php echo esc_html__('Options affect the shipping cost. Delivery is calculated in the currency specified ', 'mrkv-ua-shipping'); ?><a href="admin.php?page=wc-settings&tab=general" target="blanc"><?php echo esc_html__('in the Woocommerce settings', 'mrkv-ua-shipping'); ?></a></p>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'sender_middle_12'); ?>
	<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
		<h4><?php echo esc_html__('Sticker format', 'mrkv-ua-shipping'); ?></h4>
		<p class="mrkv-ua-ship-only-pro"><?php echo esc_html__('Only in the Pro version', 'mrkv-ua-shipping'); ?></p>
		<div class="admin_ua_ship_morkva_settings_row admin_ua_ship_morkva_settings_row_wrap">
			<?php
				$mrkv_ua_shipping_data = '';
				echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('Covering address form (cp71)', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][sticker]', 'cp71', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_international_sticker_cp', 'cp71', 'disabled'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('Customs declaration form (cn22)', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][sticker]', 'cn22', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_international_sticker_cn', '', 'disabled'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('Form for an envelope in the format (c6)', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][sticker]', 'c6', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_international_sticker_c', '', 'disabled'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('Form for an envelope in (dl) format', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][sticker]', 'dl', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_international_sticker_dl', '', 'disabled'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('Post-it note (100mm x 100mm)', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][sticker]', 'forms', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_international_sticker_forms', '', 'disabled'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				echo wp_kses( $mrkv_global_option_generator->get_input_radio(__('Form for sending cash on delivery (tfp3)', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][sticker]', 'tfp3', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_international_sticker_tfp3', '', 'disabled'), MRKV_UA_SHIPPING_ALLOW_TAGS);
			?>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'sender_middle_13'); ?>
	<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
		<?php
			$mrkv_ua_shipping_data = '';
			$mrkv_ua_shipping_description = '<span class="mrkv-ua-ship-only-pro">' . __('Only in the Pro version', 'mrkv-ua-shipping') . '</span>';

			echo wp_kses( $mrkv_global_option_generator->get_textarea(__('Description of an international shipment (Latin)', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[international][description]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_international_description' , '', __('For example, products for children...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description, 'readonly'), MRKV_UA_SHIPPING_ALLOW_TAGS);
		?>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'sender_middle_14'); ?>
	<div class="admin_ua_ship_morkva_settings_line admin_ua_ship_morkva_settings_line_p_info">
		<p><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/info-icon.svg'); ?>"><?php echo esc_html__('The following words are not allowed in the name of the shipment in English:
                BRYUKI, ACCESSEROISE, ACCESSORIES, GIFT, GIFT BOX, GIFTS, HANDMADE GIFT, KOSTYUM, KURTKA,
                ODEZHDA, PODAROK, PRESENT, PREZENT, SHAPKA, SOLDATYKY, SOUVENIR, SOUVENIR SET, SOUVENIRS,
                SUVENIER, SUVENIR, TAINA, XYDI, Other, item, cadeau, or only numbers (for example, 963258).
                The length should not exceed 32 characters.', 'mrkv-ua-shipping'); ?></p>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'sender_last'); ?>
</section>
<section id="email_settings" class="mrkv_up_ship_shipping_tab_block">
	<h2><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/mention-square-icon.svg'); ?>" alt="Sending email from TTN" title="Sending email from TTN"><?php echo esc_html__('Email settings', 'mrkv-ua-shipping'); ?></h2>
	<p><?php echo esc_html__('Create a custom message that will be sent after creating the shipment', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'email_first'); ?>
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
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'email_middle_1'); ?>
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
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'email_last'); ?>
</section>
<section id="automation_settings" class="mrkv_up_ship_shipping_tab_block">
	<h2><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/automation-icon.svg'); ?>" alt="Automation Settings" title="Automation Settings"><?php echo esc_html__('Automation Settings', 'mrkv-ua-shipping'); ?></h2>
	<p><?php echo esc_html__('Connect automation when working with shipments', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'automation_first'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line mrkv-field-disabled">
				<?php
					$mrkv_ua_shipping_data = '';
					echo wp_kses( $mrkv_global_option_generator->get_input_checkbox(__('Auto-creation of a shipment', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[automation][autocreate][enabled]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_automation_autocreate_enabled', '', 'disabled'), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
				<span class="mrkv-ua-ship-only-pro"><?php echo esc_html__('Only in the Pro version', 'mrkv-ua-shipping'); ?></span>
				<p class="mrkv-ua-ship-description">
					<?php echo esc_html__('A shipment will be created after the order is placed', 'mrkv-ua-shipping'); ?>
				</p>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'automation_last'); ?>
</section>
<section id="checkout_settings" class="mrkv_up_ship_shipping_tab_block">
	<h2><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/clapperboard-edit-icon.svg'); ?>" alt="Sending email from TTN" title="Sending email from TTN"><?php echo esc_html__('Checkout settings', 'mrkv-ua-shipping'); ?></h2>
	<p><?php echo esc_html__('Customize the output of the plugin fields in relation to your theme', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'checkout_first'); ?>
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
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'checkout_middle_1'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<?php
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['checkout']['middlename']['enabled']) ? MRKV_SHIPPING_SETTINGS['checkout']['middlename']['enabled'] : '';
					echo wp_kses( $mrkv_global_option_generator->get_input_checkbox(__('Enable patronymic (Warehouse)', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[checkout][middlename][enabled]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_checkout_middlename_enabled', ), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
				<p class="mrkv-ua-ship-description">
					<?php echo esc_html__('If the buyer has chosen cash on delivery, the "middle name" field will be displayed automatically, regardless of this setting (requirement of Ukrposhta API).', 'mrkv-ua-shipping'); ?>
				</p>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<?php
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['checkout']['middlename']['required']) ? MRKV_SHIPPING_SETTINGS['checkout']['middlename']['required'] : '';
					echo wp_kses( $mrkv_global_option_generator->get_input_checkbox(__('Patronymic is required (Warehouse)', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[checkout][middlename][required]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_checkout_middlename_required', ), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
				<p class="mrkv-ua-ship-description">
					<?php echo esc_html__('The patronymic is required if the sender is an individual. For sole proprietors and LLCs, this field is not required when creating a waybill.', 'mrkv-ua-shipping'); ?>
				</p>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'checkout_middle_2'); ?>
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
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'checkout_last'); ?>
</section>
<section id="log_settings" class="mrkv_up_ship_shipping_tab_block">
	<h2><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/clipboard-list-icon.svg'); ?>" alt="Debug Log" title="Debug Log"><?php echo esc_html__('Debug Log', 'mrkv-ua-shipping'); ?></h2>
	<p><?php echo esc_html__('To keep an error log, enable it in the settings', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'log_first'); ?>	
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
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'ukr-poshta', 'log_last'); ?>	
</section>