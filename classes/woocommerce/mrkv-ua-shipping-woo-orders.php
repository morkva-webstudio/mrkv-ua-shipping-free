<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Utilities\OrderUtil;

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_WOO_ORDERS'))
{
	/**
	 * Class for setup WOO settings orders
	 */
	class MRKV_UA_SHIPPING_WOO_ORDERS
	{
		/**
		 * Constructor for WOO settings orders
		 * */
		function __construct()
		{
			if(class_exists( \Automattic\WooCommerce\Utilities\OrderUtil::class ) &&  OrderUtil::custom_orders_table_usage_is_enabled()){
	            add_filter('manage_woocommerce_page_wc-orders_columns', array( $this, 'mrkv_ua_ship_woo_custom_column' ));
	            add_action('manage_woocommerce_page_wc-orders_custom_column', array( $this, 'mrkv_ua_ship_woo_column_get_data_hpos' ), 20, 2 );
	        }
	        else{
	            add_filter('manage_edit-shop_order_columns', array( $this, 'mrkv_ua_ship_woo_custom_column' ));
	            add_action('manage_shop_order_posts_custom_column', array( $this, 'mrkv_ua_ship_woo_column_get_data' ));
	        }

	        add_action('admin_footer', array($this, 'mrkv_ua_ship_form_create_invoice'));

	        add_filter( 'woocommerce_account_orders_columns', array($this, 'add_account_orders_column_ttn'), 10, 1 );
	        add_action( 'woocommerce_my_account_my_orders_column_mrkv_ua_ship_ttn-column', array($this, 'add_account_orders_column_rows_ttn') );
		}

		public function add_account_orders_column_ttn($columns)
		{
			$order_actions  = $columns['order-actions']; 
		    unset($columns['order-actions']); 

		    $columns['mrkv_ua_ship_ttn-column'] = __('Invoice', 'mrkv-ua-shipping');

		    $columns['order-actions'] = $order_actions;

		    return $columns;
		}

		public function add_account_orders_column_rows_ttn($order)
		{
			$keys_shipping = array_keys(MRKV_UA_SHIPPING_LIST);
    		$key = '';
    		$current_shipping = '';

	    	foreach($order->get_shipping_methods() as $shipping)
            {
            	foreach($keys_shipping as $key_ship)
				{
					$current_shipping = $shipping->get_method_id();

					if(str_contains($shipping->get_method_id(), $key_ship))
					{
						$key = $key_ship;
					}
					if(in_array($current_shipping, MRKV_UA_SHIPPING_LIST[$key_ship]['old_slugs']))
					{
						$key = $key_ship;
						$current_shipping = array_search($shipping->get_method_id(), MRKV_UA_SHIPPING_LIST[$key_ship]['old_slugs']);
					}
				}
            }

            if(!$key)
            {
            	return;
            }

			$mrkv_ua_ship_invoice = $order->get_meta('mrkv_ua_ship_invoice_number');

			if(!$mrkv_ua_ship_invoice)
			{
				$mrkv_ua_ship_invoice = $order->get_meta(MRKV_UA_SHIPPING_LIST[$key]['old_ttn_slug']);
			}

			echo esc_html($mrkv_ua_ship_invoice);

			$shipping_settings = get_option($key . '_m_ua_settings');

			if($key == 'ukr-poshta')
			{
				$mrkv_ua_ship_invoice = $order->get_meta('mrkv_ua_ship_invoice_ref');
			}
			else
			{
				$invoices_array = array('invoice' => $mrkv_ua_ship_invoice);
				$invoices_object = json_decode(json_encode($invoices_array));
				$mrkv_ua_ship_invoice = array($invoices_object);
			}

			$api_class = MRKV_UA_SHIPPING_LIST[$key]['api_class'];

			require_once MRKV_UA_SHIPPING_PLUGIN_PATH . 'classes/shipping_methods/' . $key 
							. '/api/mrkv-ua-shipping-api-' . $key . '.php';
			$mrkv_global_shipping_object = new $api_class($shipping_settings);
			$invoices_result = $mrkv_global_shipping_object->get_status_documents($mrkv_ua_ship_invoice);

			if($key == 'ukr-poshta')
			{
				if(isset($invoices_result['status']))
				{
					echo '<br>';
					echo esc_html($invoices_result['status']);
				}
			}
			else
			{
				foreach($invoices_result as $data_invoice)
				{
					if(isset($data_invoice['Status']))
					{
						echo '<br>';
						echo esc_html($data_invoice['Status']);
					}
				}
			}
		}

		public function mrkv_ua_ship_form_create_invoice()
		{
			$screen = get_current_screen();

	    	if($screen && ( $screen->id === 'shop_order' || $screen->id === 'woocommerce_page_wc-orders' ) || $screen->id === 'edit-shop_order')
	    	{
				# Include template
				include MRKV_UA_SHIPPING_PLUGIN_PATH_TEMP . '/orders/mrkv-ua-ship-popup.php';
			}
		}

		/**
	     * Creating custom column at woocommerce order page
	     * @param array Columns
	     * @since 1.1.0
	     */
	    public function mrkv_ua_ship_woo_custom_column($columns)
	    {
	        $columns['mrkv_ua_invoice'] = __('Invoice', 'mrkv-ua-shipping');
	        $columns['mrkv_ua_shipping'] = __('Shipping', 'mrkv-ua-shipping');

	        return $columns;
	    }

	    /**
	     * Getting data of order column at order page
	     *
	     * @since 1.1.0
	     */
	    public function mrkv_ua_ship_woo_column_get_data($column)
	    {
	        global $post;
	        $the_order = '';

	        if($post && $post->ID)
	        {
	        	$the_order = wc_get_order( $post->ID );
	        }

	        $this->mrkv_ua_ship_columns_content($column, $the_order);
	    }

	    /**
	     * Getting data of order column at order page
	     *
	     * @since 1.1.0
	     */
	    public function mrkv_ua_ship_woo_column_get_data_hpos($column, $the_order)
	    {
	    	$this->mrkv_ua_ship_columns_content($column, $the_order);
	    }

	    private function mrkv_ua_ship_columns_content($column, $the_order)
	    {
	    	if($the_order)
	    	{
	    		$keys_shipping = array_keys(MRKV_UA_SHIPPING_LIST);
	    		$key = '';
	    		$current_shipping = '';

		    	foreach($the_order->get_shipping_methods() as $shipping)
	            {
	            	foreach($keys_shipping as $key_ship)
					{
						$current_shipping = $shipping->get_method_id();

						if(str_contains($shipping->get_method_id(), $key_ship))
						{
							$key = $key_ship;
						}
						if(in_array($current_shipping, MRKV_UA_SHIPPING_LIST[$key_ship]['old_slugs']))
						{
							$key = $key_ship;
							$current_shipping = array_search($shipping->get_method_id(), MRKV_UA_SHIPPING_LIST[$key_ship]['old_slugs']);
						}
					}
	            }

	            $m_ua_active_plugins = get_option('m_ua_active_plugins');
	            $enabled_shipping = ($m_ua_active_plugins && isset($m_ua_active_plugins[$key]['enabled']) && $m_ua_active_plugins[$key]['enabled']) == 'on' ? true : false;

		    	if ($column == 'mrkv_ua_invoice') 
		    	{
		    		if($key && $enabled_shipping  && !empty(MRKV_UA_SHIPPING_LIST[$key]['invoice_links']))
		    		{
		    			$mrkv_ua_ship_invoice = $the_order->get_meta('mrkv_ua_ship_invoice_number');

		    			if(!$mrkv_ua_ship_invoice)
		    			{
		    				$mrkv_ua_ship_invoice = $the_order->get_meta(MRKV_UA_SHIPPING_LIST[$key]['old_ttn_slug']);
		    			}

			            if($mrkv_ua_ship_invoice)
						{
							$shipping_settings = get_option($key . '_m_ua_settings');
							?>
								<div class="mrkv_column-li_action">
							<?php
			            	if(isset(MRKV_UA_SHIPPING_LIST[$key]['invoice_links']['invoice_view']))
			            	{
			            		?>
				            		<a target="blanc" href="<?php echo esc_url(MRKV_UA_SHIPPING_LIST[$key]['invoice_links']['invoice_view']) . esc_html($mrkv_ua_ship_invoice); ?>" class="mrkv_ua_ship_global__invoice mrkv_ua_ship_global_invoice-link" data-ttn="<?php echo esc_html($mrkv_ua_ship_invoice); ?>"><span><?php echo esc_html($mrkv_ua_ship_invoice); ?></span> <img src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/global'); ?>/external-link.svg" alt="External" title="External"></a>
				            	<?php
			            	}
			            	else
			            	{
			            		?>
			            			<div class="mrkv_ua_ship_global__invoice" data-ttn="<?php echo esc_html($mrkv_ua_ship_invoice); ?>"><?php echo esc_html($mrkv_ua_ship_invoice); ?></div>
			            		<?php
			            	}

							switch($key)
							{
								case 'nova-poshta':
									?>
										<a class="mrkv-col-added-link" target="blanc" href="<?php echo esc_url(MRKV_UA_SHIPPING_LIST[$key]['invoice_links']['invoice_pdf'] . $mrkv_ua_ship_invoice . MRKV_UA_SHIPPING_LIST[$key]['invoice_links']['invoice_link_end'] . $shipping_settings['api_key']); ?>">
											<img src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/global'); ?>/printer-icon.svg" alt="<?php echo esc_html__('Print invoice', 'mrkv-ua-shipping'); ?>" title="<?php echo esc_html__('Print invoice', 'mrkv-ua-shipping'); ?>">
										</a>
										<a class="mrkv-col-added-link" target="blanc" href="<?php echo esc_url(MRKV_UA_SHIPPING_LIST[$key]['invoice_links']['invoice_sticker'] . $mrkv_ua_ship_invoice . MRKV_UA_SHIPPING_LIST[$key]['invoice_links']['invoice_link_end'] . $shipping_settings['api_key']); ?>">
											<img src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/global'); ?>/sticker-icon.svg" alt="<?php echo esc_html__('Print sticker', 'mrkv-ua-shipping'); ?>" title="<?php echo esc_html__('Print sticker', 'mrkv-ua-shipping'); ?>">
										</a>
									<?php
								break;
								case 'ukr-poshta':
									?>
										<a class="mrkv-col-added-link mrkv_ua_ship_print_inv_ukr" data-form="form-ukr-poshta-ttn" data-order-id="<?php echo esc_attr($the_order->get_id()); ?>">
											<img src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/global'); ?>/printer-icon.svg" alt="<?php echo esc_html__('Print invoice', 'mrkv-ua-shipping'); ?>" title="<?php echo esc_html__('Print invoice', 'mrkv-ua-shipping'); ?>">
										</a>
									<?php
								break;
							}
							?>
								</div>
							<?php
			            }
			            elseif($current_shipping != 'mrkv_ua_shipping_nova-poshta_international' && $current_shipping != 'mrkv_ua_shipping_nova-poshta_inter_address')
							{
			            	?>
			            	<a>
			            		<div data-method="<?php echo esc_attr($current_shipping); ?>" data-ship="<?php echo esc_attr($key); ?>" data-order-id="<?php echo esc_attr($the_order->get_id()); ?>" class="mrkv_ua_ship_global_create__invoice">
			            			<img src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/global'); ?>/add-square-icon.svg" alt="<?php echo esc_attr($the_order->get_shipping_method()); ?>" title="<?php echo esc_attr($the_order->get_shipping_method()); ?>">
			            			<span><?php echo esc_html(__('Create', 'mrkv-ua-shipping')); ?></span>
			            			<div class="mrkv_ua_ship_create_invoice__loader"></div>
			            		</div>
			            	</a>
			            	<?php
			            }
		    		}
		    		else
		    		{
		    			?>
	            			<div class="mrkv_ua_ship_global_orders_li">
	            				<span>-</span>
	            			</div>
	            		<?php
		    		}
		        }

		        if ($column == 'mrkv_ua_shipping') 
		        {
		            if($key && $enabled_shipping)
	            	{
	            		?>
	            			<div class="mrkv_ua_ship_global_orders_li">
	            				<img src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/' . $key); ?>/logo-mini.png" alt="<?php echo esc_attr($the_order->get_shipping_method()); ?>" title="<?php echo esc_attr($the_order->get_shipping_method()); ?>">
	            			</div>
	            		<?php
	            	}
	            	else
	            	{
	            		?>
	            			<div class="mrkv_ua_ship_global_orders_li">
	            				<span>-</span>
	            			</div>
	            		<?php
	            	}
		        }
	    	}
	    }
	}
}