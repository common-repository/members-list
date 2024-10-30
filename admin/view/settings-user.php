<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>
<?php global $ml_options; ?>
<div id="tab-user">
	<h3><?php echo _e('User Settings','members-list-pro'); ?></h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="allow_display"><?php _e('Allow users to edit which members lists they\'re in','members-list-pro'); ?> <strong>(<?php _e('PRO Only','members-list-pro'); ?>)</strong>:</label></th>
			<td>
				<input disabled type="checkbox" class="switchery" name="allow_display" value="1" />
				<span class="description"><?php _e('If set to yes, users will be able to add or remove themselves from lists.','members-list-pro'); ?></span>
			</td>
		</tr>
	</table>
	<p class="submit"><input type="submit" name="submit" class="button-primary tern-button tern-button-medium" value="<?php echo _e('Save Changes','members-list-pro'); ?>" /></p>
</div>
