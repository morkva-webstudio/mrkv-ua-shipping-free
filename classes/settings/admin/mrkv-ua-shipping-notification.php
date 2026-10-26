<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_NOTIFICATION'))
{
	/**
	 * Class for setup plugin admin notification
	 */
	class MRKV_UA_SHIPPING_NOTIFICATION
	{
		/**
		 * Constructor for plugin admin notification
		 * */
		function __construct()
		{
			add_action('admin_init', array($this, 'mrkv_check_api_status_once_per_day'));
			add_action('admin_notices', array($this, 'mrkv_show_api_error_notice'));
			add_action('admin_notices', array($this, 'mrkv_show_shipping_zone_notice'));
			add_action('wp_ajax_mrkv_ua_ship_dismiss_zone_notice', array($this, 'mrkv_dismiss_shipping_zone_notice'));

			# A plugin method may have left the zones or the carriers changed: check the zones again
			add_action('woocommerce_shipping_zone_method_deleted', array($this, 'mrkv_reset_shipping_zone_check'));
			add_action('woocommerce_delete_shipping_zone_method', array($this, 'mrkv_reset_shipping_zone_check'));
			add_action('woocommerce_shipping_zone_method_status_toggled', array($this, 'mrkv_reset_shipping_zone_check'));
			add_action('update_option_m_ua_active_plugins', array($this, 'mrkv_reset_shipping_zone_check'));
		}

		public function mrkv_reset_shipping_zone_check()
		{
			delete_option('mrkv_ua_ship_zone_ok');
		}

		private function mrkv_get_enabled_method_ids()
		{
			require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'constants-mrkv-ua-shipping-methods.php';

			$m_ua_active_plugins = get_option('m_ua_active_plugins');
			$method_ids = array();

			foreach(MRKV_UA_SHIPPING_LIST as $slug => $shipping)
			{
				if(isset($m_ua_active_plugins[$slug]['enabled']) && $m_ua_active_plugins[$slug]['enabled'] == 'on')
				{
					foreach($shipping['method'] as $method)
					{
						$method_ids[] = $method['slug'];
					}
				}
			}

			return $method_ids;
		}

		public function mrkv_show_shipping_zone_notice()
		{
			if(get_option('mrkv_ua_ship_zone_ok') || !current_user_can('manage_woocommerce') || get_user_meta(get_current_user_id(), 'mrkv_ua_ship_zone_notice_dismissed', true))
			{
				return;
			}

			$method_ids = $this->mrkv_get_enabled_method_ids();
			if(empty($method_ids))
			{
				return;
			}

			global $wpdb;

			$placeholders = implode(',', array_fill(0, count($method_ids), '%s'));
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- placeholders are built from the count above
			$methods_in_zones = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}woocommerce_shipping_zone_methods WHERE is_enabled = 1 AND method_id IN ($placeholders)", $method_ids));

			if($methods_in_zones > 0)
			{
				# Remember it: no query on the next admin pages until the zones or the carriers change
				update_option('mrkv_ua_ship_zone_ok', 1, true);
				return;
			}
			?>
			<div id="mrkv-ua-ship-zone-notice" class="notice notice-error is-dismissible">
				<p>
					<?php echo esc_html__('Please note: none of the shipping methods (Nova Poshta, Ukrposhta, Rozetka Delivery) has been added to your shipping zones yet. The plugin fields will not appear on the checkout page until you set up a region.', 'mrkv-ua-shipping'); ?>
					<a href="<?php echo esc_url(admin_url('admin.php?page=wc-settings&tab=shipping')); ?>"><?php echo esc_html__('Set up a shipping zone', 'mrkv-ua-shipping'); ?></a>.
				</p>
			</div>
			<script>
				jQuery(document).on('click', '#mrkv-ua-ship-zone-notice .notice-dismiss', function(){
					jQuery.post(ajaxurl, { action: 'mrkv_ua_ship_dismiss_zone_notice', nonce: '<?php echo esc_js(wp_create_nonce('mrkv_ua_ship_zone_notice')); ?>' });
				});
			</script>
			<?php
		}

		public function mrkv_dismiss_shipping_zone_notice()
		{
			check_ajax_referer('mrkv_ua_ship_zone_notice', 'nonce');

			if(!current_user_can('manage_woocommerce'))
			{
				wp_send_json_error(null, 403);
			}

			update_user_meta(get_current_user_id(), 'mrkv_ua_ship_zone_notice_dismissed', 1);
			wp_send_json_success();
		}

		public function mrkv_check_api_status_once_per_day()
		{
			$m_ua_active_plugins = get_option('m_ua_active_plugins');

			if(isset($m_ua_active_plugins['nova-poshta']['enabled']) && $m_ua_active_plugins['nova-poshta']['enabled'] == 'on')
			{
				$last_check_np = get_option('mrkv_api_last_check_np');

				if (!$last_check_np || (time() - $last_check_np) > DAY_IN_SECONDS) {
					update_option('mrkv_api_last_check_np', time());

					$api_working = $this->mrkv_is_api_working('nova-poshta');

					if ($api_working) {
						update_option('mrkv_api_fixed_np', false); 
					} else {
						update_option('mrkv_api_fixed_np', true); 
					}
				}
			}
		}

		public function mrkv_is_api_working($key_shipping)
		{
			$mrkv_object_api = new MRKV_UA_SHIPPING_API_NOVA_POSHTA(get_option($key_shipping . '_m_ua_settings'));

			if(is_string($mrkv_object_api->active_api))
			{
				return false;
			}
			
			return true;
		}

		public function mrkv_show_api_error_notice()
		{
			$api_fixed = get_option('mrkv_api_fixed_np');

		    if ($api_fixed) {
				?>
				<div class="notice notice-error is-dismissible">
					<p>
						<?php
						printf(
							/* translators: 1: Nova Poshta account link, 2: Plugin settings link */
							esc_html__( 'Your Nova Poshta API key needs to be updated so that the branches are pulled up on the Checkout page, update the API key in your %1$s and save it in %2$s.', 'mrkv-ua-shipping' ),
							'<a href="' . esc_url( 'http://my.novaposhta.ua/' ) . '" target="_blank">' . esc_html__( 'Nova Poshta account', 'mrkv-ua-shipping' ) . '</a>',
							'<a href="' . esc_url( admin_url( 'admin.php?page=mrkv_ua_shipping_nova-poshta' ) ) . '">' . esc_html__( 'the settings', 'mrkv-ua-shipping' ) . '</a>'
						);
						?>
					</p>
				</div>
				<?php
			}
		}
	}
}