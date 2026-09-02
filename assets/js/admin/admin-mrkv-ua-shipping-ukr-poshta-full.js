jQuery(window).on('load', function() 
{
	jQuery('.admin_ua_ship_morkva_settings_line select').select2({
        width: '100%',
    });

    jQuery('#ukr-poshta_m_ua_settings_sender_type').change(function()
    {
    	var current_sender_type = jQuery(this).val();

    	jQuery('.ukr-poshta_m_ua_settings_sender_type_block').removeClass('active');

    	if(current_sender_type){
    		jQuery('.ukr-poshta_m_ua_settings_sender_type_block[data-sender="' + current_sender_type + '"]').addClass('active');
    	}
    	else{
    		jQuery('.ukr-poshta_m_ua_settings_sender_type_block[data-sender="DEFAULT"]').addClass('active');
    	}
    });

    var mrkv_ua_typing_timer;
    var mrkv_ua_done_typing_interval = 2000;

    jQuery('#ukr-poshta_m_ua_settings_sender_warehouse_name').on('keyup', function() 
    {
        clearTimeout(mrkv_ua_typing_timer);
        mrkv_ua_typing_timer = setTimeout(mrkvUaShipGetUUID, mrkv_ua_done_typing_interval);       
    });

    jQuery('#ukr-poshta_m_ua_settings_sender_warehouse_name').on('keydown', function() 
    {
        clearTimeout(mrkv_ua_typing_timer);       
    });

    function mrkvUaShipGetUUID() 
    {
        let warehouse_name = jQuery('#ukr-poshta_m_ua_settings_sender_warehouse_name').val();

        if(warehouse_name)
        {
            jQuery.ajax({
                type: 'POST',
                url: mrkv_ua_ship_helper.ajax_url,
                data: {
                    action: 'mrkv_ua_ship_ukr_poshta_warehouse_id',
                    warehouse_name: warehouse_name,
                    nonce: mrkv_ua_ship_helper.nonce,
                },
                success: function (data) {
                    if(data)
                    {
                        jQuery('#ukr-poshta_m_ua_settings_sender_warehouse_id').val(data.replace(/['"]+/g, ''));
                    }                   
                }
            });
        }
    }

    var $enabled  = jQuery('#ukr-poshta_m_ua_settings_checkout_middlename_enabled');
    var $required = jQuery('#ukr-poshta_m_ua_settings_checkout_middlename_required');

    function toggleRequiredState() {
        if ($enabled.is(':checked')) {
            $required.prop('disabled', false);
        } else {
            $required.prop('checked', false);
            $required.prop('disabled', true);
        }
    }

    toggleRequiredState();
    $enabled.on('change', function(){
        toggleRequiredState();
    });

    jQuery('.adm-textarea-btn').on('click', function() 
    {
         var shotcode = jQuery(this).attr('data-added');
         var exist_text = jQuery(this).closest('.admin_ua_ship_morkva_settings_line').find('textarea').val();
         var new_text_data = '' + exist_text + shotcode;
         jQuery(this).closest('.admin_ua_ship_morkva_settings_line').find('textarea').val(new_text_data);
    });

    jQuery('.adm_morkva_row_size input[type="number"]').on('input', function() {
        let max = parseFloat(jQuery(this).attr('max'));
        let min = parseFloat(jQuery(this).attr('min'));
        let value = parseFloat(jQuery(this).val());

        if (value > max) {
            jQuery(this).val(max);
        } else if (value < min) {
            jQuery(this).val(min);
        }
    });
});