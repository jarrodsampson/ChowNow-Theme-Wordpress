<h1>ChowNow Theme Options</h1>
<?php settings_errors(); ?>
<form method="POST" action ="options.php">
	<?php settings_fields('chownow-settings-group'); ?>
	<?php do_settings_sections('chownow_theme'); ?>
	<?php submit_button(); ?>
</form>