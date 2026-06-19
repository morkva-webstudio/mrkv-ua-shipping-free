jQuery(window).on('load', function() 
{
	/** UKR POSHTA SHIPPING **/
	var up_settings_city_select = { 
		minimumInputLength: 3,
			ajax: {
				delay: 200,
		    url: mrkv_ua_ship_helper.ajax_url,
		    type: "POST",
		    data: function (params) {
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

	if(jQuery('#mrkv_ua_shipping_ukr-poshta_city').length != 0)
 	{
 		jQuery('#mrkv_ua_shipping_ukr-poshta_city').select2(up_settings_city_select);

 		jQuery('#mrkv_ua_shipping_ukr-poshta_city').on('select2:opening', function (e) {
 			jQuery(this).data('select2').$dropdown.find(':input.select2-search__field').attr('placeholder', mrkv_ua_ship_helper.city_placeholder);
 		});
 		jQuery('#mrkv_ua_shipping_ukr-poshta_city').on('select2:closing', function (e) {
 			jQuery(this).data('select2').$dropdown.find(':input.select2-search__field').attr('placeholder', '');
 		});

 		jQuery('#mrkv_ua_shipping_ukr-poshta_city').on('select2:select', function (e) {
 			let current_option = e.params.data;
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
	               	}

	               	jQuery('#mrkv_ua_shipping_ukr-poshta_warehouse').removeClass('mrkv-ua-shipping-loading');
	            }
	        });
		});
 	}

 	if(jQuery('#mrkv_ua_shipping_ukr-poshta_warehouse').length != 0)
 	{
 		jQuery('#mrkv_ua_shipping_ukr-poshta_warehouse').selectWoo();

 		let mrkv_ua_ship_warehouse = jQuery('#mrkv_ua_shipping_ukr-poshta_city_ref').val();
 		let mrkv_ua_ship_choosen_warehouse = jQuery('#mrkv_ua_shipping_ukr-poshta_warehouse_ref').val();

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

           			jQuery('#mrkv_ua_shipping_ukr-poshta_warehouse option[data-ref="' + mrkv_ua_ship_choosen_warehouse + '"]').attr('selected','selected');
               	}

               	jQuery('#mrkv_ua_shipping_ukr-poshta_warehouse').removeClass('mrkv-ua-shipping-loading');
            }
        });

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
 		jQuery('#mrkv_ua_shipping_ukr-poshta_address_street').selectWoo();

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
 		jQuery('#mrkv_ua_shipping_ukr-poshta_address_house').selectWoo();

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
			var let = (jQuery('#ship-to-different-address-checkbox').is(':checked') ? jQuery('#shipping_country').val() : jQuery('#billing_country').val()) || '';
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

 	if(jQuery('.mrkv_ua_ship_print_inv_ukr').length != 0)
 	{
 		jQuery('.mrkv_ua_ship_print_inv_ukr').click(function()
 		{
 			let form_name = jQuery(this).attr('data-form');

	 		jQuery('.' + form_name).submit();
 		});
 	}

 	function mrkvUaShipUpdateCartUkr()
 	{
	}
});