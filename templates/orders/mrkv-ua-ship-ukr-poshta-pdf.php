<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! isset( $_POST['mrkv_ua_shipping_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash($_POST['mrkv_ua_shipping_nonce'])), 'mrkv_ua_print_pdf_action' ) ) {
    wp_die( esc_html__( 'Security check failed. Please refresh the page and try again.', 'mrkv-ua-shipping' ) );
}

header("Content-type:application/pdf");
header("Content-disposition: attachment; filename=ttn.pdf");
header("Content-disposition: inline; filename=ttn.pdf");

if(isset($_POST['bearer']) && isset($_POST['cp_token']) && isset($_POST['invoice_number']) && isset($_POST['type'])) 
{
    $mrkv_ua_shipping_token = sanitize_text_field( wp_unslash($_POST['bearer']));
    $mrkv_ua_shipping_cptoken = sanitize_text_field( wp_unslash($_POST['cp_token']));
    $mrkv_ua_shipping_ttn = sanitize_text_field( wp_unslash($_POST['invoice_number']));
    $mrkv_ua_shipping_type = sanitize_text_field( wp_unslash($_POST['type']));
    $mrkv_ua_shipping_size='';
  
    if($mrkv_ua_shipping_type=='100*100A4')
    {
      $mrkv_ua_shipping_size = '&size=SIZE_A4';
    }
    elseif($mrkv_ua_shipping_type =='100*100A5')
    {
      $mrkv_ua_shipping_size = '&size=SIZE_A5';
    }
  
    $mrkv_ua_shipping_url = 'https://www.ukrposhta.ua/ecom/0.0.1/shipments/' . $mrkv_ua_shipping_ttn . '/sticker?token=' . $mrkv_ua_shipping_cptoken . $mrkv_ua_shipping_size;
  
    $mrkv_ua_shipping_formurl = 'https://www.ukrposhta.ua/forms/ecom/0.0.1/';

    $mrkv_ua_shipping_args = array(
      'headers' => array(
          'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $mrkv_ua_shipping_token,
        ),
        'timeout' => 30, 
    );

    $mrkv_ua_shipping_response = wp_remote_get( esc_url_raw( $mrkv_ua_shipping_url ), $mrkv_ua_shipping_args );

    if ( is_wp_error( $mrkv_ua_shipping_response ) ) {
        $mrkv_ua_shipping_html = ''; 
    } else {
        $mrkv_ua_shipping_html = wp_remote_retrieve_body( $mrkv_ua_shipping_response );
    }

    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo $mrkv_ua_shipping_html;
}