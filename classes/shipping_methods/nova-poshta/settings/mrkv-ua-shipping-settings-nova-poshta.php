<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 
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
			global $mrkv_global_shipping_object;
			$sender_nova_obj = new MRKV_UA_SHIPPING_SENDER_NOVA_POSHTA($mrkv_global_shipping_object);
			$counterparties = array();
			$counterparty_ref = '';
			$contacts = array();

			# The lists are cached: the settings page does not ask Nova Poshta on every load
			if(is_object($mrkv_global_shipping_object) && $mrkv_global_shipping_object->get_api_key() && $mrkv_global_shipping_object->active_api === true)
			{
				$counterparties = $sender_nova_obj->get_sender_counterparties();
				$saved_counterparty_ref = isset(MRKV_SHIPPING_SETTINGS['sender']['counterparty_ref']) ? MRKV_SHIPPING_SETTINGS['sender']['counterparty_ref'] : '';

				foreach($counterparties as $counterparty)
				{
					if($counterparty['ref'] == $saved_counterparty_ref)
					{
						$counterparty_ref = $saved_counterparty_ref;
						break;
					}
				}

				# Not chosen yet (or chosen under another key): the first counterparty, as before
				if(!$counterparty_ref && isset($counterparties[0]['ref']))
				{
					$counterparty_ref = $counterparties[0]['ref'];
				}

				$contacts = $sender_nova_obj->get_contact_persons($counterparty_ref);
			}

			define('MRKV_OPTION_NOVA_COUNTERPARTIES', $counterparties);
			define('MRKV_OPTION_NOVA_COUNTERPARTY_REF', $counterparty_ref);
			define('MRKV_OPTION_NOVA_SENDER', MRKV_UA_SHIPPING_SENDER_NOVA_POSHTA::get_contact_persons_options($contacts, $counterparty_ref));
		}

		private function create_tabs_data()
		{
			define('MRKV_OPTION_TABS', array(
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