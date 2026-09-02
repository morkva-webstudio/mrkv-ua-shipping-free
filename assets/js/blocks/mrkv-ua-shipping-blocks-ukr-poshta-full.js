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
    if (mrkv_ua_ship_helper.ukr_city_area) {
        mrkv_ua_ship_helper.ukr_city_area.map(function (item) {
            default_cities.push({ id: item.label, text: item.label, ref: item.value, area: item.area });
        });
    }

    var up_settings_city_select = {
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
                var query = { action: 'mrkv_ua_ship_ukr_poshta_city', nonce: mrkv_ua_ship_helper.nonce };
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
                            return { id: item.label, text: item.label, ref: item.value, area: item.area, area_id: item.area_id, district_id: item.district_id };
                        })
                    };
                } else {
                    return {
                        results: mrkv_ua_ship_helper.ukr_city_area.map(function (item) {
                            return { id: item.label, text: item.label, ref: item.value, area: item.area };
                        })
                    };
                }
            },
        },
    };

    function loadUpWarehouses(prefix, cityRef, selectedWarehouseName) {
        if (!cityRef) return;

        var warehouseId = prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_warehouse';
        var $warehouseSelect = jQuery('#' + warehouseId);

        jQuery.ajax({
            type: 'POST',
            url: mrkv_ua_ship_helper.ajax_url,
            data: {
                action: 'mrkv_ua_ship_ukr_poshta_warehouse',
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
                        getUpWarehouseAddressId(prefix, targetRef);
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

    function loadUpStreets(prefix, cityRef, selectedStreet) {
        if (!cityRef) return;
        var streetId = prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_street';
        var $streetSelect = jQuery('#' + streetId);

        jQuery.ajax({
            type: 'POST',
            url: mrkv_ua_ship_helper.ajax_url,
            data: { action: 'mrkv_ua_ship_ukr_poshta_street', ref: cityRef, nonce: mrkv_ua_ship_helper.nonce },
            beforeSend: function () {
                if ($streetSelect.length !== 0) {
                    $streetSelect.find('option:not(:first-child)').remove();
                    $streetSelect.addClass('mrkv-ua-shipping-loading');
                }
            },
            success: function (json) {
                var data = JSON.parse(json);
                if (data && data.length > 0) {
                    jQuery.each(data, function () {
                        $streetSelect.append(jQuery("<option></option>").attr('value', this.label).text(this.label).attr('data-ref', this.value));
                    });

                    if (selectedStreet && $streetSelect.find('option[value="' + selectedStreet.replace(/"/g, '\\"') + '"]').length) {
                        $streetSelect.val(selectedStreet).trigger('change.select2');
                        dispatchReactEvent(streetId, selectedStreet);
                        let targetRef = $streetSelect.find('option[value="' + selectedStreet.replace(/"/g, '\\"') + '"]').attr('data-ref');
                        dispatchReactEvent(streetId + '_ref', targetRef || '');
                        loadUpHouses(prefix, targetRef, null); // Load houses after street
                    }
                }
                $streetSelect.removeClass('mrkv-ua-shipping-loading');
                $streetSelect.data('is-loaded', true);
            }
        });
    }

    function loadUpHouses(prefix, streetRef, selectedHouse) {
        if (!streetRef) return;
        var houseId = prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_house';
        var $houseSelect = jQuery('#' + houseId);

        jQuery.ajax({
            type: 'POST',
            url: mrkv_ua_ship_helper.ajax_url,
            data: { action: 'mrkv_ua_ship_ukr_poshta_house', ref: streetRef, nonce: mrkv_ua_ship_helper.nonce },
            beforeSend: function () {
                if ($houseSelect.length !== 0) {
                    $houseSelect.find('option:not(:first-child)').remove();
                    $houseSelect.addClass('mrkv-ua-shipping-loading');
                }
            },
            success: function (json) {
                var data = JSON.parse(json);
                if (data && data.length > 0) {
                    jQuery.each(data, function () {
                        $houseSelect.append(jQuery("<option></option>").attr('value', this.label).text(this.label).attr('data-ref', this.value));
                    });

                    if (selectedHouse && $houseSelect.find('option[value="' + selectedHouse.replace(/"/g, '\\"') + '"]').length) {
                        $houseSelect.val(selectedHouse).trigger('change.select2');
                        dispatchReactEvent(houseId, selectedHouse);
                        let targetRef = $houseSelect.find('option[value="' + selectedHouse.replace(/"/g, '\\"') + '"]').attr('data-ref');
                        dispatchReactEvent(houseId + '_ref', targetRef || '');
                    }
                }
                $houseSelect.removeClass('mrkv-ua-shipping-loading');
                $houseSelect.data('is-loaded', true);
            }
        });
    }

    function getUpWarehouseAddressId(prefix, warehouseRef) {
        if(!warehouseRef) return;
        jQuery.ajax({
            type: 'POST',
            url: mrkv_ua_ship_helper.ajax_url,
            data: { action: 'mrkv_ua_ship_ukr_poshta_warehouse_id', warehouse_name: warehouseRef, nonce: mrkv_ua_ship_helper.nonce },
            success: function (data) {
                if (data) dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_ref', data.replace(/['"]+/g, ''));
            }
        });
    }

    function getUpCourierAddressId(prefix) {
        let postcode = jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_house_ref_hidden_val').val() || jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_house_ref').val();
        let country = 'UA';
        let region = jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_area_name_hidden_val').val() || jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_area_name').val();
        let city = jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_city_hidden_val').val() || jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_city').val();
        let street = jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_street_hidden_val').val() || jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_street').val();
        let apartment_number = jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_house_hidden_val').val() || jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_house').val();

        if (postcode && country && region && city && street && apartment_number) {
            jQuery.ajax({
                type: 'POST',
                url: mrkv_ua_ship_helper.ajax_url,
                data: { action: 'mrkv_ua_ship_ukr_poshta_address_id', postcode: postcode, country: country, region: region, city: city, street: street, apartment_number: apartment_number, nonce: mrkv_ua_ship_helper.nonce },
                success: function (data) {
                    if (data) dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_address_ref', data.replace(/['"]+/g, ''));
                }
            });
        }
    }

    function initDynamicUkrPoshtaFields() {
        
        jQuery('select[id$="-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_city"]').each(function () {
            var $citySelect = jQuery(this);
            if ($citySelect.hasClass('select2-hidden-accessible')) return;

            var id = $citySelect.attr('id');
            var prefix = id.split('-')[0];

            var savedCity = jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_city_hidden_val').val();
            var savedWarehouse = jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_warehouse_hidden_val').val();

            if (savedCity && savedCity.trim() !== '') {
                jQuery.ajax({
                    type: "POST",
                    url: mrkv_ua_ship_helper.ajax_url,
                    data: { action: 'mrkv_ua_ship_ukr_poshta_city', nonce: mrkv_ua_ship_helper.nonce, name: savedCity },
                    success: function (json) {
                        var data = typeof json === 'string' ? JSON.parse(json) : json;
                        var exactCityMatch = null;
                        if (data && data.length > 0) {
                            exactCityMatch = data.find(item => item.label === savedCity);
                            if (!exactCityMatch) exactCityMatch = data[0];
                        }
                        if (exactCityMatch) {
                            var $newOption = jQuery('<option>', { value: exactCityMatch.label, text: exactCityMatch.label });
                            $citySelect.append($newOption);
                            $citySelect.val(exactCityMatch.label);
                            $citySelect.select2(up_settings_city_select);
                            
                            var $warehouseSelect = jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_warehouse');
                            $warehouseSelect.data('is-loaded', false);
                            loadUpWarehouses(prefix, exactCityMatch.value, savedWarehouse);
                        } else {
                            $citySelect.select2(up_settings_city_select);
                        }
                    },
                    error: function () { $citySelect.select2(up_settings_city_select); }
                });
            } else {
                $citySelect.select2(up_settings_city_select);
                let up_city = $citySelect.attr('data-default');
                if (up_city) dispatchReactEvent(id, up_city);
            }

            $citySelect.on('select2:opening', function () { jQuery(this).data('select2').$dropdown.find(':input.select2-search__field').attr('placeholder', mrkv_ua_ship_helper.city_placeholder); });
            $citySelect.on('select2:closing', function () { jQuery(this).data('select2').$dropdown.find(':input.select2-search__field').attr('placeholder', ''); });

            $citySelect.on('select2:select', function (e) {
                if (isProgrammaticChange) return;
                let current_option = e.params.data;

                var $warehouseSelect = jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_warehouse');
                $warehouseSelect.data('is-loaded', false);

                dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_city', current_option.id || '');
                dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_city_ref', current_option.ref || '');
                dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_area_name', current_option.area || '');

                jQuery(this).removeClass('ui-autocomplete-loading');

                setTimeout(function() { loadUpWarehouses(prefix, current_option.ref, null); }, 100);
            });
        });

        jQuery('select[id$="-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_warehouse"]').each(function () {
            var $warehouseSelect = jQuery(this);
            if ($warehouseSelect.hasClass('select2-hidden-accessible')) return;

            var id = $warehouseSelect.attr('id');
            var prefix = id.split('-')[0];
            $warehouseSelect.select2();

            $warehouseSelect.on('select2:opening', function (e) {
                if (!$warehouseSelect.data('is-loaded')) { e.preventDefault(); }
            });

            $warehouseSelect.on('select2:select', function () {
                if (isProgrammaticChange) return;
                let option_selected = jQuery(this).find('option:selected');
                let dataRef = jQuery(option_selected).attr('data-ref');
                let currentVal = jQuery(this).val();
                
                if (currentVal) {
                    dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_warehouse', currentVal);
                    dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_warehouse_ref', dataRef || '');
                    getUpWarehouseAddressId(prefix, dataRef);
                }
            });
        });

        jQuery('select[id$="-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_city"]').each(function () {
            var $citySelect = jQuery(this);
            if ($citySelect.hasClass('select2-hidden-accessible')) return;

            var id = $citySelect.attr('id');
            var prefix = id.split('-')[0];

            var savedCity = jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_city_hidden_val').val();
            var savedStreet = jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_street_hidden_val').val();

            if (savedCity && savedCity.trim() !== '') {
                jQuery.ajax({
                    type: "POST",
                    url: mrkv_ua_ship_helper.ajax_url,
                    data: { action: 'mrkv_ua_ship_ukr_poshta_city', nonce: mrkv_ua_ship_helper.nonce, name: savedCity },
                    success: function (json) {
                        var data = typeof json === 'string' ? JSON.parse(json) : json;
                        var exactCityMatch = null;
                        if (data && data.length > 0) {
                            exactCityMatch = data.find(item => item.label === savedCity);
                            if (!exactCityMatch) exactCityMatch = data[0];
                        }
                        if (exactCityMatch) {
                            var $newOption = jQuery('<option>', { value: exactCityMatch.label, text: exactCityMatch.label });
                            $citySelect.append($newOption);
                            $citySelect.val(exactCityMatch.label);
                            $citySelect.select2(up_settings_city_select);
                            
                            var $streetSelect = jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_street');
                            $streetSelect.data('is-loaded', false);
                            loadUpStreets(prefix, exactCityMatch.value, savedStreet);
                        } else {
                            $citySelect.select2(up_settings_city_select);
                        }
                    },
                    error: function () { $citySelect.select2(up_settings_city_select); }
                });
            } else {
                $citySelect.select2(up_settings_city_select);
            }

            $citySelect.on('select2:opening', function () { jQuery(this).data('select2').$dropdown.find(':input.select2-search__field').attr('placeholder', mrkv_ua_ship_helper.city_placeholder); });
            $citySelect.on('select2:closing', function () { jQuery(this).data('select2').$dropdown.find(':input.select2-search__field').attr('placeholder', ''); });

            $citySelect.on('select2:select', function (e) {
                if (isProgrammaticChange) return;
                let current_option = e.params.data;

                var $streetSelect = jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_street');
                $streetSelect.data('is-loaded', false);

                dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_city', current_option.id || '');
                dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_city_ref', current_option.ref || '');
                dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_area_name', current_option.area || '');
                dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_area_id', current_option.area_id || '');
                dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_district_id', current_option.district_id || '');

                jQuery(this).removeClass('ui-autocomplete-loading');
                setTimeout(function() { loadUpStreets(prefix, current_option.ref, null); }, 100);
            });
        });

        jQuery('select[id$="-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_street"]').each(function () {
            var $streetSelect = jQuery(this);
            if ($streetSelect.hasClass('select2-hidden-accessible')) return;

            var id = $streetSelect.attr('id');
            var prefix = id.split('-')[0];
            $streetSelect.select2();

            $streetSelect.on('select2:opening', function (e) {
                if (!$streetSelect.data('is-loaded')) { e.preventDefault(); }
            });

            $streetSelect.on('select2:select', function () {
                if (isProgrammaticChange) return;
                let option_selected = jQuery(this).find('option:selected');
                let dataRef = jQuery(option_selected).attr('data-ref');
                let currentVal = jQuery(this).val();
                
                if (currentVal) {
                    dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_street', currentVal);
                    dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_street_ref', dataRef || '');
                    
                    var $houseSelect = jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_house');
                    $houseSelect.data('is-loaded', false);
                    setTimeout(function() { loadUpHouses(prefix, dataRef, null); }, 100);
                }
            });
        });

        jQuery('select[id$="-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_house"]').each(function () {
            var $houseSelect = jQuery(this);
            if ($houseSelect.hasClass('select2-hidden-accessible')) return;

            var id = $houseSelect.attr('id');
            var prefix = id.split('-')[0];
            $houseSelect.select2();

            $houseSelect.on('select2:opening', function (e) {
                if (!$houseSelect.data('is-loaded')) { e.preventDefault(); }
            });

            $houseSelect.on('select2:select', function () {
                if (isProgrammaticChange) return;
                let option_selected = jQuery(this).find('option:selected');
                let dataRef = jQuery(option_selected).attr('data-ref');
                let currentVal = jQuery(this).val();
                
                if (currentVal) {
                    dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_house', currentVal);
                    dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_house_ref', dataRef || '');
                    
                    dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_flat', '');
                    dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_address_ref', '');
                }
            });
        });
    }

    let upFlatTypingTimer;
    let upFlatDoneTypingInterval = 2000;

    jQuery(document.body).on('keyup', 'input[id$="-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_flat"]', function() {
        clearTimeout(upFlatTypingTimer);
        var id = jQuery(this).attr('id');
        var prefix = id.split('-')[0];
        upFlatTypingTimer = setTimeout(function() { getUpCourierAddressId(prefix); }, upFlatDoneTypingInterval);
    });

    jQuery(document.body).on('keydown', 'input[id$="-mrkv-ua-shipping-mrkv_ua_shipping_ukr-poshta_address_flat"]', function() {
        clearTimeout(upFlatTypingTimer);
    });

    let initTimeout;
    const observer = new MutationObserver(() => {
        clearTimeout(initTimeout);
        initTimeout = setTimeout(initDynamicUkrPoshtaFields, 100);
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    initDynamicUkrPoshtaFields();
});