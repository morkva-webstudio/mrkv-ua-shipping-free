<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Utilities\OrderUtil;

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_WOO_PRODUCT'))
{
	/**
	 * Class for setup WOO settings product
	 */
	class MRKV_UA_SHIPPING_WOO_PRODUCT
	{
		/**
		 * Constructor for WOO settings product
		 * */
		function __construct()
		{
			add_filter('woocommerce_product_data_tabs', [$this, 'admin_mrkv_ua_shipping_add_new_tab']);
			add_action('woocommerce_product_data_panels', [$this, 'admin_mrkv_ua_shipping_add_new_tab_content']);
			add_action('woocommerce_process_product_meta', [$this, 'admin_mrkv_ua_shipping_add_new_tab_data_save']);
		}

		private function get_all_shipping_settings_html() {
			$m_ua_active_plugins = get_option('m_ua_active_plugins');
			if (empty($m_ua_active_plugins) || !is_array($m_ua_active_plugins)) {
				return '';
			}

			ob_start(); 
			
			global $post;
			$product = wc_get_product($post->ID);

			foreach (MRKV_UA_SHIPPING_LIST as $slug => $shipping) {
				if (isset($m_ua_active_plugins[$slug]['enabled']) && $m_ua_active_plugins[$slug]['enabled'] == 'on') {
					$settings_shipping = get_option($slug . '_m_ua_settings');
					$file_path = MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/' . $slug . '/product/mrkv-ua-shipping-product.php';
					
					if (file_exists($file_path)) {
						include $file_path;
					}
				}
			}

			$output = ob_get_clean();
			return trim($output);
		}

		public function admin_mrkv_ua_shipping_add_new_tab($tabs) {
			$settings_html = $this->get_all_shipping_settings_html();

			if (!empty($settings_html)) {
				$tabs['mrkv_ua_shipping'] = [
					'label'    => __('morkva UA Shipping', 'mrkv-ua-shipping'),
					'target'   => 'mrkv_ua_shipping_options',
					'class'    => [],
					'priority' => 50,
				];
			}
			return $tabs;
		}

		public function admin_mrkv_ua_shipping_add_new_tab_content() {
			$settings_html = $this->get_all_shipping_settings_html();

			if (!empty($settings_html)) 
			{
				$allowed_form_html = array(
					'hr'    => array(),
					'h3'    => array( 'style' => array() ),
					'p'     => array( 'class' => array() ),
					'label' => array( 'for' => array() ),
					'select'=> array(
						'id'    => array(),
						'name'  => array(),
						'class' => array(),
					),
					'option'=> array(
						'value'    => array(),
						'selected' => array(),
					),
					'span'  => array( 'class' => array() ),
				);
				?>
				<div id="mrkv_ua_shipping_options" class="panel woocommerce_options_panel">
					<h3 style="padding: 0px 20px;"><?php esc_html_e('morkva UA Shipping', 'mrkv-ua-shipping'); ?></h3>
					<?php echo wp_kses( $settings_html, $allowed_form_html ); ?>
					<hr>
				</div>
				<?php
			}
		}

		public function admin_mrkv_ua_shipping_add_new_tab_data_save($post_id)
		{
			if ( ! isset( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['woocommerce_meta_nonce'] ) ), 'woocommerce_save_data' ) ) {
				return;
			}

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			if ( isset( $_POST['_mrkv_tire_type'] ) ) {
				update_post_meta(
					$post_id, 
					'_mrkv_tire_type', 
					sanitize_text_field( wp_unslash( $_POST['_mrkv_tire_type'] ) )
				);
			}

		    if ( isset( $_POST['_mrkv_document_weight'] ) ) {
				update_post_meta(
					$post_id, 
					'_mrkv_document_weight', 
					sanitize_text_field( wp_unslash( $_POST['_mrkv_document_weight'] ) )
				);
			}
		}
	}
}