<?php

// Exit if accessed directly
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// List of options to delete
$options_to_delete = [
    'plugin_folder_generator_exclude_list',    
];

// Delete each option
foreach ($options_to_delete as $option) {
    delete_option($option);
}
