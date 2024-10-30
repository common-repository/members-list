<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); ?>
<div class="wrap tern-wrap">
	<h2><?php echo _e('Plugin Settings','members-list-pro'); ?></h2>
	<br /><br />
	<form method="post" action="">

		<div class="tern-tabs">
			<ul>
				<li><a href="#tab-api"><?php echo _e('API Settings','members-list-pro'); ?></a></li>
				<li><a href="#tab-author"><?php echo _e('Author','members-list-pro'); ?></a></li>
				<li><a href="#tab-display"><?php echo _e('Display','members-list-pro'); ?></a></li>
				<li><a href="#tab-styling"><?php echo _e('Styling','members-list-pro'); ?></a></li>
				<li><a href="#tab-user"><?php echo _e('User','members-list-pro'); ?></a></li>
			</ul>

			<?php include('settings-api.php'); ?>
			<?php include('settings-author.php'); ?>
			<?php include('settings-display.php'); ?>
			<?php include('settings-styling.php'); ?>
			<?php include('settings-user.php'); ?>

		</div>

		<input type="hidden" name="action" value="update_ml_options" />
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce('WP_ml_nonce');?>" />
		<input type="hidden" name="_wp_http_referer" value="<?php wp_get_referer(); ?>" />

	</form>
</div>
