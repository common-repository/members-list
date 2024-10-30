<?php use MLP\ternstyle\tern_select as tern_select; ?>
<?php global $ml_options; ?>
<div class="wrap tern-wrap">

	<h2><?php _e('Fields for List:','members-list-pro'); ?> <?php if(isset($this->list['name'])) { echo $this->list['name']; } ?></h2>

	<?php if(!isset($this->list['name'])) { ?>
		<div class="tern-well">
			<h3><?php _e('No List Selected','members-list-pro'); ?></h3>
			<p><?php _e('Please select a list from the dropdown below.','members-list-pro'); ?></p>
			<?php echo (new tern_select)->create(array(
				'type'			=>	'assoc',
				'data'			=>	$this->get_lists(),
				'name'			=>	'list-select',
				'select_value'		=>	__('Select List','members-list-pro'),
			)); ?>
		</div>
	<?php } ?>

	<?php if(isset($this->list) and !empty($this->list)) { ?>
	<div id="col-container">
		<div id="col-right">
			<div class="col-wrap">
				<form method="post" action="">
					<div class="tablenav top">
						<div class="alignleft actions">
							<select name="action">
								<option value="" selected="selected"><?php _e('Bulk Actions','members-list-pro'); ?></option>
								<option value="delete"><?php _e('Delete','members-list-pro'); ?></option>
							</select>
							<input type="submit" class="button-secondary action" value="<?php _e('Apply','members-list-pro'); ?>" />
						</div>
						<br class="clear" />
					</div>
					<table id="WP-ml-fields" class="wp-list-table widefat fixed sort">
						<thead>
							<tr class="thead">
								<th width="5%" scope="col"></th>
								<td width="20%" class="manage-column column-cb check-column">
									<label class="screen-reader-text" for="cb-select-all-2"><?php _e('Select All','members-list-pro'); ?></label>
									<input id="cb-select-all-2" type="checkbox" />
								</td>
								<th width="30%" scope="col"><?php _e('Name','members-list-pro'); ?></th>
								<th width="20%" scope="col"><?php _e('Field','members-list-pro'); ?></th>
								<th width="10%" scope="col"><?php _e('Truncate','members-list-pro'); ?></th>
								<th width="15%" scope="col"><?php _e('Actions','members-list-pro'); ?></th>
							</tr>
						</thead>
						<tfoot>
							<tr class="thead">
								<th width="5%" scope="col"></th>
								<td width="20%" class="manage-column column-cb check-column">
									<label class="screen-reader-text" for="cb-select-all-2"><?php _e('Select All','members-list-pro'); ?></label>
									<input id="cb-select-all-2" type="checkbox" />
								</td>
								<th width="30%" scope="col"><?php _e('Name','members-list-pro'); ?></th>
								<th width="20%" scope="col"><?php _e('Field','members-list-pro'); ?></th>
								<th width="10%" scope="col"><?php _e('Truncate','members-list-pro'); ?></th>
								<th width="15%" scope="col"><?php _e('Actions','members-list-pro'); ?></th>
							</tr>
						</tfoot>
						<tbody>
							<?php if(!isset($this->list['fields']) or empty($this->list['fields'])) { ?>
							<tr>
								<td colspan="4"><strong><?php _e('You currently have no fields. Add one!','members-list-pro'); ?></strong></td>
							</tr>
							<?php } else { ?>
								<?php foreach((array)$this->list['fields'] as $field_id => $field) { ?>
								<tr id="field-<?php echo $field_id; ?>">
									<th scope="col"><a href="#" class="drag handle"><i class="fa fa-bars"></i></a></th>
									<th scope="row" class="manage-column column-cb check-column">
										<input type="checkbox" name="items[]" value="<?php echo $field_id;?>" />
									</th>
									<td class="WP-ml-field-name"><strong><?php echo $field['name']; ?></strong></td>
									<td class="WP-ml-field-field"><?php echo $field['field']; ?></td>
									<td class="WP-ml-field-trunc"><?php echo (isset($field['truncate']) and $field['truncate']) ? 'yes' : (isset($field['truncate']) ? 'no' : ''); ?></td>
									<td class="actions">
										<a data-modal="WP_ml_field_edit" href="#TB_inline?width=400&height=600&inlineId=WP_ml_field_edit" class="button edit thickbox tern-button">
											<i class="fa fa-pencil-alt"></i>
										</a>
										<a href="admin.php?page=ml-list-field&list=<?php echo $this->list_id; ?>&items%5B%5D=<?php echo $field_id; ?>&action=delete&_wpnonce=<?php echo wp_create_nonce('WP_ml_nonce'); ?>" class="button tern-button">
											<i class="fa fa-times"></i>
										</a>

										<input type="hidden" name="field_id" value="<?php echo $field_id;?>" />
										<input type="hidden" name="name" value="<?php echo $field['name'];?>" />
										<input type="hidden" name="field" value="<?php echo $field['field'];?>" />
										<input type="hidden" name="truncate" value=<?php echo (isset($field['truncate']) and (int)$field['truncate']) ? 1 : 0; ?> />
									</td>
								</tr>
								<?php } ?>
							<?php } ?>
						</tbody>
					</table>
					<div class="tablenav top">
						<div class="alignleft actions">
							<select name="action2">
								<option value="" selected="selected">Bulk Actions</option>
								<option value="delete">Delete</option>
							</select>
							<input type="submit" class="button-secondary action" value="<?php _e('Apply','members-list-pro'); ?>">
						</div>
						<br class="clear" />
					</div>
					<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('WP_ml_nonce');?>" />
					<input type="hidden" name="page" value="<?php echo esc_attr($_GET['page']); ?>" />
				</form>
				<br class="clear" />
			</div>
		</div>
		<div id="col-left">
			<div class="col-wrap">
				<div class="form-wrap tern-well">
					<h3> <?php _e('Add a New Field','members-list-pro'); ?> :</h3>
					<form id="WP_ml_add_item_form" method="post" action="<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=ml-list-field&list=<?php echo $this->list_id; ?>">
						<div class="form-field"> <label><?php _e('Name','members-list-pro'); ?>:</label>
							<input type="text" name="name" size="40" />
						</div>
						<div class="form-field">
							<label class="hidden" for="new-field-input">Field:</label>
							<?php echo (new tern_select)->create(array(
								'type'			=>	'tiered',
								'data'			=>	(new ML_user_common())->get_user_db_fields(),
								'key'			=>	0,
								'value'			=>	1,
								'id'				=>	'field',
								'name'			=>	'field',
								'select_value'		=>	__('Database Field','members-list-pro'),
								'localization'		=>	'members-list-pro',
							));?>
						</div>
						<div class="form-field field-truncate"><div><label><?php _e('Truncate this field','members-list-pro'); ?>:</label>
							<input type="checkbox" name="truncate" value=1 class="yes chk switchery" />
						</div></div>
						<p class="submit">
							<input type="submit" name="submit" id="submit" class="button-primary tern-button tern-button-big" value="<?php _e('Add Field','members-list-pro'); ?>">
						</p>
						<input type="hidden" name="list" value="<?php echo $this->list_id; ?>" />
						<input type="hidden" name="action" value="add" />
						<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('WP_ml_nonce'); ?>" />
					</form>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
	<br class="clear" />

	<?php if(isset($members)) { ?>
	<div class="tern_error"><p><?php echo _e('Please note that some users may not have the fields specified filled out. In these cases they will not be displayed below.','members-list-pro'); ?></p></div>
	<div id="WP_ml_list_preview">
		<h3><?php _e('Preview','members-list-pro'); ?></h3>
		<?php echo $members->render(); ?>
	</div>
	<?php } ?>

</div>

<div id="WP_ml_field_edit" class="form-modal">
	<form id="WP_ml_field_edit_form" class="WP-ml-modal-form" method="POST" action="<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=ml-list-field">
		<div class="form-field">
			<div class="form-field"> <label><?php _e('Name','members-list-pro'); ?>:</label>
				<input type="text" name="name" size="40" />
			</div>
			<div class="form-field">
				<label class="hidden" for="new-field-input">Field:</label>
				<?php echo (new tern_select)->create(array(
					'type'			=>	'tiered',
					'data'			=>	(new ML_user_common())->get_user_db_fields(),
					'key'			=>	0,
					'value'			=>	1,
					'id'				=>	'field',
					'name'			=>	'field',
					'select_value'		=>	__('Database Field','members-list-pro'),
					'localization'		=>	'members-list-pro',
				));
				?>
			</div>
			<div class="form-field field-truncate"><div><label><?php _e('Truncate this field','members-list-pro'); ?>:</label>
				<input type="checkbox" name="truncate" value=1 class="yes chk switchery" />
			</div></div>
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button-primary tern-button tern-button-medium" value="Edit Field" />
			</p>
			<input type="hidden" name="list" value="<?php echo $this->list_id; ?>" />
			<input type="hidden" name="field_id" />
			<input type="hidden" name="action" value="edit" />
			<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('WP_ml_nonce'); ?>" />
		</div>
	</form>
</div>
