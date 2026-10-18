jQuery(window).on('load', function() 
{
	var mrkv_np_api_key_value = jQuery('#nova-poshta_m_ua_settings_api_key').val();

	jQuery('#nova-poshta_m_ua_settings_api_key').on('keyup change', function() {
		// Tab, arrows and paste of the same key fire keyup too: clear the sender only for another key
		if(jQuery(this).val() === mrkv_np_api_key_value)
		{
			return;
		}
		mrkv_np_api_key_value = jQuery(this).val();

		jQuery('#nova-poshta_m_ua_settings_sender_counterparty_description').val('');
		jQuery('#nova-poshta_m_ua_settings_sender_list').val('');
		jQuery('#nova-poshta_m_ua_settings_sender_counterparty_ref').val('');
		jQuery('#nova-poshta_m_ua_settings_sender_middlename').val('');
		jQuery('#nova-poshta_m_ua_settings_sender_firstname').val('');
		jQuery('#nova-poshta_m_ua_settings_sender_lastname').val('');
		jQuery('#nova-poshta_m_ua_settings_sender_email').val('');
		jQuery('#nova-poshta_m_ua_settings_sender_phones').val('');
		jQuery('#nova-poshta_m_ua_settings_sender_description').val('');
		jQuery('#nova-poshta_m_ua_settings_sender_ref').val('').trigger('change.select2');
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

	jQuery('#nova-poshta_m_ua_settings_sender_address_type').change(function()
	{
		var option_selected = jQuery(this).find('option:selected');

		if(option_selected.length === 0){
			return;
		}

		jQuery('.mrkv-address-active').removeClass('mrkv-address-active');

		jQuery('.adresstype-form-' + jQuery(this).val()).addClass('mrkv-address-active');
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
	                counterparty_ref: jQuery('#nova-poshta_m_ua_settings_sender_counterparty_ref').val(),
	                api_key: jQuery('#nova-poshta_m_ua_settings_api_key').val(),
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
		const isApiKeyCorrect = jQuery('label[for="nova-poshta_m_ua_settings_api_key"] .admin_ua_ship_morkva__notification').hasClass('mrkv-notification-green');
        
        const isCredentialsEntered = jQuery.trim(apiKey.val()) !== '' && jQuery.trim(counterpartyRef.val()) !== '';

        const addressType = jQuery('select[name="nova-poshta_m_ua_settings[sender][address_type]"]').val();
        
        let isAddressValid = false;

        if (addressType === 'W') {
            const warehouseRef = jQuery('input[name="nova-poshta_m_ua_settings[sender][warehouse][ref]"]').val();
            isAddressValid = jQuery.trim(warehouseRef) !== '';
        } else {
            const streetRef = jQuery('input[name="nova-poshta_m_ua_settings[sender][street][ref]"]').val();
            const houseNum = jQuery('input[name="nova-poshta_m_ua_settings[sender][street][house]"]').val();
            isAddressValid = jQuery.trim(streetRef) !== '' && jQuery.trim(houseNum) !== '';
        }

        if (isApiKeyCorrect && isCredentialsEntered && !isAddressValid) {
			return true;
        }
		if(isApiKeyCorrect){
			return false;
		}
    }

    validateSettings();

    jQuery(document).on('input change', 
        '#nova-poshta_m_ua_settings_api_key, ' +
        '#nova-poshta_m_ua_settings_sender_counterparty_ref, ' +
        'select[name="nova-poshta_m_ua_settings[sender][address_type]"], ' +
        'input[name="nova-poshta_m_ua_settings[sender][warehouse][ref]"], ' +
        'input[name="nova-poshta_m_ua_settings[sender][street][ref]"], ' +
        'input[name="nova-poshta_m_ua_settings[sender][street][house]"]', 
        validateSettings
    );

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

	let mrkvUaShipMiddlenameRequiredToggle = function() {
		const middlenameEnabled = jQuery('#nova-poshta_m_ua_settings_checkout_middlename_enabled').is(':checked');
		const middlenameRequired = jQuery('#nova-poshta_m_ua_settings_checkout_middlename_required');

		if (!middlenameEnabled) {
			middlenameRequired.prop('checked', false).prop('disabled', true);
		} else {
			middlenameRequired.prop('disabled', false);
		}
	};

	mrkvUaShipMiddlenameRequiredToggle();
	jQuery('#nova-poshta_m_ua_settings_checkout_middlename_enabled').change(function() { mrkvUaShipMiddlenameRequiredToggle(); });
});

/**
 * Sender tab: API key -> sender type and counterparty -> contact person -> shipping place.
 * Only two states can be saved: the API key alone, or the whole sender. A half-filled sender blocks
 * saving, and leaving the tab with it asks first and then leaves it out of the form.
 */
jQuery(window).on('load', function()
{
	var section = jQuery('#sender_settings[data-mrkv-np-sender]');

	if(section.length === 0)
	{
		return;
	}

	var np_data = section.data('mrkv-np-sender') || {};
	var i18n = np_data.i18n || {};
	var counterparties = np_data.counterparties || [];
	var api_key = jQuery('#nova-poshta_m_ua_settings_api_key');
	var connect_btn = section.find('.mrkv-ua-ship-np-connect__btn');
	var connect_status = section.find('.mrkv-ua-ship-np-connect__status');
	var step_sender = section.find('.mrkv-ua-ship-np-step--sender');
	var step_place = section.find('.mrkv-ua-ship-np-step--place');
	var type_radios = section.find('input[name="nova-poshta_m_ua_settings[sender][counterparty_type]"]');
	var counterparty_line = section.find('.mrkv-ua-ship-np-counterparty');
	var counterparty_select = jQuery('#nova-poshta_m_ua_settings_sender_counterparty_select');
	var counterparty_ref = jQuery('#nova-poshta_m_ua_settings_sender_counterparty_ref');
	var counterparty_description = jQuery('#nova-poshta_m_ua_settings_sender_counterparty_description');
	var contact = jQuery('#nova-poshta_m_ua_settings_sender_ref');
	var notice = section.find('.mrkv-ua-ship-np-sender-notice');
	var is_pro = section.find('.admin_mrkv_ua_ship_address_list').length > 0;
	var connected_key = np_data.connected ? jQuery.trim(api_key.val()) : '';
	var contact_fields = ['description', 'phones', 'email', 'lastname', 'firstname', 'middlename'];
	var discarded_inputs = null;
	var submitting = false;

	function setLocked(step, locked)
	{
		step.toggleClass('mrkv-ua-ship-np-step--locked', locked);

		if(locked)
		{
			step.attr('inert', '');
		}
		else
		{
			step.removeAttr('inert');
		}
	}

	function setStatus(text, type)
	{
		connect_status.text(text || '').removeClass('mrkv-notification-red mrkv-notification-green');

		if(type)
		{
			connect_status.addClass(type === 'error' ? 'mrkv-notification-red' : 'mrkv-notification-green');
		}
	}

	function counterpartyLabel(counterparty)
	{
		return counterparty.description + (counterparty.edrpou ? ' · ' + i18n.edrpou + ' ' + counterparty.edrpou : '');
	}

	function counterpartiesOfType(type)
	{
		return counterparties.filter(function(counterparty) { return counterparty.type === type; });
	}

	/**
	 * Sender type radios: only the types the key has are active, a single type is chosen automatically
	 * */
	function renderTypes()
	{
		var available = {};

		counterparties.forEach(function(counterparty) { available[counterparty.type] = true; });

		type_radios.each(function()
		{
			jQuery(this).prop('disabled', !available[this.value]);
		});

		var current = type_radios.filter(':checked').val();

		if(!current || !available[current])
		{
			type_radios.prop('checked', false);

			var types = Object.keys(available);

			if(types.length === 1)
			{
				type_radios.filter('[value="' + types[0] + '"]').prop('checked', true);
			}
		}

		renderCounterparties();
	}

	/**
	 * Counterparties of the chosen type: the select shows only when there is a choice
	 * */
	function renderCounterparties()
	{
		var list = counterpartiesOfType(type_radios.filter(':checked').val());
		var current = counterparty_ref.val();
		var chosen = list.filter(function(counterparty) { return counterparty.ref === current; })[0] || list[0] || null;

		counterparty_select.empty();

		list.forEach(function(counterparty)
		{
			counterparty_select.append(jQuery('<option>').val(counterparty.ref).text(counterpartyLabel(counterparty)));
		});

		counterparty_line.toggleClass('hide_element', list.length < 2);
		counterparty_select.val(chosen ? chosen.ref : '').trigger('change.select2');

		setCounterparty(chosen);
	}

	function setCounterparty(counterparty)
	{
		var ref = counterparty ? counterparty.ref : '';

		counterparty_description.val(counterparty ? counterparty.description : '');

		if(ref === counterparty_ref.val())
		{
			return;
		}

		counterparty_ref.val(ref);
		setContacts([], '');

		if(ref)
		{
			loadContacts(false);
		}
	}

	/**
	 * Fill the contact person select. The options carry the person's data, copied to the hidden fields on change
	 * */
	function setContacts(contacts, selected)
	{
		contact.find('option[value!=""]').remove();

		contacts.forEach(function(person)
		{
			var option = jQuery('<option>').val(person.data).text(person.description);

			jQuery.each(person.attr || {}, function(key, value)
			{
				option.attr('data-' + key, value);
			});

			contact.append(option);
		});

		var keep = contacts.some(function(person) { return person.data === selected; });

		contact.val(keep ? selected : '').trigger('change');

		if(!keep)
		{
			contact_fields.forEach(function(field) { jQuery('#nova-poshta_m_ua_settings_sender_' + field).val(''); });
		}

		// The list goes to the order popup, as the page saved it before
		jQuery('#nova-poshta_m_ua_settings_sender_list').val(contacts.length ? btoa(unescape(encodeURIComponent(JSON.stringify(contacts)))) : '');
	}

	function loadContacts(force)
	{
		var selected = contact.val();

		setStatus(i18n.loading);

		jQuery.ajax({
			type: 'POST',
			url: mrkv_ua_ship_helper.ajax_url,
			dataType: 'json',
			data: {
				action: 'mrkv_ua_ship_nova_poshta_contact_persons',
				api_key: jQuery.trim(api_key.val()),
				counterparty_ref: counterparty_ref.val(),
				force: force ? 1 : 0,
				nonce: mrkv_ua_ship_helper.nonce,
			},
			success: function(answer)
			{
				if(answer && answer.success)
				{
					setContacts(answer.data.contacts || [], selected);
					setStatus(i18n.connected, 'ok');
				}
				else
				{
					setContacts([], '');
					setStatus(answer && answer.data ? answer.data : i18n.error, 'error');
				}
			},
			error: function()
			{
				setStatus(i18n.error, 'error');
			}
		});
	}

	connect_btn.on('click', function()
	{
		var key = jQuery.trim(api_key.val());

		if(!key)
		{
			api_key.trigger('focus');
			return;
		}

		var refresh = key === connected_key;

		connect_btn.prop('disabled', true);
		setStatus(i18n.loading);

		jQuery.ajax({
			type: 'POST',
			url: mrkv_ua_ship_helper.ajax_url,
			dataType: 'json',
			data: {
				action: 'mrkv_ua_ship_nova_poshta_connect',
				api_key: key,
				force: refresh ? 1 : 0,
				nonce: mrkv_ua_ship_helper.nonce,
			},
			success: function(answer)
			{
				if(answer && answer.success)
				{
					var counterparty_before = counterparty_ref.val();

					counterparties = answer.data.counterparties || [];
					connected_key = key;
					connect_btn.text(i18n.refresh);
					setStatus(i18n.connected, 'ok');
					setLocked(step_sender, false);
					renderTypes();

					// The counterparty stayed, so renderTypes() did not load its people: load them now
					if(counterparty_ref.val() && counterparty_ref.val() === counterparty_before)
					{
						loadContacts(refresh);
					}
				}
				else
				{
					setStatus(answer && answer.data ? answer.data : i18n.error, 'error');
				}
			},
			error: function()
			{
				setStatus(i18n.error, 'error');
			},
			complete: function()
			{
				connect_btn.prop('disabled', false);
			}
		});
	});

	// Another key: its senders are not loaded yet
	api_key.on('input change', function()
	{
		var key = jQuery.trim(api_key.val());

		if(connected_key && key === connected_key)
		{
			return;
		}

		connected_key = '';
		counterparties = [];
		connect_btn.text(i18n.connect);
		setStatus(key ? i18n.key_changed : '');
		setLocked(step_sender, true);
		setLocked(step_place, true);
	});

	type_radios.on('change', renderCounterparties);

	counterparty_select.on('change', function()
	{
		var ref = jQuery(this).val();

		setCounterparty(counterparties.filter(function(counterparty) { return counterparty.ref === ref; })[0] || null);
	});

	contact.on('change', function()
	{
		if(jQuery(this).val())
		{
			setLocked(step_place, false);
		}
	});

	/**
	 * Shipping place: started (anything filled) and complete
	 * */
	function placeState()
	{
		if(is_pro)
		{
			var addresses = section.find('.admin_mrkv_ua_ship_address_list .mrkv-ua-shipping-address-line').length;
			var default_checked = section.find('input[name="nova-poshta_m_ua_settings[sender][default_addresses]"]:checked').length > 0;
			var draft = jQuery('#_sender_city_ref_time').val() || jQuery('#_sender_warehouse_ref_time').val() || jQuery('#_sender_street_ref_time').val();

			return { started: addresses > 0 || !!draft, complete: addresses > 0 && default_checked };
		}

		var city = jQuery('#nova-poshta_m_ua_settings_sender_city_ref').val();
		var warehouse = jQuery('#nova-poshta_m_ua_settings_sender_warehouse_ref').val();
		var street = jQuery('#nova-poshta_m_ua_settings_sender_street_ref').val();
		var house = jQuery.trim(jQuery('#nova-poshta_m_ua_settings_sender_street_house').val() || '');
		var complete = jQuery('#nova-poshta_m_ua_settings_sender_address_type').val() === 'D' ? (city && street && house) : (city && warehouse);

		return { started: !!(city || warehouse || street || house), complete: !!complete };
	}

	/**
	 * "empty" (only the API key), "complete" or "partial". The type and counterparty fill in by themselves, so they do not start the block
	 * */
	function senderState()
	{
		var place = placeState();
		var has_contact = !!contact.val();

		if(!has_contact && !place.started)
		{
			return 'empty';
		}

		return (has_contact && counterparty_ref.val() && place.complete) ? 'complete' : 'partial';
	}

	function senderSnapshot()
	{
		return JSON.stringify(section.find(':input').not(api_key).serializeArray()) + '|' + section.find('.mrkv-ua-shipping-address-line').length;
	}

	var initial_snapshot = senderSnapshot();

	function isDirty()
	{
		return senderSnapshot() !== initial_snapshot;
	}

	function hideNotice()
	{
		notice.addClass('hide_element').empty();
		section.find('.mrkv-field-error').removeClass('mrkv-field-error');
	}

	function showMissing()
	{
		var missing = [];
		var place = placeState();

		hideNotice();

		if(!counterparty_ref.val())
		{
			missing.push(i18n.missing_type);
		}
		if(!contact.val())
		{
			missing.push(i18n.missing_contact);
			contact.next('.select2-container').find('.select2-selection').addClass('mrkv-field-error');
		}
		if(!place.complete)
		{
			missing.push(i18n.missing_place);
			section.find(is_pro ? '.admin_mrkv_ua_ship_address_list, .mrkv_ua_ship_form_create_address' : '.adresstype-form').addClass('mrkv-field-error');
		}

		notice.text(i18n.partial_save + ' ' + missing.join(', ') + '.').removeClass('hide_element');
	}

	function showSenderTab()
	{
		var tab_btn = jQuery('.mrkv_up_ship_tab_btn[data-tab="sender_settings"]');

		if(tab_btn.length)
		{
			tab_btn.trigger('click');
		}

		jQuery('html, body').animate({ scrollTop: section.offset().top - 50 }, 400);
	}

	/**
	 * Leave the half-filled sender out of the form: disabled fields are not sent, the saved sender stays
	 * */
	function discardSender()
	{
		discarded_inputs = section.find(':input[name^="nova-poshta_m_ua_settings[sender]"]').not(':disabled');
		discarded_inputs.prop('disabled', true);
		hideNotice();
	}

	function restoreSender()
	{
		if(discarded_inputs)
		{
			discarded_inputs.prop('disabled', false);
			discarded_inputs = null;
		}
	}

	// Capture phase: runs before the tab switch handler of the tab button
	document.addEventListener('click', function(e)
	{
		var tab_btn = e.target && e.target.closest ? e.target.closest('.mrkv_up_ship_tab_btn') : null;

		if(!tab_btn)
		{
			return;
		}

		if(tab_btn.getAttribute('data-tab') === 'sender_settings')
		{
			restoreSender();
			return;
		}

		if(!section.hasClass('active') || discarded_inputs || !isDirty() || senderState() !== 'partial')
		{
			return;
		}

		if(window.confirm(i18n.partial_leave))
		{
			discardSender();
			return;
		}

		e.preventDefault();
		e.stopPropagation();
		e.stopImmediatePropagation();
	}, true);

	jQuery('#submit').on('click', function(e)
	{
		if(discarded_inputs || senderState() !== 'partial')
		{
			submitting = true;
			hideNotice();
			return true;
		}

		e.preventDefault();
		e.stopImmediatePropagation();

		showSenderTab();
		showMissing();

		return false;
	});

	window.addEventListener('beforeunload', function(e)
	{
		if(submitting || discarded_inputs || !isDirty() || senderState() !== 'partial')
		{
			return;
		}

		e.preventDefault();
		e.returnValue = '';
	});

	// "Basic" tab is gone: a saved "#basic_settings-mrkv" left the page without an active tab
	if(jQuery('.mrkv_up_ship_shipping_tab_block.active').length === 0)
	{
		jQuery('.mrkv_up_ship_tab_btn[data-tab="sender_settings"]').trigger('click');
	}
});
