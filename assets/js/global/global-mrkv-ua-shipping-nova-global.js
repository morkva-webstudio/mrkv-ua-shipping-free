jQuery(window).on('load', function() 
{
	if (jQuery('form[name="checkout"]').length == 0) return;

	var currentLang = jQuery('html').attr('lang');

	if (currentLang === 'uk') 
	{
		jQuery.fn.select2.defaults.set("language", {
		    errorLoading: function () {
		        return "Пошук...";
		    },
		    inputTooLong: function (args) {
		        var overChars = args.input.length - args.maximum;
		        return "Будь ласка, видаліть " + overChars + " символ(и/ів).";
		    },
		    inputTooShort: function (args) {
		        return "Будь ласка, введіть ще " + args.minimum + " символ(и/ів).";
		    },
		    loadingMore: function () {
		        return "Завантаження додаткових результатів...";
		    },
		    maximumSelected: function (args) {
		        return "Ви можете вибрати лише " + args.maximum + " елемент(и/ів).";
		    },
		    noResults: function () {
		        return "Нічого не знайдено.";
		    },
		    searching: function () {
		        return "Пошук...";
		    },
		    removeAllItems: function () {
		        return "Видалити всі елементи";
		    },
		});
		if (typeof jQuery.fn.selectWoo !== 'undefined') {
			jQuery.fn.selectWoo.defaults.set("language", {
			    errorLoading: function () {
			        return "Пошук...";
			    },
			    inputTooLong: function (args) {
			        var overChars = args.input.length - args.maximum;
			        return "Будь ласка, видаліть " + overChars + " символ(и/ів).";
			    },
			    inputTooShort: function (args) {
			        return "Будь ласка, введіть ще " + args.minimum + " символ(и/ів).";
			    },
			    loadingMore: function () {
			        return "Завантаження додаткових результатів...";
			    },
			    maximumSelected: function (args) {
			        return "Ви можете вибрати лише " + args.maximum + " елемент(и/ів).";
			    },
			    noResults: function () {
			        return "Нічого не знайдено.";
			    },
			    searching: function () {
			        return "Пошук...";
			    },
			    removeAllItems: function () {
			        return "Видалити всі елементи";
			    },
			});
		}

		currentLang = 'UA';
	}
	else
	{
		currentLang = 'EN';
	}

	setTimeout(function(){
		let mrkv_ua_current_shipping = mrkvUaShipGetCurrentShipping();

		if(mrkv_ua_current_shipping && ~mrkv_ua_current_shipping.indexOf("mrkv_ua_shipping_nova-global"))
		{
			mrkvUaShipGetWarehouse(mrkv_ua_current_shipping);
		}
	}, 200);

	jQuery( document.body ).on( 'updated_checkout', () => {
 		let mrkv_ua_current_shipping = mrkvUaShipGetCurrentShipping();

 		if(mrkv_ua_current_shipping && ~mrkv_ua_current_shipping.indexOf("mrkv_ua_shipping_nova-global"))
 		{
 			mrkvUaShipGetWarehouse(mrkv_ua_current_shipping);
 		}
 	});

 	if(jQuery('#mrkv_ua_shipping_nova-global_warehouse').length != 0)
 	{
 		jQuery('#mrkv_ua_shipping_nova-global_warehouse').select2();

 		jQuery('#mrkv_ua_shipping_nova-global_warehouse').on('select2:select', function (e) 
    	{
    		let current_option = e.params.data;

    		jQuery('#mrkv_ua_shipping_nova-global_warehouse_ref').val(current_option.ref);
			jQuery('#mrkv_ua_shipping_nova-global_city_label').val(current_option.city);
			jQuery('#mrkv_ua_shipping_nova-global_address').val(current_option.address);
			jQuery('#mrkv_ua_shipping_nova-global_area_name').val(current_option.area);
			jQuery('#mrkv_ua_shipping_nova-global_zipcode').val(current_option.zipcode);
    	});
 	}

 	function mrkvUaShipGetWarehouse(shipping_method)
 	{
 		var country = (jQuery('#ship-to-different-address-checkbox').is(':checked') ? jQuery('#shipping_country').val() : jQuery('#billing_country').val()) || '';

 		if(country)
 		{
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

               	if(data)
               	{
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

               	if(data.length == 1 && data[0].value == 'none')
               	{
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

	function mrkvUaShipGetCurrentShipping()
 	{
 		let mrkv_ua_current_shipping = jQuery('.shipping_method').length > 1 ?
	      jQuery('.shipping_method:checked').val() :
	      jQuery('.shipping_method').val();

	      mrkv_ua_current_shipping = mrkv_ua_current_shipping.replace(/_\d+$/, '');

	      return mrkv_ua_current_shipping;
 	}
});