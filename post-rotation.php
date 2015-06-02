<?php
/*
Plugin Name: Post Rotation
Plugin URI: http://www.digitalemphasis.com/wordpress-plugins/post-rotation/
Description: 'Post Rotation' takes the oldest post that matches with your criteria and automatically converts it in the most recent one, as just published.
Version: 1.1
Author: digitalemphasis
Author URI: http://www.digitalemphasis.com/
License: GPLv2 or later
*/

defined('ABSPATH') or die("Cannot access pages directly.");

function pr_the_settings() {
	register_setting('pr-settings-group', 'pr_enabled');
	register_setting('pr-settings-group', 'pr_interval', 'interval_validate');
	register_setting('pr-settings-group', 'pr_also_alter_last_modified');
	register_setting('pr-settings-group', 'pr_included_categories');
	register_setting('pr-settings-group', 'pr_clean_uninstall');
}
add_action('admin_init', 'pr_the_settings');

function interval_validate($value) {
	$error_message = 'You have entered an invalid interval value. The default interval value of 24 hours will be used instead.';
	if ((!ctype_digit($value)) || ($value <= 0)) {
	    $value = 24;
		add_settings_error('pr_interval', 'invalid-interval', $error_message);
    }
    return $value;
}

function pr_admin_init() {
	wp_register_style('pr-admin-style', plugins_url('admin/post-rotation-admin.css', __FILE__));
}
add_action('admin_init', 'pr_admin_init');

function pr_settings_page() {
	include('admin/post-rotation-admin.php');
}

function pr_admin_style() {
	wp_enqueue_style('pr-admin-style');
}

function pr_menu() {
	$page = add_submenu_page('edit.php', 'Post Rotation', 'Post Rotation', 'manage_options', 'post-rotation', 'pr_settings_page');
	add_action('admin_print_styles-' . $page, 'pr_admin_style');
}
add_action('admin_menu', 'pr_menu');

function pr_add_options_link($links) {
	$settings_link = '<a href="edit.php?page=post-rotation">Settings</a>';
	array_unshift($links, $settings_link);
	return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'pr_add_options_link');

function pr_install() {
	add_option('pr_enabled', '');
	add_option('pr_interval', '24');
	add_option('pr_also_alter_last_modified', '1');
	add_option('pr_included_categories', '');
	add_option('pr_clean_uninstall', '1');
}

function pr_deactivation() {
	unregister_setting('pr-settings-group', 'pr_enabled');
	unregister_setting('pr-settings-group', 'pr_interval');
	unregister_setting('pr-settings-group', 'pr_also_alter_last_modified');
	unregister_setting('pr-settings-group', 'pr_included_categories');
	unregister_setting('pr-settings-group', 'pr_clean_uninstall');
}

function pr_uninstall() {
	if (get_option('pr_clean_uninstall') == 1) {
		delete_option('pr_enabled');
        delete_option('pr_interval');
        delete_option('pr_also_alter_last_modified');
		delete_option('pr_included_categories');
        delete_option('pr_clean_uninstall');
	}
}

register_activation_hook(__FILE__, 'pr_install');
register_deactivation_hook(__FILE__, 'pr_deactivation');
register_uninstall_hook(__FILE__, 'pr_uninstall');

include('post-rotation-core.php');
?>