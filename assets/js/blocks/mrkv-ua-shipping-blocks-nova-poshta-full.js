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
        if (tracker) { tracker.setValue(''); }

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
                    if (errorNotice) { errorNotice.remove(); }
                }
            }
        } catch (err) { console.error(err); }

        isProgrammaticChange = false;
    }

    var default_cities = [];
    if (mrkv_ua_ship_helper.nova_city_area) {
        mrkv_ua_ship_helper.nova_city_area.map(function (item) {
            default_cities.push({ id: item.label, text: item.label, ref: item.value, area: item.area, simple_label: item.label });
        });
    }

    var np_settings_city_select = {
        data: default_cities,
        dataAdapter: jQuery.fn.select2.amd.require('select2/data/extended-ajax'),
        defaultResults: default_cities,
        language: languageTexts,
        minimumInputLength: 3,
        ajax: {
            delay: 400,
            url: mrkv_ua_ship_helper.ajax_url,
            type: "POST",
            data: function (params) {
                var query = { action: 'mrkv_ua_ship_nova_poshta_city', nonce: mrkv_ua_ship_helper.nonce };
                if (params.term && params.term.length > 2) { query.name = params.term; }
                return query;
            },
            beforeSend: function () {
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
                            return { id: item.label, text: item.label, ref: item.value, area: item.area, simple_label: item.label_simple || item.label };
                        })
                    };
                } else {
                    return { results: default_cities };
                }
            },
        },
    };

    function loadAddressStreets(prefix, cityRef, selectedStreetName) {
        if (!cityRef) return;
        var streetSelectId = prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_nova-poshta_address_street';
        var $streetSelect = jQuery('#' + streetSelectId);
        if ($streetSelect.length === 0) return;

        jQuery.ajax({
            type: 'POST',
            url: mrkv_ua_ship_helper.ajax_url,
            data: {
                action: 'mrkv_ua_ship_nova_poshta_street_default',
                ref: cityRef,
                nonce: mrkv_ua_ship_helper.nonce,
            },
            beforeSend: function() {
                $streetSelect.find('option').remove();
                $streetSelect.addClass('mrkv-ua-shipping-loading');
            },
            success: function (json) {
                var data = (typeof json === 'string') ? JSON.parse(json) : json;
                if (data) {
                    var default_streets = [];
                    jQuery.each(data, function() {
                        default_streets.push({ id: this.label, text: this.label, ref: this.value });
                    });

                    var np_settings_street_select = { 
                        data: default_streets,
                        dataAdapter: jQuery.fn.select2.amd.require('select2/data/extended-ajax'),
                        defaultResults: default_streets,
                        language: languageTexts,
                        minimumInputLength: 3,
                        ajax: {
                            delay: 400,
                            url: mrkv_ua_ship_helper.ajax_url,
                            type: "POST",
                            data: function (params) {
                                let ref_city = jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_nova-poshta_address_city_ref').val();
                                var query = { action: 'mrkv_ua_ship_nova_poshta_street', nonce: mrkv_ua_ship_helper.nonce };
                                if (params.term && params.term.length > 2) {
                                    query.name = params.term;
                                    query.ref = ref_city;
                                }
                                return query;
                            },
                            processResults: function (streetJson) {
                                if (typeof streetJson == 'string') {
                                    var streetData = JSON.parse(streetJson);
                                    return {
                                        results: streetData.map(function(item) { return { id: item.label, text: item.label, ref: item.value }; })
                                    };
                                } else {
                                    return { results: default_streets };
                                }
                            },
                        },
                    };

                    $streetSelect.select2(np_settings_street_select);

                    if (selectedStreetName) {
                        let exactStreetOpt = default_streets.find(s => s.id === selectedStreetName);
                        if (exactStreetOpt) {
                            $streetSelect.val(selectedStreetName).trigger('change.select2');
                            dispatchReactEvent(streetSelectId, selectedStreetName);
                            dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_nova-poshta_address_street_ref', exactStreetOpt.ref);
                        }
                    }
                }

                if (data && data.length == 1 && data[0].value == 'none') {
                    jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_nova-poshta_address_street_field .select2-selection__rendered').hide();
                    setTimeout(function(){ 
                        jQuery('#' + prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_nova-poshta_address_street_field .select2-selection__rendered').text(mrkv_ua_ship_helper.city_text_weight).show();
                    }, 10);
                }
                $streetSelect.removeClass('mrkv-ua-shipping-loading');
            }
        });
    }

    function mrkvUaShipNPGetAddress(prefix) {
        let method = 'mrkv_ua_shipping_nova-poshta_address';
        let sender_street_ref = jQuery('#' + prefix + '-mrkv-ua-shipping-' + method + '_street_ref').val();
        let sender_building_number = jQuery('#' + prefix + '-mrkv-ua-shipping-' + method + '_house').val();
        let sender_flat = jQuery('#' + prefix + '-mrkv-ua-shipping-' + method + '_flat').val();

        if (sender_street_ref && sender_building_number && sender_flat) {
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
                    if (data) {
                        dispatchReactEvent(prefix + '-mrkv-ua-shipping-' + method + '_address_ref', data.replace(/['"]+/g, ''));
                    }                  
                }
            });
        }
    }

    function initDynamicNovaPoshtaFields() {
        
        jQuery('select[id$="-mrkv-ua-shipping-mrkv_ua_shipping_nova-poshta_city"], select[id$="-mrkv-ua-shipping-mrkv_ua_shipping_nova-poshta_poshtamat_city"], select[id$="-mrkv-ua-shipping-mrkv_ua_shipping_nova-poshta_address_city"]').each(function () {
            var $citySelect = jQuery(this);
            if ($citySelect.hasClass('select2-hidden-accessible')) return;

            var id = $citySelect.attr('id');
            var prefix = id.split('-')[0];
            
            var methodKey = 'mrkv_ua_shipping_nova-poshta';
            if (id.includes('poshtamat')) methodKey = 'mrkv_ua_shipping_nova-poshta_poshtamat';
            if (id.includes('address')) methodKey = 'mrkv_ua_shipping_nova-poshta_address';

            var savedCity = jQuery('#' + prefix + '-mrkv-ua-shipping-' + methodKey + '_city_hidden_val').val();
            
            var savedTargetId = prefix + '-mrkv-ua-shipping-' + methodKey + '_warehouse_hidden_val';
            if (id.includes('poshtamat')) savedTargetId = prefix + '-mrkv-ua-shipping-' + methodKey + '_name_hidden_val';
            if (id.includes('address')) savedTargetId = prefix + '-mrkv-ua-shipping-' + methodKey + '_street_hidden_val';
            var savedTargetValue = jQuery('#' + savedTargetId).val();

            if (savedCity && savedCity.trim() !== '') {
                jQuery.ajax({
                    type: "POST",
                    url: mrkv_ua_ship_helper.ajax_url,
                    data: { action: 'mrkv_ua_ship_nova_poshta_city', nonce: mrkv_ua_ship_helper.nonce, name: savedCity },
                    success: function (json) {
                        var data = typeof json === 'string' ? JSON.parse(json) : json;
                        var exactCityMatch = (data && data.length > 0) ? (data.find(item => item.label === savedCity) || data[0]) : null;

                        if (exactCityMatch) {
                            $citySelect.append(jQuery('<option>', { value: exactCityMatch.label, text: exactCityMatch.label })).val(exactCityMatch.label);
                            $citySelect.select2(np_settings_city_select);
                            
                            if (methodKey.includes('address')) {
                                loadAddressStreets(prefix, exactCityMatch.value, savedTargetValue);
                            } else {
                                var targetWhSelectId = id.includes('poshtamat') ? prefix + '-mrkv-ua-shipping-' + methodKey + '_name' : prefix + '-mrkv-ua-shipping-' + methodKey + '_warehouse';
                                var $whSelect = jQuery('#' + targetWhSelectId);
                                if ($whSelect.length && savedTargetValue) {
                                    if (!$whSelect.find('option[value="' + savedTargetValue.replace(/"/g, '\\"') + '"]').length) {
                                        $whSelect.append(jQuery('<option>', { value: savedTargetValue, text: savedTargetValue }));
                                    }
                                    $whSelect.val(savedTargetValue).trigger('change.select2');
                                }
                            }
                        } else {
                            $citySelect.select2(np_settings_city_select);
                        }
                    },
                    error: function () { $citySelect.select2(np_settings_city_select); }
                });
            } else {
                $citySelect.select2(np_settings_city_select);
                let defaultCity = $citySelect.attr('data-default');
                if (defaultCity) { dispatchReactEvent(id, defaultCity); }
            }

            $citySelect.on('select2:opening', function () { jQuery(this).data('select2').$dropdown.find(':input.select2-search__field').attr('placeholder', mrkv_ua_ship_helper.city_placeholder); });
            $citySelect.on('select2:closing', function () { jQuery(this).data('select2').$dropdown.find(':input.select2-search__field').attr('placeholder', ''); });

            $citySelect.on('select2:select', function (e) {
                if (isProgrammaticChange) return;
                let current_option = e.params.data;
                let simpleLabel = current_option.simple_label || current_option.id || ' ';

                dispatchReactEvent(prefix + '-mrkv-ua-shipping-' + methodKey + '_city', current_option.id || ' ');
                dispatchReactEvent(prefix + '-mrkv-ua-shipping-' + methodKey + '_city_ref', current_option.ref || ' ');
                dispatchReactEvent(prefix + '-mrkv-ua-shipping-' + methodKey + '_area_name', current_option.area || ' ');
                dispatchReactEvent(prefix + '-mrkv-ua-shipping-' + methodKey + '_city_label', simpleLabel);
                
                if (methodKey.includes('address')) {
                    dispatchReactEvent(prefix + '-mrkv-ua-shipping-' + methodKey + '_street_ref', '');
                    dispatchReactEvent(prefix + '-mrkv-ua-shipping-' + methodKey + '_house', '');
                    dispatchReactEvent(prefix + '-mrkv-ua-shipping-' + methodKey + '_flat', '');
                    setTimeout(function() { loadAddressStreets(prefix, current_option.ref, null); }, 100);
                } else {
                    let isPsh = id.includes('poshtamat');
                    var targetWhSelectId = isPsh ? prefix + '-mrkv-ua-shipping-' + methodKey + '_name' : prefix + '-mrkv-ua-shipping-' + methodKey + '_warehouse';
                    var $whSelect = jQuery('#' + targetWhSelectId);
                    
                    dispatchReactEvent(targetWhSelectId, '');
                    dispatchReactEvent(prefix + '-mrkv-ua-shipping-' + methodKey + (isPsh ? '_poshtamat_ref' : '_warehouse_ref'), '');
                    dispatchReactEvent(prefix + '-mrkv-ua-shipping-' + methodKey + (isPsh ? '_poshtamat_number' : '_warehouse_number'), '');
                    
                    if ($whSelect.data('select2')) {
                        var emptyOptionText = $whSelect.find('option[value=""]').text() || '';
                        $whSelect.empty().append(new Option(emptyOptionText, '', true, true)).val('').trigger('change.select2');
                    }
                }
            });
        });

        jQuery('select[id$="-mrkv-ua-shipping-mrkv_ua_shipping_nova-poshta_warehouse"], select[id$="-mrkv-ua-shipping-mrkv_ua_shipping_nova-poshta_poshtamat_name"]').each(function () {
            var $warehouseSelect = jQuery(this);
            if ($warehouseSelect.hasClass('select2-hidden-accessible')) return;

            var id = $warehouseSelect.attr('id');
            var prefix = id.split('-')[0];
            var isPoshtamat = id.includes('poshtamat_name');
            var methodKey = isPoshtamat ? 'mrkv_ua_shipping_nova-poshta_poshtamat' : 'mrkv_ua_shipping_nova-poshta';
            var typeWh = isPoshtamat ? mrkv_ua_ship_helper.nova_poshtamat_type : mrkv_ua_ship_helper.nova_warehouse_type;

            var extendedLanguage = jQuery.extend({}, languageTexts, {
                noResults: function () {
                    let city_ref = jQuery('#' + prefix + '-mrkv-ua-shipping-' + methodKey + '_city_ref').val();
                    
                    if (!city_ref || city_ref.trim() === '') {
                        return mrkv_ua_ship_helper.select2_texts.noResults; 
                    }
                    if (typeof languageTexts.noResults === 'function') {
                        return languageTexts.noResults();
                    } else if (typeof languageTexts.noResults === 'string') {
                        return languageTexts.noResults;
                    }

                    return mrkv_ua_ship_helper.select2_texts.noResults;
                }
            });
            
            var np_settings_warehouse_select = {
                language: extendedLanguage,
                minimumInputLength: 0,
                ajax: {
                    delay: 250,
                    url: mrkv_ua_ship_helper.ajax_url,
                    type: "POST",
                    transport: function (params, success, failure) {
                        let city_ref = jQuery('#' + prefix + '-mrkv-ua-shipping-' + methodKey + '_city_ref').val();

                        if (!city_ref || city_ref.trim() === '') {
                            success([]);
                            return null;
                        }

                        // Return the jqXHR itself: select2 aborts the previous request only if the transport result has abort()
                        var request = jQuery.ajax(params);
                        request.then(success);
                        request.fail(failure);
                        return request;
                    },
                    data: function (params) {
                        let city_ref = jQuery('#' + prefix + '-mrkv-ua-shipping-' + methodKey + '_city_ref').val();
                        return {
                            action: 'mrkv_ua_ship_nova_poshta_warehouse',
                            ref: city_ref,
                            warehouse_type: typeWh,
                            search_by: 'yes',
                            source_query: 'front',
                            name: params.term || '',
                            page: params.page || 1,
                            default_content: 'part',
                            nonce: mrkv_ua_ship_helper.nonce,
                        };
                    },
                    processResults: function (json, params) {
                        params.page = params.page || 1;
                        var rawData = (typeof json === 'string') ? JSON.parse(json) : json;

                        var formattedResults = rawData.map(function(item) {
                            return { 
                                id: item.label, 
                                text: item.label, 
                                ref: item.value, 
                                number: item.number 
                            };
                        });

                        var pageSize = 20;
                        var hasMore = rawData.length >= pageSize;

                        return {
                            results: formattedResults,
                            pagination: {
                                more: hasMore
                            }
                        };
                    },
                    cache: true
                }
            };

            $warehouseSelect.select2(np_settings_warehouse_select);
            
            $warehouseSelect.on('select2:opening', function (e) { 
                let city_ref = jQuery('#' + prefix + '-mrkv-ua-shipping-' + methodKey + '_city_ref').val();
                if (!city_ref || city_ref.trim() === '') {
                    return mrkv_ua_ship_helper.select2_texts.noResults;
                }
            });

            $warehouseSelect.on('select2:select', function (e) {
                if (isProgrammaticChange) return;
                let current_option = e.params.data;
                
                if (current_option && current_option.id) {
                    dispatchReactEvent(id, current_option.id);
                    dispatchReactEvent(prefix + '-mrkv-ua-shipping-' + methodKey + (isPoshtamat ? '_poshtamat_ref' : '_warehouse_ref'), current_option.ref || '');
                    dispatchReactEvent(prefix + '-mrkv-ua-shipping-' + methodKey + (isPoshtamat ? '_poshtamat_number' : '_warehouse_number'), current_option.number || '');
                }
            });
        });

        jQuery('select[id$="-mrkv-ua-shipping-mrkv_ua_shipping_nova-poshta_address_street"]').each(function () {
            var $streetSelect = jQuery(this);
            if ($streetSelect.hasClass('select2-hidden-accessible')) return;

            var id = $streetSelect.attr('id');
            var prefix = id.split('-')[0];

            $streetSelect.select2();
            $streetSelect.on('select2:opening', function () { jQuery(this).data('select2').$dropdown.find(':input.select2-search__field').attr('placeholder', mrkv_ua_ship_helper.city_placeholder); });
            $streetSelect.on('select2:closing', function () { jQuery(this).data('select2').$dropdown.find(':input.select2-search__field').attr('placeholder', ''); });

            $streetSelect.on('select2:select', function (e) {
                if (isProgrammaticChange) return;
                let current_option = e.params.data;
                dispatchReactEvent(id, current_option.id);
                dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_nova-poshta_address_street_ref', current_option.ref || '');
                mrkvUaShipNPGetAddress(prefix);
            });
        });

        jQuery('input[id$="-mrkv-ua-shipping-mrkv_ua_shipping_nova-poshta_address_flat"]').each(function() {
            if (jQuery(this).data('has-watcher')) return;
            var prefix = jQuery(this).attr('id').split('-')[0];
            let typingTimer;

            jQuery(this).data('has-watcher', true).on('keyup input', function() {
                clearTimeout(typingTimer);
                let val = jQuery(this).val();
                typingTimer = setTimeout(function() {
                    mrkvUaShipNPGetAddress(prefix);
                }, 1000);
            });
        });

        jQuery('input[id$="-mrkv-ua-shipping-mrkv_ua_shipping_nova-poshta_international_warehouse"]').each(function() {
            var $interInput = jQuery(this);
            if ($interInput.data('has-autocomplete')) return;
            var prefix = $interInput.attr('id').split('-')[0];

            $interInput.data('has-autocomplete', true).autocomplete({
                source: function(request, response) {
                    $interInput.removeClass('ui-autocomplete-loading');
                    if (request.term.length > 2) {
                        var country_sender = jQuery('#shipping_country').val() || jQuery('#billing_country').val() || 'UA';
                        $interInput.addClass('ui-autocomplete-loading');
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
                                response(Array.isArray(data) ? data : data.response);
                                $interInput.removeClass('ui-autocomplete-loading');
                            }
                        });
                    }
                },
                select: function(event, ui) {
                    event.preventDefault();
                    $interInput.val(ui.item.label);
                    dispatchReactEvent($interInput.attr('id'), ui.item.label);
                    dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_nova-poshta_international_warehouse_ref', ui.item.value);
                    dispatchReactEvent(prefix + '-mrkv-ua-shipping-mrkv_ua_shipping_nova-poshta_international_warehouse_number', ui.item.number);
                },
                minLength: 0,
                delay: 0,
            }).focus(function() {
                jQuery(this).data("uiAutocomplete").search(jQuery(this).val());
                $interInput.removeClass('ui-autocomplete-loading');
            });
        });

        if (mrkv_ua_ship_helper.nova_warehouse_text && mrkv_ua_ship_helper.nova_warehouse_text !== '') {
            jQuery('label[for$="nova-poshta_warehouse"], label[for$="nova-poshta_poshtamat_name"]').text(mrkv_ua_ship_helper.nova_warehouse_text);
        }

        jQuery('input[id$="-mrkv-ua-shipping-mrkv_ua_shipping_nova-poshta_address_patronymic"]').each(function() {
            if (jQuery(this).data('has-watcher')) return;
            
            let id = jQuery(this).attr('id');
            if (mrkv_ua_ship_helper.nova_middlename_exclude == 'yes') {
                jQuery(this).closest('.wc-block-components-text-input').hide();
            }
        });
    }

    let initTimeout;
    const observer = new MutationObserver(() => {
        clearTimeout(initTimeout);
        initTimeout = setTimeout(initDynamicNovaPoshtaFields, 100);
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    initDynamicNovaPoshtaFields();
});