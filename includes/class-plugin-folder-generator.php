<?php

class Plugin_Folder_Generator {

    private static $debug_log = [];

    public static function generate_folder_structure($dir, $exclude, $type = 'plugin') {
        $structure = self::get_folder_structure($dir, '', $exclude);

        // Set the directory to save the structure
        $folder_structure_dir = plugin_dir_path(__FILE__) . '../structure/';
        if (!is_dir($folder_structure_dir)) {
            mkdir($folder_structure_dir, 0755, true);
            self::add_debug_log("Created directory: $folder_structure_dir");
        }

        // Save the structure to the file
        $output_file = $folder_structure_dir . basename($dir) . "-{$type}-structure.txt";
        file_put_contents($output_file, $structure);
        self::add_debug_log("Generated file: $output_file");

        return $output_file;
    }

    private static function get_folder_structure($dir, $prefix = '', $exclude = []) {
        $output = '';
        $files = scandir($dir);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || in_array($file, $exclude, true)) {
                continue;
            }

            $output .= $prefix . $file . "\n";
            if (is_dir($dir . '/' . $file)) {
                $output .= self::get_folder_structure($dir . '/' . $file, $prefix . '│   ', $exclude);
            }
        }

        return mb_convert_encoding($output, 'UTF-8', 'auto');
    }

    public static function add_debug_log($message) {
        self::$debug_log[] = $message;
    }

    public static function get_debug_log() {
        return self::$debug_log;
    }
}
