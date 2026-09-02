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

	mrkv_ua_ship_helper.rozetka_city_area.map(function(item) {
        default_cities.push({ id: item.label, text: item.label, ref: item.value, area: item.area });
    });

    var $select;

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
		    	$select = jQuery(this);
		    	if(params.term && params.term.length > 2)
		    	{
		    		var query = {
				      	action: 'mrkv_ua_ship_rozetka_delivery_city',
				        name: params.term,
				        nonce: mrkv_ua_ship_helper.nonce,
				    }
		    	}
		    	else
		    	{
		    		var query = {
				      	action: 'mrkv_ua_ship_rozetka_delivery_city',
				      	nonce: mrkv_ua_ship_helper.nonce,
				    }
		    	}

		      return query;
		    },
		    beforeSend: function (e) {
		    	var current_select_name = 'select2-' + jQuery($select).attr('name') + '-results';
		    	jQuery('input[aria-owns="' + current_select_name + '"]').prop('disabled', true);
		    	jQuery('input[aria-owns="' + current_select_name + '"]').closest('.select2-search').append('<span class="mrkv-public-loader"></span>');

		    },
		    complete: function () {
		    	var current_select_name = 'select2-' + jQuery($select).attr('name') + '-results';
		    	jQuery('input[aria-owns="' + current_select_name + '"]').prop('disabled', false);
		    	jQuery('input[aria-owns="' + current_select_name + '"]').focus();
		    	jQuery('.mrkv-public-loader').remove();
            },
		    processResults: function (json) {
		    	var data;
		    
		    	if(typeof json == 'string')
		    	{
		    		data = JSON.parse(json);

		    		return {
				        results: data.map(function(item) {
	                        return { id: item.label, text: item.label, ref: item.value, area: item.area, district: item.district, city: item.city_label, area_id: item.area_id, district_id: item.district_id };
	                    })
				    };
		    	}
		    	else
		    	{
		    		data = json;

		    		return {
				        results: mrkv_ua_ship_helper.rozetka_city_area.map(function(item) {
	                        return { id: item.label, text: item.label, ref: item.value, area: item.area };
	                    })
				    };	
		    	}
		    },
	  	},
	};

	/** ROZETKA SHIPPING **/

	if(jQuery('#mrkv_ua_shipping_rozetka-delivery_city').length != 0)
 	{
 		jQuery('#mrkv_ua_shipping_rozetka-delivery_city').select2(rztk_settings_city_select);

 		let rztk_city = jQuery('#mrkv_ua_shipping_rozetka-delivery_city').attr('data-default');

 		if(rztk_city)
 		{
 			jQuery('#mrkv_ua_shipping_rozetka-delivery_city').val(rztk_city).trigger('change');
 		}

 		jQuery('#mrkv_ua_shipping_rozetka-delivery_city').on('select2:opening', function (e) {
 			jQuery(this).data('select2').$dropdown.find(':input.select2-search__field').attr('placeholder', mrkv_ua_ship_helper.city_placeholder);
 		});
 		jQuery('#mrkv_ua_shipping_rozetka-delivery_city').on('select2:closing', function (e) {
 			jQuery(this).data('select2').$dropdown.find(':input.select2-search__field').attr('placeholder', '');
 		});

 		var isWarehouseDataLoadedRZTK = true;

 		jQuery('#mrkv_ua_shipping_rozetka-delivery_city').on('select2:select', function (e) {
 			let current_option = e.params.data;
 			isWarehouseDataLoadedRZTK = false;
 			jQuery(this).val(current_option.id);

	    	jQuery('#mrkv_ua_shipping_rozetka-delivery_city_ref').val(current_option.ref);
	    	jQuery('#mrkv_ua_shipping_rozetka-delivery_area_name').val(current_option.area);
	    	jQuery('#mrkv_ua_shipping_rozetka-delivery_district').val(current_option.district);
	    	jQuery('#mrkv_ua_shipping_rozetka-delivery_city_label').val(current_option.city);
	    	jQuery('#mrkv_ua_shipping_rozetka-delivery_area_id').val(current_option.area_id);
	    	jQuery('#mrkv_ua_shipping_rozetka-delivery_district_id').val(current_option.district_id);
	    	jQuery(this).removeClass('ui-autocomplete-loading');
	        
	        jQuery.ajax({
	            type: 'POST',
	            url: mrkv_ua_ship_helper.ajax_url,
	            data: {
	                action: 'mrkv_ua_ship_rozetka_delivery_warehouse',
	                ref: current_option.ref,
	                nonce: mrkv_ua_ship_helper.nonce,
	            },
	            beforeSend: function() {
	                if (jQuery('#mrkv_ua_shipping_rozetka-delivery_warehouse').length != 0) {
	                    jQuery('#mrkv_ua_shipping_rozetka-delivery_warehouse').find('option').remove();
	                    jQuery('#mrkv_ua_shipping_rozetka-delivery_warehouse').addClass('mrkv-ua-shipping-loading');
	                }
	            },
	            success: function (json) {
	                var data = JSON.parse(json);
	               	if(data)
	               	{
               			jQuery.each(data, function(key, value) {
			                jQuery('#mrkv_ua_shipping_rozetka-delivery_warehouse')
			                .append(jQuery("<option></option>")
			                  .attr('value', this.label)
			                  .text(this.label)
			                  .attr('data-ref', this.value)
			                );
		              	});

		              	let first_element = jQuery('#mrkv_ua_shipping_rozetka-delivery_warehouse option:first').val();
               			jQuery('#mrkv_ua_shipping_rozetka-delivery_warehouse').val(first_element).trigger('change');
	               	}

	               	jQuery('#mrkv_ua_shipping_rozetka-delivery_warehouse').removeClass('mrkv-ua-shipping-loading');
	               	isWarehouseDataLoadedRZTK = true;
	            }
	        });
		});
 	}

 	if(jQuery('#mrkv_ua_shipping_rozetka-delivery_warehouse').length != 0)
 	{
 		jQuery('#mrkv_ua_shipping_rozetka-delivery_warehouse').select2();

 		jQuery('#mrkv_ua_shipping_rozetka-delivery_warehouse').on('select2:opening', function(e) {
	        if (!isWarehouseDataLoadedRZTK) {
	            e.preventDefault();
	        }
	    });

	    jQuery('body').on('change', '#mrkv_ua_shipping_rozetka-delivery_warehouse', function() {
		    let option_selected = jQuery(this).find('option:selected');
		    jQuery('#mrkv_ua_shipping_rozetka-delivery_warehouse_ref').val(jQuery(option_selected).attr('data-ref'));
		});
 	}
});