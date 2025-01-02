<?php
/**
 * Plugin Name: Plugin Folder Structure Generator
 * Description: Generate and export folder structures of available plugins to debug or visualize the plugin structure.
 * Version: 1.0.0
 * Author: Prabakaran Shankar
 * Author URI: https://prabakaranshankar.com  
 * plugin uri: https://nviewsweb.com/plugin-folder-structure-generator/
 * Text Domain: plugin-folder-structure
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined('ABSPATH') || exit;

// Include required files
require_once plugin_dir_path(__FILE__) . 'includes/class-plugin-folder-generator.php';
require_once plugin_dir_path(__FILE__) . 'admin/settings-page.php';

// Initialize the plugin
add_action('admin_menu', function () {
    Plugin_Folder_Generator_Settings::add_settings_page();
});

add_action('admin_post_view_file', function () {
    if (isset($_POST['file_path'])) {
        $file_path = sanitize_text_field($_POST['file_path']);
        Plugin_Folder_Generator::serve_file_for_browser($file_path);
    }
});

add_action('admin_post_download_file', function () {
    if (isset($_POST['file_path'])) {
        $file_path = sanitize_text_field($_POST['file_path']);
        Plugin_Folder_Generator::serve_file_for_download($file_path);
    }
});

/**
 * Initialize the default exclude list on plugin activation
 */
function plugin_folder_generator_activate() {
    $default_list = [
        'node_modules' => true,
        'vendor' => true,
        '.git' => true,
        'build' => true,
        '__MACOSX' => true,
    ];

    // Retrieve the existing list
    $current_list = get_option('plugin_folder_generator_exclude_list', []);

    // Merge new defaults without overwriting existing items
    foreach ($default_list as $key => $value) {
        if (!array_key_exists($key, $current_list)) {
            $current_list[$key] = $value;
        }
    }

    // Save the updated list
    update_option('plugin_folder_generator_exclude_list', $current_list);
}
// Register activation hook
register_activation_hook(__FILE__, 'plugin_folder_generator_activate');

/**
 * Clean up options on plugin deactivation
 */
function plugin_folder_generator_deactivate() {
    delete_option('plugin_folder_generator_exclude_list');
}

register_deactivation_hook(__FILE__, 'plugin_folder_generator_deactivate');