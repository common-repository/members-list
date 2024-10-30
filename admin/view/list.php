<?php global $ml_options,$wpdb; ?>
<div class="wrap tern-wrap">
	<input type="hidden" name="page" value="<?php echo esc_attr($_GET['page']); ?>" />
	<h2><?php _e('Members Lists','members-list-pro'); ?></h2>
	<br class="clear" />
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
					<table class="wp-list-table widefat fixed">
						<thead>
							<tr class="thead">
								<th scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></th>
								<th scope="col"><?php _e('Name','members-list-pro'); ?></th>
								<th scope="col"><?php _e('Users','members-list-pro'); ?></th>
								<th scope="col"><?php _e('Actions','members-list-pro'); ?></th>
							</tr>
						</thead>
						<tfoot>
							<tr class="thead">
								<th scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></th>
								<th scope="col"><?php _e('Name','members-list-pro'); ?></th>
								<th scope="col"><?php _e('Users','members-list-pro'); ?></th>
								<th scope="col"><?php _e('Actions','members-list-pro'); ?></th>
							</tr>
						</tfoot>
						<tbody>
							<?php foreach((array)$ml_options['lists'] as $list_key => $list) { ?>
							<tr>
								<th scope="row" class="manage-column column-cb check-column">
									<input type="checkbox" name="items[]" id="field_<?php echo $list_key;?>" value="<?php echo $list_key;?>" />
								</th>
								<td><strong><?php echo $list['name']; ?></strong></td>
								<td>
									<?php echo $wpdb->get_var('select count(user_id) from '.$wpdb->usermeta.' where meta_key="_WP_ML" and meta_value="'.$list['name'].'"'); ?>
								</td>
								<td class="actions">
									<a data-modal="WP_ml_field_edit" href="#TB_inline?width=400&height=600&inlineId=WP_ml_field_edit" class="button tern-button edit thickbox"><i class="fa fa-pencil-alt"></i></a>
									<a href="admin.php?page=ml-list-field&list=<?php echo $list_key; ?>" class="button tern-button"><i class="fa fa-edit"></i></a>
									<a href="admin.php?page=ml-list-user&list=<?php echo $list_key; ?>" class="button tern-button"><i class="fa fa-user"></i></a>
									<a href="admin.php?page=ml-create-list&items%5B%5D=<?php echo $list_key; ?>&action=delete&_wpnonce=<?php echo wp_create_nonce('WP_ml_nonce'); ?>" class="button tern-button"><i class="fa fa-times"></i></a>

									<input type="hidden" name="list" value="<?php echo $list_key; ?>" />
									<?php foreach((array)$this->fields as $field_key => $field) { ?>
										<?php if(isset($list[$field_key]) and !is_array($list[$field_key])) { ?>
											<input
												type="hidden"
												name="<?php echo $field_key; ?>"
												value="<?php echo isset($list[$field_key]) ? $list[$field_key] : ''; ?>"
											/>
										<?php } elseif(isset($list[$field_key])) { ?>
											<input
												type="hidden"
												class="array"
												name="<?php echo $field_key; ?>"
												value="<?php echo isset($list[$field_key]) ? implode(',',$list[$field_key]) : ''; ?>"
											/>
											<input
												type="hidden"
												class="array"
												name="<?php echo $field_key.'_title'; ?>"
												value="<?php echo  isset($list[$field_key]) ? implode(',',array_keys($list[$field_key])) : ''; ?>"
											/>
										<?php } ?>
									<?php } ?>
								</td>
							</tr>
							<?php
								}
							?>
						</tbody>
					</table>
					<div class="tablenav top">
						<div class="alignleft actions">
							<select name="action2">
								<option value="" selected="selected"><?php _e('Bulk Actions','members-list-pro'); ?></option>
								<option value="delete"><?php _e('Delete','members-list-pro'); ?></option>
							</select>
							<input type="submit" class="button-secondary action" value="<?php _e('Apply','members-list-pro'); ?>" />
						</div>
						<br class="clear" />
					</div>
					<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce('WP_ml_nonce');?>" />
				</form>
			</div>
		</div>
		<div id="col-left">
			<div class="col-wrap">
				<div class="form-wrap tern-well">
					<h3><?php _e('Add a New List','members-list-pro'); ?>:</h3>
					<form id="WP_ml_add_item_form" class="WP_ml_form" method="post" action="<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=ml-create-list">
						<?php include(ML_ADMIN_DIR.'/view/list-edit.php'); ?>
						<p class="submit">
							<input type="submit" name="submit" class="button-primary tern-button tern-button-big" value="<?php _e('Add List','members-list-pro'); ?>" />
						</p>
						<input type="hidden" name="item" />
						<input type="hidden" name="action" value="add" />
						<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('WP_ml_nonce'); ?>" />
					</form>
				</div>
			</div>
		</div>
	</div>
	<br class="clear" />
</div>

<div id="WP_ml_field_edit" class="form-modal">
	<form id="WP_ml_edit_item_form" class="WP-ml-modal-form WP_ml_form" method="post" action="<?php bloginfo('wpurl'); ?>/wp-admin/admin.php?page=ml-create-list">
		<?php include(ML_ADMIN_DIR.'/view/list-edit.php'); ?>
		<p class="submit">
			<input type="submit" name="submit" class="button-primary tern-button tern-button-big" value="<?php _e('Edit List','members-list-pro'); ?>" />
		</p>
		<input type="hidden" name="action" value="WP_ml_list_edit" />
		<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('WP_ml_nonce'); ?>" />
	</form>
</div>
