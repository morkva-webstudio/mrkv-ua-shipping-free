window.addEventListener('load', function () {
    if (typeof wp === 'undefined' || !wp.data || typeof wp.data.select !== 'function') {
        return;
    }

    const { select, subscribe, dispatch } = wp.data;
    const config = window.mrkvShippingConfig ? window.mrkvShippingConfig.list : null;
    const providerLocation = window.mrkvShippingConfig ? window.mrkvShippingConfig.provider_location : {};
    const patranomicPositions = window.mrkvShippingConfig ? window.mrkvShippingConfig.patranomic_positions : {};
    const requiredText = window.mrkvShippingConfig ? window.mrkvShippingConfig.required_text : null;
    const STORAGE_KEY = 'mrkv_last_shipping_method';
    const originalFetch = window.fetch;
    let lastCheckboxState = null;
    let previousCountry = null;
    let isInitialLoad = true;
    let isUnderFieldsLoaded = false;
    let isFirstSubscriptionPass = true;
    let isClearingPostcode = false;
    let observer = null;
    let isReloadingShippingMethods = false;
    let lastRawMethod = null;
    window.mrkvErrorsCache = {};

    window.fetch = async function (...args) {
        let [resource, configFetch] = args;

        if (typeof resource === 'string' && resource.includes('/wc/store/') && resource.includes('/checkout')) {
            const currentMethod = getCurrentMethod();
            const activeGroup = getActiveBlockType();

            if (currentMethod && (currentMethod.startsWith('mrkv_ua_shipping') || currentMethod.startsWith('local_pickup'))) {
                configFetch = configFetch || {};
                configFetch.headers = configFetch.headers || {};

                if (configFetch.headers instanceof Headers) {
                    configFetch.headers.append('X-MRKV-Shipping-Method', currentMethod);
                    configFetch.headers.append('X-MRKV-Shipping-Group', activeGroup);
                } else {
                    configFetch.headers['X-MRKV-Shipping-Method'] = currentMethod;
                    configFetch.headers['X-MRKV-Shipping-Group'] = activeGroup;
                }
            }
        }

        return originalFetch(resource, configFetch);
    };

    function returnFieldsToOriginalContainers() {
        const configData = window.mrkvShippingConfig || {};
        if (!configData.is_under_methods) return;
        const addressForm = document.getElementById('order');
        if (!addressForm) return;

        if (observer) observer.disconnect();

        document.querySelectorAll('.mrkv-fields-under-method-wrapper').forEach(wrapper => {

            while (wrapper.firstChild) {
                addressForm.appendChild(wrapper.firstChild);
            }

            wrapper.remove();
        });

        if (observer) {
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    }

    function distributeFieldsToMethods() {
        if (isReloadingShippingMethods) {
            return;
        }
        const configData = window.mrkvShippingConfig || {};
        if (!configData.is_under_methods) return;

        const addressForm = document.getElementById('order');
        if (!addressForm) return;

        const methodRadios = document.querySelectorAll('input[type="radio"][value*="mrkv_ua_shipping"]');
        
        if (observer) observer.disconnect();

        methodRadios.forEach(radio => {
            const rawValue = radio.value || '';
            const methodSlug = rawValue.split(':')[0].replace(/_\d+$/, '');

            const methodContainer = radio.closest('.wc-block-components-radio-control-item') || radio.parentElement;
            if (!methodContainer) return;

            let wrapper = methodContainer.querySelector('.mrkv-fields-under-method-wrapper');
            if (!wrapper) {
                wrapper = document.createElement('div');
                wrapper.className = 'mrkv-fields-under-method-wrapper';
                wrapper.style.setProperty('width', '100%', 'important');
                wrapper.style.setProperty('transition', 'all 0.3s ease', 'important');
                methodContainer.appendChild(wrapper);
            }

            const provider = getProviderByMethod(methodSlug);
            if (!provider) return;

            const checkoutFields =
                config[provider]?.method?.[methodSlug]?.checkout_fields || {};

            const inputs = [];
            Object.keys(checkoutFields).forEach(fieldKey => {
                const expected = `${methodSlug}${fieldKey}`;
                
                addressForm
                    .querySelectorAll('[name*="mrkv-ua-shipping/"]')
                    .forEach(input => {

                        if (input.name.endsWith(expected)) {
                            inputs.push(input);
                        }

                        if (input.name.endsWith(expected + '_hidden_val')) {
                            inputs.push(input);
                        }
                    });
            });

            let hasVisible = false;

            inputs.forEach(input => {
                if (!isHiddenField(input)) {
                    const container = getFieldRoot(input);
                    if (!container) return;

                    if (wrapper !== container.parentNode) {
                        wrapper.appendChild(container);
                    }

                    if (container.style.display !== 'none') {
                        hasVisible = true;
                    }
                }
            });

            if (hasVisible) {
                wrapper.style.setProperty('padding-top', '15px', 'important');
                wrapper.style.setProperty('padding-bottom', '15px', 'important');
                wrapper.style.setProperty('display', 'block', 'important');
            } else {
                wrapper.style.removeProperty('padding-top');
                wrapper.style.removeProperty('padding-bottom');
                wrapper.style.setProperty('display', 'none', 'important');
            }
        });

        if (observer) observer.observe(document.body, { childList: true, subtree: true });
    }

    function getActiveBlockType() {
        const checkbox = document.querySelector('.wc-block-checkout__use-address-for-billing input[type="checkbox"]');
        if (!checkbox) return 'billing';
        return checkbox.checked ? 'shipping' : 'billing';
    }

    function getProviderByMethod(methodSlug) {
        if (!config || !methodSlug) return null;
        for (const provider in config) {
            if (config[provider]?.method?.[methodSlug]) {
                return provider;
            }
        }
        return null;
    }

    function getFieldPrefix(methodSlug, fieldKey = '') {
        const provider = getProviderByMethod(methodSlug);
        let location = (provider && providerLocation[provider]) ? providerLocation[provider] : 'address';

        if (fieldKey && fieldKey.includes('_patronymic') && provider && patranomicPositions[provider]) {
            const patronymicPosition = patranomicPositions[provider];
            if (patronymicPosition !== 'default') {
                location = 'address';
            }
        }

        if (location === 'address') {
            return getActiveBlockType() + '_';
        }
        return location + '_';
    }

    function initMrkvErrorsCache() {
        const validationSelect = select('wc/store/validation');
        if (validationSelect) {
            const initialErrors = validationSelect.getValidationErrors() || {};
            const coreFields = [
                'billing-address-1', 'billing-address-2', 'billing-city', 'billing-state', 'billing-postcode',
                'shipping-address-1', 'shipping-address-2', 'shipping-city', 'shipping-state', 'shipping-postcode',
                'billing_address_1', 'billing_address_2', 'billing_city', 'billing_state', 'billing_postcode',
                'shipping_address_1', 'shipping_address_2', 'shipping_city', 'shipping_state', 'shipping_postcode'
            ];
            
            window.mrkvErrorsCache = {};
            Object.keys(initialErrors).forEach(key => {
                if (coreFields.includes(key) || key.includes('mrkv-ua-shipping/')) {
                    window.mrkvErrorsCache[key] = initialErrors[key];
                }
            });
        }
        addDefaultErrorsForAllMethods();
    }

    function addDefaultErrorsForAllMethods() {
        if (!config) return;

        for (const provider in config) {
            const methods = config[provider]?.method || {};
            const location = providerLocation[provider] || 'address';
            const patronymicPosition = patranomicPositions[provider] || 'default';

            for (const methodSlug in methods) {
                if (!methodSlug.startsWith('mrkv_ua_shipping')) continue;

                const checkoutFields = methods[methodSlug].checkout_fields;
                if (!checkoutFields) continue;

                for (const fieldKey in checkoutFields) {
                    const fieldConfig = checkoutFields[fieldKey];

                    if (fieldConfig.type === 'hidden') continue;
                    if (!fieldConfig.required) continue;

                    const fieldLabel = fieldConfig.label || fieldKey;
                    
                    let fieldLocation = location;
                    if (fieldKey === '_patronymic' && patronymicPosition !== 'default') {
                        fieldLocation = 'address';
                    }

                    let prefixes = [];
                    if (fieldLocation === 'address') {
                        prefixes = ['billing_', 'shipping_'];
                    } else {
                        prefixes = [fieldLocation + '_'];
                    }

                    prefixes.forEach(prefix => {
                        const errorKey = prefix + 'mrkv-ua-shipping/' + methodSlug + fieldKey;
                        
                        if (!window.mrkvErrorsCache[errorKey]) {
                            window.mrkvErrorsCache[errorKey] = {
                                message: '' + requiredText + ' ' + fieldLabel,
                                hidden: false
                            };
                        }
                    });
                }
            }
        }
    }

    function getFieldRoot(el) {
        if (!el) return null;

        return (
            el.closest('.wc-block-components-select-input') ||
            el.closest('.wc-block-components-text-input') ||
            el.closest('.wc-block-components-state-input') ||
            el.closest('.wc-blocks-components-select') ||
            el.parentNode
        );
    }

    function getSavedMethod() {
        return sessionStorage.getItem(STORAGE_KEY);
    }

    function setSavedMethod(method) {
        sessionStorage.setItem(STORAGE_KEY, method);
    }

    function getCurrentMethod() {
        const cartStore = select('wc/store/cart');
        if (!cartStore) return null;

        const cartData = cartStore.getCartData();
        const packages = cartData.shippingRates || cartData.shipping_rates;
        if (!packages || !packages.length) return 'none_exact_method';

        const rates = packages[0].shipping_rates || packages[0].shippingRates;
        if (!rates) return 'none_exact_method';

        const selectedRate = rates.find(r => r.selected === true);
        const raw = selectedRate ? (selectedRate.rate_id || selectedRate.id || '') : 'none_exact_method';

        return raw.split(':')[0].replace(/_\d+$/, '');
    }

    function isHiddenField(input) {
        if (!input) return false;
        const container = input.closest('.wc-block-components-text-input') || input.closest('.wc-block-components-select-input') || input.closest('.wc-blocks-components-select');
        const label = container?.querySelector('label');
        return (
            (label && label.textContent && label.textContent.trim().toLowerCase() === 'hidden') ||
            input.name.endsWith('_hidden_val')
        );
    }

    function hideAllHiddenFields() {
        const inputs = document.querySelectorAll('[name*="mrkv-ua-shipping/"]');

        inputs.forEach(input => {
            if (!isHiddenField(input)) return;
            const container = getFieldRoot(input);
            if (container) {
                container.style.setProperty('display', 'none', 'important');
            }
            input.required = false;
        });
    }

    function handleAddress2ToggleVisibility(isBypass) {
        const toggles = document.querySelectorAll('.wc-block-components-address-form__address_2-toggle');
        toggles.forEach(toggle => {
            if (isBypass) {
                toggle.style.setProperty('display', 'none', 'important');
            } else {
                toggle.style.removeProperty('display');
            }
        });
    }

    function repositionPatronymicField(methodSlug) {
        if (!methodSlug || !methodSlug.startsWith('mrkv_ua_shipping')) return;

        const provider = getProviderByMethod(methodSlug);
        if (!provider) return;

        const positionMode = patranomicPositions[provider];
        if (!positionMode || positionMode === 'default') return;
        const allPatronymicInputs = document.querySelectorAll(
            `[name^="billing_"][name*="mrkv-ua-shipping/${methodSlug}_patronymic"], ` +
            `[name^="shipping_"][name*="mrkv-ua-shipping/${methodSlug}_patronymic"]`
        );
        const patronymicInputs = Array.from(allPatronymicInputs).filter(input => {
            const name = input.name || '';
            return new RegExp(`mrkv-ua-shipping\\/${methodSlug}_patronymic$`).test(name);
        });
        if (!patronymicInputs.length) return;

        patronymicInputs.forEach(patronymicInput => {
            const patronymicContainer = getFieldRoot(patronymicInput);
            if (!patronymicContainer) return;

            const nameAttr = patronymicInput.name || '';
            const idAttr = patronymicInput.id || '';
            const currentBlock = (nameAttr.startsWith('shipping_') || idAttr.startsWith('shipping-')) ? 'shipping' : 'billing';

            let targetName = currentBlock + '_first_name';
            if (positionMode.includes('last_name')) {
                targetName = currentBlock + '_last_name';
            }

            let targetInput = document.querySelector(`[name="${targetName}"]`);
            if (!targetInput) {
                const searchPart = positionMode.includes('last_name') ? 'last_name' : 'first_name';
                targetInput = document.querySelector(`[name*="${searchPart}"]`);
            }
            if (!targetInput) return;

            const targetContainer = getFieldRoot(targetInput);
            if (!targetContainer || !targetContainer.parentNode) return;

            if (targetContainer.nextSibling !== patronymicContainer) {
                targetContainer.parentNode.insertBefore(patronymicContainer, targetContainer.nextSibling);
            }
        });
    }

    function isCurrentPaymentMethodCod() {
        const paymentStore = select('wc/store/payment');
        if (!paymentStore || typeof paymentStore.getActivePaymentMethod !== 'function') {
            return false;
        }

        const activeMethod = paymentStore.getActivePaymentMethod();
        return activeMethod === 'cod';
    }

    function isCodPaymentMethodAvailable() {
        const cartStore = select('wc/store/cart');
        if (!cartStore || typeof cartStore.getCartData !== 'function') {
            return false;
        }
        
        const cartData = cartStore.getCartData();
        const paymentMethods = cartData?.paymentMethods || cartData?.payment_methods || [];
        if (Array.isArray(paymentMethods)) {
            return paymentMethods.includes('cod');
        }
        
        if (typeof paymentMethods === 'object' && paymentMethods !== null) {
            return Object.keys(paymentMethods).includes('cod');
        }
        
        return false;
    }

    function applyUI(methodSlug) {
        const isBypassMethod = methodSlug && (methodSlug.startsWith('mrkv_ua_shipping') || methodSlug.startsWith('local_pickup'));
        
        handleAddress2ToggleVisibility(isBypassMethod);

        const coreFields = [
            'billing_address_1', 'billing_address_2', 'billing_city', 'billing_state', 'billing_postcode',
            'shipping_address_1', 'shipping_address_2', 'shipping_city', 'shipping_state', 'shipping_postcode'
        ];

        coreFields.forEach(name => {
            const el = document.querySelector(`[name="${name}"], #${name.replace(/_/g, '-')}`);
            if (!el) return;
            if (isHiddenField(el)) return;

            const container = getFieldRoot(el);
            if (!container) return;

            if (isBypassMethod) {
                container.style.setProperty('display', 'none', 'important');
                el.required = false;
            } else {
                container.style.removeProperty('display');
            }
        });
        if (!config || !methodSlug) return;

        const provider = getProviderByMethod(methodSlug);
        const activeFields = config[provider]?.method?.[methodSlug]?.checkout_fields || null;
        const location = (provider && providerLocation[provider]) ? providerLocation[provider] : 'address';

        const inputs = document.querySelectorAll('[name*="mrkv-ua-shipping/"]');
        
        inputs.forEach(input => {
            const container = getFieldRoot(input);
            if (container) {
                container.style.setProperty('display', 'none', 'important');
            }
            input.required = false;
        });

        if (!activeFields) return;

        for (const key in activeFields) {
            const expected = `${methodSlug}${key}`;
            const matchedInputs = Array.from(inputs).filter(el => el.name.endsWith(expected));

            matchedInputs.forEach(input => {
                if (isHiddenField(input)) return;

                let fieldLocation = location;
                if (key === '_patronymic' && provider && patranomicPositions[provider] && patranomicPositions[provider] !== 'default') {
                    fieldLocation = 'address';
                }
                

                if (fieldLocation === 'address') {
                    const nameAttr = input.name || '';
                    const idAttr = input.id || '';
                    const inputBlock = (nameAttr.startsWith('shipping_') || idAttr.startsWith('shipping_')) ? 'shipping' : 'billing';
                    if (inputBlock !== getActiveBlockType()) {
                        return;
                    }
                }

                const container = getFieldRoot(input);
                if (container) {
                    container.style.removeProperty('display');
                }

                if (activeFields[key].required) {
                    if (activeFields[key].type === 'select') {
                        const cleanName = input.name;
                        const hiddenInput = Array.from(inputs).find(el => el.name === cleanName + '_hidden_val');
                        if (hiddenInput) hiddenInput.required = true;
                    } else {
                        input.required = true;
                    }
                }
            });
        }

        repositionPatronymicField(methodSlug);
        if (methodSlug === 'mrkv_ua_shipping_ukr-poshta') {
            const isCod = isCurrentPaymentMethodCod();
            const provider = getProviderByMethod(methodSlug);
            let location = (provider && providerLocation[provider]) ? providerLocation[provider] : 'address';
            const expectedPatronymic = 'mrkv_ua_shipping_ukr-poshta_patronymic';

            if (provider && patranomicPositions[provider]) {
                const patronymicPosition = patranomicPositions[provider];
                if (patronymicPosition !== 'default') {
                    location = 'address';
                }
            }

            inputs.forEach(input => {
                if (input.name.endsWith(expectedPatronymic)) {
                    
                    if (location === 'address') {
                        const nameAttr = input.name || '';
                        const idAttr = input.id || '';
                        const inputBlock = (nameAttr.startsWith('shipping_') || idAttr.startsWith('shipping_')) ? 'shipping' : 'billing';
                    
                        if (inputBlock !== getActiveBlockType()) {
                            return;
                        }
                    }

                    const container = getFieldRoot(input);
                    if (container) {
                        if (isCurrentPaymentMethodCod()) {
                            container.style.removeProperty('display');
                            input.required = true;
                        } else {
                            const isOriginallyRequired = config[provider]?.method?.[methodSlug]?.checkout_fields?._patronymic?.required;
                            
                            if (!isOriginallyRequired) {
                                container.style.setProperty('display', 'none', 'important');
                                input.required = false;
                            }
                        }
                    }
                }
            });
        }
    }

    function getActiveMethodFields(methodSlug) {
        const provider = getProviderByMethod(methodSlug);
        const activeFields = config[provider]?.method?.[methodSlug]?.checkout_fields || null;

        if (!activeFields) return [];
        
        const fieldsArr = [];
        Object.keys(activeFields).forEach(key => {
            fieldsArr.push(`${methodSlug}${key}`);
            if (activeFields[key].type === 'select') {
                fieldsArr.push(`${methodSlug}${key}_hidden_val`);
            }
        });
        return fieldsArr;
    }

    function clearValidationErrors() {
        const validation = select('wc/store/validation');
        const validationDispatch = dispatch('wc/store/validation');
        if (!validation) return;
        
        const errors = validation.getValidationErrors();
        if (!errors) return;

        const method = getCurrentMethod();

        const addressFieldsToBypass = [
            'billing-address-1', 'billing-address-2', 'billing-city', 'billing-state', 'billing-postcode',
            'billing_address_1', 'billing_address_2', 'billing_city', 'billing_state', 'billing_postcode',
            'shipping-address-1', 'shipping-address-2', 'shipping-city', 'shipping-state', 'shipping-postcode',
            'shipping_address_1', 'shipping_address_2', 'shipping_city', 'shipping_state', 'shipping_postcode'
        ];

        if (!method || (!method.startsWith('mrkv_ua_shipping') && !method.startsWith('local_pickup'))) {
            Object.keys(errors).forEach(field => {
                if (field.includes('mrkv-ua-shipping/')) {
                    validationDispatch.clearValidationError(field);
                }
            });
            return;
        }

        const activeMethodFields = getActiveMethodFields(method);

        Object.keys(errors).forEach(field => {
            if (addressFieldsToBypass.includes(field)) {
                validationDispatch.clearValidationError(field);
                return;
            }

            if (!field.includes('mrkv-ua-shipping/')) return;

            const belongsToCurrentMethod = activeMethodFields.some(activeField => field.includes(activeField));
            
            const errorPrefix = getFieldPrefix(method, field);
            const belongsToActiveBlock = field.startsWith(errorPrefix);

            if (!belongsToCurrentMethod || !belongsToActiveBlock) {
                validationDispatch.clearValidationError(field);
            }
        });
    }

    function isProcessingCheckout() {
        const checkout = select('wc/store/checkout');
        if (!checkout) return false;
        return checkout.isProcessing && checkout.isProcessing();
    }

    function init() {
        const method = getCurrentMethod();
        if (method) {
            setSavedMethod(method);
            applyUI(method);
        }

        hideAllHiddenFields();
        initMrkvErrorsCache();
        updateAllFieldLabels();
        updateAllFieldSelect(); 
        
        const checkbox = document.querySelector('.wc-block-checkout__use-address-for-billing input[type="checkbox"]');
        if (checkbox) {
            lastCheckboxState = checkbox.checked;
        }
    }

    function updateAllFieldLabels() {
        if (!config) return;

        for (const provider in config) {
            const methods = config[provider]?.method || {};
            for (const methodSlug in methods) {
                if (!methodSlug.startsWith('mrkv_ua_shipping')) continue;
                const checkoutFields = methods[methodSlug].checkout_fields;
                if (!checkoutFields) continue;

                for (const fieldKey in checkoutFields) {
                    const fieldConfig = { ...checkoutFields[fieldKey] };
                    if (methodSlug === 'mrkv_ua_shipping_ukr-poshta' && fieldKey === '_patronymic') {
                        if (isCodPaymentMethodAvailable()) {
                            fieldConfig.required = true;
                        }
                    }

                    const expectedName = methodSlug + fieldKey;
                    const inputs = document.querySelectorAll(`[name*="${expectedName}"]`);
                    
                    inputs.forEach(input => {
                        const container = getFieldRoot(input);
                        if (!container) return;

                        const label = container.querySelector('label');
                        if (!label) return;

                        if (fieldConfig.required) {
                            label.textContent = fieldConfig.label || label.textContent;
                        }

                        if (fieldConfig.type === 'hidden') {
                            label.textContent = 'hidden';
                        }
                    });
                }
            }
        }
    }

    function updateAllFieldSelect() {
        if (!config) return;

        for (const provider in config) {
            const methods = config[provider]?.method || {};
            for (const methodSlug in methods) {
                if (!methodSlug.startsWith('mrkv_ua_shipping')) continue;
                const checkoutFields = methods[methodSlug].checkout_fields;
                if (!checkoutFields) continue;

                for (const fieldKey in checkoutFields) {
                    if (checkoutFields[fieldKey].type === 'select') {
                        const expectedName = methodSlug + fieldKey;
                        const inputs = document.querySelectorAll(`select[name*="${expectedName}"]`);
                        
                        inputs.forEach(input => {
                            if (input.dataset.mrkvCleared === 'true') {
                                return;
                            }

                            if (input.options && input.options.length > 0) {
                                input.remove(0);
                                input.dataset.mrkvCleared = 'true'; 
                            }
                        });
                    }
                }
            }
        }
    }

    function restoreValidationFromCache() {
        const validationSelect = select('wc/store/validation');
        const validationDispatch = dispatch('wc/store/validation');
        if (!validationDispatch || !validationSelect) return;

        const currentMethod = getCurrentMethod();
        const activeMethodFields = getActiveMethodFields(currentMethod);
        const liveErrors = validationSelect.getValidationErrors() || {};

        const coreFields = [
            'billing-address-1', 'billing-address-2', 'billing-city', 'billing-state', 'billing-postcode',
            'shipping-address-1', 'shipping-address-2', 'shipping-city', 'shipping-state', 'shipping-postcode',
            'billing_address_1', 'billing_address_2', 'billing_city', 'billing_state', 'billing_postcode',
            'shipping_address_1', 'shipping_address_2', 'shipping_city', 'shipping_state', 'shipping_postcode'
        ];

        const filteredErrors = {};

        Object.keys(liveErrors).forEach(fieldKey => {
            if (!coreFields.includes(fieldKey) && !fieldKey.includes('mrkv-ua-shipping/')) {
                filteredErrors[fieldKey] = liveErrors[fieldKey];
            }
        });

        Object.keys(window.mrkvErrorsCache).forEach(fieldKey => {
            const errorData = window.mrkvErrorsCache[fieldKey];

            if (coreFields.includes(fieldKey)) {
                if (currentMethod && (currentMethod.startsWith('mrkv_ua_shipping') || currentMethod.startsWith('local_pickup'))) return;
                filteredErrors[fieldKey] = errorData;
            } else if (fieldKey.includes('mrkv-ua-shipping/')) {
                const belongsToCurrentMethod = activeMethodFields.some(activeField => fieldKey.includes(activeField));
                
                const errorPrefix = getFieldPrefix(currentMethod, fieldKey);
                const belongsToActiveBlock = fieldKey.startsWith(errorPrefix);
                
                if (belongsToCurrentMethod && belongsToActiveBlock) {
                    filteredErrors[fieldKey] = errorData;
                }
            }
        });

        validationDispatch.setValidationErrors(filteredErrors);
    }

    function getRawCurrentMethod() {
        const cartStore = select('wc/store/cart');
        if (!cartStore) return null;

        const cartData = cartStore.getCartData();
        const packages = cartData.shippingRates || cartData.shipping_rates;
        if (!packages || !packages.length) return null;

        const rates = packages[0].shipping_rates || packages[0].shippingRates;
        if (!rates) return null;

        const selectedRate = rates.find(r => r.selected === true);
        return selectedRate ? (selectedRate.rate_id || selectedRate.id || '') : null;
    }

    function toggleShippingFieldsVisibility(methodSlug) {
        const configData = window.mrkvShippingConfig || {};
        if (!configData.is_under_methods) return;

        document.querySelectorAll('.mrkv-fields-under-method-wrapper').forEach(wrapper => {
            wrapper.style.setProperty('display', 'none', 'important');
            wrapper.style.setProperty('padding-top', '0', 'important');
            wrapper.style.setProperty('padding-bottom', '0', 'important');
        });

        if (!methodSlug || !methodSlug.startsWith('mrkv_ua_shipping')) {
            return;
        }

        const targetRadio = document.querySelector(`input[type="radio"][value^="${methodSlug}"]:checked`);
        if (!targetRadio) return;

        const methodContainer = targetRadio.closest('.wc-block-components-radio-control-item') || targetRadio.parentElement;
        if (!methodContainer) return;

        const currentWrapper = methodContainer.querySelector('.mrkv-fields-under-method-wrapper');
        if (!currentWrapper) return;

        const currentInputs = currentWrapper.querySelectorAll('[name*="mrkv-ua-shipping/"]');
        if (currentInputs.length === 0) {
            const provider = getProviderByMethod(methodSlug);
            if (provider) {
                const checkoutFields = config[provider]?.method?.[methodSlug]?.checkout_fields || {};
                Object.keys(checkoutFields).forEach(fieldKey => {
                    const expected = `${methodSlug}${fieldKey}`;
                    document.querySelectorAll('[name*="mrkv-ua-shipping/"]').forEach(input => {
                        if (input.name.endsWith(expected) || input.name.endsWith(expected + '_hidden_val')) {
                            const container = getFieldRoot(input);
                            if (container && currentWrapper !== container.parentNode) {
                                currentWrapper.appendChild(container);
                            }
                        }
                    });
                });
            }
        }

        let hasVisibleFields = false;
        const inputs = currentWrapper.querySelectorAll('[name*="mrkv-ua-shipping/"]');
        
        inputs.forEach(input => {
            if (!isHiddenField(input)) {
                const container = getFieldRoot(input);
                if (container && container.style.display !== 'none') {
                    hasVisibleFields = true;
                }
            }
        });

        if (hasVisibleFields) {
            currentWrapper.style.setProperty('display', 'block', 'important');
            currentWrapper.style.setProperty('padding-top', '15px', 'important');
            currentWrapper.style.setProperty('padding-bottom', '15px', 'important');
        }
    }

    function handleShippingPriceVisibility() {
        if (!window.mrkvShippingConfig || !window.mrkvShippingConfig.hide_free_methods) {
            return;
        }

        const cartStore = wp.data.select('wc/store/cart');
        if (!cartStore) {
            return;
        }

        const cartData = cartStore.getCartData();
        const packages = cartData.shippingRates || cartData.shipping_rates;
        if (!packages || !packages.length) {
            return;
        }

        const rates = packages[0].shipping_rates || packages[0].shippingRates;
        if (!rates) {
            return;
        }

        const selectedRate = rates.find(r => r.selected === true);
        if (!selectedRate) {
            return;
        }

        const rawId = selectedRate.rate_id || selectedRate.id || '';
        const normalizedMethod = rawId.replace(':', '_');

        if (window.mrkvShippingConfig.hide_free_methods.includes(normalizedMethod)) {
            const targetElement = document.querySelector('.wp-block-woocommerce-checkout-order-summary-shipping-block.wc-block-components-totals-wrapper .wc-block-components-totals-item__value');
            if (targetElement) {
                targetElement.textContent = '';
            }
        }
    }

    init();

    subscribe(() => {
        const currentMethod = getCurrentMethod();
        const rawCurrentMethod = getRawCurrentMethod();
        if (!currentMethod || !rawCurrentMethod) return;
        
        const savedMethod = getSavedMethod();

        if (isFirstSubscriptionPass) {
            isFirstSubscriptionPass = false;
            setSavedMethod(currentMethod);
            lastRawMethod = rawCurrentMethod;
            applyUI(currentMethod);
            hideAllHiddenFields();
            return;
        }

        if (currentMethod !== savedMethod || rawCurrentMethod !== lastRawMethod) {
            setSavedMethod(currentMethod);
            lastRawMethod = rawCurrentMethod;
            applyUI(currentMethod);
            hideAllHiddenFields();
            toggleShippingFieldsVisibility(currentMethod);
        }
    });

    subscribe(() => {
        if (!isProcessingCheckout()) return;

        const currentMethod = getCurrentMethod();
        if (currentMethod === 'mrkv_ua_shipping_ukr-poshta' && isCurrentPaymentMethodCod()) {
            
            const provider = getProviderByMethod(currentMethod);
            let location = (provider && providerLocation[provider]) ? providerLocation[provider] : 'address';

            if (provider && patranomicPositions[provider]) {
                const patronymicPosition = patranomicPositions[provider];
                if (patronymicPosition !== 'default') {
                    location = 'address';
                }
            }
            
            let prefixes = [];
            if (location === 'address') {
                prefixes = ['billing_', 'shipping_'];
            } else {
                prefixes = [location + '_'];
            }

            prefixes.forEach(prefix => {
                const errorKey = prefix + 'mrkv-ua-shipping/' + currentMethod + '_patronymic';
                
                const normalizedFormName = errorKey.replace('/', '_');
                const mainInput = document.querySelector(`[name="${errorKey}"], [name="${normalizedFormName}"], [name*="mrkv_ua_shipping_ukr-poshta_patronymic"]`);
                
                if (mainInput && (!mainInput.value || mainInput.value.trim() === '')) {
                    const labelText = (config[provider]?.method?.[currentMethod]?.checkout_fields?._patronymic?.label) || 'По батькові';
                    
                    window.mrkvErrorsCache[errorKey] = {
                        message: '' + (requiredText || '') + ' ' + labelText,
                        hidden: false
                    };
                }
            });
        }

        restoreValidationFromCache();
        clearValidationErrors();
    });

    subscribe(() => {
        const validationSelect = select('wc/store/validation');
        if (!validationSelect) return;
        
        const liveErrors = validationSelect.getValidationErrors() || {};
        
        Object.keys(liveErrors).forEach(fieldKey => {
            if (!fieldKey.includes('mrkv-ua-shipping/')) return;
            if (!window.mrkvErrorsCache[fieldKey]) {
                window.mrkvErrorsCache[fieldKey] = JSON.parse(JSON.stringify(liveErrors[fieldKey]));
            }
        });
        
        Object.keys(window.mrkvErrorsCache).forEach(fieldKey => {
            if (!fieldKey.includes('mrkv-ua-shipping/')) return;

            const prefix = fieldKey.split('mrkv-ua-shipping/')[0];
            const fieldPath = fieldKey.substring(prefix.length);
            const baseFieldPath = fieldPath.replace('_hidden_val', '');
            const cleanKeyPart = baseFieldPath.split('/').pop();

            if (!cleanKeyPart) return;

            const mainName = prefix + baseFieldPath;
            const mainNameNormalized = mainName.replace(/\//g, '_');
            const hiddenName = mainName + '_hidden_val';
            const hiddenNameNormalized = mainNameNormalized + '_hidden_val';

            let mainSelector = `[name="${mainName}"], [name="${mainNameNormalized}"]`;
            let hiddenSelector = `[name="${hiddenName}"], [name="${hiddenNameNormalized}"]`;

            if (prefix) {
                mainSelector += `, [name^="${prefix}"][name*="${cleanKeyPart}"]`;
                hiddenSelector += `, [name^="${prefix}"][name*="${cleanKeyPart}_hidden_val"]`;
            } else {
                mainSelector += `, [name*="${cleanKeyPart}"]`;
                hiddenSelector += `, [name*="${cleanKeyPart}_hidden_val"]`;
            }

            const mainInput = document.querySelector(mainSelector);
            const hiddenInput = document.querySelector(hiddenSelector);
            const targetInput = hiddenInput || mainInput;

            if (targetInput && targetInput.value && targetInput.value.trim() !== '') {
                delete window.mrkvErrorsCache[fieldKey];
            }
        });
    });

    let isClearing = false;

    subscribe(() => {
        if (isClearing) return;

        const validationSelect = select('wc/store/validation');
        const validationDispatch = dispatch('wc/store/validation');
        if (!validationSelect || !validationDispatch) return;
        
        const liveErrors = validationSelect.getValidationErrors() || {};
        const liveErrorKeys = Object.keys(liveErrors);
        if (liveErrorKeys.length === 0) return;
        liveErrorKeys.forEach(fieldKey => {
            if (!fieldKey.includes('mrkv-ua-shipping/')) return;

            const cleanKeyPart = fieldKey.split('/').pop();
            if (!cleanKeyPart) return;

            const cleanKeyWithoutHidden = cleanKeyPart.replace('_hidden_val', '');
            const normalizedFormName = fieldKey.replace('/', '_');
            
            const mainInput = document.querySelector(`[name="${fieldKey}"], [name="${normalizedFormName}"], [name*="${cleanKeyPart}"]`);
            const hiddenInput = document.querySelector(`[name="${normalizedFormName}_hidden_val"], [name*="${cleanKeyWithoutHidden}_hidden_val"]`);
            const targetInput = hiddenInput || mainInput;

            if (targetInput && targetInput.value && targetInput.value.trim() !== '') {
                isClearing = true;
                try {
                    if (typeof validationDispatch.clearValidationError === 'function') {
                        validationDispatch.clearValidationError(fieldKey);
                    }
                } catch (err) {
                    console.error('[MRKV] Error in clearValidationError:', err);
                } finally {
                    isClearing = false;
                }
            }
        });

        isClearing = true;
    });

    subscribe(() => {
        const currentMethod = getCurrentMethod();
        if (!currentMethod) return;

        const checkbox = document.querySelector('.wc-block-checkout__use-address-for-billing input[type="checkbox"]');
        const currentState = checkbox ? checkbox.checked : 'no_checkbox';

        if (lastCheckboxState === null) {
            lastCheckboxState = currentState;
            return;
        }

        if (currentState !== lastCheckboxState) {
            lastCheckboxState = currentState;
            setTimeout(() => {
                updateAllFieldLabels();
                hideAllHiddenFields();
                updateAllFieldSelect();
                applyUI(currentMethod);
            }, 50);
        }

    });

    subscribe(() => {
        const cartStore = select('wc/store/cart');

        if (!cartStore) return;

        const cartData = cartStore.getCartData();
        const customerData = cartStore.getCustomerData();

        const country =
            customerData?.shippingAddress?.country ||
            cartData?.shippingAddress?.country;

        if (!country) return;

        if (isInitialLoad) {
            previousCountry = country;
            isInitialLoad = false;
            return;
        }

        if (country === previousCountry) return;
        previousCountry = country;

        returnFieldsToOriginalContainers();

        isReloadingShippingMethods = true;

        wp.data.dispatch('wc/store/cart').updateCustomerData({
            billing_address: {
                ...customerData.billingAddress
            },
            shipping_address: {
                ...customerData.shippingAddress
            },
            extension_data: {
                mrkv_override_country: country
            }
        }).then(() => {
            const currentMethod = getCurrentMethod();
            applyUI(currentMethod);
            isReloadingShippingMethods = false;
            distributeFieldsToMethods();
        }).catch((error) => {
        });
    });

    observer = new MutationObserver(() => {
        hideAllHiddenFields();
        const currentMethod = getCurrentMethod();
        if (currentMethod) {
            const isBypassMethod = currentMethod.startsWith('mrkv_ua_shipping') || currentMethod.startsWith('local_pickup');
            handleAddress2ToggleVisibility(isBypassMethod);
            repositionPatronymicField(currentMethod);
        }
        if (window.mrkvShippingConfig && window.mrkvShippingConfig.is_under_methods && !isUnderFieldsLoaded) {
            const methodRadios = document.querySelectorAll('input[type="radio"][value*="mrkv_ua_shipping"]');
            const hasWrappers = document.querySelector('.mrkv-fields-under-method-wrapper');
            if (methodRadios.length > 0 && !hasWrappers) {
                distributeFieldsToMethods();
                isUnderFieldsLoaded = true;
            }
        }
        handleShippingPriceVisibility();
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});