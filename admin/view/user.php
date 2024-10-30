<?php use MLP\ternstyle\tern_select as tern_select; ?>
<div class="wrap tern-wrap">
	<input type="hidden" name="page" value="<?php echo esc_attr($_GET['page']);?>" />
	<h1><?php _e('Users for List','members-list-pro'); ?>: <?php  if(isset($this->list['name'])) { echo $this->list['name']; } ?></h2>

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
	<ul class="subsubsub">
		<li class="all">
			<a href="admin.php?page=ml-list-user&list=<?php echo esc_attr($_GET['list']); ?>" class="<?php if(!isset($_GET['notin']) or !$_GET['notin']) { ?>current<?php } ?>">
				<?php _e('All','members-list-pro'); ?> <span class="count">(<?php echo $members->get_count_total(); ?>)</span>
			</a> |
		</li>
		<li>
			<a class="<?php if(isset($_GET['notin']) and $_GET['notin']) { ?>current<?php } ?>" href="admin.php?page=ml-list-user&list=<?php echo esc_attr($_GET['list']); ?>&notin=true">
				<?php _e('Users not in List','members-list-pro'); ?> <span class="count">(<?php echo count_users()['total_users']-$members->get_count_total(); ?>)</span>
			</a>
		</li>
	</ul>

	<form method="get" action="admin.php">
		<input type="hidden" name="page" value="ml-list-user" />
		<p class="search-box">
			<label class="screen-reader-text" for="user-search-input"><?php _e('Search Users','members-list-pro'); ?>:</label>
			<input type="search" name="search" value="" />
			<input type="submit" id="search-submit" class="button" value="Search Users" />
		</p>
		<input type="hidden" name="list" value="<?php echo esc_attr($_REQUEST['list']); ?>" />
		<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('WP_ml_nonce');?>" />
	</form>
	<form method="post" action="">
		<div class="tablenav top">
			<div class="alignleft actions">
				<select name="action">
					<option value="" selected="selected"><?php _e('Bulk Actions','members-list-pro'); ?></option>
					<option value="remove"><?php _e('Remove from List','members-list-pro'); ?></option>
					<option value="add"><?php _e('Add to List','members-list-pro'); ?></option>
				</select>
				<input type="submit" class="button-secondary action" value="<?php _e('Apply','members-list-pro'); ?>" />
			</div>
			<div class="tablenav-pages">
				<span class="displaying-num"><?php echo $members->get_count_total(); ?> <?php _e('items','members-list-pro'); ?></span> <?php echo $members->pagination; ?>
			</div>
			<br class="clear" />
		</div>
		<table id="WP-ml-fields" class="wp-list-table widefat fixed sort">
			<thead>
				<tr class="thead">
					<td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2"><?php _e('Select All','members-list-pro'); ?></label>
						<input type="checkbox" /></td>
					<th scope="col"><?php _e('Name','members-list-pro'); ?></th>
					<th scope="col"><?php _e('Username','members-list-pro'); ?></th>
					<th scope="col"><?php _e('Email','members-list-pro'); ?></th>
					<th scope="col"><?php _e('Actions','members-list-pro'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr class="thead">
					<td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2"><?php _e('Select All','members-list-pro'); ?></label>
						<input id="cb-select-all-2" type="checkbox" /></td>
					<th scope="col"><?php _e('Name','members-list-pro'); ?></th>
					<th scope="col"><?php _e('Username','members-list-pro'); ?></th>
					<th scope="col"><?php _e('Email','members-list-pro'); ?></th>
					<th scope="col"><?php _e('Actions','members-list-pro'); ?></th>
				</tr>
			</tfoot>
			<tbody>
				<?php if(empty($users)) { ?>
				<tr>
					<td colspan="4"><strong><?php _e('You currently have no users. Add one!','members-list-pro'); ?></strong></td>
				</tr>
				<?php } ?> <?php foreach((array)$users as $k => $v) { ?>
				<tr>
					<th scope="row" class="manage-column column-cb check-column"> <input type="checkbox" name="user[]" value="<?php echo $v['ID']; ?>" />
					</th>
					<td><?php echo get_user_meta($v['ID'],'first_name',true); ?> <?php echo get_user_meta($v['ID'],'last_name',true); ?></td>
					<td><?php echo $v['user_nicename']; ?></td>
					<td><?php echo $v['user_email']; ?></td>
					<td class="actions">
						<?php if(isset($_GET['notin']) and $_GET['notin']) { ?>
							<a href="admin.php?page=ml-list-user&action=add&list=<?php echo esc_attr($_GET['list']); ?>&user[]=<?php echo $v['ID']; ?>&_wpnonce=<?php echo wp_create_nonce('WP_ml_nonce');?>" class="button tern-button">
								<i class="fa fa-plus"></i>
							</a>
						<?php } else { ?>
							<a href="admin.php?page=ml-list-user&action=remove&list=<?php echo esc_attr($_GET['list']); ?>&user[]=<?php echo $v['ID']; ?>&_wpnonce=<?php echo wp_create_nonce('WP_ml_nonce');?>" class="button tern-button">
								<i class="fa fa-minus"></i>
							</a>
						<?php } ?>
						<a target="_blank" href="user-edit.php?user_id=<?php echo $v['ID']; ?>" class="button tern-button"><i class="fa fa-pencil-alt"></i></a></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		<div class="tablenav top">
			<div class="alignleft actions">
				<select name="action2">
					<option value="" selected="selected"><?php _e('Bulk Actions','members-list-pro'); ?></option>
					<option value="remove"><?php _e('Remove from List','members-list-pro'); ?></option>
					<option value="add"><?php _e('Add to List','members-list-pro'); ?></option>
				</select>
				<input type="submit" class="button-secondary action" value="<?php _e('Apply','members-list-pro'); ?>" />
			</div>
			<br class="clear" />
		</div>
		<input type="hidden" name="list" value="<?php echo esc_attr($_REQUEST['list']); ?>" />
		<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('WP_ml_nonce');?>" />
	</form>
	<?php } ?>
</div>
