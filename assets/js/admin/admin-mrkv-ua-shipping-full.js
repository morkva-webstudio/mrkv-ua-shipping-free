function setCookie(key, value, expiry) {
    var expires = new Date();
    expires.setTime(expires.getTime() + (expiry * 24 * 60 * 60 * 1000));
    document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
}

function getCookie(key) {
    var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
    return keyValue ? keyValue[2] : null;
}

function eraseCookie(key) {
    var keyValue = getCookie(key);
    setCookie(key, keyValue, '-1');
}

var hash = window.location.hash;

if(!hash)
{
	var hash = getCookie('current_page_hash');

	if(hash)
	{
		window.location.hash = hash;
	}
	eraseCookie('current_page_hash');
}

if(hash)
{
	var tab_main = hash.replace('-mrkv','');
	tab_main = tab_main.replace('#','');
	jQuery('.admin_mrkv_ua_shipping__tabs_main__inner .active').removeClass('active');
	jQuery('.mrkv_up_ship_tab_btn[data-tab="' + tab_main + '"]').addClass('active');

	jQuery('.mrkv_up_ship_shipping_tab_block').removeClass('active');
	jQuery('#' + tab_main).addClass('active');
}

jQuery(window).on('load', function() 
{
	jQuery('.mrkv_ua_shipping_method_form').on('submit', function(e) {
		var hash = window.location.hash;

		if(hash)
		{
			setCookie('current_page_hash',hash,'1');
		}
	});
	if(jQuery('.mrkv_up_ship_tab_btn').length != 0)
 	{
		jQuery('.mrkv_up_ship_tab_btn').click(function()
		{
			jQuery('.admin_mrkv_ua_shipping__tabs_main__inner .active').removeClass('active');
			jQuery(this).addClass('active');

			const shipping_tab = jQuery(this).attr('data-tab');

			jQuery('.mrkv_up_ship_shipping_tab_block').removeClass('active');
			jQuery('#' + shipping_tab).addClass('active');
		});
	}
});