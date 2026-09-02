jQuery(window).on('load', function () {
    var languageTexts = {
        errorLoading: function () { return mrkv_ua_ship_helper.select2_texts.errorLoading; },
        inputTooLong: function (args) { return mrkv_ua_ship_helper.select2_texts.inputTooLong.replace('%d', args.input.length - args.maximum); },
        inputTooShort: function (args) { return mrkv_ua_ship_helper.select2_texts.inputTooShort.replace('%d', args.minimum - args.input.length); },
        loadingMore: function () { return mrkv_ua_ship_helper.select2_texts.loadingMore; },
        maximumSelected: function (args) { return mrkv_ua_ship_helper.select2_texts.maximumSelected.replace('%d', args.maximum); },
        noResults: function () { return mrkv_ua_ship_helper.select2_texts.noResults; },
        searching: function () { return mrkv_ua_ship_helper.select2_texts.searching; },
        removeAllItems: function () { return mrkv_ua_ship_helper.select2_texts.removeAllItems; }
    };

    jQuery.fn.select2.amd.define('select2/data/extended-ajax', ['./ajax', '../utils', 'jquery'], function (AjaxAdapter, Utils, $) {
        function ExtendedAjaxAdapter($element, options) {
            this.minimumInputLength = options.get('minimumInputLength');
            this.defaultResults = options.get('defaultResults');
            ExtendedAjaxAdapter.__super__.constructor.call(this, $element, options);
        }

        Utils.Extend(ExtendedAjaxAdapter, AjaxAdapter);
        var originQuery = AjaxAdapter.prototype.query;

        ExtendedAjaxAdapter.prototype.query = function (params, callback) {
            var defaultResults = (typeof this.defaultResults == 'function') ? this.defaultResults.call(this) : this.defaultResults;
            if (defaultResults && defaultResults.length && (!params.term || params.term.length < this.minimumInputLength)) {
                var processedResults = this.processResults(defaultResults, params.term);
                callback(processedResults);
            } else {
                originQuery.call(this, params, callback);
            }
        };

        return ExtendedAjaxAdapter;
    });

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

    var default_cities = [];
    mrkv_ua_ship_helper.rozetka_city_area.map(function (item) {
        default_cities.push({ id: item.label, text: item.label, ref: item.value, area: item.area });
    });

    var rztk_settings_city_select = {
        data: default_cities,
        dataAdapter: jQuery.fn.select2.amd.require('select2/data/extended-ajax'),
        defaultResults: default_cities,
        language: languageTexts,
        minimumInputLength: 3,
        ajax: {
            delay: 800,
            url: mrkv_ua_ship_helper.ajax_url,
            type: "POST",
            data: function (params) {
                var query = { action: 'mrkv_ua_ship_rozetka_delivery_city', nonce: mrkv_ua_ship_helper.nonce };
                if (params.term && params.term.length > 2) { query.name = params.term; }
                return query;
            },
            beforeSend: function (e) {
                var $searchField = jQuery('.select2-search__field').last();
                $searchField.prop('disabled', true);
                $searchField.closest('.select2-search').append('<span class="mrkv-public-loader"></span>');
            },
            complete: function () {
                var $searchField = jQuery('.select2-search__field').last();
                $searchField.prop('disabled', false);
                $searchField.focus();
                jQuery('.mrkv-public-loader').remove();
            },
            processResults: function (json) {
                if (typeof json == 'string') {
                    var data = JSON.parse(json);
                    return {
                        results: data.map(function (item) {
                            return { id: item.label, text: item.label, ref: item.value, area: item.area, district: item.district, city: item.city_label, area_id: item.area_id, district_id: item.district_id };
                        })
                    };
                } else {
                    return {
                        results: mrkv_ua_ship_helper.rozetka_city_area.map(function (item) {
                            return { id: item.label, text: item.label, ref: item.value, area: item.area };
                        })
                    };
                }
            },
        },
    };

    function loadWarehouses(prefix, cityRef, selectedWarehouseName) {
        if (!cityRef) return;

        var warehouseId = prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_rozetka-delivery_warehouse';
        var $warehouseSelect = jQuery('#' + warehouseId);

        jQuery.ajax({
            type: 'POST',
            url: mrkv_ua_ship_helper.ajax_url,
            data: {
                action: 'mrkv_ua_ship_rozetka_delivery_warehouse',
                ref: cityRef,
                nonce: mrkv_ua_ship_helper.nonce,
            },
            beforeSend: function () {
                if ($warehouseSelect.length !== 0) {
                    $warehouseSelect.find('option').remove();
                    $warehouseSelect.addClass('mrkv-ua-shipping-loading');
                }
            },
            success: function (json) {
                var data = JSON.parse(json);

                if (data && data.length > 0) {
                    jQuery.each(data, function () {
                        $warehouseSelect.append(jQuery("<option></option>")
                            .attr('value', this.label)
                            .text(this.label)
                            .attr('data-ref', this.value)
                        );
                    });

                    let targetWarehouse = selectedWarehouseName;
                    if (!targetWarehouse || !$warehouseSelect.find('option[value="' + targetWarehouse.replace(/"/g, '\\"') + '"]').length) {
                        targetWarehouse = $warehouseSelect.find('option:first').val();
                    }

                    let targetRef = $warehouseSelect.find('option[value="' + targetWarehouse.replace(/"/g, '\\"') + '"]').attr('data-ref');

                    if (targetWarehouse) {
                        $warehouseSelect.val(targetWarehouse).trigger('change.select2');
                        dispatchReactEvent(warehouseId, targetWarehouse);
                        dispatchReactEvent(warehouseId + '_ref', targetRef || '');
                    }
                } else {
                    dispatchReactEvent(warehouseId, '');
                    dispatchReactEvent(warehouseId + '_ref', '');
                }

                $warehouseSelect.removeClass('mrkv-ua-shipping-loading');
                $warehouseSelect.data('is-loaded', true);
            }
        });
    }

    function initDynamicRozetkaFields() {
        jQuery('select[id$="-mrkv-ua-shipping-mrkv_ua_shipping_rozetka-delivery_city"]').each(function () {
            var $citySelect = jQuery(this);
            if ($citySelect.hasClass('select2-hidden-accessible')) return;

            var id = $citySelect.attr('id');
            var prefix = id.split('-')[0];

            var savedCity = jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_rozetka-delivery_city_hidden_val').val();
            var savedWarehouse = jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_rozetka-delivery_warehouse_hidden_val').val();

            if (savedCity && savedCity.trim() !== '') {
                jQuery.ajax({
                    type: "POST",
                    url: mrkv_ua_ship_helper.ajax_url,
                    data: {
                        action: 'mrkv_ua_ship_rozetka_delivery_city',
                        nonce: mrkv_ua_ship_helper.nonce,
                        name: savedCity
                    },
                    success: function (json) {
                        var data = typeof json === 'string' ? JSON.parse(json) : json;
                        var exactCityMatch = null;

                        if (data && data.length > 0) {
                            exactCityMatch = data.find(item => item.label === savedCity);
                            if (!exactCityMatch) exactCityMatch = data[0];
                        }

                        if (exactCityMatch) {
                            var $newOption = jQuery('<option>', {
                                value: exactCityMatch.label,
                                text: exactCityMatch.label
                            });
                            $citySelect.append($newOption);
                            $citySelect.val(exactCityMatch.label);
                            
                            $citySelect.select2(rztk_settings_city_select);
                            
                            var $warehouseSelect = jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_rozetka-delivery_warehouse');
                            $warehouseSelect.data('is-loaded', false);
                            
                            loadWarehouses(prefix, exactCityMatch.value, savedWarehouse);
                        } else {
                            $citySelect.select2(rztk_settings_city_select);
                        }
                    },
                    error: function () {
                        $citySelect.select2(rztk_settings_city_select);
                    }
                });
            } else {
                $citySelect.select2(rztk_settings_city_select);
                let rztk_city = $citySelect.attr('data-default');
                if (rztk_city) {
                    dispatchReactEvent(id, rztk_city);
                }
            }

            $citySelect.on('select2:opening', function () {
                jQuery(this).data('select2').$dropdown.find(':input.select2-search__field').attr('placeholder', mrkv_ua_ship_helper.city_placeholder);
            });
            $citySelect.on('select2:closing', function () {
                jQuery(this).data('select2').$dropdown.find(':input.select2-search__field').attr('placeholder', '');
            });

            $citySelect.on('select2:select', function (e) {
				if (isProgrammaticChange) return;
				
				let current_option = e.params.data;
				let cityLabel = current_option.city || current_option.id || ' ';

				var $warehouseSelect = jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_rozetka-delivery_warehouse');
				$warehouseSelect.data('is-loaded', false);

				dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_rozetka-delivery_city', current_option.id || ' ');
				dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_rozetka-delivery_city_ref', current_option.ref || ' ');
				dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_rozetka-delivery_area_name', current_option.area || ' ');
				dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_rozetka-delivery_district', current_option.district || ' ');
				dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_rozetka-delivery_city_label', cityLabel);
				dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_rozetka-delivery_area_id', current_option.area_id || ' ');
				dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_rozetka-delivery_district_id', current_option.district_id || ' ');

				jQuery(this).removeClass('ui-autocomplete-loading');

				setTimeout(function() {
					loadWarehouses(prefix, current_option.ref, null);
				}, 100);
			});
        });

        jQuery('select[id$="-mrkv-ua-shipping-mrkv_ua_shipping_rozetka-delivery_warehouse"]').each(function () {
            var $warehouseSelect = jQuery(this);
            if ($warehouseSelect.hasClass('select2-hidden-accessible')) return;

            var id = $warehouseSelect.attr('id');
            var prefix = id.split('-')[0];
            $warehouseSelect.select2();

            $warehouseSelect.on('select2:opening', function (e) {
                if (!$warehouseSelect.data('is-loaded')) { 
                    e.preventDefault(); 
                }
            });

            $warehouseSelect.on('select2:select', function () {
                if (isProgrammaticChange) return;
                
                let option_selected = jQuery(this).find('option:selected');
                let dataRef = jQuery(option_selected).attr('data-ref');
                let currentVal = jQuery(this).val();
                
                if (currentVal) {
                    dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_rozetka-delivery_warehouse', currentVal);
                    dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_rozetka-delivery_warehouse_ref', dataRef || '');
                }
            });
        });
    }

    let initTimeout;
    const observer = new MutationObserver(() => {
        clearTimeout(initTimeout);
        initTimeout = setTimeout(initDynamicRozetkaFields, 100);
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    initDynamicRozetkaFields();
});