jQuery(window).on('load', function() 
{
	if (jQuery('form[name="checkout"]').length == 0) return;

	var languageTexts = {
	    errorLoading: function () {
	        return mrkv_ua_ship_helper.select2_texts.errorLoading;
	    },
	    inputTooLong: function (args) {
	        return mrkv_ua_ship_helper.select2_texts.inputTooLong.replace('%d', args.input.length - args.maximum);
	    },
	    inputTooShort: function (args) {
	        return mrkv_ua_ship_helper.select2_texts.inputTooShort.replace('%d', args.minimum - args.input.length);
	    },
	    loadingMore: function () {
	        return mrkv_ua_ship_helper.select2_texts.loadingMore;
	    },
	    maximumSelected: function (args) {
	        return mrkv_ua_ship_helper.select2_texts.maximumSelected.replace('%d', args.maximum);
	    },
	    noResults: function () {
	        return mrkv_ua_ship_helper.select2_texts.noResults;
	    },
	    searching: function () {
	        return mrkv_ua_ship_helper.select2_texts.searching;
	    },
	    removeAllItems: function () {
	        return mrkv_ua_ship_helper.select2_texts.removeAllItems;
	    }
	};

	jQuery.fn.select2.amd.define('select2/data/extended-ajax',['./ajax','../utils','jquery'], function(AjaxAdapter, Utils, $){

	  function ExtendedAjaxAdapter ($element,options) {
	    //we need explicitly process minimumInputLength value 
	    //to decide should we use AjaxAdapter or return defaultResults,
	    //so it is impossible to use MinimumLength decorator here
	    this.minimumInputLength = options.get('minimumInputLength');
	    this.defaultResults     = options.get('defaultResults');

	    ExtendedAjaxAdapter.__super__.constructor.call(this,$element,options);
	  }

	  Utils.Extend(ExtendedAjaxAdapter,AjaxAdapter);
	  
	  //override original query function to support default results
	  var originQuery = AjaxAdapter.prototype.query;

	  ExtendedAjaxAdapter.prototype.query = function (params, callback) {
	    var defaultResults = (typeof this.defaultResults == 'function') ? this.defaultResults.call(this) : this.defaultResults;

	    if (defaultResults && defaultResults.length && (!params.term || params.term.length < this.minimumInputLength)){
	      var processedResults = this.processResults(defaultResults,params.term);
	      callback(processedResults);
	    }
	    else {
	      originQuery.call(this, params, callback);
	    }
	  };

	  return ExtendedAjaxAdapter;
	});

	var default_cities = [];

	mrkv_ua_ship_helper.ukr_city_area.map(function(item) {
        default_cities.push({ id: item.label, text: item.label, ref: item.value, area: item.area });
    });

    var $select;

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
		    	$select = jQuery(this);
		    	if(params.term && params.term.length > 2)
		    	{
		    		var query = {
				      	action: 'mrkv_ua_ship_ukr_poshta_city',
				        name: params.term,
				        nonce: mrkv_ua_ship_helper.nonce,
				    }
		    	}
		    	else
		    	{
		    		var query = {
				      	action: 'mrkv_ua_ship_ukr_poshta_city',
				      	nonce: mrkv_ua_ship_helper.nonce,
				    }
		    	}

		      return query;
		    },
		    beforeSend: function (e) {
		    	/*var current_select_name = 'select2-' + jQuery($select).attr('name') + '-results';
		    	jQuery('input[aria-owns="' + current_select_name + '"]').prop('disabled', true);
		    	jQuery('input[aria-owns="' + current_select_name + '"]').closest('.select2-search').append('<span class="mrkv-public-loader"></span>');*/

		    },
		    complete: function () {
		    	/*var current_select_name = 'select2-' + jQuery($select).attr('name') + '-results';
		    	jQuery('input[aria-owns="' + current_select_name + '"]').prop('disabled', false);
		    	jQuery('input[aria-owns="' + current_select_name + '"]').focus();
		    	jQuery('.mrkv-public-loader').remove();*/
            },
		    processResults: function (json) {
		    	var data;
		    
		    	if(typeof json == 'string')
		    	{
		    		data = JSON.parse(json);

		    		return {
				        results: data.map(function(item) {
	                        return { id: item.label, text: item.label, ref: item.value, area: item.area, area_id: item.area_id, district_id: item.district_id };
	                    })
				    };
		    	}
		    	else
		    	{
		    		data = json;

		    		return {
				        results: mrkv_ua_ship_helper.ukr_city_area.map(function(item) {
	                        return { id: item.label, text: item.label, ref: item.value, area: item.area };
	                    })
				    };	
		    	}
		    },
	  	},
	};

	/** UKR POSHTA SHIPPING **/

	if(jQuery('#mrkv_ua_shipping_ukr-poshta_city').length != 0)
 	{
 		jQuery('#mrkv_ua_shipping_ukr-poshta_city').select2(up_settings_city_select);

 		let ukr_poshta_city = jQuery('#mrkv_ua_shipping_ukr-poshta_city').attr('data-default');

 		if(ukr_poshta_city)
 		{
 			jQuery('#mrkv_ua_shipping_ukr-poshta_city').val(ukr_poshta_city).trigger('change');
 		}

 		jQuery('#mrkv_ua_shipping_ukr-poshta_city').on('select2:opening', function (e) {
 			jQuery(this).data('select2').$dropdown.find(':input.select2-search__field').attr('placeholder', mrkv_ua_ship_helper.city_placeholder);
 		});
 		jQuery('#mrkv_ua_shipping_ukr-poshta_city').on('select2:closing', function (e) {
 			jQuery(this).data('select2').$dropdown.find(':input.select2-search__field').attr('placeholder', '');
 		});

 		var isWarehouseDataLoadedUP = true;

 		jQuery('#mrkv_ua_shipping_ukr-poshta_city').on('select2:select', function (e) {
 			let current_option = e.params.data;
 			isWarehouseDataLoadedUP = false;
 			jQuery(this).val(current_option.id);

	    	jQuery('#mrkv_ua_shipping_ukr-poshta_city_ref').val(current_option.ref);
	    	jQuery('#mrkv_ua_shipping_ukr-poshta_area_name').val(current_option.area);
	    	jQuery(this).removeClass('ui-autocomplete-loading');
	        
	        jQuery.ajax({
	            type: 'POST',
	            url: mrkv_ua_ship_helper.ajax_url,
	            data: {
	                action: 'mrkv_ua_ship_ukr_poshta_warehouse',
	                ref: current_option.ref,
	                nonce: mrkv_ua_ship_helper.nonce,
	            },
	            beforeSend: function() {
	                if (jQuery('#mrkv_ua_shipping_ukr-poshta_warehouse').length != 0) {
	                    jQuery('#mrkv_ua_shipping_ukr-poshta_warehouse').find('option').remove();
	                    jQuery('#mrkv_ua_shipping_ukr-poshta_warehouse').addClass('mrkv-ua-shipping-loading');
	                }
	            },
	            success: function (json) {
	                var data = JSON.parse(json);
	               	if(data)
	               	{
               			jQuery.each(data, function(key, value) {
			                jQuery('#mrkv_ua_shipping_ukr-poshta_warehouse')
			                .append(jQuery("<option></option>")
			                  .attr('value', this.label)
			                  .text(this.label)
			                  .attr('data-ref', this.value)
			                );
		              	});

		              	let first_element = jQuery('#mrkv_ua_shipping_ukr-poshta_warehouse option:first').val();
               			jQuery('#mrkv_ua_shipping_ukr-poshta_warehouse').val(first_element).trigger('change');
	               	}

	               	jQuery('#mrkv_ua_shipping_ukr-poshta_warehouse').removeClass('mrkv-ua-shipping-loading');
	               	isWarehouseDataLoadedUP = true; 
	            }
	        });
		});
 	}

 	if(jQuery('#mrkv_ua_shipping_ukr-poshta_warehouse').length != 0)
 	{
 		jQuery('#mrkv_ua_shipping_ukr-poshta_warehouse').select2();

 		jQuery('#mrkv_ua_shipping_ukr-poshta_warehouse').on('select2:opening', function(e) {
	        if (!isWarehouseDataLoadedUP) {
	            e.preventDefault();
	        }
	    });

 		let mrkv_ua_ship_warehouse = jQuery('#mrkv_ua_shipping_ukr-poshta_city_ref').val();
 		let mrkv_ua_ship_choosen_warehouse = jQuery('#mrkv_ua_shipping_ukr-poshta_warehouse_ref').val();

 		if(mrkv_ua_ship_warehouse)
 		{
 			jQuery.ajax({
	            type: 'POST',
	            url: mrkv_ua_ship_helper.ajax_url,
	            data: {
	                action: 'mrkv_ua_ship_ukr_poshta_warehouse',
	                ref: mrkv_ua_ship_warehouse,
	                nonce: mrkv_ua_ship_helper.nonce,
	            },
	            beforeSend: function() {
	                if (jQuery('#mrkv_ua_shipping_ukr-poshta_warehouse').length != 0) {
	                    jQuery('#mrkv_ua_shipping_ukr-poshta_warehouse').find('option:not(:first-child)').remove();
	                    jQuery('#mrkv_ua_shipping_ukr-poshta_warehouse').addClass('mrkv-ua-shipping-loading');
	                }
	            },
	            success: function (json) {
	                var data = JSON.parse(json);

	               	if(data)
	               	{
	           			jQuery.each(data, function(key, value) {
			                jQuery('#mrkv_ua_shipping_ukr-poshta_warehouse')
			                .append(jQuery("<option></option>")
			                  .attr('value', this.label)
			                  .text(this.label)
			                  .attr('data-ref', this.value)
			                );
		              });

	           			if(mrkv_ua_ship_choosen_warehouse)
	           			{
	           				jQuery('#mrkv_ua_shipping_ukr-poshta_warehouse option[data-ref="' + mrkv_ua_ship_choosen_warehouse + '"]').attr('selected','selected');
	           			}
	               	}

	               	jQuery('#mrkv_ua_shipping_ukr-poshta_warehouse').removeClass('mrkv-ua-shipping-loading');
	            }
	        });
 		}

 		jQuery('body').on('change', '#mrkv_ua_shipping_ukr-poshta_warehouse', function() {
		    let option_selected = jQuery(this).find('option:selected');
		    jQuery('#mrkv_ua_shipping_ukr-poshta_warehouse_ref').val(jQuery(option_selected).attr('data-ref'));
		    mrkvUaShipUpdateCartUkr();

		    jQuery.ajax({
                type: 'POST',
                url: mrkv_ua_ship_helper.ajax_url,
                data: {
                    action: 'mrkv_ua_ship_ukr_poshta_warehouse_id',
                    warehouse_name: jQuery(option_selected).attr('data-ref'),
                    nonce: mrkv_ua_ship_helper.nonce,
                },
                success: function (data) {
                    if(data)
                    {
                        jQuery('#mrkv_ua_shipping_ukr-poshta_address_ref').val(data.replace(/['"]+/g, ''));
                    }                   
                }
            });
		});
 	}

 	/** UKR POSHTA SHIPPING ADDRESS **/

 	if(jQuery('#mrkv_ua_shipping_ukr-poshta_address_city').length != 0)
 	{
 		jQuery('#mrkv_ua_shipping_ukr-poshta_address_city').select2(up_settings_city_select);

 		let ukr_poshta_address_city = jQuery('#mrkv_ua_shipping_ukr-poshta_address_city').attr('data-default');

 		if(ukr_poshta_address_city)
 		{
 			jQuery('#mrkv_ua_shipping_ukr-poshta_address_city').val(ukr_poshta_address_city).trigger('change');
 		}

 		jQuery('#mrkv_ua_shipping_ukr-poshta_address_city').on('select2:opening', function (e) {
 			jQuery(this).data('select2').$dropdown.find(':input.select2-search__field').attr('placeholder', mrkv_ua_ship_helper.city_placeholder);
 		});
 		jQuery('#mrkv_ua_shipping_ukr-poshta_address_city').on('select2:closing', function (e) {
 			jQuery(this).data('select2').$dropdown.find(':input.select2-search__field').attr('placeholder', '');
 		});

 		jQuery('#mrkv_ua_shipping_ukr-poshta_address_city').on('select2:select', function (e) {
 			let current_option = e.params.data;
 			jQuery(this).val(current_option.id);
	        
	    	jQuery('#mrkv_ua_shipping_ukr-poshta_address_city_ref').val(current_option.ref);
	    	jQuery('#mrkv_ua_shipping_ukr-poshta_address_area_name').val(current_option.area);
	    	jQuery('#mrkv_ua_shipping_ukr-poshta_address_area_id').val(current_option.area_id);
	    	jQuery('#mrkv_ua_shipping_ukr-poshta_address_district_id').val(current_option.district_id);
	    	jQuery(this).removeClass('ui-autocomplete-loading');
	        
	        jQuery.ajax({
	            type: 'POST',
	            url: mrkv_ua_ship_helper.ajax_url,
	            data: {
	                action: 'mrkv_ua_ship_ukr_poshta_street',
	                ref: current_option.ref,
	                nonce: mrkv_ua_ship_helper.nonce,
	            },
	            beforeSend: function() {
	                if (jQuery('#mrkv_ua_shipping_ukr-poshta_address_street').length != 0) {
	                    jQuery('#mrkv_ua_shipping_ukr-poshta_address_street').find('option:not(:first-child)').remove();
	                    jQuery('#mrkv_ua_shipping_ukr-poshta_address_street').addClass('mrkv-ua-shipping-loading');
	                }
	            },
	            success: function (json) {
	                var data = JSON.parse(json);

	               	if(data)
	               	{
               			jQuery.each(data, function(key, value) {
			                jQuery('#mrkv_ua_shipping_ukr-poshta_address_street')
			                .append(jQuery("<option></option>")
			                  .attr('value', this.label)
			                  .text(this.label)
			                  .attr('data-ref', this.value)
			                );
		              });
	               	}

	               	jQuery('#mrkv_ua_shipping_ukr-poshta_address_street').removeClass('mrkv-ua-shipping-loading');
	            }
	        });
		});
 	}

 	if(jQuery('#mrkv_ua_shipping_ukr-poshta_address_street').length != 0)
 	{
 		jQuery('#mrkv_ua_shipping_ukr-poshta_address_street').select2();

 		jQuery('body').on('change', '#mrkv_ua_shipping_ukr-poshta_address_street', function() {
		    let option_selected = jQuery(this).find('option:selected');
		    jQuery('#mrkv_ua_shipping_ukr-poshta_address_street_ref').val(jQuery(option_selected).attr('data-ref'));

		    jQuery.ajax({
	            type: 'POST',
	            url: mrkv_ua_ship_helper.ajax_url,
	            data: {
	                action: 'mrkv_ua_ship_ukr_poshta_house',
	                ref: jQuery(option_selected).attr('data-ref'),
	                nonce: mrkv_ua_ship_helper.nonce,
	            },
	            beforeSend: function() {
	                if (jQuery('#mrkv_ua_shipping_ukr-poshta_address_house').length != 0) {
	                    jQuery('#mrkv_ua_shipping_ukr-poshta_address_house').find('option:not(:first-child)').remove();
	                    jQuery('#mrkv_ua_shipping_ukr-poshta_address_house').addClass('mrkv-ua-shipping-loading');
	                }
	            },
	            success: function (json) {
	                var data = JSON.parse(json);

	               	if(data)
	               	{
               			jQuery.each(data, function(key, value) {
			                jQuery('#mrkv_ua_shipping_ukr-poshta_address_house')
			                .append(jQuery("<option></option>")
			                  .attr('value', this.label)
			                  .text(this.label)
			                  .attr('data-ref', this.value)
			                );
		              });
	               	}

	               	jQuery('#mrkv_ua_shipping_ukr-poshta_address_house').removeClass('mrkv-ua-shipping-loading');
	            }
	        });
		});
 	}

 	if(jQuery('#mrkv_ua_shipping_ukr-poshta_address_house').length != 0)
 	{
 		jQuery('#mrkv_ua_shipping_ukr-poshta_address_house').select2();

 		jQuery('body').on('change', '#mrkv_ua_shipping_ukr-poshta_address_house', function() {
		    let option_selected = jQuery(this).find('option:selected');
		    jQuery('#mrkv_ua_shipping_ukr-poshta_address_house_ref').val(jQuery(option_selected).attr('data-ref'));
		    mrkvUaShipUpdateCartUkr();
		    jQuery('#mrkv_ua_shipping_ukr-poshta_address_flat').val();
		    jQuery('#mrkv_ua_shipping_ukr-poshta_address_address_ref').val();
		});
 	}

 	if(jQuery('#mrkv_ua_shipping_ukr-poshta_address_flat').length != 0)
 	{
 		var mrkv_ua_typing_timer;
	    var mrkv_ua_done_typing_interval = 2000;

	    jQuery('#mrkv_ua_shipping_ukr-poshta_address_flat').on('keyup', function() 
	    {
	        clearTimeout(mrkv_ua_typing_timer);
	        mrkv_ua_typing_timer = setTimeout(mrkvUaShipGetUUID, mrkv_ua_done_typing_interval);       
	    });

	    jQuery('#mrkv_ua_shipping_ukr-poshta_address_flat').on('keydown', function() 
	    {
	        clearTimeout(mrkv_ua_typing_timer);       
	    });

	    function mrkvUaShipGetUUID() 
	    {
	        let postcode = jQuery('#mrkv_ua_shipping_ukr-poshta_address_house_ref').val();
			let country = 'UA';
			let region = jQuery('#mrkv_ua_shipping_ukr-poshta_address_area_name').val();
			let city = jQuery('#mrkv_ua_shipping_ukr-poshta_address_city').val();
			let street = jQuery('#mrkv_ua_shipping_ukr-poshta_address_street').val();
			let apartment_number = jQuery('#mrkv_ua_shipping_ukr-poshta_address_house').val();

	        if(postcode && country && region && city && street && apartment_number)
	        {
	            jQuery.ajax({
	                type: 'POST',
	                url: mrkv_ua_ship_helper.ajax_url,
	                data: {
	                    action: 'mrkv_ua_ship_ukr_poshta_address_id',
	                    postcode: postcode,
						country: country,
						region: region,
						city: city,
						street: street,
						apartment_number: apartment_number,
						nonce: mrkv_ua_ship_helper.nonce,
	                },
	                success: function (data) {
	                    if(data)
	                    {
	                        jQuery('#mrkv_ua_shipping_ukr-poshta_address_address_ref').val(data.replace(/['"]+/g, ''));
	                    }                   
	                }
	            });
	        }
	    }
 	}

 	if(jQuery('#mrkv_ua_shipping_ukr-poshta_international_flat').length != 0)
 	{
 		var mrkv_ua_inter_typing_timer;
	    var mrkv_ua_inter_done_typing_interval = 2000;

	    jQuery('#mrkv_ua_shipping_ukr-poshta_international_flat').on('keyup', function() 
	    {
	        clearTimeout(mrkv_ua_inter_typing_timer);
	        mrkv_ua_inter_typing_timer = setTimeout(mrkvUaShipInterGetUUID, mrkv_ua_inter_done_typing_interval);       
	    });

	    jQuery('#mrkv_ua_shipping_ukr-poshta_international_flat').on('keydown', function() 
	    {
	        clearTimeout(mrkv_ua_inter_typing_timer);       
	    });

	    function mrkvUaShipInterGetUUID() 
	    {
	        let postcode = jQuery('#mrkv_ua_shipping_ukr-poshta_international_postcode').val();
			var country = (jQuery('#ship-to-different-address-checkbox').is(':checked') ? jQuery('#shipping_country').val() : jQuery('#billing_country').val()) || '';
			let region = jQuery('#mrkv_ua_shipping_ukr-poshta_international_region').val();
			let city = jQuery('#mrkv_ua_shipping_ukr-poshta_international_city').val();
			let street = jQuery('#mrkv_ua_shipping_ukr-poshta_international_street').val();
			let apartment_number = jQuery('#mrkv_ua_shipping_ukr-poshta_international_house').val();

	        if(postcode && country && region && city && street && apartment_number)
	        {
	            jQuery.ajax({
	                type: 'POST',
	                url: mrkv_ua_ship_helper.ajax_url,
	                data: {
	                    action: 'mrkv_ua_ship_ukr_poshta_address_id',
	                    postcode: postcode,
						country: country,
						region: region,
						city: city,
						street: street,
						apartment_number: apartment_number,
						nonce: mrkv_ua_ship_helper.nonce,
	                },
	                success: function (data) {
	                    if(data)
	                    {
	                        jQuery('#mrkv_ua_shipping_ukr-poshta_international_address_ref').val(data.replace(/['"]+/g, ''));
	                    }                   
	                }
	            });
	        }
	    }
 	}

 	if(jQuery('#mrkv_ua_shipping_ukr-poshta_patronymic_field').length != 0)
 	{
 		if(mrkv_ua_ship_helper.up_middlename_exclude == 'yes')
 		{
 			jQuery('#mrkv_ua_shipping_ukr-poshta_patronymic_field').hide();
 		}
 		if(mrkv_ua_ship_helper.up_middlename_required == 'no')
 		{
 			jQuery('#mrkv_ua_shipping_ukr-poshta_patronymic_enabled').val('off');
 			jQuery('label[for="mrkv_ua_shipping_ukr-poshta_patronymic"] abbr').hide();
 			jQuery('label[for="mrkv_ua_shipping_ukr-poshta_patronymic"]').find('.required').remove();
 		}
 		else
 		{
 			var $label = jQuery('label[for="mrkv_ua_shipping_ukr-poshta_patronymic"]');

		    if ($label.length && !$label.find('.require').length) {
		        $label.append(' <span class="require" aria-hidden="true">*</span>');
		    }
 		}
 	}

 	function checkPaymentMethod() 
 	{
 		if(mrkv_ua_ship_helper.up_middlename_exclude == 'yes')
 		{
 			jQuery('#mrkv_ua_shipping_ukr-poshta_patronymic_field').hide();
 		}
 		if(mrkv_ua_ship_helper.up_middlename_required == 'no')
 		{
 			jQuery('#mrkv_ua_shipping_ukr-poshta_patronymic_enabled').val('off');
 			jQuery('label[for="mrkv_ua_shipping_ukr-poshta_patronymic"] abbr').hide();
 			jQuery('label[for="mrkv_ua_shipping_ukr-poshta_patronymic"]').find('.required').remove();
 		}
 		else
 		{
 			var $label = jQuery('label[for="mrkv_ua_shipping_ukr-poshta_patronymic"]');

		    if ($label.length && !$label.find('.require').length) {
		        $label.append(' <span class="require" aria-hidden="true">*</span>');
		    }
 		}
 		
	    var selected = jQuery('input[name="payment_method"]:checked').val();

	    if (selected === 'cod') 
	    {
	    	jQuery('#mrkv_ua_shipping_ukr-poshta_patronymic_field').show();
	    	jQuery('label[for="mrkv_ua_shipping_ukr-poshta_patronymic"] abbr').show();

	    	var $label = jQuery('label[for="mrkv_ua_shipping_ukr-poshta_patronymic"]');

		    if ($label.length && !$label.find('.require').length) {
		        $label.append(' <span class="require" aria-hidden="true">*</span>');
		    }
	    }
	}

 	checkPaymentMethod();

 	jQuery('form.checkout').on('change', 'input[name="payment_method"]', function(){
        checkPaymentMethod();
    });

    jQuery(document.body).on('updated_checkout', function(){
        checkPaymentMethod();
    });

 	function mrkvUaShipUpdateCartUkr()
 	{
 		jQuery('body').trigger('update_checkout', { update_shipping_method: true });
	}
});