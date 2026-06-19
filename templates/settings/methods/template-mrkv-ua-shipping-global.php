<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="admin_mrkv_ua_shipping_page">
	<div class="admin_mrkv_ua_shipping_page__header">
		<?php 
			include MRKV_UA_SHIPPING_PLUGIN_PATH_TEMP . '/elements/template-mrkv-ua-shipping-header.php';
		?>
	</div>
	<div class="admin_mrkv_ua_shipping_page__links">
		<?php 
			include MRKV_UA_SHIPPING_PLUGIN_PATH_TEMP . '/elements/template-mrkv-ua-shipping-links.php';
		?>
	</div>
	<div class="admin_mrkv_ua_shipping_page__inner">
		<div class="admin_mrkv_ua_shipping__block col-mrkv-10">
			<div class="admin_mrkv_ua_shipping__info">
				<?php settings_errors(); ?>
			</div>
		</div>
		<div class="admin_mrkv_ua_shipping__block col-mrkv-10">
			<div class="admin_mrkv_ua_shipping__tabs">
				<?php 
					include MRKV_UA_SHIPPING_PLUGIN_PATH_TEMP . '/elements/template-mrkv-ua-shipping-tabs.php';
				?>
			</div>
		</div>
		<div class="admin_mrkv_ua_shipping__block col-mrkv-7">
			<form class="mrkv_ua_shipping_method_form" method="post" action="options.php">
				<?php settings_fields('mrkv-ua-shipping-' . MRKV_UA_SHIPPING_SETTINGS_SLUG .'-group'); ?>
				<div class="mrkv_block_rounded">
					<?php 
					include MRKV_UA_SHIPPING_PLUGIN_PATH_TEMP . '/settings/methods/shipping_fields/mrkv-ua-shipping-' . MRKV_UA_SHIPPING_SETTINGS_SLUG . '.php';
				?>
					<?php submit_button(__('Save', 'mrkv-ua-shipping')); ?>
				</div>
			</form>
		</div>
		<div class="admin_mrkv_ua_shipping__block col-mrkv-3">
			<?php 
				include MRKV_UA_SHIPPING_PLUGIN_PATH_TEMP . '/elements/template-mrkv-ua-shipping-support.php';
			?>
		</div>
	</div>
</div>