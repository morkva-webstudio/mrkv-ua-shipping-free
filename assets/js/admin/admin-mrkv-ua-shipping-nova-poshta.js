jQuery(window).on('load', function() 
{
	jQuery('#nova-poshta_m_ua_settings_api_key').on('keyup change', function() {
		jQuery('#nova-poshta_m_ua_settings_sender_list').val('');
		jQuery('#nova-poshta_m_ua_settings_sender_counterparty_ref').val('');
		jQuery('#nova-poshta_m_ua_settings_sender_middlename').val('');
		jQuery('#nova-poshta_m_ua_settings_sender_firstname').val('');
		jQuery('#nova-poshta_m_ua_settings_sender_lastname').val('');
		jQuery('#nova-poshta_m_ua_settings_sender_email').val('');
		jQuery('#nova-poshta_m_ua_settings_sender_phones').val('');
		jQuery('#nova-poshta_m_ua_settings_sender_description').val('');
		jQuery('#nova-poshta_m_ua_settings_sender_ref').val('');
	});

 	jQuery('#nova-poshta_m_ua_settings_sender_ref').change(function()
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
				
				jQuery('#nova-poshta_m_ua_settings_sender_' + attr_name).val(this.value);
			}	
		});
	});	

	jQuery('#nova-poshta_m_ua_settings_sender_area_name').autocomplete({
		source: function (request, response) {
			if(request.term.length < 2)
			{
				return false;
			}
	        jQuery.ajax({
	            type: 'POST',
	            url: mrkv_ua_ship_helper.ajax_url,
	            data: {
	                action: 'mrkv_ua_ship_nova_poshta_area',
	                name: request.term,
	                nonce: mrkv_ua_ship_helper.nonce,
	            },
	            success: function (json) {
	                var data = JSON.parse(json);
	                response(data);
	                jQuery(this).removeClass('ui-autocomplete-loading');
	            }
	        })
	    },
	    focus: function (event, ui) {
	        return false;
	    },
	    unfocus: function (event, ui) {
	    	jQuery(this).removeClass('ui-autocomplete-loading');
	        return false;
	    },
	    select: function (event, ui) {
	    	if(ui.item.value != 'none')
	    	{
	    		jQuery(this).val(ui.item.label);
		    	jQuery('#nova-poshta_m_ua_settings_sender_area_ref').val(ui.item.value);
		    	jQuery(this).removeClass('ui-autocomplete-loading');
		        mrkvUaShipNpClearCity();
		        mrkvUaShipNpClearAddress();
	    	}
	        return false;
	    }
	});

	jQuery('#nova-poshta_m_ua_settings_sender_city_name').autocomplete({
		source: function (request, response) {
			if(request.term.length < 2)
			{
				return false;
			}

	        jQuery.ajax({
	            type: 'POST',
	            url: mrkv_ua_ship_helper.ajax_url,
	            data: {
	                action: 'mrkv_ua_ship_nova_poshta_city',
	                name: request.term,
	                nonce: mrkv_ua_ship_helper.nonce,
	            },
	            success: function (json) {
	                var data = JSON.parse(json);
	                response(data);
	                jQuery(this).removeClass('ui-autocomplete-loading');
	            }
	        })
	    },
	    focus: function (event, ui) {
	        return false;
	    },
	    unfocus: function (event, ui) {
	    	jQuery(this).removeClass('ui-autocomplete-loading');
	        return false;
	    },
	    select: function (event, ui) {
	    	if(ui.item.value != 'none')
	    	{
	    		jQuery(this).val(ui.item.label);
		    	jQuery('#nova-poshta_m_ua_settings_sender_city_ref').val(ui.item.value);
		    	jQuery(this).removeClass('ui-autocomplete-loading');
		        mrkvUaShipNpClearAddress();
	    	}

	        return false;
	    }
	});
	jQuery('#nova-poshta_m_ua_settings_sender_warehouse_name').autocomplete({
		source: function (request, response) {
			if(request.term.length < 1)
			{
				return false;
			}

			var city_ref = jQuery('#nova-poshta_m_ua_settings_sender_city_ref').val();

	        jQuery.ajax({
	            type: 'POST',
	            url: mrkv_ua_ship_helper.ajax_url,
	            data: {
	                action: 'mrkv_ua_ship_nova_poshta_warehouse',
	                name: request.term,
	                ref: city_ref,
	                nonce: mrkv_ua_ship_helper.nonce,
	            },
	            success: function (json) {
	                var data = JSON.parse(json);
	                response(data);
	                jQuery(this).removeClass('ui-autocomplete-loading');
	            }
	        })
	    },
	    focus: function (event, ui) {
			validateSettings();
	        return false;
	    },
	    unfocus: function (event, ui) {
	    	jQuery(this).removeClass('ui-autocomplete-loading');
			validateSettings();
	        return false;
	    },
	    select: function (event, ui) {
	    	if(ui.item.value != 'none')
	    	{
	    		jQuery(this).val(ui.item.label);
		    	jQuery('#nova-poshta_m_ua_settings_sender_warehouse_ref').val(ui.item.value);
		    	jQuery('#nova-poshta_m_ua_settings_sender_warehouse_number').val(ui.item.number);
		    	jQuery(this).removeClass('ui-autocomplete-loading');
				validateSettings();
	    	}

	        return false;
	    }
	});

	jQuery('#nova-poshta_m_ua_settings_sender_street_name').autocomplete({
		source: function (request, response) {

			var city_ref = jQuery('#nova-poshta_m_ua_settings_sender_city_ref').val();

	        jQuery.ajax({
	            type: 'POST',
	            url: mrkv_ua_ship_helper.ajax_url,
	            data: {
	                action: 'mrkv_ua_ship_nova_poshta_street',
	                name: request.term,
	                ref: city_ref,
	                nonce: mrkv_ua_ship_helper.nonce,
	            },
	            success: function (json) {
	                var data = JSON.parse(json);
	                response(data);
	                jQuery(this).removeClass('ui-autocomplete-loading');
	            }
	        })
	    },
	    focus: function (event, ui) {
	        return false;
	    },
	    select: function (event, ui) {
	    	if(ui.item.value != 'none')
	    	{
	    		jQuery(this).val(ui.item.label);
		    	jQuery('#nova-poshta_m_ua_settings_sender_street_ref').val(ui.item.value);
		    	jQuery('#nova-poshta_m_ua_settings_sender_street_house').val('');
		    	jQuery('#nova-poshta_m_ua_settings_sender_street_flat').val('');
		    	jQuery(this).removeClass('ui-autocomplete-loading');
				validateSettings();
	    	}

	        return false;
	    }
	});

	jQuery('#nova-poshta_m_ua_settings_sender_street_house').change(function()
	{
		jQuery('#nova-poshta_m_ua_settings_sender_street_flat').val('');
	});

	jQuery('#nova-poshta_m_ua_settings_shipment_length, #nova-poshta_m_ua_settings_shipment_width, #nova-poshta_m_ua_settings_shipment_height, #nova-poshta_m_ua_settings_shipment_weight')
        .on('keyup', function() {
            jQuery('#nova-poshta_m_ua_settings_shipment_volume')
                .val(mrkvnpCalcVolumeWeightSettings());
    });

    jQuery('#nova-poshta_m_ua_settings_inter_length, #nova-poshta_m_ua_settings_inter_width, #nova-poshta_m_ua_settings_inter_height, #nova-poshta_m_ua_settings_inter_weight')
        .on('keyup', function() {
            jQuery('#nova-poshta_m_ua_settings_inter_volume')
                .val(mrkvnpCalcVolumeWeightSettingsInternal());
    });

    jQuery('.adm-textarea-btn').on('click', function() 
    {
         var shotcode = jQuery(this).attr('data-added');
         var exist_text = jQuery(this).closest('.admin_ua_ship_morkva_settings_line').find('textarea').val();
         var new_text_data = '' + exist_text + shotcode;
         jQuery(this).closest('.admin_ua_ship_morkva_settings_line').find('textarea').val(new_text_data);
    });

    jQuery('.admin_ua_ship_morkva_settings_line select').select2({
        width: '100%',
    });

    var mrkv_typing_timer;
    var done_typing_interval = 2000;

    jQuery('#nova-poshta_m_ua_settings_sender_street_flat').on('keyup', function() 
    {
    	clearTimeout(mrkv_typing_timer);
  		mrkv_typing_timer = setTimeout(mrkvUaShipGetAddress, done_typing_interval);       
    });

    jQuery('#nova-poshta_m_ua_settings_sender_street_flat').on('keydown', function() 
    {
    	clearTimeout(mrkv_typing_timer);       
    });

    function mrkvUaShipGetAddress() 
    {
		let sender_street_ref = jQuery('#nova-poshta_m_ua_settings_sender_street_ref').val();
		let sender_building_number = jQuery('#nova-poshta_m_ua_settings_sender_street_house').val();
		let sender_flat = jQuery('#nova-poshta_m_ua_settings_sender_street_flat').val();

		if(sender_street_ref && sender_building_number && sender_flat)
		{
			jQuery.ajax({
	            type: 'POST',
	            url: mrkv_ua_ship_helper.ajax_url,
	            data: {
	                action: 'mrkv_ua_ship_nova_poshta_sender_get_address_ref',
					sender_street_ref: sender_street_ref,
					sender_building_number: sender_building_number,
					sender_flat: sender_flat,
					nonce: mrkv_ua_ship_helper.nonce,
	            },
	            success: function (data) {
	                if(data)
	                {
                		jQuery('#nova-poshta_m_ua_settings_sender_address_ref').val(data.replace(/['"]+/g, ''));
						validateSettings();
	                }	                
	            }
	        });
		}
	}

	const submitBtn = jQuery('#submit');

    function validateSettings() {
        const apiKey = jQuery('#nova-poshta_m_ua_settings_api_key');
        const counterpartyRef = jQuery('#nova-poshta_m_ua_settings_sender_counterparty_ref');
        
        const isCredentialsEntered = jQuery.trim(apiKey.val()) !== '' && jQuery.trim(counterpartyRef.val()) !== '';

        const addressType = jQuery('input[name="nova-poshta_m_ua_settings[sender][address_type]"]:checked').val();
        
        let isAddressValid = false;

        if (addressType === 'W') {
            const warehouseRef = jQuery('input[name="nova-poshta_m_ua_settings[sender][warehouse][ref]"]').val();
            isAddressValid = jQuery.trim(warehouseRef) !== '';
        } else {
            const streetRef = jQuery('input[name="nova-poshta_m_ua_settings[sender][street][ref]"]').val();
            const houseNum = jQuery('input[name="nova-poshta_m_ua_settings[sender][street][house]"]').val();
            isAddressValid = jQuery.trim(streetRef) !== '' && jQuery.trim(houseNum) !== '';
        }

        if (isCredentialsEntered && !isAddressValid) {
            submitBtn.addClass('custom-disabled').css({
                'opacity': '0.5',
                'cursor': 'not-allowed'
            });
        } else {
            submitBtn.removeClass('custom-disabled').css({
                'opacity': '1',
                'cursor': 'pointer'
            });
        }
    }

    validateSettings();

    jQuery(document).on('input change', 
        '#nova-poshta_m_ua_settings_api_key, ' +
        '#nova-poshta_m_ua_settings_sender_counterparty_ref, ' +
        'input[name="nova-poshta_m_ua_settings[sender][address_type]"], ' +
        'input[name="nova-poshta_m_ua_settings[sender][warehouse][ref]"], ' +
        'input[name="nova-poshta_m_ua_settings[sender][street][ref]"], ' +
        'input[name="nova-poshta_m_ua_settings[sender][street][house]"]', 
        validateSettings
    );

    submitBtn.on('click', function(e) {
        if (jQuery(this).hasClass('custom-disabled')) {
            e.preventDefault();
            e.stopImmediatePropagation();
            
            alert('Виберіть місто та відділення відправки, та натисніть “додати”. Після цього можете зберегти налаштування.');
            return false;
        }
    });

	let autoSelectCityPo = function() 
	{
	    jQuery('#nova-poshta_m_ua_settings_inter_division_address').autocomplete({

	    source: function(request, response) { 

	      if(request.term.length > 2){
	        var country_sender = 'UA';
	        jQuery('#nova-poshta_m_ua_settings_inter_division_address').addClass('ui-autocomplete-loading');
	        jQuery.ajax({
	            method: 'POST',
	            url: mrkv_ua_ship_helper.ajax_url,
	            dataType: 'json',
	            data: {
	              term: request.term,
	              action: 'mrkv_ua_ship_novapost_divisions',
	              mrkvup_country_suggestion: country_sender,
	              nonce: mrkv_ua_ship_helper.nonce,
	            },
	            success: function(data) {
	              if(!Array.isArray(data))
	              {
	                response(data.response);
	              }
	              else
	              {
	                response(data);
	              }

	              
	              jQuery('#nova-poshta_m_ua_settings_inter_division_address').removeClass('ui-autocomplete-loading');
	            },
	                error: function(xhr, status, error) {
	                    
	                },
	          });
	      }
	    },
	    select: function(event, ui) {
	      event.preventDefault();
	      jQuery(this).val( ui.item.label );
	      jQuery( "#nova-poshta_m_ua_settings_inter_division_id" ).val( ui.item.value );
	      jQuery( "#nova-poshta_m_ua_settings_inter_division_number" ).val( ui.item.number );
	      },
	      minLength: 0,
	      delay: 0,
	    }).focus(function(){            
	            jQuery(this).data("uiAutocomplete").search(jQuery(this).val());
	        });
	  }

	  autoSelectCityPo();
	  mrkvUaShipNpCronSettings();

	 jQuery('input[name="nova-poshta_m_ua_settings[automation][cron][type]"]').change(function(){ mrkvUaShipNpCronSettings(); });

	function mrkvUaShipNpCronSettings()
	{
		var cron_type = jQuery('input[name="nova-poshta_m_ua_settings[automation][cron][type]"]:checked').val();

		if(cron_type == 'wp_cron')
		{
			jQuery('.mrkv-ua-shipping-server').hide();
			jQuery('.mrkv-ua-shipping-wpcron').show();
		}
		else
		{
			jQuery('.mrkv-ua-shipping-server').show();
			jQuery('.mrkv-ua-shipping-wpcron').hide();
		}
	}

	function mrkvUaShipNpClearCity()
	{
		jQuery('#nova-poshta_m_ua_settings_sender_city_name').val('');
		jQuery('#nova-poshta_m_ua_settings_sender_city_ref').val('');
	}

	function mrkvUaShipNpClearAddress()
	{
		jQuery('#nova-poshta_m_ua_settings_sender_warehouse_name').val('');
		jQuery('#nova-poshta_m_ua_settings_sender_warehouse_ref').val('');
		jQuery('#nova-poshta_m_ua_settings_sender_warehouse_number').val('');
		jQuery('#nova-poshta_m_ua_settings_sender_street_name').val('');
		jQuery('#nova-poshta_m_ua_settings_sender_street_ref').val('');
		jQuery('#nova-poshta_m_ua_settings_sender_street_house').val('');
		jQuery('#nova-poshta_m_ua_settings_sender_street_flat').val('');
	}

	function mrkvnpCalcVolumeWeightSettings() {
	    let length = jQuery('#nova-poshta_m_ua_settings_shipment_length').val();
	    let width = jQuery('#nova-poshta_m_ua_settings_shipment_width').val();
	    let height = jQuery('#nova-poshta_m_ua_settings_shipment_height').val();
	    let weight = jQuery('#nova-poshta_m_ua_settings_shipment_weight').val();
	    let volumeWeight = length * width * height / 4000;
	    if (volumeWeight > weight) {
	        return volumeWeight;
	    } else {
	        return weight;
	    }
	}

	function mrkvnpCalcVolumeWeightSettingsInternal() {
	    let length = jQuery('#nova-poshta_m_ua_settings_inter_length').val();
	    let width = jQuery('#nova-poshta_m_ua_settings_inter_width').val();
	    let height = jQuery('#nova-poshta_m_ua_settings_inter_height').val();
	    let weight = jQuery('#nova-poshta_m_ua_settings_inter_weight').val();
	    let volumeWeight = length * width * height / 4000;
	    if (volumeWeight > weight) {
	        return volumeWeight;
	    } else {
	        return weight;
	    }
	}
});