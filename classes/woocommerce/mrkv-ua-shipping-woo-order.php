<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Utilities\OrderUtil;

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_WOO_ORDER'))
{
	/**
	 * Class for setup WOO settings order
	 */
	class MRKV_UA_SHIPPING_WOO_ORDER
	{
		/**
		 * Constructor for WOO settings order
		 * */
		function __construct()
		{
			add_action('add_meta_boxes', array( $this, 'mrkv_ua_ship_add_meta_boxes' ), 10, 2);

			add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'mrkv_ua_ship_order_editable_billing' ) );
			add_action( 'woocommerce_admin_order_data_after_shipping_address', array( $this, 'mrkv_ua_ship_order_editable_billing' ) );
			add_action( 'woocommerce_process_shop_order_meta', array( $this, 'mrkv_ua_ship_save_admin_order_billing' ) );
		}

		public function mrkv_ua_ship_add_meta_boxes($post_type, $post)
		{
			# Check hpos
	        if(class_exists( CustomOrdersTableController::class )){
	            $screen = wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled()
	            ? wc_get_page_screen_id( 'shop-order' )
	            : 'shop_order';
	        }
	        else{
	            $screen = 'shop_order';
	        }

	        if ( $post instanceof \WP_Post ) {
                $order_id = $post->ID;
            } elseif ( $post instanceof \WC_Order ) {
                $order_id = $post->get_id();
            } else {
                $order_id = false;
            }

	        if ($order_id) 
	        {
	            $order = wc_get_order($order_id);

	            if($order)
	            {
	            	$keys_shipping = array_keys(MRKV_UA_SHIPPING_LIST);
		    		$key = '';
		    		$current_shipping = '';

			    	foreach($order->get_shipping_methods() as $shipping)
		            {
		            	foreach($keys_shipping as $key_ship)
						{

							if(str_contains($shipping->get_method_id(), $key_ship))
							{
								$key = $key_ship;
								$current_shipping = $shipping->get_method_id();
							}
							if(in_array($shipping->get_method_id(), MRKV_UA_SHIPPING_LIST[$key_ship]['old_slugs']))
							{
								$key = $key_ship;
								$current_shipping = $shipping->get_method_id();
							}
						}
		            }

		            if($key)
		            {
		            	add_meta_box('mrkv_ua_shipping_data_box', MRKV_UA_SHIPPING_LIST[$key]['name'], array( $this, 'mrkv_ua_shipping_add_plugin_meta_box' ), $screen, 'side', 'core');
		            }
		            else
		            {
		            	add_meta_box('mrkv_ua_shipping_data_box', __('morkva UA Shipping', 'mrkv-ua-shipping'), array( $this, 'mrkv_ua_shipping_changer_add_plugin_meta_box' ), $screen, 'side', 'core');
		            }
	            }
	        }
		}

		public function mrkv_ua_shipping_changer_add_plugin_meta_box($post)
		{
			if ( $post instanceof \WP_Post ) {
                $order_id = $post->ID;
            } elseif ( $post instanceof \WC_Order ) {
                $order_id = $post->get_id();
            } else {
                $order_id = false;
            }

			if ($order_id) 
	        {
	            $order = wc_get_order($order_id);

	            if($order)
	            {
	            	$order_status = $order->get_status();
	            	$order_already_created = $order_status != 'auto-draft' ? true : false;

	            	$available_methods = WC()->shipping->load_shipping_methods();
	            	?>
	            		<h3><?php echo esc_html__('Change shipping method', 'mrkv-ua-shipping'); ?></h3>
	            		<div class="mrkv-ua-shipping-line-choose-method">
	            			<select name="mrkv_shipping_method" id="mrkv_ua_shipping_method">
		            			<option value=""><?php echo esc_html__('Choose method', 'mrkv-ua-shipping'); ?></option>
		            			<?php 
		            				foreach ($available_methods as $method_id => $method) 
		            				{
		            					if(str_contains($method_id, 'mrkv_ua_shipping'))
		            					{
		            						echo '<option value="' . esc_attr($method_id) . '" ' . selected($current_shipping_method, $method_id, false) . '>';
									        echo esc_html($method->get_method_title());
									        echo '</option>';
		            					}
		            				}
		            			?>
		            		</select>
		            		<div class="mrkv-ua-shupping-change-method button <?php if(!$order_already_created){ echo 'disabled'; } ?>"><?php echo esc_html__('Change method', 'mrkv-ua-shipping'); ?></div>
	            		</div>
	            		<?php
	            			if($order_already_created)
	            			{
	            				?>
	            					<script>
			            			jQuery(document).ready(function($) {
			            				jQuery('.mrkv-ua-shupping-change-method').on('click', function() 
			            				{
			            					var shippingMethod = jQuery('#mrkv_ua_shipping_method').val();
			            					var shippingMethodName = jQuery('#mrkv_ua_shipping_method option:selected').text();
		                					var orderId = '<?php echo esc_attr($order_id); ?>';

		                					if(shippingMethod)
		                					{
		                						jQuery.ajax({
							                    url: '<?php echo esc_url(admin_url( "admin-ajax.php" )); ?>',
							                    method: 'POST',
							                    data: {
							                        action: 'mrkv_update_shipping_method',
							                        order_id: orderId,
							                        shipping_method: shippingMethod,
							                        shipping_method_name: shippingMethodName,
							                        nonce: '<?php echo esc_js( wp_create_nonce( 'mrkv_ua_ship_nonce' ) ); ?>'
							                    },
							                    success: function(response) 
							                    {
							                        location.reload();
							                    }
							                });
		                					}
		            					});
			            			});
			            		</script>
	            				<?php
	            			}
	            		?>
	            	<?php
	            }
	        }
		}

		public function mrkv_ua_shipping_add_plugin_meta_box($post)
		{
			if ( $post instanceof \WP_Post ) {
                $order_id = $post->ID;
            } elseif ( $post instanceof \WC_Order ) {
                $order_id = $post->get_id();
            } else {
                $order_id = false;
            }
			
			if ($order_id) 
	        {
            	$order = wc_get_order($order_id);

	            if($order)
	            {
	            	$keys_shipping = array_keys(MRKV_UA_SHIPPING_LIST);
		    		$key = '';
		    		$current_shipping = '';

			    	foreach($order->get_shipping_methods() as $shipping)
		            {
		            	foreach($keys_shipping as $key_ship)
						{

							if(str_contains($shipping->get_method_id(), $key_ship))
							{
								$key = $key_ship;
								$current_shipping = $shipping->get_method_id();
							}
							if(in_array($shipping->get_method_id(), MRKV_UA_SHIPPING_LIST[$key_ship]['old_slugs']))
							{
								$key = $key_ship;
								$current_shipping = array_search($shipping->get_method_id(), MRKV_UA_SHIPPING_LIST[$key_ship]['old_slugs']);
							}
						}
		            }

		            $m_ua_active_plugins = get_option('m_ua_active_plugins');
	            	$enabled_shipping = ($m_ua_active_plugins && isset($m_ua_active_plugins[$key]['enabled']) && $m_ua_active_plugins[$key]['enabled']) == 'on' ? true : false;

		            if($key && $enabled_shipping)
		    		{
		    			$mrkv_ua_ship_invoice = $order->get_meta('mrkv_ua_ship_invoice_number');

		    			if(!$mrkv_ua_ship_invoice)
		    			{
		    				$mrkv_ua_ship_invoice = $order->get_meta(MRKV_UA_SHIPPING_LIST[$key]['old_ttn_slug']);
		    			}

						do_action( 'mrkv_ua_shipping_metabox_start', $key, $order);

			            if($mrkv_ua_ship_invoice){
			            	?>
			            		<h3><?php echo esc_html__('Invoice number', 'mrkv-ua-shipping'); ?></h3>
			            		<div class="mrkv_ua_ship_global__invoice" data-ttn="<?php echo esc_html($mrkv_ua_ship_invoice); ?>"><?php echo esc_html($mrkv_ua_ship_invoice); ?><img src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/global'); ?>/copy-ttn.svg" alt="<?php echo esc_html__('Copy invoice', 'mrkv-ua-shipping'); ?>" title="<?php echo esc_html__('Copy invoice', 'mrkv-ua-shipping'); ?>"></div>
			            		<hr class="mrkv-hr-sidebar">
			            		<h3><?php echo esc_html__('Invoice action', 'mrkv-ua-shipping'); ?></h3>
			            		<div class="mrkv_ua_invoice_action_list">
			            			<?php 
			            				$shipping_settings = get_option($key . '_m_ua_settings');
			            				switch($key)
			            				{
			            					case 'nova-poshta':
				            					?>
				            						<a target="blanc" href="<?php echo esc_url(MRKV_UA_SHIPPING_LIST[$key]['invoice_links']['invoice_pdf'] . $mrkv_ua_ship_invoice . MRKV_UA_SHIPPING_LIST[$key]['invoice_links']['invoice_link_end'] . $shipping_settings['api_key']); ?>">
				            							<img src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/global'); ?>/printer-icon.svg" alt="<?php echo esc_html__('Print invoice', 'mrkv-ua-shipping'); ?>" title="<?php echo esc_html__('Print invoice', 'mrkv-ua-shipping'); ?>">
				            							<?php echo esc_html__('Print invoice', 'mrkv-ua-shipping'); ?>
			            							</a>
				            						<a target="blanc" href="<?php echo esc_url(MRKV_UA_SHIPPING_LIST[$key]['invoice_links']['invoice_sticker'] . $mrkv_ua_ship_invoice . MRKV_UA_SHIPPING_LIST[$key]['invoice_links']['invoice_link_end'] . $shipping_settings['api_key']); ?>">
				            							<img src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/global'); ?>/sticker-icon.svg" alt="<?php echo esc_html__('Print sticker', 'mrkv-ua-shipping'); ?>" title="<?php echo esc_html__('Print sticker', 'mrkv-ua-shipping'); ?>">
				            							<?php echo esc_html__('Print sticker', 'mrkv-ua-shipping'); ?>
			            							</a>
				            					<?php
			            					break;
			            					case 'ukr-poshta':
				            					?>
													<a class="mrkv_ua_ship_print_inv_ukr" data-form="form-ukr-poshta-ttn">
					            							<img src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/global'); ?>/printer-icon.svg" alt="<?php echo esc_html__('Print invoice', 'mrkv-ua-shipping'); ?>" title="<?php echo esc_html__('Print invoice', 'mrkv-ua-shipping'); ?>">
				            								<?php echo esc_html__('Print invoice', 'mrkv-ua-shipping'); ?>
				            							</a>
												<?php
			            					break;
			            				}
			            			?>
			            			<a class="mrkv_ua_ship_send_remove_ttn">
	        							<img src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/global'); ?>/trash-icon.svg" alt="<?php echo esc_html__('Remove ttn', 'mrkv-ua-shipping'); ?>" title="<?php echo esc_html__('Remove ttn', 'mrkv-ua-shipping'); ?>">
	        							<?php echo esc_html__('Remove ttn', 'mrkv-ua-shipping'); ?>
	    							</a>
			            		</div>
			            	<?php
			            }
			            else{
			            	if(!empty(MRKV_UA_SHIPPING_LIST[$key]['invoice_links']) && $current_shipping != 'mrkv_ua_shipping_nova-poshta_international' && $current_shipping != 'mrkv_ua_shipping_nova-poshta_inter_address')
			            	{
				            	?>
				            	<a>
				            		<div data-method="<?php echo esc_html($current_shipping); ?>" data-ship="<?php echo esc_html($key); ?>" data-order-id="<?php echo esc_html($order->get_id()); ?>" class="mrkv_ua_ship_global_create__invoice">
				            			<img src="<?php echo esc_url(MRKV_UA_SHIPPING_IMG_URL . '/global'); ?>/add-square-icon.svg" alt="<?php echo esc_html($order->get_shipping_method()); ?>" title="<?php echo esc_html($order->get_shipping_method()); ?>">
				            			<span><?php echo esc_html__('Create Invoice', 'mrkv-ua-shipping'); ?></span>
				            			<div class="mrkv_ua_ship_create_invoice__loader"></div>
				            		</div>
				            	</a>
				            	<?php
				            }
			            }

			            if(!empty(MRKV_UA_SHIPPING_LIST[$key]['invoice_links'])  && $current_shipping != 'mrkv_ua_shipping_nova-poshta_international' && $current_shipping != 'mrkv_ua_shipping_nova-poshta_inter_address')
		            	{
		            		?>
		            			<hr class="mrkv-hr-sidebar">
				            	<h3><?php echo esc_html__('Custom Invoice number', 'mrkv-ua-shipping'); ?></h3>
			            		<input type="text" name="custom_invoice_number" minlength="13" value="<?php echo esc_html($mrkv_ua_ship_invoice); ?>">
			            		<div class="mrkv_ua_ship_custom_invoice button">
			            			<?php echo esc_html__('Save Invoice', 'mrkv-ua-shipping'); ?>
						            <div class="mrkv_ua_ship_create_invoice__loader"></div>
			            		</div>
			            		<hr class="mrkv-hr-sidebar">
		            		<?php
		            	}

			            ?>
			            	<h3><?php echo esc_html__('Change address', 'mrkv-ua-shipping'); ?></h3>
			            	<div class="mrkv_ua_ship_edit_data">
			            		<input type="hidden" name="mrkv_order_id" value="<?php echo esc_html($order_id); ?>">
			            		<input type="hidden" name="mrkv_current_shipping" value="<?php echo esc_html($current_shipping); ?>">
			            		<input type="hidden" name="mrkv_current_shipping_key" value="<?php echo esc_html($key); ?>">
					            <?php
						            foreach(MRKV_UA_SHIPPING_LIST[$key]['method'][$current_shipping]['checkout_fields'] as $id => $field_val)
				            		{
				            			if(isset($field_val['replace']))
				            			{
				            				if($field_val['replace'] == '_city')
				            				{
									            $default_value = $order->get_shipping_city();
					    					}
					    					elseif($field_val['replace'] == '_state')
					    					{
					    						$default_value = $order->get_shipping_state();
					    					}
					    					elseif($field_val['replace'] == '_address_1')
					    					{
					    						$default_value = $order->get_shipping_address_1();
					    					}
					    					elseif($field_val['replace'] == '_postcode')
					    					{
					    						$default_value = $order->get_shipping_postcode();
					    					}
					    					elseif($field_val['replace'] == '_address_2')
					    					{
					    						$default_value = $order->get_shipping_address_2();
					    					}
					    					else
					    					{
					    						$default_value = $order->get_meta($current_shipping . $id);

					    						if(!$default_value && isset($field_val['old_slug']))
					    						{
					    							$default_value = $order->get_meta($field_val['old_slug']);
					    						}
					    					}

				            				$field_val['default'] = $default_value;
				            			}

				            			if(isset($field_val['default']) && $field_val['default'] && $field_val['type'] == 'select')
					        			{
					        				$field_val['options'][$default_value] = $default_value;
					        			}

					        			if(isset($field_val['type']) && $field_val['type'] == 'hidden')
					        			{
					        				unset($field_val['label']);
					        			}

										woocommerce_form_field($current_shipping . $id, $field_val);
						            }
						            wp_nonce_field( 'mrkv_ua_ship_nonce_action', 'mrkv_ua_ship_nonce' );
					            ?>
					            <div class="mrkv_ua_shipping_change_field_address button">
					            	<?php echo esc_html__('Save Field', 'mrkv-ua-shipping'); ?>
					            	<div class="mrkv_ua_ship_create_invoice__loader"></div>
					            </div>
			            	</div>
			            <?php
						do_action( 'mrkv_ua_shipping_metabox_end', $key, $order);
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

		public function mrkv_ua_ship_order_editable_billing($order)
		{
			$keys_shipping = array_keys(MRKV_UA_SHIPPING_LIST);
    		$key = '';
    		$current_shipping = '';

	    	foreach($order->get_shipping_methods() as $shipping)
            {
            	foreach($keys_shipping as $key_ship)
				{

					if(str_contains($shipping->get_method_id(), $key_ship))
					{
						$key = $key_ship;
						$current_shipping = $shipping->get_method_id();
					}
				}
            }

            if($key && $current_shipping)
            {
            	foreach(MRKV_UA_SHIPPING_LIST[$key]['method'][$current_shipping]['checkout_fields'] as $key_field => $field_value)
            	{
            		if(isset($field_value['order_edit']) && $field_value['order_edit'])
            		{
            			$billing_data    = $order->get_meta( $current_shipping . $key_field );

					    printf('<div class="address"><p%s><strong>%s:</strong> %s</p></div>
					        <div class="edit_address">', 
					        '',
					        esc_html($field_value['label']),
					        esc_html($billing_data)
					    );

					    woocommerce_wp_text_input( array(
					        'id'            => $current_shipping . $key_field,
					        'label'         => $field_value['label'],
					        'wrapper_class' => 'form-field-wide',
					        'class'         => '',
					        'style'         => 'width:100%',
					        'value'         => $billing_data,
					    ) );

					    echo '</div>';
            		}
            	}
            }
		}

		public function mrkv_ua_ship_save_admin_order_billing($order_id)
		{
			if ( !isset( $_POST['mrkv_ua_ship_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash($_POST['mrkv_ua_ship_nonce'])), 'mrkv_ua_ship_nonce_action' ) ) 
			{
		        return;
		    }
			$order = wc_get_order($order_id);

			if($order)
			{
				$keys_shipping = array_keys(MRKV_UA_SHIPPING_LIST);
	    		$key = '';
	    		$current_shipping = '';

		    	foreach($order->get_shipping_methods() as $shipping)
	            {
	            	foreach($keys_shipping as $key_ship)
					{

						if(str_contains($shipping->get_method_id(), $key_ship))
						{
							$key = $key_ship;
							$current_shipping = $shipping->get_method_id();
						}
					}
	            }

	            if($key && $current_shipping)
	            {
	            	$order = wc_get_order( $order_id );

	            	if($order)
	            	{
	            		foreach(MRKV_UA_SHIPPING_LIST[$key]['method'][$current_shipping]['checkout_fields'] as $key_field => $field_value)
		            	{
		            		if(isset($field_value['order_edit']) && $field_value['order_edit'])
		            		{
							    if ( isset($_POST[ $current_shipping . $key_field ]) ) 
							    {
							        $data_field = sanitize_text_field( wp_unslash($_POST[ $current_shipping . $key_field ]));

							        $order->update_meta_data( $current_shipping . $key_field, wc_clean( $data_field ) );
							        $order->save();
							    }
		            		}
		            	}
	            	}
	            }
			}
		}
	}
}