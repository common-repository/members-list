<?php use MLP\ternstyle\tern_select as tern_select; ?>
<form id="WP-ml-shortcode" class="form form-modal">
	<div id="WP-ml-shortcode-fields">
		<h2><?php _e('Select a Members List','members-list-pro'); ?></h2>
		<div class="form-field">
			<?php echo (new tern_select)->create(array(
				'type'			=>	'assoc',
				'data'			=>	$lists,
				'name'			=>	'list-select',
				'select_value'		=>	__('Select List','members-list-pro'),
			)); ?>
		</div>
		<p class="submit">
			<input type="submit" name="submit" class="button-primary" value="<?php _e('Add Shortcode','members-list-pro'); ?>" />
		</p>
	</div>
</form>
