<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php global $mrkv_global_option_generator; global $mrkv_global_shipping_object; ?>
<section id="basic_settings" class="mrkv_up_ship_shipping_tab_block active">
	<h2><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/settings-icon.svg'); ?>" alt="Basic settings" title="Basic settings"><?php echo esc_html__('Basic settings', 'mrkv-ua-shipping'); ?></h2>
	<p><?php echo esc_html__('Login and password are issued separately for the test environment and the product environment', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-global', 'basic_first'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<?php 
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['production_username']) ? MRKV_SHIPPING_SETTINGS['production_username'] : '';
					$mrkv_ua_shipping_description = __('It`s issued to the partner after signing the cooperation agreement', 'mrkv-ua-shipping');
					$mrkv_ua_shipping_label = __('Username / Login', 'mrkv-ua-shipping');
					global $mrkv_global_option_generator;
					global $mrkv_global_shipping_object;

					echo wp_kses( $mrkv_global_option_generator->get_input_text($mrkv_ua_shipping_label, MRKV_OPTION_OBJECT_NAME . '[production_username]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_production_username' , '', __('Enter the username...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
		<div class="col-mrkv-5">
			<div class="admin_ua_ship_morkva_settings_line">
				<?php 
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['production_password']) ? MRKV_SHIPPING_SETTINGS['production_password'] : '';
					$mrkv_ua_shipping_description = __('It`s issued to the partner after signing the cooperation agreement', 'mrkv-ua-shipping');
					echo wp_kses( $mrkv_global_option_generator->get_input_text(__('Password', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[production_password]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_production_password' , '', __('Enter the password...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-global', 'basic_middle_1'); ?>
	<div class="admin_ua_ship_morkva_settings_line">
		<?php
			$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['test_mode']) ? MRKV_SHIPPING_SETTINGS['test_mode'] : '';
			echo wp_kses( $mrkv_global_option_generator->get_input_checkbox(__('Test mode', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[test_mode]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME . '_test_mode', ), MRKV_UA_SHIPPING_ALLOW_TAGS);
		?>
		<div class="admin_ua_ship_morkva_settings_line__inner">
			<div class="admin_ua_ship_morkva_settings_row">
				<div class="col-mrkv-5">
					<?php 
						$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['test_username']) ? MRKV_SHIPPING_SETTINGS['test_username'] : '';
						$mrkv_ua_shipping_description = __('It`s issued to the partner after signing the cooperation agreement', 'mrkv-ua-shipping');
						$mrkv_ua_shipping_label = __('Username / Login', 'mrkv-ua-shipping');

						echo wp_kses( $mrkv_global_option_generator->get_input_text($mrkv_ua_shipping_label, MRKV_OPTION_OBJECT_NAME . '[test_username]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_test_username' , '', __('Enter the username...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
					?>
				</div>
				<div class="col-mrkv-5">
					<?php 
					$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['test_password']) ? MRKV_SHIPPING_SETTINGS['test_password'] : '';
					$mrkv_ua_shipping_description = __('It`s issued to the partner after signing the cooperation agreement', 'mrkv-ua-shipping');
					echo wp_kses( $mrkv_global_option_generator->get_input_text(__('Password', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[test_password]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_test_password' , '', __('Enter the password...', 'mrkv-ua-shipping'), $mrkv_ua_shipping_description), MRKV_UA_SHIPPING_ALLOW_TAGS);
				?>
				</div>
			</div>
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-global', 'basic_last'); ?>
</section>
<section id="default_settings" class="mrkv_up_ship_shipping_tab_block">
	<h2><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/box-icon.svg'); ?>" alt="Default values" title="Default values"><?php echo esc_html__('Default values', 'mrkv-ua-shipping'); ?></h2>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-global', 'default_first'); ?>
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
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-global', 'default_middle_1'); ?>
	<div class="admin_ua_ship_morkva_settings_line admin_ua_ship_morkva_one_data">
		<?php 
			$mrkv_ua_shipping_data = isset(MRKV_SHIPPING_SETTINGS['shipment']['volume']) ? MRKV_SHIPPING_SETTINGS['shipment']['volume'] : '';
			$mrkv_ua_shipping_description = __('It is calculated automatically according to the dimensions in the settings.', 'mrkv-ua-shipping');

			echo wp_kses( $mrkv_global_option_generator->get_input_number(__('Volumetric weight', 'mrkv-ua-shipping'), MRKV_OPTION_OBJECT_NAME . '[shipment][volume]', $mrkv_ua_shipping_data, MRKV_OPTION_OBJECT_NAME. '_shipment_volume' , '', '', $mrkv_ua_shipping_description, 'readonly'), MRKV_UA_SHIPPING_ALLOW_TAGS);
		?>
		<p><strong><?php echo esc_html__('These standard weight and dimensions apply when products do not have ones of their own', 'mrkv-ua-shipping'); ?></strong></p>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-global', 'default_last'); ?>
</section>
<section id="checkout_settings" class="mrkv_up_ship_shipping_tab_block">
	<h2><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/clapperboard-edit-icon.svg'); ?>" alt="Sending email from TTN" title="Sending email from TTN"><?php echo esc_html__('Checkout settings', 'mrkv-ua-shipping'); ?></h2>
	<p><?php echo esc_html__('Customize the output of the plugin fields in relation to your theme', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-global', 'checkout_first'); ?>
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
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-global', 'checkout_middle_1'); ?>
	<div class="admin_ua_ship_morkva_settings_row">
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
		<div class="col-mrkv-5">
		</div>
	</div>
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-global', 'checkout_last'); ?>
</section>
<section id="log_settings" class="mrkv_up_ship_shipping_tab_block">
	<h2><img src="<?php echo esc_url(MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/clipboard-list-icon.svg'); ?>" alt="Debug Log" title="Debug Log"><?php echo esc_html__('Debug Log', 'mrkv-ua-shipping'); ?></h2>
	<p><?php echo esc_html__('To keep an error log, enable it in the settings', 'mrkv-ua-shipping'); ?></p>
	<hr class="mrkv-ua-ship__hr">
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-global', 'log_first'); ?>
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
	<?php do_action('mrkv_ua_shipping_settings_page_row', 'nova-global', 'log_last'); ?>
</section>