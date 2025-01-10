<?php

class Plugin_Folder_Generator {

    private static $debug_log = [];

    /**
     * Generate folder structure and save it as a UTF-8 encoded text file.
     */
    public static function generate_folder_structure($dir, $exclude, $type = 'plugin') {
        // Generate folder structure
        $structure = self::get_folder_structure($dir, '', $exclude);

        // Ensure the structure folder exists
        $folder_structure_dir = plugin_dir_path(__FILE__) . '../structure/';
        if (!is_dir($folder_structure_dir)) {
            mkdir($folder_structure_dir, 0755, true);
            self::add_debug_log("Created directory: $folder_structure_dir");
        }

        // Add UTF-8 BOM to the content
        $bom = "\xEF\xBB\xBF"; // UTF-8 BOM
        $output_file = $folder_structure_dir . basename($dir) . "-{$type}-structure.txt";
        file_put_contents($output_file, $bom . mb_convert_encoding($structure, 'UTF-8', 'auto'));
        self::add_debug_log("Generated file with BOM: $output_file");

        return $output_file;
    }

    /**
     * Recursively generate the folder structure of a directory.
     */
    private static function get_folder_structure($dir, $prefix = '', $exclude = []) {
        // Initialize output only for the root directory
        if ($prefix === '') {
            $output = mb_convert_encoding(basename($dir), 'UTF-8', 'auto') . "\n";
        } else {
            $output = '';
        }
        $files = scandir($dir);
    
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || in_array($file, $exclude, true)) {
                continue;
            }
    
            // Check if the current item is a directory or a file
            if (is_dir($dir . '/' . $file)) {
                // Append with directory indicator `│--`
                $output .= $prefix . mb_convert_encoding('¦-- ', 'UTF-8', 'auto') . mb_convert_encoding($file, 'UTF-8', 'auto') . "\n";
    
                // Recursively process subdirectories with an updated prefix
                $output .= self::get_folder_structure($dir . '/' . $file, $prefix . mb_convert_encoding('¦   ', 'UTF-8', 'auto'), $exclude);
            } else {
                // Append with file indicator `│---`
                $output .= $prefix . mb_convert_encoding('¦--- ', 'UTF-8', 'auto') . mb_convert_encoding($file, 'UTF-8', 'auto') . "\n";
            }
        }
    
        return $output;
    }
    

    /**
     * Serve the generated file for viewing in the browser.
     */
    public static function serve_file_for_browser($file_path) {
        if (file_exists($file_path)) {
            header('Content-Type: text/plain; charset=UTF-8'); // Ensure proper encoding for browser viewing
            header('Content-Length: ' . filesize($file_path));

            // Output the file content
            readfile($file_path);
            exit;
        } else {
            echo 'File not found.';
            exit;
        }
    }

    /**
     * Serve the generated file for download.
     */
    public static function serve_file_for_download($file_path) {
        if (file_exists($file_path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: text/plain; charset=UTF-8'); // Ensure proper encoding
            header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));

            // Output the file content
            readfile($file_path);
            exit;
        } else {
            echo 'File not found.';
            exit;
        }
    }

    /**
     * Add a debug log message.
     */
    public static function add_debug_log($message) {
        self::$debug_log[] = $message;
    }

    /**
     * Retrieve the debug log.
     */
    public static function get_debug_log() {
        return self::$debug_log;
    }
}
