jQuery(window).on('load', function() 
{
	jQuery('#mrkv_ua_shipping_method').select2();
	// Get the modal
	var mrkv_ua_ship_create_invoice = document.getElementById("mrkv_ua_ship_create_invoice");
	let isProcessingOpenForm = false;
	let isProcessingCreateInvoice = false;

	jQuery('.mrkv_ua_ship_global_create__invoice').click(function()
	{
		if (isProcessingOpenForm) return;
    	isProcessingOpenForm = true;

		var loader_btn = jQuery(this).find('.mrkv_ua_ship_create_invoice__loader');
		jQuery(loader_btn).show();
		let current_ship = jQuery(this).attr('data-ship');
		let current_order_id = jQuery(this).attr('data-order-id');
		let current_shipping_method = jQuery(this).attr('data-method');

		jQuery('#mrkv_ua_ship_create_invoice').attr('data-method', current_shipping_method);
		jQuery('body').css('overflow', 'hidden');
		jQuery('#adminmenu').css('overflow-y', 'scroll');
		jQuery('#adminmenu').css('height', '100vh');
		jQuery('.mrkv_ua_shipping_order_id').text(current_order_id);
		jQuery('.mrkv_ua_ship_create_invoice__changed[data-ship="' + current_ship + '"]').addClass('active');

		if(current_shipping_method == 'mrkv_ua_shipping_ukr-poshta_international'){
			jQuery('.mrkv_ua_ship_create_invoice__changed form[data-ship="' + current_ship + '"]').hide();
			jQuery('.mrkv_ua_ship_create_invoice__changed form[data-ship="' + current_ship + '-international"]').show();
		}
		else
		{
			jQuery('.mrkv_ua_ship_create_invoice__changed form[data-ship="' + current_ship + '-international"]').hide();
		}

		let order_link = jQuery(this).closest('tr').find('.column-order_number a.order-view').attr('href');
		jQuery('.open-order-mrkv-ua-ship').attr('href', order_link);

		var description_shipping = jQuery('form[data-ship="' + current_ship + '"] textarea[name="mrkv_ua_ship_invoice_shipment_description"]').val();

		jQuery('input[name="mrkv_ua_ship_invoice_money_transfer"]').prop( "checked", false );

		jQuery.ajax({
            type: 'POST',
            url: mrkv_ua_ship_helper.ajax_url,
            data: {
                action: 'mrkv_ua_ship_get_order_data',
                order_id: current_order_id,
                description: description_shipping,
                nonce: mrkv_ua_ship_helper.nonce,
            },
            success: function (json) {
                var data = JSON.parse(json);
                
            	jQuery.each(data, function( index, value ) {
            		if(index != 'mrkv_ua_ship_key')
            		{
            			if(index == 'mrkv_ua_ship_invoice_address')
	            		{
	            			jQuery('[data-ship="' + data.mrkv_ua_ship_key + '"] form .' + index).text(value);
	            		}
	            		else if(index == 'mrkv_ua_ship_invoice_money_transfer')
	            		{
	            			jQuery('[data-ship="' + data.mrkv_ua_ship_key + '"] form [name=' + index + ']').prop( "checked", true );
	            		}
	            		else if(index == 'mrkv_ua_ship_invoice_payer_delivery' || index == 'mrkv_ua_ship_invoice_shipment_type')
	            		{
	            			if(value)
	            			{
	            				jQuery('[data-ship="' + data.mrkv_ua_ship_key + '"] form [name=' + index + '][value=' + value + ']').attr('checked', 'checked');
	            			}
	            		}
	            		else if(index == 'mrkv_ua_ship_validate_latin')
	            		{
	            			jQuery.each(value, function(i, fieldName) {
						        var input_latin = jQuery('[data-ship="' + data.mrkv_ua_ship_key + '"] form [name=' + fieldName + ']');
						        jQuery(input_latin).closest('label').append('<span class="mrkv-ua-ship-latin-error">' + mrkv_ua_ship_helper.error_latin_text + '</span>');
						        jQuery(input_latin).addClass('mrkv-ua-ship-latin-error-input');
						    });
	            		}
	            		else
	            		{
	            			if(value)
	            			{
	            				jQuery('[data-ship="' + data.mrkv_ua_ship_key + '"] form [name=' + index + ']').val(value);
	            			}
	            		}
            		}
				});
				jQuery('input[name="mrkv_ua_ship_invoice_shipment_volume"]').val(mrkvnpCalcVolumeWeightSettings());
				jQuery('.mrkv_ua_ship_create_invoice__changed form input[name="order_id"]').val(current_order_id);
				let desc_symb_amount = jQuery('[data-ship="' + data.mrkv_ua_ship_key + '"] form textarea[name="mrkv_ua_ship_invoice_shipment_description"]').val();
				let desc_length = desc_symb_amount.length;
				jQuery('[data-ship="' + data.mrkv_ua_ship_key + '"] form .mrkv-ua-ship-cout-symb').text(desc_length);

				if (length > 100) {
					jQuery('[data-ship="' + data.mrkv_ua_ship_key + '"] form .mrkv-ua-shipping-desc-validation').addClass('red');
					let desc_text = jQuery('[data-ship="' + data.mrkv_ua_ship_key + '"] form .mrkv-ua-shipping-desc-validation').attr('data-error');
				    jQuery('[data-ship="' + data.mrkv_ua_ship_key + '"] form .mrkv-ua-ship-message-symb').text(desc_text);
				}
				else
				{
					jQuery('[data-ship="' + data.mrkv_ua_ship_key + '"] form .mrkv-ua-shipping-desc-validation').removeClass('red');
					let desc_text = jQuery('[data-ship="' + data.mrkv_ua_ship_key + '"] form .mrkv-ua-shipping-desc-validation').attr('data-success');
					jQuery('[data-ship="' + data.mrkv_ua_ship_key + '"] form .mrkv-ua-ship-message-symb').text(desc_text);
				}

                jQuery('#mrkv_ua_ship_create_invoice').fadeIn(300);
                jQuery(loader_btn).hide();
                isProcessingOpenForm = false;
            }
        });
	});

let latinRegex = /[A-Za-z]/;

    // Selector for all target fields
    let selector = 'form[data-ship="nova-poshta"] input[name="mrkv_ua_ship_invoice_first_name"],' +
        'form[data-ship="nova-poshta"] input[name="mrkv_ua_ship_invoice_last_name"],' +
        'form[data-ship="nova-poshta"] input[name="mrkv_ua_ship_invoice_patronymic"],' +
        'form[data-ship="ukr-poshta"] input[name="mrkv_ua_ship_invoice_first_name"],' +
        'form[data-ship="ukr-poshta"] input[name="mrkv_ua_ship_invoice_last_name"],' +
        'form[data-ship="ukr-poshta"] input[name="mrkv_ua_ship_invoice_patronymic"]';

	jQuery(document).on('keyup', selector, function() {
        let $input = jQuery(this);
        let val = $input.val();

        // Remove old error before checking
        $input.removeClass('mrkv-ua-ship-latin-error-input');
        $input.closest('.admin_ua_ship_morkva_settings_line').find('label').find('.mrkv-ua-ship-latin-error').remove();

        // If contains Latin letters -> show error
        if (latinRegex.test(val)) {
            $input.addClass('mrkv-ua-ship-latin-error-input');
            $input.closest('.admin_ua_ship_morkva_settings_line').find('label').append(
                '<span class="mrkv-ua-ship-latin-error">' + mrkv_ua_ship_helper.error_latin_text + '</span>'
            );
        }
    });

    jQuery(document).on('keyup', 'textarea[name="mrkv_ua_ship_invoice_shipment_description"]', function () {
	    let text = jQuery(this).val();
	    let length = text.length;

	    let validation_desc = jQuery(this).closest('.admin_ua_ship_morkva_settings_line').find('.mrkv-ua-shipping-desc-validation');
	    jQuery(validation_desc).find('.mrkv-ua-ship-cout-symb').text(length);

	    if (length > 100) {
	        jQuery(validation_desc).addClass('red');
	        let desc_text = jQuery(validation_desc).attr('data-error');
		    jQuery(validation_desc).find('.mrkv-ua-ship-message-symb').text(desc_text);

	    } else {
	        jQuery(validation_desc).removeClass('red');
	        let desc_text = jQuery(validation_desc).attr('data-success');
		    jQuery(validation_desc).find('.mrkv-ua-ship-message-symb').text(desc_text);
	    }
	});

	jQuery('.mrkv_ua_ship_create_invoice__action').click(function()
	{
		if (isProcessingCreateInvoice) return;
    	isProcessingCreateInvoice = true;

		var loading_create = jQuery(this).closest('.mrkv_ua_ship_create_invoice__footer').find('.mrkv_ua_ship_create_invoice__loader');
		jQuery(loading_create).show();
		let current_ship_key = jQuery(this).attr('data-ship');
		let post_fields = '';

		if(jQuery('#mrkv_ua_ship_create_invoice').attr('data-method') == 'mrkv_ua_shipping_ukr-poshta_international')
		{
			post_fields = jQuery('form[data-ship="' + current_ship_key + '-international"]').serialize();
		}
		else
		{
			post_fields = jQuery('form[data-ship="' + current_ship_key + '"]').serialize();
		}
		
		var params = new URLSearchParams(post_fields);
		var description_method = params.get('mrkv_ua_ship_invoice_shipment_description');
		
		if(!description_method)
		{
			jQuery('form[data-ship="' + current_ship_key + '"] textarea[name="mrkv_ua_ship_invoice_shipment_description"]').css('border-color', 'red').focus();
			jQuery(loading_create).hide();
			isProcessingCreateInvoice = false;
		}
		else
		{
			jQuery.ajax({
	            type: 'POST',
	            url: mrkv_ua_ship_helper.ajax_url,
	            data: 'action=mrkv_ua_ship_create_invoice&' + post_fields + '&current_ship_key=' + current_ship_key + '&nonce=' + mrkv_ua_ship_helper.nonce + '',
	            success: function (json) {
	              var data = JSON.parse(json);
	              if(data)
	              {
	              	if(data.status == 'completed')
	              	{
	              		jQuery('.mrkv_ua_ship_modal-content__message_completed').html('<div class="mrkv_ua_ship_ttn_copy" data-ttn="' + data.invoice + '">' + data.message + '</div>');

	              		jQuery('.print-ttn-mrkv-ua-ship').attr('href', '');
	              		jQuery('.print-ttn-mrkv-ua-ship').attr('data-ship', current_ship_key);
	              		jQuery('.print-ttn-mrkv-ua-ship').attr('data-invoice', data.invoice);
	              		jQuery('.print-sticker-mrkv-ua-ship').hide();
	              		if(data.print)
	              		{
	              			jQuery('.print-ttn-mrkv-ua-ship').attr('href', data.print);
	              		}
	              		if(data.form_print)
	              		{
	              			jQuery('.print-ttn-mrkv-ua-ship').attr('data-form', data.form_print);
	              		}
	              		if(data.print_sticker)
	              		{
	              			jQuery('.print-sticker-mrkv-ua-ship').attr('href', data.print_sticker);
	              			jQuery('.print-sticker-mrkv-ua-ship').show();
	              		}

	              		if(current_ship_key == 'ukr-poshta')
	              		{
	              			jQuery('.mrkv_orders_list .print-sticker-mrkv-ua-ship').hide();
	              			jQuery('.mrkv_orders_list .print-ttn-mrkv-ua-ship').hide();
	              		}
	              		else{
	              			jQuery('.mrkv_orders_list .print-ttn-mrkv-ua-ship').show();
	              		}

	              		jQuery('#mrkv_ua_ship_create_invoice_completed').fadeIn(300);
	              		var order_id = jQuery('.mrkv_ua_ship_create_invoice__changed.active input[name="order_id"]').val();
	              		jQuery('#the-list #order-' + order_id + ' .column-mrkv_ua_invoice a').empty();
	              		jQuery('#the-list #order-' + order_id + ' .column-mrkv_ua_invoice a').html('<div class="mrkv_ua_ship_global__invoice" data-ttn="' + data.invoice + '">' + data.invoice + '</div>');
	              	}
	              	else
	              	{
	              		let message = '<ul>';

	              		jQuery.each(data.message, function( index, value ) {
						  message += '<li>' + value + '</li>';
						});

						message += '</ul>';

						console.log(message);

	              		jQuery('.mrkv_ua_ship_modal-content__message_failed').html(message);
	              		jQuery('#mrkv_ua_ship_create_invoice_failed').fadeIn(300);
	              	}
	              }

	              jQuery(loading_create).hide();
	              isProcessingCreateInvoice = false;
	            }
	        });
		}
	});

	jQuery(document).on('click','.mrkv_ua_ship_ttn_copy',function(){
		let current_ttn = jQuery(this).attr('data-ttn');
		if (navigator.clipboard) {
			navigator.clipboard.writeText(current_ttn);
		}
	});

	jQuery(document).on('click','.mrkv_ua_ship_global__invoice',function(){
		let current_ttn = jQuery(this).attr('data-ttn');
		if (navigator.clipboard) {
			navigator.clipboard.writeText(current_ttn);
		}
	});

	jQuery('.mrkv_ua_ship_close, .close-error-mrkv-ua-ship').click(function()
	{
		location.reload();
	});
	jQuery('#mrkv_ua_ship_create_invoice .mrkv_ua_ship_close').click(function()
	{
		jQuery('.mrkv_ua_ship_create_invoice__changed').removeClass('active');
	    jQuery('body').css('overflow', 'auto');
	    jQuery('#adminmenu').css('overflow-y', 'initial');
		jQuery('#adminmenu').css('height', 'auto');
	    jQuery('.mrkv_ua_ship_create_invoice__changed from').show();
	});
	jQuery('.close-all-mrkv-ua-ship').click(function(){
		jQuery('body').css('overflow', 'auto');
		jQuery('#adminmenu').css('overflow-y', 'initial');
		jQuery('#adminmenu').css('height', 'auto');
		jQuery('.mrkv_ua_ship_modal').fadeOut(300);
		jQuery('.mrkv_ua_ship_create_invoice__changed from').show();
	});

	if(jQuery('.mrkv_ua_shipping_change_field_address').length != 0)
 	{
 		jQuery('.mrkv_ua_shipping_change_field_address').click(function()
 		{
 			jQuery(this).find('.mrkv_ua_ship_create_invoice__loader').show();
 			let post_fields = jQuery('.mrkv_ua_ship_edit_data').find("select, textarea, input").serialize();

 			jQuery.ajax({
	            type: 'POST',
	            url: mrkv_ua_ship_helper.ajax_url,
	            data: 'action=mrkv_ua_ship_update_order_data&' + post_fields  + '&nonce=' + mrkv_ua_ship_helper.nonce + '',
	            success: function (json) {
	              location.reload();
	            }
	        });
 		});
 	}

 	if(jQuery('.mrkv_ua_ship_custom_invoice').length != 0)
 	{
 		jQuery('.mrkv_ua_ship_custom_invoice').click(function()
 		{
 			jQuery(this).find('.mrkv_ua_ship_create_invoice__loader').show();
 			let invoice = jQuery('input[name="custom_invoice_number"]').val();
 			let order_id = jQuery('input[name="mrkv_order_id"]').val();

 			if(invoice.length > 12)
 			{
 				jQuery.ajax({
		            type: 'POST',
		            url: mrkv_ua_ship_helper.ajax_url,
		            data: 'action=mrkv_ua_ship_update_invoice_data&order_id=' + order_id + '&invoice=' + invoice  + '&nonce=' + mrkv_ua_ship_helper.nonce + '',
		            success: function (json) {
		              location.reload();
		            }
		        });
 			}
 			else
 			{
 				jQuery('input[name="custom_invoice_number"]').css('border', '1px solid red');
 				setTimeout(function(){ jQuery('input[name="custom_invoice_number"]').css('border', '1px solid #c5c5c5'); }, 3000);
 			}
 		});
 	}

 	if(jQuery('.mrkv_ua_ship_send_remove_ttn').length != 0)
 	{
 		jQuery('.mrkv_ua_ship_send_remove_ttn').click(function()
 		{
 			jQuery(this).find('.mrkv_ua_ship_create_invoice__loader').show();
 			let invoice = jQuery('input[name="custom_invoice_number"]').val();
 			let order_id = jQuery('input[name="mrkv_order_id"]').val();

 			jQuery.ajax({
	            type: 'POST',
	            url: mrkv_ua_ship_helper.ajax_url,
	            data: 'action=mrkv_ua_ship_remove_invoice_data&order_id=' + order_id + '&invoice=' + invoice  + '&nonce=' + mrkv_ua_ship_helper.nonce + '',
	            success: function (json) {
	              location.reload();
	            }
	        });
 		});
 	}

 	jQuery('#nova-poshta_m_ua_settings_mrkv_ua_ship_invoice_sender_ref').change(function()
	{
		var option_selected = jQuery(this).find('option:selected');

		if(option_selected.length === 0){
			return;
		}

		jQuery.each(jQuery(option_selected)[0].attributes, function() 
		{
			if(~this.name.indexOf("data-"))
			{
				var attr_name = this.name.replace('data-','');
				
				jQuery('#nova-poshta_m_ua_settings_mrkv_ua_ship_invoice_sender_' + attr_name).val(this.value);
			}	
		});
	});	

	if(jQuery('.mrkv-col-added-link.mrkv_ua_ship_print_inv_ukr').length != 0)
 	{
		jQuery('.mrkv-col-added-link.mrkv_ua_ship_print_inv_ukr').click(function()
 		{
			let invoice_id = jQuery(this).closest('.column-mrkv_ua_invoice').find('.mrkv_ua_ship_global__invoice').attr('data-ttn');
			let form_name = jQuery(this).attr('data-form');


			if(invoice_id)
			{
				jQuery('.' + form_name + '.form-ukr-poshta-ttn-orders input[name="invoice_number"]').val(invoice_id);
				jQuery('.' + form_name + '.form-ukr-poshta-ttn-orders').submit();
			}
 		});
	}

	jQuery('input[name="mrkv_ua_ship_invoice_shipment_weight"], input[name="mrkv_ua_ship_invoice_shipment_length"], input[name="mrkv_ua_ship_invoice_shipment_width"], input[name="mrkv_ua_ship_invoice_shipment_height"]')
        .on('keyup', function() {
            jQuery('input[name="mrkv_ua_ship_invoice_shipment_volume"]')
                .val(mrkvnpCalcVolumeWeightSettings());
    });

        jQuery(document).on('input', 'form[data-ship="ukr-poshta"] .adm_morkva_row_size input[type="number"]', function() {
    let max = parseFloat(jQuery(this).attr('max'));
    let min = parseFloat(jQuery(this).attr('min'));
    let value = parseFloat(jQuery(this).val());

    if (value > max) {
        jQuery(this).val(max);
    } else if (value < min) {
        jQuery(this).val(min);
    }
});

	function mrkvnpCalcVolumeWeightSettings() {
	    let length = jQuery('input[name="mrkv_ua_ship_invoice_shipment_length"]').val();
	    let width = jQuery('input[name="mrkv_ua_ship_invoice_shipment_width"]').val();
	    let height = jQuery('input[name="mrkv_ua_ship_invoice_shipment_height"]').val();
	    let weight = jQuery('input[name="mrkv_ua_ship_invoice_shipment_weight"]').val();
	    let volumeWeight = length * width * height / 4000;
	    if (volumeWeight > weight) {
	        return volumeWeight;
	    } else {
	        return weight;
	    }
	}
});