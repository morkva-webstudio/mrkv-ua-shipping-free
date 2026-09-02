jQuery(window).on('load', function() 
{
	jQuery('#nova-global_m_ua_settings_shipment_length, #nova-global_m_ua_settings_shipment_width, #nova-global_m_ua_settings_shipment_height, #nova-global_m_ua_settings_shipment_weight')
        .on('keyup', function() {
            jQuery('#nova-global_m_ua_settings_shipment_volume')
                .val(mrkvnpGlobalCalcVolumeWeightSettings());
    });
        
	function mrkvnpGlobalCalcVolumeWeightSettings() {
	    let length = jQuery('#nova-global_m_ua_settings_shipment_length').val();
	    let width = jQuery('#nova-global_m_ua_settings_shipment_width').val();
	    let height = jQuery('#nova-global_m_ua_settings_shipment_height').val();
	    let weight = jQuery('#nova-global_m_ua_settings_shipment_weight').val();
	    let volumeWeight = length * width * height / 5000;
	    if (volumeWeight > weight && weight > 5) {
	        return volumeWeight;
	    } else {
	        return weight;
	    }
	}
});