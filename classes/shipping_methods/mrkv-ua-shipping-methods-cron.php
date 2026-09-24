<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_METHODS_CRON'))
{
	/**
	 * Class for setup shipping methods cron
	 */
	class MRKV_UA_SHIPPING_METHODS_CRON
	{
		/**
		 * Constructor for plugin shipping methods cron
		 * */
		function __construct()
		{
			# Load settings page constants
			add_action( 'wp_loaded', array($this, 'mrkv_ua_shup_cron'), 100);
			add_action('mrknp_ua_shipping_nova_poshta_statuses', array($this, 'mrknp_ua_shipping_nova_poshta_statuses_run'));

			add_action('rest_api_init', array($this, 'mrkv_ua_shipping_rest_api'));
		}

		public function mrkv_ua_shipping_rest_api()
		{
			register_rest_route('mrkv_ua_shipping/v1', '/check_ttn', [
		        'methods'  => 'GET',
		        'callback' => array($this, 'mrkv_ua_shipping_check_ttn'),
		        'permission_callback' => '__return_true'
		    ]);
		}

		/**
		 * Recurrence of an already scheduled event.
		 *
		 * WP stores it as $crons[$timestamp][$hook][$key]['schedule'], where $key is
		 * md5(serialize($args)). Reading [$hook]['schedule'] skips that level and always
		 * yields null, which made the caller clear + reschedule on EVERY request: two writes
		 * to the autoloaded `cron` option per page load, and a next-run timestamp reset to
		 * time() each time, so the event was permanently due instead of running on schedule.
		 *
		 * @param string $hook      Hook name.
		 * @param int    $timestamp Timestamp the event is scheduled at.
		 * @return string Recurrence name, or '' when the event is not a recurring one.
		 * */
		private function mrkv_ua_shipping_scheduled_recurrence($hook, $timestamp)
		{
			$crons = _get_cron_array();

			if ( ! isset($crons[$timestamp][$hook]) || ! is_array($crons[$timestamp][$hook]) ) {
				return '';
			}

			# No args are passed when scheduling, so there is a single entry - but read it
			# generically instead of hardcoding md5(serialize(array())).
			foreach ( $crons[$timestamp][$hook] as $event ) {
				if ( isset($event['schedule']) && $event['schedule'] ) {
					return $event['schedule'];
				}
			}

			return '';
		}

		/**
		 * Apply a carrier status to an order, but only when it actually differs.
		 *
		 * update_status() calls save() unconditionally, and WC bumps date_modified and fires
		 * woocommerce_update_order on every save - which queues a wc-admin lookup rebuild and
		 * every registered webhook. Re-applying the status an order already has therefore cost
		 * a full write per order per run, on every order with a TTN from the last 30 days.
		 *
		 * @param int    $order_id   Order ID.
		 * @param string $new_status Target status, with or without the wc- prefix.
		 * @return bool True when the status was changed.
		 * */
		private function mrkv_ua_shipping_apply_status($order_id, $new_status)
		{
			$new_status = str_replace('wc-', '', (string) $new_status);

			if ('' === $new_status) {
				return false;
			}

			$order = wc_get_order($order_id);

			if (!$order) {
				return false;
			}

			# Nothing to do - and, importantly, nothing to save
			if ($order->get_status() === $new_status) {
				return false;
			}

			# update_status() saves the order itself, a second save() would write it twice
			$order->update_status($new_status);

			return true;
		}

		/**
		 * Run a status job at most once at a time.
		 *
		 * Both entry points can be triggered far more often than the job takes to finish:
		 * WP-Cron re-spawns after its own 60s lock expires, and the REST route above is open
		 * to anyone. Without this, runs stack up and each one walks the same orders.
		 *
		 * @param string   $lock_key Unique key per job.
		 * @param callable $job      The job to run.
		 * @return bool False when another run holds the lock.
		 * */
		private function mrkv_ua_shipping_run_locked($lock_key, $job)
		{
			$lock_key = 'mrkv_ua_shipping_lock_' . $lock_key;

			if ( get_transient($lock_key) ) {
				return false;
			}

			# TTL is only a crash guard - the lock is released right after the job
			set_transient($lock_key, 1, 10 * MINUTE_IN_SECONDS);

			try {
				call_user_func($job);
			} finally {
				delete_transient($lock_key);
			}

			return true;
		}

		public function mrkv_ua_shipping_check_ttn($request)
		{
			$this->mrkv_ua_shipping_run_locked('np_check_ttn', function () {
				$this->mrkv_ua_shipping_check_ttn_run();
			});
		}

		private function mrkv_ua_shipping_check_ttn_run()
		{
			$log_file_date = dirname(__FILE__) . '/cron-log/cron-mrkv-ttn-status.log';
			$log_file_offset = dirname(__FILE__) . '/cron-log/cron-mrkv-ttn-status-offset.log';

			if (!file_exists($log_file_date)) {
		        file_put_contents($log_file_date, '');
		    }
		    if (!file_exists($log_file_offset)) {
		        file_put_contents($log_file_offset, '');
		    }

		    $last_check_raw_date = trim(file_get_contents($log_file_date));
		    $last_check_raw_offset = trim(file_get_contents($log_file_offset));

		    if(!empty($last_check_raw_offset))
		    {
		    	$this->mrkv_ua_shipping_run_status_ttn_check($last_check_raw_offset, $log_file_date, $log_file_offset);
		    }
		    else
		    {
		    	if (empty($last_check_raw_date)) 
			    {
			    	$this->mrkv_ua_shipping_run_status_ttn_check(0, $log_file_date, $log_file_offset);
			        return;
			    }

			    $settings = get_option('nova-poshta_m_ua_settings');
			    $period_between = '1440';

			    if(isset($settings['automation']['cron']['frequency']) && $settings['automation']['cron']['frequency'])
			    {
			    	$period_between = $settings['automation']['cron']['frequency'];
			    }

			    $last_check = new DateTime($last_check_raw_date);
			    $now = new DateTime(current_time('mysql'));

			    $diff_in_minutes = ($now->getTimestamp() - $last_check->getTimestamp()) / 60;

			    if ($diff_in_minutes >= $period_between) 
			    {
			     	$this->mrkv_ua_shipping_run_status_ttn_check(0, $log_file_date, $log_file_offset);
			     	return;
			    }
		    }

		    return;
		}

		private function mrkv_ua_shipping_run_status_ttn_check($offset, $log_file_date, $log_file_offset)
		{
			$settings = get_option('nova-poshta_m_ua_settings');
			
			global $wpdb;

			$post_per_page = 300;
			$max_ttn = 10000;
			$order_status = 'wc-shipped';
			$days_limit = 30;

			if(isset($settings['automation']['cron']['max_count']) && $settings['automation']['cron']['max_count'])
		    {
		    	$max_ttn = $settings['automation']['cron']['max_count'];
		    }

		    if(isset($settings['automation']['cron']['count_step']) && $settings['automation']['cron']['count_step'])
		    {
		    	$post_per_page = $settings['automation']['cron']['count_step'];
		    }

		    if(isset($settings['automation']['cron']['status']) && $settings['automation']['cron']['status'])
		    {
		    	$order_status = $settings['automation']['cron']['status'];
		    }

		    if(isset($settings['automation']['cron']['days']) && $settings['automation']['cron']['days'])
		    {
		    	$days_limit = $settings['automation']['cron']['days'];
		    }

		    $message = "Offset: " . wp_json_encode( $offset, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) . "\r\n";
			$message .= "Max_ttn: " . wp_json_encode( $max_ttn, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) . "\r\n";
			$message .= "Date: " . wp_json_encode( gmdate('Y-m-d H:i:s'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) . "\r\n";

			if($offset >= $max_ttn)
			{
				file_put_contents($log_file_offset, '');
				file_put_contents($log_file_date, current_time('mysql'));
				return;
			}

			if(!get_option('woocommerce_custom_orders_table_enabled') || get_option('woocommerce_custom_orders_table_enabled') == 'no')
			{
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, PluginCheck.Security.DirectDB.UnescapedDBParameter
				$orders = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT MAX(ordermeta.meta_value) as invoice, ordermeta.post_id as order_id
						FROM {$wpdb->prefix}postmeta as ordermeta
						INNER JOIN (
							SELECT orderitemmeta.meta_value as rate_id, orderitem.order_id as order_id
							FROM {$wpdb->prefix}woocommerce_order_itemmeta as orderitemmeta
							INNER JOIN {$wpdb->prefix}woocommerce_order_items as orderitem 
								ON orderitemmeta.order_item_id = orderitem.order_item_id
							WHERE orderitem.order_item_type = 'shipping'
							AND orderitemmeta.meta_key = 'method_id'
							AND (
								orderitemmeta.meta_value LIKE %s
								OR orderitemmeta.meta_value LIKE %s
								OR orderitemmeta.meta_value LIKE %s
							)
						) as meta ON meta.order_id = ordermeta.post_id
						INNER JOIN {$wpdb->prefix}posts as posts ON posts.ID = ordermeta.post_id
						WHERE (
							ordermeta.meta_key = 'mrkv_ua_ship_invoice_number' 
							OR ordermeta.meta_key = 'novaposhta_ttn'
						)
						AND posts.post_status = %s
						GROUP BY ordermeta.post_id
						ORDER BY ordermeta.post_id ASC
						LIMIT %d OFFSET 0",
						'mrkv_ua_shipping_nova-poshta%', 
						'nova_poshta_shipping_method%',  
						'npttn_address_shipping_method%',
						$order_status,                   
						$post_per_page                   
					),
					OBJECT_K
				);
			}
			else
			{
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, PluginCheck.Security.DirectDB.UnescapedDBParameter
				$orders = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT MAX(ordermeta.meta_value) as invoice, ordermeta.order_id
						FROM {$wpdb->prefix}wc_orders_meta as ordermeta
						INNER JOIN (
							SELECT orderitemmeta.meta_value as rate_id, orderitem.order_id as order_id
							FROM {$wpdb->prefix}woocommerce_order_itemmeta as orderitemmeta
							INNER JOIN {$wpdb->prefix}woocommerce_order_items as orderitem 
								ON orderitemmeta.order_item_id = orderitem.order_item_id
							WHERE orderitem.order_item_type = 'shipping'
							AND orderitemmeta.meta_key = 'method_id'
							AND (
								orderitemmeta.meta_value LIKE %s
								OR orderitemmeta.meta_value LIKE %s
								OR orderitemmeta.meta_value LIKE %s
							)
						) as meta ON meta.order_id = ordermeta.order_id
						INNER JOIN {$wpdb->prefix}wc_orders as orders ON orders.id = ordermeta.order_id
						WHERE (
							ordermeta.meta_key = 'mrkv_ua_ship_invoice_number'
							OR ordermeta.meta_key = 'novaposhta_ttn'
						)
						AND orders.status = %s
						AND orders.date_created_gmt >= DATE_SUB(UTC_TIMESTAMP(), INTERVAL %d DAY)
						GROUP BY ordermeta.order_id
						ORDER BY MAX(orders.date_created_gmt) ASC
						LIMIT %d OFFSET 0",
						'mrkv_ua_shipping_nova-poshta%', 
						'nova_poshta_shipping_method%',  
						'npttn_address_shipping_method%',
						$order_status,                   
						$days_limit,                     
						$post_per_page                   
					),
					OBJECT_K
				);
			}

			$offset += $post_per_page;
			file_put_contents($log_file_offset, '');
			file_put_contents($log_file_offset, $offset);

			$message .= "Orders: " . wp_json_encode( $orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) . "\r\n";

			if(is_array($orders) && empty($orders))
			{
				do_action('mrkv_ua_shipping_log_cron', $message);
				file_put_contents($log_file_offset, '');
				file_put_contents($log_file_date, current_time('mysql'));
				return;
			}				
		
			$api_class = MRKV_UA_SHIPPING_LIST['nova-poshta']['api_class'];
			$nova_api = new $api_class($settings);

			$chunks = array_chunk($orders, 100);
			$invoices_result = array();

			foreach ($chunks as $index => $chunk) 
			{
				$invoices_result_intermediate = $nova_api->get_status_documents($chunk);
				$invoices_result = array_merge($invoices_result, $invoices_result_intermediate);
			}
	
			foreach($invoices_result as $data_invoice)
			{
				$orders[$data_invoice['Number']]->statuscode = $data_invoice['StatusCode'];
			}

			$orders = apply_filters( 'mrkv_invoice_statuses_args', $orders );

			$updated_status_received = (isset($settings['automation']['status']['received']) && $settings['automation']['status']['received']) ? $settings['automation']['status']['received'] : false;
			$updated_status_moneysms = (isset($settings['automation']['status']['moneysms']) && $settings['automation']['status']['moneysms']) ? $settings['automation']['status']['moneysms'] : false;
			$updated_status_money = (isset($settings['automation']['status']['money']) && $settings['automation']['status']['money']) ? $settings['automation']['status']['money'] : false;
			$updated_status_refused = (isset($settings['automation']['status']['refused']) && $settings['automation']['status']['refused']) ? $settings['automation']['status']['refused'] : false;
			$updated_status_canceled = (isset($settings['automation']['status']['canceled']) && $settings['automation']['status']['canceled']) ? $settings['automation']['status']['canceled'] : false;
			$updated_status_shipping = (isset($settings['automation']['status']['shipping']) && $settings['automation']['status']['shipping']) ? $settings['automation']['status']['shipping'] : false;

			$message .= "Orders with status: " . wp_json_encode( $orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) . "\r\n";
			do_action('mrkv_ua_shipping_log_cron', $message);

			if(is_array($orders) && !empty($orders))
			{
				foreach($orders as $order)
				{
					switch($order->statuscode)
				        {
				        	case '9':
				        		if($updated_status_received)
				        		{
					        			$this->mrkv_ua_shipping_apply_status( $order->order_id, $updated_status_received );
				        		}
				        	break;
				        	case '10':
				        		if($updated_status_received)
				        		{
					        			$this->mrkv_ua_shipping_apply_status( $order->order_id, $updated_status_moneysms );
				        		}
				        	break;
				        	case '11':
				        		if($updated_status_money)
				        		{
					        			$this->mrkv_ua_shipping_apply_status( $order->order_id, $updated_status_money );
				        		}
				        	break;
				        	case '111':
				        		if($updated_status_canceled)
				        		{
					        			$this->mrkv_ua_shipping_apply_status( $order->order_id, $updated_status_canceled );
				        		}
				        	break;
				        	case '103':
				        	case '102':
				        	case '105':
				        		if($updated_status_refused)
				        		{
					        			$this->mrkv_ua_shipping_apply_status( $order->order_id, $updated_status_refused );
				        		}
				        	break;
				        	case '5':
				        		if($updated_status_shipping)
				        		{
					        			$this->mrkv_ua_shipping_apply_status( $order->order_id, $updated_status_shipping );
				        		}
				        	break;
				        }
				}
			}
		}

		public function mrkv_ua_shup_cron()
		{
		    $m_ua_active_plugins = get_option('m_ua_active_plugins');

		    if ( isset($m_ua_active_plugins['nova-poshta']['enabled']) 
		         && $m_ua_active_plugins['nova-poshta']['enabled'] == 'on' ) 
		    {
		        $settings = get_option('nova-poshta_m_ua_settings');

		        $cron_frequency = isset($settings['automation']['cron']['wp_frequency']) 
		            ? $settings['automation']['cron']['wp_frequency'] 
		            : 'hourly';

		        if ( isset($settings['automation']['status']['enabled']) 
		             && $settings['automation']['status']['enabled'] == 'on'
		             && ( !isset($settings['automation']['cron']['type']) 
		                  || $settings['automation']['cron']['type'] != 'server_cron' ) ) 
		        {

		            $hook = 'mrknp_ua_shipping_nova_poshta_statuses';
					$timestamp = wp_next_scheduled($hook);

					if ( $timestamp ) {
					    if ( $this->mrkv_ua_shipping_scheduled_recurrence($hook, $timestamp) !== $cron_frequency ) {
					        wp_clear_scheduled_hook($hook);
					        wp_schedule_event(time(), $cron_frequency, $hook);
					    }

					} else {
					    wp_schedule_event(time(), $cron_frequency, $hook);
					}
		        }
		        else 
		        {
		            if ( wp_next_scheduled('mrknp_ua_shipping_nova_poshta_statuses') ) {
		                wp_clear_scheduled_hook('mrknp_ua_shipping_nova_poshta_statuses');
		            }
		        }
		    }
		}

		public function mrknp_ua_shipping_nova_poshta_statuses_run()
		{
			$this->mrkv_ua_shipping_run_locked('np_statuses', function () {
				$this->mrkv_ua_shipping_nova_poshta_statuses_do_run();
			});
		}

		private function mrkv_ua_shipping_nova_poshta_statuses_do_run()
		{
			$settings = get_option('nova-poshta_m_ua_settings');
			$change_status = (isset($settings['automation']['status']['enabled']) && $settings['automation']['status']['enabled'] == 'on'  && (!isset($settings['automation']['cron']['type']) || $settings['automation']['cron']['type'] != 'server_cron')) ? true : false;

			$key_ship = 'nova-poshta';

			if($change_status)
			{
				global $wpdb;

				$post_per_page = 100;

				if(!get_option('woocommerce_custom_orders_table_enabled') || get_option('woocommerce_custom_orders_table_enabled') == 'no')
				{
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, PluginCheck.Security.DirectDB.UnescapedDBParameter
					$orders = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT MAX(ordermeta.meta_value) as invoice, ordermeta.post_id as order_id
							FROM {$wpdb->prefix}postmeta as ordermeta
							INNER JOIN (
								SELECT orderitemmeta.meta_value as rate_id, orderitem.order_id as order_id
								FROM {$wpdb->prefix}woocommerce_order_itemmeta as orderitemmeta
								INNER JOIN {$wpdb->prefix}woocommerce_order_items as orderitem 
									ON orderitemmeta.order_item_id = orderitem.order_item_id
								WHERE orderitem.order_item_type = 'shipping'
								AND orderitemmeta.meta_key = 'method_id'
								AND (
									orderitemmeta.meta_value LIKE %s
									OR orderitemmeta.meta_value LIKE %s
									OR orderitemmeta.meta_value LIKE %s
								)
							) as meta ON meta.order_id = ordermeta.post_id
							INNER JOIN {$wpdb->prefix}posts as posts ON posts.ID = ordermeta.post_id
							WHERE (
								ordermeta.meta_key = 'mrkv_ua_ship_invoice_number' 
								OR ordermeta.meta_key = 'novaposhta_ttn'
							)
							AND posts.post_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
							GROUP BY ordermeta.post_id
							ORDER BY ordermeta.post_id DESC
							LIMIT %d",
							'mrkv_ua_shipping_' . $key_ship . '%',
							'nova_poshta_shipping_method%',       
							'npttn_address_shipping_method%',     
							$post_per_page                        
						),
						OBJECT_K
					);
				}
				else
				{
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, PluginCheck.Security.DirectDB.UnescapedDBParameter
					$orders = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT MAX(ordermeta.meta_value) as invoice, ordermeta.order_id
							FROM {$wpdb->prefix}wc_orders_meta as ordermeta
							INNER JOIN (
								SELECT orderitemmeta.meta_value as rate_id, orderitem.order_id as order_id
								FROM {$wpdb->prefix}woocommerce_order_itemmeta as orderitemmeta
								INNER JOIN {$wpdb->prefix}woocommerce_order_items as orderitem 
									ON orderitemmeta.order_item_id = orderitem.order_item_id
								WHERE orderitem.order_item_type = 'shipping'
								AND orderitemmeta.meta_key = 'method_id'
								AND (
									orderitemmeta.meta_value LIKE %s
									OR orderitemmeta.meta_value LIKE %s
									OR orderitemmeta.meta_value LIKE %s
								)
							) as meta ON meta.order_id = ordermeta.order_id
							INNER JOIN {$wpdb->prefix}wc_orders as orders ON orders.id = ordermeta.order_id
							WHERE (
								ordermeta.meta_key = 'mrkv_ua_ship_invoice_number'
								OR ordermeta.meta_key = 'novaposhta_ttn'
							)
							AND orders.date_created_gmt >= DATE_SUB(NOW(), INTERVAL 30 DAY)
							GROUP BY ordermeta.order_id
							ORDER BY ordermeta.order_id DESC
							LIMIT %d",
							'mrkv_ua_shipping_' . $key_ship . '%',
							'nova_poshta_shipping_method%',       
							'npttn_address_shipping_method%',     
							$post_per_page                        
						),
						OBJECT_K
					);
				}

				if(empty($orders))
				{
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, PluginCheck.Security.DirectDB.UnescapedDBParameter
					$orders = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT MAX(ordermeta.meta_value) as invoice, ordermeta.post_id as order_id
							FROM {$wpdb->prefix}postmeta as ordermeta
							INNER JOIN (
								SELECT orderitemmeta.meta_value as rate_id, orderitem.order_id as order_id
								FROM {$wpdb->prefix}woocommerce_order_itemmeta as orderitemmeta
								INNER JOIN {$wpdb->prefix}woocommerce_order_items as orderitem 
									ON orderitemmeta.order_item_id = orderitem.order_item_id
								WHERE orderitem.order_item_type = 'shipping'
								AND orderitemmeta.meta_key = 'method_id'
								AND (
									orderitemmeta.meta_value LIKE %s
									OR orderitemmeta.meta_value LIKE %s
									OR orderitemmeta.meta_value LIKE %s
								)
							) as meta ON meta.order_id = ordermeta.post_id
							INNER JOIN {$wpdb->prefix}posts as posts ON posts.ID = ordermeta.post_id
							WHERE (
								ordermeta.meta_key = 'mrkv_ua_ship_invoice_number' 
								OR ordermeta.meta_key = 'novaposhta_ttn'
							)
							AND posts.post_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
							GROUP BY ordermeta.post_id
							ORDER BY ordermeta.post_id DESC
							LIMIT %d",
							'mrkv_ua_shipping_' . $key_ship . '%',
							'nova_poshta_shipping_method%',       
							'npttn_address_shipping_method%',     
							$post_per_page                        
						),
						OBJECT_K
					);
				}

				$api_class = MRKV_UA_SHIPPING_LIST['nova-poshta']['api_class'];
				$nova_api = new $api_class($settings);

				$invoices_result = $nova_api->get_status_documents($orders);
		
				foreach($invoices_result as $data_invoice)
				{
					$orders[$data_invoice['Number']]->statuscode = $data_invoice['StatusCode'];
				}

				$orders = apply_filters( 'mrkv_invoice_statuses_args', $orders );

				$updated_status_received = (isset($settings['automation']['status']['received']) && $settings['automation']['status']['received']) ? $settings['automation']['status']['received'] : false;
				$updated_status_moneysms = (isset($settings['automation']['status']['moneysms']) && $settings['automation']['status']['moneysms']) ? $settings['automation']['status']['moneysms'] : false;
				$updated_status_money = (isset($settings['automation']['status']['money']) && $settings['automation']['status']['money']) ? $settings['automation']['status']['money'] : false;
				$updated_status_refused = (isset($settings['automation']['status']['refused']) && $settings['automation']['status']['refused']) ? $settings['automation']['status']['refused'] : false;
				$updated_status_canceled = (isset($settings['automation']['status']['canceled']) && $settings['automation']['status']['canceled']) ? $settings['automation']['status']['canceled'] : false;
				$updated_status_shipping = (isset($settings['automation']['status']['shipping']) && $settings['automation']['status']['shipping']) ? $settings['automation']['status']['shipping'] : false;

				if(is_array($orders) && !empty($orders))
				{
					foreach($orders as $order)
					{
						switch($order->statuscode)
					        {
					        	case '9':
					        		if($updated_status_received)
					        		{
					        			$this->mrkv_ua_shipping_apply_status( $order->order_id, $updated_status_received );
					        		}
					        	break;
					        	case '10':
					        		if($updated_status_received)
					        		{
					        			$this->mrkv_ua_shipping_apply_status( $order->order_id, $updated_status_moneysms );
					        		}
					        	break;
					        	case '11':
					        		if($updated_status_money)
					        		{
					        			$this->mrkv_ua_shipping_apply_status( $order->order_id, $updated_status_money );
					        		}
					        	break;
					        	case '111':
					        		if($updated_status_canceled)
					        		{
					        			$this->mrkv_ua_shipping_apply_status( $order->order_id, $updated_status_canceled );
					        		}
					        	break;
					        	case '103':
					        	case '102':
				        		case '105':
					        		if($updated_status_refused)
					        		{
					        			$this->mrkv_ua_shipping_apply_status( $order->order_id, $updated_status_refused );
					        		}
					        	break;
					        	case '5':
					        		if($updated_status_shipping)
					        		{
					        			$this->mrkv_ua_shipping_apply_status( $order->order_id, $updated_status_shipping );
					        		}
					        	break;
					        }
					}
				}
			}
		}
	}
}