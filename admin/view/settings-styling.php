<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>
<?php global $ml_options; ?>
<div id="tab-styling">
	<h3><?php echo _e('Styling Settings','members-list-pro'); ?></h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="color"><?php _e('Color Picker','members-list-pro'); ?> <strong>(<?php _e('PRO Only','members-list-pro'); ?>)</strong>:</label></th>
			<td>
				<input disabled type="text" name="color" class="regular-text color-picker" value="<?php if(isset($ml_options['color']) and !empty($ml_options['color'])) { echo $ml_options['color']; } else { ?>#0000ff<?php } ?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="css"><?php _e('Use this plugin\'s CSS','members-list-pro'); ?>:</label></th>
			<td>
				<input type="checkbox" class="switchery" name="css" value="1" <?php if((isset($ml_options['css']) and $ml_options['css']) or !isset($ml_options['css'])) { echo 'checked'; } ?> />
				<span class="description"><?php _e('If set to yes, this plugin will control the look of the list and author pages with CSS.','members-list-pro'); ?></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="custom_css"><?php _e('Custom CSS','members-list-pro'); ?> <strong>(<?php _e('PRO Only','members-list-pro'); ?>)</strong>:</label></th>
			<td>
				<textarea disabled name="custom_css" rows="10" cols="50" class="large-text code"><?php echo isset($ml_options['custom_css']) ? $ml_options['custom_css'] : ''; ?></textarea>
			</td>
		</tr>
	</table>
	<p class="submit"><input type="submit" name="submit" class="button-primary tern-button tern-button-medium" value="<?php echo _e('Save Changes','members-list-pro'); ?>" /></p>
</div>
