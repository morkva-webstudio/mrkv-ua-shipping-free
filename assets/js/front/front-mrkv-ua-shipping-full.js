jQuery(window).on('load', function() 
{
	if (jQuery('form[name="checkout"]').length == 0) return;

	setTimeout(function(){
		let mrkv_ua_current_shipping = mrkvUaShipGetCurrentShipping();

		if(mrkv_ua_current_shipping && ~mrkv_ua_current_shipping.indexOf("mrkv_ua_shipping"))
		{
			mrkvUaShipDisableDefaultFieldsforup();
			mrkvUaShipShowGroup(mrkv_ua_current_shipping);
		}
		else
		{
			mrkvUaShipEnableDefaultFieldsforup();
			mrkvUaShipHideAllGroups();
		}
	}, 200);

 	jQuery( document.body ).on( 'updated_checkout', () => {
 		let mrkv_ua_current_shipping = mrkvUaShipGetCurrentShipping();

 		if(mrkv_ua_current_shipping && ~mrkv_ua_current_shipping.indexOf("mrkv_ua_shipping"))
 		{
 			mrkvUaShipDisableDefaultFieldsforup();
 			mrkvUaShipShowGroup(mrkv_ua_current_shipping);
 		}
 		else
 		{
 			mrkvUaShipEnableDefaultFieldsforup();
 			mrkvUaShipHideAllGroups();
 		}
 	});

 	function mrkvUaShipGetCurrentShipping()
 	{
 		let mrkv_ua_current_shipping = jQuery('.shipping_method').length > 1 ?
	      jQuery('.shipping_method:checked').val() :
	      jQuery('.shipping_method').val();

	      if(mrkv_ua_current_shipping)
	      {
	      	mrkv_ua_current_shipping = mrkv_ua_current_shipping.replace(/_\d+$/, '');
	      }

	      return mrkv_ua_current_shipping;
 	}

 	function mrkvUaShipDisableDefaultFieldsforup()
 	{
 		if(jQuery('input[name="ship_to_different_address"]').length != 0 && jQuery('input[name="ship_to_different_address"]').is(':checked'))
 		{
 			jQuery('#shipping_address_1_field').addClass('mrkv-ua-shipping-disabled');
		    jQuery('#shipping_address_2_field').addClass('mrkv-ua-shipping-disabled');
		    jQuery('#shipping_city_field').addClass('mrkv-ua-shipping-disabled');
		    jQuery('#shipping_state_field').addClass('mrkv-ua-shipping-disabled');
		    jQuery('#shipping_postcode_field').addClass('mrkv-ua-shipping-disabled');
 		}
 		jQuery('#billing_address_1_field').addClass('mrkv-ua-shipping-disabled');
	    jQuery('#billing_address_2_field').addClass('mrkv-ua-shipping-disabled');
	    jQuery('#billing_city_field').addClass('mrkv-ua-shipping-disabled');
	    jQuery('#billing_state_field').addClass('mrkv-ua-shipping-disabled');
	    jQuery('#billing_postcode_field').addClass('mrkv-ua-shipping-disabled');
 	}

 	function mrkvUaShipEnableDefaultFieldsforup()
 	{
 		jQuery('.mrkv-ua-shipping-disabled').removeClass('mrkv-ua-shipping-disabled');
 	}

 	function mrkvUaShipShowGroup(mrkv_ua_current_shipping)
 	{
 		jQuery('.mrkv_ua_shipping_checkout_fields').hide();
 		jQuery('.mrkv_ua_shipping_inner_field_arg').hide();
 		jQuery('#' + mrkv_ua_current_shipping + '_fields').show();
 		jQuery('.' + mrkv_ua_current_shipping + '.mrkv_ua_shipping_inner_field_arg').show();
 	}
 	function mrkvUaShipHideAllGroups()
 	{
 		jQuery('.mrkv_ua_shipping_checkout_fields').hide();
 		jQuery('.mrkv_ua_shipping_inner_field_arg').hide();
 	}
});