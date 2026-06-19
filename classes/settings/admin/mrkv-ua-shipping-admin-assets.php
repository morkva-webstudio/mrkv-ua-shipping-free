<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_ADMIN_ASSETS'))
{
	/**
	 * Class for setup plugin admin assets
	 */
	class MRKV_UA_SHIPPING_ADMIN_ASSETS
	{
		/**
		 * Constructor for plugin admin assets
		 * */
		function __construct()
		{
			# Add plugin scripts and styles
			add_action('admin_enqueue_scripts', array($this, 'mrkv_ua_shipping_styles_and_scripts'));
		}

		/**
		 * Register plugin admin assets
		 * 
		 * */
	    public function mrkv_ua_shipping_styles_and_scripts($hook)
	    {
	    	$screen = get_current_screen();

			if ( ! $screen ) {
				return;
			}

			$allowed_screens = array(
				'shop_order', 
				'edit-shop_order', 
				'woocommerce_page_wc-orders'
			);

			if ( in_array($screen->id, $allowed_screens, true) ) {
	    		wp_enqueue_style('global-mrkv-ua-shipping', MRKV_UA_SHIPPING_ASSETS_URL . '/css/global/global-mrkv-ua-shipping.css', array(), MRKV_UA_SHIPPING_PLUGIN_VERSION);
	    		wp_register_script('admin-mrkv-ua-select2-js', MRKV_UA_SHIPPING_ASSETS_URL.'/js/global/select2.min.js', array('jquery'), MRKV_UA_SHIPPING_PLUGIN_VERSION, true);
            	wp_enqueue_script('admin-mrkv-ua-select2-js', MRKV_UA_SHIPPING_ASSETS_URL.'/js/global/select2.min.js', array('jquery'), MRKV_UA_SHIPPING_PLUGIN_VERSION, true);
	    		wp_enqueue_script('global-mrkv-ua-shipping', MRKV_UA_SHIPPING_ASSETS_URL . '/js/global/global-mrkv-ua-shipping.js', array('jquery', 'jquery-ui-autocomplete', 'admin-mrkv-ua-select2-js'), MRKV_UA_SHIPPING_PLUGIN_VERSION, true);

    			wp_localize_script('global-mrkv-ua-shipping', 'mrkv_ua_ship_helper', [
	            	'ajax_url' => admin_url( "admin-ajax.php" ),
	            	'nonce'    => wp_create_nonce('mrkv_ua_ship_nonce'),
	            	'error_latin_text'    => __('contains Latin characters', 'mrkv-ua-shipping')
	        	]);

				$order_id = filter_input(INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT);
        
				if (!$order_id) {
					$order_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
				}

	        	if($order_id)
	        	{
		            $order = wc_get_order($order_id);

		            $keys_shipping = array_keys(MRKV_UA_SHIPPING_LIST);
		    		$key = '';

			    	foreach($order->get_shipping_methods() as $shipping)
		            {
		            	foreach($keys_shipping as $key_ship)
						{

							if(str_contains($shipping->get_method_id(), $key_ship))
							{
								$key = $key_ship;
							}
							if(in_array($shipping->get_method_id(), MRKV_UA_SHIPPING_LIST[$key_ship]['old_slugs']))
							{
								$key = $key_ship;
								$current_shipping = array_search($shipping->get_method_id(), MRKV_UA_SHIPPING_LIST[$key_ship]['old_slugs']);
							}
						}
		            }

		            if($key)
		            {
		            	$mrkv_ua_shipping_args = array(
				        	'ajax_url' => admin_url( 'admin-ajax.php' ), 
				        	'nonce'    => wp_create_nonce('mrkv_ua_ship_nonce'),
				        	'select2_texts' => array(
						        'errorLoading'   => __('Error loading results.', 'mrkv-ua-shipping'),
								// translators: %d: number of characters
						        'inputTooLong'   => __('Please delete %d character(s).', 'mrkv-ua-shipping'),
								// translators: %d: number of characters
						        'inputTooShort'  => __('Please enter %d more character(s).', 'mrkv-ua-shipping'),
						        'loadingMore'    => __('Loading more results...', 'mrkv-ua-shipping'),
								// translators: %d: number of items
						        'maximumSelected'=> __('You can only select %d item(s).', 'mrkv-ua-shipping'),
						        'noResults'      => __('No results found.', 'mrkv-ua-shipping'),
						        'searching'      => __('Searching...', 'mrkv-ua-shipping'),
						        'removeAllItems' => __('Remove all items', 'mrkv-ua-shipping'),
						    )
				        );

		            	# Include settings checkout by shipping
						include MRKV_UA_SHIPPING_PLUGIN_PATH_SHIP .'/' . $key . '/checkout/mrkv-ua-shipping-checkout.php';

		            	wp_enqueue_script('global-mrkv-ua-shipping' . $key, MRKV_UA_SHIPPING_ASSETS_URL . '/js/global/global-mrkv-ua-shipping-' . $key . '.js', array('jquery', 'jquery-ui-autocomplete'), MRKV_UA_SHIPPING_PLUGIN_VERSION, true);

		    			wp_localize_script('global-mrkv-ua-shipping' . $key, 'mrkv_ua_ship_helper', $mrkv_ua_shipping_args);
		            }
	        	}
	    	}
	    	
	    	$all_hooks = array('toplevel_page_mrkv_ua_shipping_settings', 'mrkv-ua-shipping_page_mrkv_ua_shipping_about_us', 'morkva-ua-shipping_page_mrkv_ua_shipping_about_us');
	    	$all_hooks_shipping = array();
	    	$method_page = false;
	    	$slug_shipping = '';

	    	foreach(MRKV_UA_SHIPPING_LIST as $slug => $shipping)
			{
				$all_hooks[] = 'mrkv-ua-shipping_page_mrkv_ua_shipping_' . $slug;
				$all_hooks_shipping[] = 'mrkv-ua-shipping_page_mrkv_ua_shipping_' . $slug;

				$all_hooks[] = 'morkva-ua-shipping_page_mrkv_ua_shipping_' . $slug;
				$all_hooks_shipping[] = 'morkva-ua-shipping_page_mrkv_ua_shipping_' . $slug;

				if(isset($shipping['pages']))
				{
					foreach($shipping['pages'] as $page_slug => $page_name)
					{
						$all_hooks[] = 'admin_page_mrkv_ua_shipping_' . $slug . '_' . $page_slug;
					}
				}

				if($hook == 'mrkv-ua-shipping_page_mrkv_ua_shipping_' . $slug || $hook == 'morkva-ua-shipping_page_mrkv_ua_shipping_' . $slug)
				{
					$method_page = true;
					$slug_shipping = $slug;
				}
			}

	    	# Check page
	    	if (!in_array($hook, $all_hooks)) {
	            return;
	        }

	        # Custom style and script
	        wp_enqueue_style('admin-mrkv-ua-shipping', MRKV_UA_SHIPPING_ASSETS_URL . '/css/admin/admin-mrkv-ua-shipping.css', array(), MRKV_UA_SHIPPING_PLUGIN_VERSION);
	        wp_enqueue_script('admin-mrkv-ua-shipping', MRKV_UA_SHIPPING_ASSETS_URL . '/js/admin/admin-mrkv-ua-shipping.js', array('jquery'), MRKV_UA_SHIPPING_PLUGIN_VERSION, true);

	        wp_localize_script('admin-mrkv-ua-shipping', 'mrkv_ua_ship_helper', [
	            	'ajax_url' => admin_url( "admin-ajax.php" ),
	            	'nonce'    => wp_create_nonce('mrkv_ua_ship_nonce')
	        	]);

	        # Check page
	    	if($method_page && $slug_shipping)
	    	{
	    		wp_enqueue_style('admin-mrkv-ua-select2', MRKV_UA_SHIPPING_ASSETS_URL.'/css/global/select2.min.css', array(), MRKV_UA_SHIPPING_PLUGIN_VERSION);
            	wp_register_script('admin-mrkv-ua-select2-js', MRKV_UA_SHIPPING_ASSETS_URL.'/js/global/select2.min.js', array('jquery'), MRKV_UA_SHIPPING_PLUGIN_VERSION, true);
            	 wp_enqueue_script('admin-mrkv-ua-select2-js', MRKV_UA_SHIPPING_ASSETS_URL.'/js/global/select2.min.js', array('jquery'), MRKV_UA_SHIPPING_PLUGIN_VERSION, true);

	    		wp_enqueue_script('admin-mrkv-ua-shipping-' . $slug_shipping, MRKV_UA_SHIPPING_ASSETS_URL . '/js/admin/admin-mrkv-ua-shipping-' . $slug_shipping . '.js', array('jquery', 'jquery-ui-autocomplete', 'admin-mrkv-ua-select2-js'), MRKV_UA_SHIPPING_PLUGIN_VERSION, true);

    			wp_localize_script('admin-mrkv-ua-shipping-' . $slug_shipping, 'mrkv_ua_ship_helper', [
	            	'ajax_url' => admin_url( "admin-ajax.php" ),
	            	'nonce'    => wp_create_nonce('mrkv_ua_ship_nonce')
	        	]);
	    	}
	    }
	}
}