<?php use MLP\ternstyle\tern_select as tern_select; ?>
<?php global $ml_options,$WP_ml_countries; ?>

<div class="tern-wrap">
	<div class="tern-well">

		<input type="hidden" name="WP_ml_nonce" value="<?php echo wp_create_nonce('WP_ml_nonce');?>" />

		<h3>Members Lists</h3>
		<table class="form-table">
			<tr>
				<th><label for="lists">Select the lists you'd like this user displayed in:</label></th>
				<td><ul>
						<?php foreach($ml_options['lists'] as $k => $v) { ?>
						<li>
							<input
								type="checkbox"
								name="lists[]"
								value="<?php echo $v['name']; ?>"
								<?php if((new ML_user_common(false))->is_in_list($user->ID,$v['name'])) {?>
									checked
								<?php } ?>
							/>
							<?php echo $v['name']; ?></li>
						<?php } ?>
					</ul></td>
			</tr>
		</table>
		<h3>Address</h3>
		<?php foreach($this->address_field as $v) {
			$address[$v] = get_user_meta($user->ID,'_'.$v,true);
		} ?>
		<table class="form-table">
			<tr>
				<th><label for="line1">Address Line 1:</label></th>
				<td><input type="text" name="line1" value="<?php echo $address['line1']; ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th><label for="line2">Address Line 2:</label></th>
				<td><input type="text" name="line2" value="<?php echo $address['line2']; ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th><label for="state">State / Province:</label></th>
				<td>
					<input type="text" name="state" value="<?php echo $address['state']; ?>" class="regular-text" />
				</td>
			</tr>
			<tr>
				<th><label for="zip">Zip / Postal Code:</label></th>
				<td><input type="text" name="zip" value="<?php echo $address['zip']; ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th><label for="country">Country:</label></th>
				<td>
					<?php echo (new tern_select)->create(array(
						'type'			=>	'select',
						'data'			=>	array_keys($WP_ml_countries),
						'name'			=>	'country',
						'select_value'		=>	'select',
						'selected'		=>	[$address['country']]
					)); ?>
				</td>
			</tr>
			<tr>
				<th><label for="lat">Latitude:</label></th>
				<td><input type="text" name="lat" value="<?php echo get_user_meta($user->ID,'_lat',true); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th><label for="lng">Longitude:</label></th>
				<td><input type="text" name="lng" value="<?php echo get_user_meta($user->ID,'_lng',true); ?>" class="regular-text" /></td>
			</tr>
		</table>
	</div>
</div>
