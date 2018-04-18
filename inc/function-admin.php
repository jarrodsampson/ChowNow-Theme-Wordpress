<?php

function chownow_add_admin_page() {

	// Generate actual page
	add_menu_page('Theme Options', 'ChowNow', 'manage_options', 'chownow_theme', 'theme_create_page', '', 110);

	// Sub Pages
	add_submenu_page('chownow_theme', 'Theme Options', 'General', 'manage_options', 'chownow_theme');
	add_submenu_page('chownow_theme', 'Banner', 'Top Banner', 'manage_options', 'chownow_theme_settings', 'chownow_settings_page');

	// activate the settings
	add_action('admin_init', 'chownow_custom_settings');
}
add_action('admin_menu', 'chownow_add_admin_page');

function theme_create_page() {
	require_once(get_template_directory() . '/inc/templates/chownow-admin.php');
}

function chownow_settings_page() {
	echo '<h1>Top Banner Image</h1>';
}

function chownow_custom_settings() {
	register_setting('chownow-settings-group', 'banner_heading');
	register_setting('chownow-settings-group', 'banner_description');
	add_settings_section('banner-options', 'Banner Options', 'chownow_banner_options', 'chownow_theme');
	add_settings_field('banner-heading', 'Banner Heading', 'chownow_banner_heading', 'chownow_theme', 'banner-options');
	add_settings_field('banner-description', 'Banner Description', 'chownow_banner_description', 'chownow_theme', 'banner-options');
}

function chownow_banner_heading() {
	$bannerheading = esc_attr(get_option( 'banner_heading' ));
	echo '<textarea name="banner_heading" placeholder="Banner Heading">'. $bannerheading .'</textarea>';
}

function chownow_banner_description() {
	$bannerDesc = esc_attr(get_option( 'banner_description' ));
	echo '<textarea name="banner_description" placeholder="Banner Description">'. $bannerDesc .'</textarea>';
}

function chownow_banner_options() {
	echo "Customize Banner Information";
}