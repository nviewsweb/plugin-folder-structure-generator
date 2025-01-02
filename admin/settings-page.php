<?php

class Plugin_Folder_Generator_Settings {

    public static function add_settings_page() {
        add_menu_page(
            __('Folder Generator', 'plugin-folder-structure'),
            __('Folder Generator', 'plugin-folder-structure'),
            'manage_options',
            'plugin-folder-structure',
            [self::class, 'render_settings_page'],
            'dashicons-admin-tools'
        );
    }
    private static function get_exclude_list() {
        $list = get_option('plugin_folder_generator_exclude_list', []);
        return is_array($list) ? $list : [];
    }
    
    private static function update_exclude_list($list) {
        update_option('plugin_folder_generator_exclude_list', $list);
    }
    
    
    
    public static function render_settings_page() {
        // Retrieve the exclude list
        $exclude_list = self::get_exclude_list();
    
        // Handle form submissions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Update the exclude list
            if (isset($_POST['exclude_list'])) {
                foreach (array_keys($exclude_list) as $key) {
                    $exclude_list[$key] = isset($_POST['exclude_list'][$key]) ? true : false;
                }
                self::update_exclude_list($exclude_list);
            }
    
            // Add new items to the list
            if (isset($_POST['add_to_list'])) {
                $new_item = sanitize_text_field($_POST['add_to_list']);
                if (!empty($new_item) && !array_key_exists($new_item, $exclude_list)) {
                    $exclude_list[$new_item] = false; // Add as disabled by default
                    self::update_exclude_list($exclude_list);
                }
            }
        }
    
        // Render the settings page
        echo '<div class="wrap">';
        echo '<h1>' . __('Plugin and Theme Folder Structure Generator', 'plugin-folder-structure') . '</h1>';
    
        // Exclude List Management
        echo '<form method="POST"><h2>' . __('Manage Exclude List', 'plugin-folder-structure') . '</h2>';
        echo '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">';
    
        // List of Items with Checkboxes
        echo '<div>';
        echo '<h3>' . __('Exclude List', 'plugin-folder-structure') . '</h3>';
        echo '<ul class="pfs-ul">';
        echo '<style> .pfs-ul li { display: inline-flex; margin-right: 10px; }</style>';
        foreach ($exclude_list as $item => $status) {
            $checked = $status ? 'checked' : '';
            echo '<li><label>';
            echo '<input type="checkbox" name="exclude_list[' . esc_attr($item) . ']" ' . $checked . '> ' . esc_html($item);
            echo '</label></li>';
        }
        echo '</ul>';
        echo '</div>';
    
        // Add New Item
        echo '<div>';
        echo '<h3>' . __('Add New Exclusion', 'plugin-folder-structure') . '</h3>';
        echo '<input type="text" name="add_to_list" placeholder="' . __('Add new item', 'plugin-folder-structure') . '" />';
        echo '<br><button type="submit" class="button-secondary">' . __('Add to List', 'plugin-folder-structure') . '</button>';
        echo '</div>';
    
        echo '</div><br>';
        echo '<button type="submit" class="button-primary">' . __('Save Changes', 'plugin-folder-structure') . '</button>';
        echo '</form>';
    
        // Render Plugins and Themes
        echo '<h2>' . __('Generate Structure Files', 'plugin-folder-structure') . '</h2>';
        echo '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">';
        echo '<div>';
        self::render_plugin_list();
        echo '</div>';
        echo '<div>';
        self::render_theme_list();
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    

    private static function render_plugin_list() {
        $plugin_dir = WP_PLUGIN_DIR;
        $plugin_folders = array_filter(glob($plugin_dir . '/*'), 'is_dir');

        echo '<h2>' . __('Available Plugins', 'plugin-folder-structure') . '</h2>';
        echo '<table class="form-table"><thead><tr>';
        echo '<th>' . __('Plugin Name', 'plugin-folder-structure') . '</th>';
        echo '<th>' . __('Generate', 'plugin-folder-structure') . '</th>';
        echo '<th>' . __('Generated File', 'plugin-folder-structure') . '</th>';
        echo '</tr></thead><tbody>';

        foreach ($plugin_folders as $folder) {
            self::render_folder_row($folder, 'plugin');
        }

        echo '</tbody></table>';
    }

    private static function render_theme_list() {
        $theme_dir = WP_CONTENT_DIR . '/themes';
        $theme_folders = array_filter(glob($theme_dir . '/*'), 'is_dir');

        echo '<h2>' . __('Available Themes', 'plugin-folder-structure') . '</h2>';
        echo '<table class="form-table"><thead><tr>';
        echo '<th>' . __('Theme Name', 'plugin-folder-structure') . '</th>';
        echo '<th>' . __('Generate', 'plugin-folder-structure') . '</th>';
        echo '<th>' . __('Generated File', 'plugin-folder-structure') . '</th>';
        echo '</tr></thead><tbody>';

        foreach ($theme_folders as $folder) {
            self::render_folder_row($folder, 'theme');
        }

        echo '</tbody></table>';
    }

    private static function render_folder_row($folder, $type) {
        $folder_name = basename($folder);
        $structure_file = plugin_dir_path(__FILE__) . "../structure/{$folder_name}-{$type}-structure.txt";
    
        $button_label = file_exists($structure_file) ? __('Regenerate', 'plugin-folder-structure') : __('Generate', 'plugin-folder-structure');
    
        echo '<tr>';
        echo '<td>' . esc_html($folder_name) . '</td>';
        echo '<td><form method="POST">';
        echo '<input type="hidden" name="generate" value="' . esc_attr($folder) . '">';
        echo '<input type="hidden" name="type" value="' . esc_attr($type) . '">';
        echo '<button type="submit" class="button-primary">' . $button_label . '</button>';
        echo '</form></td>';
        echo '<td>';
        if (file_exists($structure_file)) {
            echo '<a href="' . esc_url(plugin_dir_url(__FILE__) . "../structure/{$folder_name}-{$type}-structure.txt") . '" target="_blank">' . __('Download', 'plugin-folder-structure') . '</a>';
        } else {
            echo __('Not Generated', 'plugin-folder-structure');
        }
        echo '</td>';
        echo '</tr>';
    }
    
    
}
