<?php use MLP\ternstyle\tern_select as tern_select; ?>
<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>
<?php global $ml_options; ?>
<div id="tab-author">
	<h3><?php echo _e('Author Settings','members-list-pro'); ?></h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="author_page"><?php _e('Use this plugin\'s author page','members-list-pro'); ?> <strong>(<?php _e('PRO Only','members-list-pro'); ?>)</strong>:</label></th>
			<td>
				<input disabled type="checkbox" class="switchery" name="author_page" value="1" />
				<span class="description"><?php _e('If set to yes, this plugin will take over the profile pages for all of your site\'s users.','members-list-pro'); ?></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="author_template"><?php _e('Choose the theme template to use for author pages','members-list-pro'); ?> <strong>(<?php _e('PRO Only','members-list-pro'); ?>)</strong>:</label></th>
			<td>
				<?php echo (new tern_select)->create([
					'type'			=>	'select',
					'data'			=>	$templates,
					'name'			=>	'author_template',
					'select_value'		=>	'select',
					'selected'		=>	(isset($ml_options['author_template']) ? [$ml_options['author_template']] : []),
					'localization'		=>	'members-list-pro',
					'disabled'		=>	true,
				]); ?>
				<span class="description"><?php _e("It is best to use your theme's page.php template.",'members-list-pro'); ?></span>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><label for="author_page"><?php _e('Which fields would you like to display on your author pages','members-list-pro'); ?> <strong>(<?php _e('PRO Only','members-list-pro'); ?>)</strong>:</label></th>
			<td>
				<?php if(isset($ml_options['author_field']) and !empty($ml_options['author_field'])) { ?>
				<?php $x=0;foreach((array)$ml_options['author_field'] as $k => $v) { ?>
				<div class="clonable<?php if($x > 0) { ?> cloned<?php } ?>">
					<?php echo (new tern_select)->create(array(
						'type'			=>	'tiered',
						'data'			=>	(new ML_user_common())->get_user_db_fields(),
						'key'			=>	0,
						'value'			=>	1,
						'name'			=>	'author_field[]',
						'select_value'		=>	'Database Field',
						'selected'		=>	[$v],
						'localization'		=>	'members-list-pro',
						'disabled'		=>	true,
					)); ?>
					<input disabled type="text" name="author_field_title[]" size="20" placeholder="Name this field..." value="<?php echo isset($ml_options['author_field_title'][$k]) ? $ml_options['author_field_title'][$k] : ''; ?>" />
					<a disabled class="button clone tern-button"><i class="fa fa-plus"></i></a> <a disabled class="button remove tern-button"><i class="fa fa-minus"></i></a>
				</div>
				<?php $x++;} ?>
				<?php } else { ?>
				<div class="clonable WP-sort-fields WP-inline-fields">
					<?php echo (new tern_select)->create([
						'type'			=>	'tiered',
						'data'			=>	(new ML_user_common())->get_user_db_fields(),
						'key'			=>	0,
						'value'			=>	1,
						'name'			=>	'author_field[]',
						'select_value'		=>	'Database Field',
						'selected'		=>	[],
						'localization'		=>	'members-list-pro',
						'disabled'		=>	true,
					]); ?>
					<input disabled type="text" name="author_field_title[]" size="20" placeholder="Name this field..." />
					<a disabled class="button clone tern-button"><i class="fa fa-plus"></i></a> <a disabled class="button remove tern-button"><i class="fa fa-minus"></i></a>
				</div>
				<?php } ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="author_posts"><?php _e('Display author\'s posts on author page','members-list-pro'); ?> <strong>(<?php _e('PRO Only','members-list-pro'); ?>)</strong>:</label></th>
			<td>
				<input disabled type="checkbox" class="switchery" name="author_posts" value="1" />
				<span class="description"><?php _e('If set to yes, the user\'s most recent posts will be displayed on his/her author page.','members-list-pro'); ?></span>
			</td>
		</tr>
	</table>
	<p class="submit"><input type="submit" name="submit" class="button-primary tern-button tern-button-medium" value="<?php echo _e('Save Changes','members-list-pro'); ?>" /></p>
</div>
