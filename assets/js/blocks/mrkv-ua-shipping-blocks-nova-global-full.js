jQuery(window).on('load', function() {
	var currentLang = jQuery('html').attr('lang');

	if (currentLang === 'uk') {
		currentLang = 'UA';
	} else {
		currentLang = 'EN';
	}

	let isProgrammaticChange = false;

    function dispatchReactEvent(id, value) {
        let element = document.getElementById(id);
        const normalizedValue = value === undefined || value === null ? '' : String(value);

        if (!element) return;

        isProgrammaticChange = true;

        if (element.tagName === 'SELECT') {
            const $el = jQuery(element);
            let $option = $el.find('option[value="' + normalizedValue.replace(/"/g, '\\"') + '"]');

            if ($option.length === 0 && normalizedValue !== '') {
                $option = jQuery('<option>', { value: normalizedValue, text: normalizedValue });
                $el.append($option);
            }

            $el.val(normalizedValue);
            if ($el.data('select2')) {
                $el.trigger('change.select2');
            }

            const hiddenElement = document.getElementById(id + '_hidden_val');
            if (hiddenElement) {
                element = hiddenElement; 
            } else {
                isProgrammaticChange = false;
                return; 
            }
        }

        const valueSetter = Object.getOwnPropertyDescriptor(window.HTMLInputElement.prototype, 'value').set;
        const prototype = Object.getPrototypeOf(element);
        const elementValueSetter = Object.getOwnPropertyDescriptor(prototype, 'value') ? Object.getOwnPropertyDescriptor(prototype, 'value').set : null;

        if (elementValueSetter && elementValueSetter !== valueSetter) {
            elementValueSetter.call(element, normalizedValue);
        } else if (valueSetter) {
            valueSetter.call(element, normalizedValue);
        } else {
            jQuery(element).val(normalizedValue);
        }

        const tracker = element._valueTracker;
        if (tracker) {
            tracker.setValue('');
        }

        element.dispatchEvent(new Event('input', { bubbles: true }));
        element.dispatchEvent(new Event('change', { bubbles: true }));

        try {
            if (window.wp && wp.data) {
                const storeErrorKey = id.replace(/-/g, '_'); 
                
                if (window.mrkvErrorsCache && window.mrkvErrorsCache[storeErrorKey]) {
                    delete window.mrkvErrorsCache[storeErrorKey];
                }
                
                const hiddenStoreErrorKey = storeErrorKey + '_hidden_val';
                if (window.mrkvErrorsCache && window.mrkvErrorsCache[hiddenStoreErrorKey]) {
                    delete window.mrkvErrorsCache[hiddenStoreErrorKey];
                }

                const validationDispatch = wp.data.dispatch('wc/store/validation');
                if (validationDispatch && typeof validationDispatch.clearValidationError === 'function') {
                    validationDispatch.clearValidationError(storeErrorKey);
                    validationDispatch.clearValidationError(hiddenStoreErrorKey);
                }
                
                const container = element.closest('.wc-block-components-select-input') || 
                                element.closest('.wc-blocks-components-select') || 
                                element.closest('.has-error');
                if (container) {
                    container.classList.remove('has-error');
                    const errorNotice = container.querySelector('.wc-block-components-validation-error');
                    if (errorNotice) {
                        errorNotice.remove();
                    }
                }
            }
        } catch (err) {
            console.error(err);
        }

        isProgrammaticChange = false;
    }

	function initDynamicNovaGlobalFields() {
		var $warehouseSelect = jQuery('#mrkv_ua_shipping_nova-global_warehouse');
		
		if($warehouseSelect.length != 0 && !$warehouseSelect.hasClass('select2-hidden-accessible')) {
			$warehouseSelect.select2();

			$warehouseSelect.on('select2:select', function (e) {
				if (isProgrammaticChange) return;

				let current_option = e.params.data;
				let option_selected = jQuery(this).find('option:selected');
				
				let dataRef = option_selected.attr('data-ref') || current_option.ref;
				let dataCity = option_selected.attr('data-city') || current_option.city;
				let dataAddress = option_selected.attr('data-address') || current_option.address;
				let dataArea = option_selected.attr('data-area') || current_option.area;
				let dataZipcode = option_selected.attr('data-zipcode') || current_option.zipcode;
				let currentVal = jQuery(this).val();

				if (currentVal) {
					dispatchReactEvent('mrkv_ua_shipping_nova-global_warehouse', currentVal);
					dispatchReactEvent('mrkv_ua_shipping_nova-global_warehouse_ref', dataRef || '');
					dispatchReactEvent('mrkv_ua_shipping_nova-global_city_label', dataCity || '');
					dispatchReactEvent('mrkv_ua_shipping_nova-global_address', dataAddress || '');
					dispatchReactEvent('mrkv_ua_shipping_nova-global_area_name', dataArea || '');
					dispatchReactEvent('mrkv_ua_shipping_nova-global_zipcode', dataZipcode || '');
				}
			});
		}
	}

	function mrkvUaShipGetWarehouse(shipping_method) {
		var country = (jQuery('#ship-to-different-address-checkbox').is(':checked') ? jQuery('#shipping_country').val() : jQuery('#billing_country').val()) || '';

		if(country) {
			jQuery.ajax({
				type: 'POST',
				url: mrkv_ua_ship_helper.ajax_url,
				data: {
					action: 'mrkv_ua_ship_nova_global_warehouse',
					warehouse_types: mrkv_ua_ship_helper.nova_global_type,
					method: shipping_method,
					country: country,
					language: currentLang,
					nonce: mrkv_ua_ship_helper.nonce,
				},
				beforeSend: function() {
					if (jQuery('#mrkv_ua_shipping_nova-global_warehouse').length != 0) {
						jQuery('#mrkv_ua_shipping_nova-global_warehouse').find('option').remove();
						jQuery('#mrkv_ua_shipping_nova-global_warehouse').addClass('mrkv-ua-shipping-loading');
					}
				},
				success: function (json) {
					var data = JSON.parse(json);

					if(data) {
						jQuery.each(data, function(key, value) {
							jQuery('#mrkv_ua_shipping_nova-global_warehouse')
							.append(jQuery("<option></option>")
								.attr('value', this.label)
								.text(this.label)
								.attr('data-ref', this.value)
								.attr('data-area', this.area)
								.attr('data-city', this.city)
								.attr('data-address', this.address)
								.attr('data-zipcode', this.zipcode)
							);
						});
					}

					if(data.length == 1 && data[0].value == 'none') {
						jQuery('#mrkv_ua_shipping_nova-global_warehouse_field .select2-selection__rendered').hide();
						setTimeout(function(){ 
							jQuery('#mrkv_ua_shipping_nova-global_warehouse_field .select2-selection__rendered').text(mrkv_ua_ship_helper.city_text_weight);
							jQuery('#mrkv_ua_shipping_nova-global_warehouse_field .select2-selection__rendered').show();
						}, 10);
					}

					jQuery('#mrkv_ua_shipping_nova-global_warehouse').removeClass('mrkv-ua-shipping-loading');
				}
			});
		}
	}

	function mrkvUaShipGetCurrentShipping() {
		let mrkv_ua_current_shipping = jQuery('.shipping_method').length > 1 ?
		  jQuery('.shipping_method:checked').val() :
		  jQuery('.shipping_method').val();

		if(mrkv_ua_current_shipping) {
			mrkv_ua_current_shipping = mrkv_ua_current_shipping.replace(/_\d+$/, '');
		}

		return mrkv_ua_current_shipping;
	}

	setTimeout(function(){
		let mrkv_ua_current_shipping = mrkvUaShipGetCurrentShipping();

		if(mrkv_ua_current_shipping && ~mrkv_ua_current_shipping.indexOf("mrkv_ua_shipping_nova-global")) {
			//mrkvUaShipGetWarehouse(mrkv_ua_current_shipping);
		}
	}, 200);

	jQuery( document.body ).on( 'updated_checkout', () => {
		let mrkv_ua_current_shipping = mrkvUaShipGetCurrentShipping();

		if(mrkv_ua_current_shipping && ~mrkv_ua_current_shipping.indexOf("mrkv_ua_shipping_nova-global")) {
			//mrkvUaShipGetWarehouse(mrkv_ua_current_shipping);
		}
	});

    let initTimeout;
    const observer = new MutationObserver(() => {
        clearTimeout(initTimeout);
        initTimeout = setTimeout(initDynamicNovaGlobalFields, 100);
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    initDynamicNovaGlobalFields();
});