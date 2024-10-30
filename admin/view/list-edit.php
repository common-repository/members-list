<?php use MLP\ternstyle\tern_select as tern_select; ?>
<?php global $wp_roles; ?>
<div class="WP_ml_edit_item_form_fields">
	<div class="form-field">
		<label><?php _e('Name','members-list-pro'); ?>:</label>
		<input type="text" name="name" size="40" />
	</div>

	<div class="form-field">
		<label><?php _e('Add all users to this list','members-list-pro'); ?> <strong>(<?php _e('PRO Only','members-list-pro'); ?>)</strong>:</label>
		<input disabled type="checkbox" name="all" value=1 class="yes chk switchery" />
	</div>

	<div class="form-field">
		<label><?php _e('Add all users of the following role(s)','members-list-pro'); ?> <strong>(<?php _e('PRO Only','members-list-pro'); ?>)</strong>:</label>

		<?php foreach ($wp_roles->get_names() as $role_value => $role_name) { ?>
		<p><input disabled name="role[]" type="checkbox" value="<?php echo $role_value; ?>"> <?php echo $role_name; ?></p>
	  	<?php } ?>
	</div>

	<div class="form-field">
		<label><?php _e('Add new users to this list when they register','members-list-pro'); ?> <strong>(<?php _e('PRO Only','members-list-pro'); ?>)</strong>:</label>
		<input disabled type="checkbox" name="add_new" value=1 class="yes chk switchery" />
	</div>
	<div class="form-field">
		<label><?php _e('How many users to display per page','members-list-pro'); ?>:</label>
		<input type="text" name="limit" size="40" value="20" />
	</div>
	<div class="form-field">
		<label><?php _e('List view','members-list-pro'); ?> <strong>(<?php _e('PRO Only','members-list-pro'); ?>)</strong>:</label>
		<select name="view">
			<option value="default"><?php _e('Default','members-list-pro'); ?></option>
			<option disabled value="table"><?php _e('Table (PRO Only)','members-list-pro'); ?></option>
			<option disabled value="tile"><?php _e('Tile (PRO Only)','members-list-pro'); ?></option>
		</select>
	</div>
	<div class="form-field">
		<label><?php _e('Allow users to search this list','members-list-pro'); ?>:</label>
		<input type="checkbox" name="search" value=1 class="yes chk switchery" />
	</div>
	<div class="form-field">
		<label><?php _e('Select fields to search by','members-list-pro'); ?> <strong>(<?php _e('PRO Only','members-list-pro'); ?>)</strong>:</label>
		<div class="clonable WP-search-fields WP-inline-fields">
			<?php echo (new tern_select)->create(array(
				'type'			=>	'tiered',
				'data'			=>	(new ML_user_common())->get_user_db_fields(),
				'key'			=>	0,
				'value'			=>	1,
				'name'			=>	'search_field[]',
				'select_value'		=>	__('Database Field','members-list-pro'),
				'localization'		=>	'members-list-pro',
				'disabled'		=>	true,
			)); ?>
			<input disabled type="text" name="search_field_title[]" size="20" />
			<a disabled class="button tern-button clone"><i class="fa fa-plus"></i></a>
			<a disabled class="button tern-button remove"><i class="fa fa-minus"></i></a>
		</div>
	</div>
	<div class="form-field">
		<label><?php _e('Allow users to search by radius','members-list-pro'); ?>:</label>
		<input type="checkbox" name="radius" value=1 class="yes chk switchery" />
	</div>
	<div class="form-field">
		<label><?php _e('Show map','members-list-pro'); ?> <strong>(<?php _e('PRO Only','members-list-pro'); ?>)</strong>:</label>
		<input disabled type="checkbox" name="map" value=1 class="yes chk switchery" />
	</div>
	<div class="form-field">
		<label><?php _e('Allow users to search by alpha','members-list-pro'); ?>:</label>
		<input type="checkbox" name="alpha" value=1 class="yes chk switchery" />
	</div>
	<div class="form-field">
		<label><?php _e('Sort the list initially by','members-list-pro'); ?>:</label>
		<div class="clonable WP-inline-fields">
			<?php echo (new tern_select)->create(array(
				'type'			=>	'tiered',
				'data'			=>	(new ML_user_common())->get_user_db_fields(),
				'key'			=>	0,
				'value'			=>	1,
				'name'			=>	'sort_by',
				'select_value'		=>	__('Database Field','members-list-pro'),
				'localization'		=>	'members-list-pro',
			)); ?>
			<select name="sort_order">
				<option value="asc"><?php _e('Ascending','members-list-pro'); ?></option>
				<option value="desc"><?php _e('Descending','members-list-pro'); ?></option>
			</select>
		</div>
	</div>
	<div class="form-field">
		<label><?php _e('Allow users to sort','members-list-pro'); ?> <strong>(<?php _e('PRO Only','members-list-pro'); ?>)</strong>:</label>
		<input disabled type="checkbox" name="sort" value=1 class="yes chk switchery" />
	</div>
	<div class="form-field">
		<label><?php _e('Select fields to sort by','members-list-pro'); ?>:</label>
		<div class="clonable WP-sort-fields WP-inline-fields">
			<?php echo (new tern_select)->create(array(
				'type'			=>	'tiered',
				'data'			=>	(new ML_user_common())->get_user_db_fields(),
				'key'			=>	0,
				'value'			=>	1,
				'name'			=>	'sort_field[]',
				'select_value'		=>	__('Database Field','members-list-pro'),
				'localization'		=>	'members-list-pro',
				'disabled'		=>	true,
			)); ?>
			<input disabled type="text" name="sort_field_title[]" size="20" />
			<a disabled class="button tern-button clone"><i class="fa fa-plus"></i></a>
			<a disabled class="button tern-button remove"><i class="fa fa-minus"></i></a>
		</div>
	</div>
	<div class="form-field">
		<label><?php _e('Display profile picture (gravatars)','members-list-pro'); ?>:</label>
		<input type="checkbox" name="img" value=1 class="yes chk switchery" />
	</div>
	<div class="form-field">
		<label><?php _e('Profile picture width','members-list-pro'); ?> <strong>(<?php _e('PRO Only','members-list-pro'); ?>)</strong>:</label>
		<input disabled type="text" name="img_size" size="40" value="60" />
	</div>
	<div class="form-field">
		<label><?php _e('Link to author profile pages','members-list-pro'); ?> <strong>(<?php _e('PRO Only','members-list-pro'); ?>)</strong>:</label>
		<input disabled type="checkbox" name="link" value=1 class="yes chk switchery" />
	</div>
	<div class="form-field">
		<label><?php _e('Display field labels','members-list-pro'); ?> <strong>(<?php _e('PRO Only','members-list-pro'); ?>)</strong>:</label>
		<input disabled type="checkbox" name="labels" value=1 class="yes chk switchery" checked />
	</div>

	<input type="hidden" name="list" />

</div>
