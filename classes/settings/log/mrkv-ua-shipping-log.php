<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_UA_SHIPPING_LOG'))
{
	/**
	 * Class for setup dedug log
	 */
	class MRKV_UA_SHIPPING_LOG
	{
		/**
		 * @var string Slug
		 * */
		private $method_slug;

		/**
		 * @var string Datetime
		 * */
		private $datetime_log;

		/**
		 * @var string Is active log
		 * */
		private $active_log;

		/**
		 * Constructor for dedug log
		 * */
		function __construct($active_log = false)
		{
            $this->active_log = $active_log;
		}

		/**
         * Log error data.
         * * @param mixed $error Data to log.
         */
        public function add_data_error( $error ) {
            if ( $this->active_log ) {
                $this->write_to_log( 'error', $error );
            }
        }

        /**
         * Log action messages.
         * * @param mixed $message Data to log.
         */
        public function add_data_message( $message ) {
            if ( $this->active_log ) {
                $this->write_to_log( 'action', $message );
            }
        }

        /**
         * Log API request/response data.
         * * @param mixed $request Data to log.
         */
        public function add_data_request( $request ) {
            if ( $this->active_log ) {
                $this->write_to_log( 'request', $request );
            }
        }

         /**
         * Hide API keys and tokens in a log line: only the last 4 characters stay (***abcd).
         * Covers JSON bodies ("apiKey":"..."), Bearer headers and token query arguments.
         */
        private function mask_secrets( $data ) {
            $mask = function ( $value ) {
                return '***' . ( strlen( $value ) > 8 ? substr( $value, -4 ) : '' );
            };

            $data = preg_replace_callback(
                '/("(?:apiKey|api_key|token|access_token|Authorization)"\s*:\s*")((?:Bearer\s+)?)([^"]*)(")/i',
                function ( $m ) use ( $mask ) {
                    return $m[1] . $m[2] . $mask( $m[3] ) . $m[4];
                },
                $data
            );

            return preg_replace_callback(
                '/([?&](?:apiKey|api_key|token)=)([^&\s"]+)/i',
                function ( $m ) use ( $mask ) {
                    return $m[1] . $mask( $m[2] );
                },
                $data
            );
        }

        /**
         * Internal helper to format and write the log entry.
         */
        private function write_to_log( $level, $data ) {
            $logger = wc_get_logger();
            $context = array( 'source' => 'mrkv-ua-shipping' );
            $timestamp = gmdate( "Y-m-d H:i:s" );

            if ( ! is_scalar( $data ) ) {
                $data = wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
            }

            $data = $this->mask_secrets( (string) $data );

            switch ( $level ) {
                case 'error':
                    $logger->error( $data, $context );
                    break;
                default:
                    $logger->info( $data, $context );
                    break;
            }
            
            do_action( 'mrkv_ua_shipping_after_log_write', $level, $data, $timestamp );
        }
	}
}