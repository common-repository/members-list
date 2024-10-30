<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>
<?php global $ml_options; ?>
<div id="tab-display">
	<h3><?php echo _e('Display Settings','members-list-pro'); ?></h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="noun"><?php _e('Use a word other than "Member" on the front-end (Singular)','members-list-pro'); ?></label></th>
			<td>
				<input type="text" name="noun_single" class="regular-text" value="<?php echo isset($ml_options['noun_single']) ? $ml_options['noun_single'] : ''; ?>" />
				<span class="setting-description"><?php _e('i.e. "Client" or "User"','members-list-pro'); ?></span>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><label for="noun"><?php _e('Use a word other than "Members" on the front-end (Plural)','members-list-pro'); ?></label></th>
			<td>
				<input type="text" name="noun" class="regular-text" value="<?php echo isset($ml_options['noun']) ? $ml_options['noun'] : ''; ?>" />
				<span class="setting-description"><?php _e('i.e. "Clients" or "Users"','members-list-pro'); ?></span>
			</td>
		</tr>
	</table>
	<p class="submit"><input type="submit" name="submit" class="button-primary tern-button tern-button-medium" value="<?php echo _e('Save Changes','members-list-pro'); ?>" /></p>
</div>
