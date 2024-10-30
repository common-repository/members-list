<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>
<?php global $ml_options; ?>
<div id="tab-api">
	<h3><?php echo _e('API Settings','members-list-pro'); ?></h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="api_key"><?php _e('Google API Key (Server)','members-list-pro'); ?></label></th>
			<td>
				<input type="text" name="api_key" class="regular-text" value="<?php echo isset($ml_options['api_key']) ? $ml_options['api_key'] : ''; ?>" />
				<br />
				<span class="description"><?php _e('Use this if you want to use the geolocation and radius search functionality.','members-list-pro'); ?></span>
				<a href="https://www.ternstyle.us/members-list-plugin-for-wordpress/documentation/settings/google-settings/google-api-key" target="_blank"><?php _e('Instructions','members-list-pro'); ?></a>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="api_key_browser"><?php _e('Google API Key (Browser)','members-list-pro'); ?> <strong>(<?php _e('PRO Only','members-list-pro'); ?>)</strong></label></th>
			<td>
				<input type="text" name="api_key_browser" class="regular-text" value="<?php echo isset($ml_options['api_key_browser']) ? $ml_options['api_key_browser'] : ''; ?>" disabled />
				<br />
				<span class="description"><?php _e('Use this if you want to use the Google Maps functionality.','members-list-pro'); ?></span>
				<a href="https://www.ternstyle.us/members-list-plugin-for-wordpress/documentation/settings/google-settings/google-browser-api-key" target="_blank"><?php _e('Instructions','members-list-pro'); ?></a>
			</td>
		</tr>
	</table>
	<p class="submit"><input type="submit" name="submit" class="button-primary tern-button tern-button-medium" value="<?php echo _e('Save Changes','members-list-pro'); ?>" /></p>
</div>
