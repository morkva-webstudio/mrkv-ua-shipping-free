<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/' . MRKV_UA_SHIPPING_SETTINGS_SLUG 
	. '/api/mrkv-ua-shipping-sender-' . MRKV_UA_SHIPPING_SETTINGS_SLUG . '.php';

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_SETTINGS_NOVA_POSHTA'))
{
	/**
	 * Class for setup shipping methods settings
	 */
	class MRKV_UA_SHIPPING_SETTINGS_NOVA_POSHTA
	{
		/**
		 * Constructor for plugin shipping methods settings
		 * */
		function __construct()
		{
			$this->create_tabs_data();
			$this->create_sender_data();
		}

		/**
		 * Sender settings constant
		 * **/
		private function create_sender_data()
		{
			$options_sender = array();
			global $mrkv_global_shipping_object;
			$sender_nova_obj = new MRKV_UA_SHIPPING_SENDER_NOVA_POSHTA($mrkv_global_shipping_object);
			$senders_contacts_ref = $sender_nova_obj->get_senders_contacts_ref();
			$counterparties_ref = $sender_nova_obj->get_counterparties_ref();

			if(is_array($senders_contacts_ref) && !empty($senders_contacts_ref))
			{
				foreach($senders_contacts_ref as $counter => $sender)
				{
					foreach($sender as $sender_data_key => $sender_data_val)
					{
						if($sender_data_key == 'Description')
						{
							$options_sender[$counter]['description'] = $sender_data_val;
						}
						if($sender_data_key == 'Ref')
						{
							$options_sender[$counter]['data'] = $sender_data_val;
							continue;
						}

						$options_sender[$counter]['attr'][strtolower($sender_data_key)] = $sender_data_val;
					}
					$options_sender[$counter]['attr']['counterparty_ref'] = $counterparties_ref;
				}
			}

			define('MRKV_OPTION_NOVA_SENDER', $options_sender);
		}

		private function create_tabs_data()
		{
			define('MRKV_OPTION_TABS', array(
				'basic_settings' => __('Basic', 'mrkv-ua-shipping'),
				'sender_settings' => __('Sender', 'mrkv-ua-shipping'),
				'default_settings' => __('Default values', 'mrkv-ua-shipping'),
				'international_settings' => __('International', 'mrkv-ua-shipping'),
				'email_settings' => __('Email', 'mrkv-ua-shipping'),
				'automation_settings' => __('Automation', 'mrkv-ua-shipping'),
				'checkout_settings' => __('Checkout', 'mrkv-ua-shipping'),
				'log_settings' => __('Log', 'mrkv-ua-shipping')
			));
		}
	}
}