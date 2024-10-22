<?php
namespace App\Utils;
// src/Utils/Logger.php
class Logger {
    private static $config = null;
    
    private static function getConfig() {
        if (self::$config === null) {
            $configPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . 
                         DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'logging.php';
            self::$config = require $configPath;
        }
        return self::$config;
    }

    private static function ensureLogFileExists($path) {
        $directory = dirname($path);
        if (!is_dir($directory)) {
            // Create directory if it doesn't exist
            if (!mkdir($directory, 0777, true) && !is_dir($directory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $directory));
            }
        }
        
        // Create file if it doesn't exist
        if (!file_exists($path)) {
            $handle = fopen($path, 'a');
            if ($handle) {
                fclose($handle);
            }
        }

        // Check if file is writable
        if (!is_writable($path)) {
            throw new \RuntimeException(sprintf('Log file "%s" is not writable', $path));
        }
    }
    
    public static function error($message) {
        try {
            $config = self::getConfig();
            self::ensureLogFileExists($config['error_log_path']);
            $timestamp = date('Y-m-d H:i:s');
            error_log("[$timestamp] ERROR: $message\n", 3, $config['error_log_path']);
        } catch (\Throwable $e) {
            // Fallback to Windows temp directory if main logging fails
            $tempFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'app_error.log';
            error_log("[$timestamp] ERROR: $message\n", 3, $tempFile);
            error_log("Logging Error: " . $e->getMessage(), 0);
        }
    }
    
    public static function debug($message) {
        try {
            $config = self::getConfig();
            self::ensureLogFileExists($config['debug_log_path']);
            $timestamp = date('Y-m-d H:i:s');
            error_log("[$timestamp] DEBUG: $message\n", 3, $config['debug_log_path']);
        } catch (\Throwable $e) {
            // Fallback to Windows temp directory if main logging fails
            $tempFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'app_debug.log';
            error_log("[$timestamp] DEBUG: $message\n", 3, $tempFile);
            error_log("Logging Error: " . $e->getMessage(), 0);
        }
    }
    
    public static function query($message) {
        try {
            $config = self::getConfig();
            self::ensureLogFileExists($config['query_log_path']);
            $timestamp = date('Y-m-d H:i:s');
            error_log("[$timestamp] QUERY: $message\n", 3, $config['query_log_path']);
        } catch (\Throwable $e) {
            // Fallback to Windows temp directory if main logging fails
            $tempFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'app_query.log';
            error_log("[$timestamp] QUERY: $message\n", 3, $tempFile);
            error_log("Logging Error: " . $e->getMessage(), 0);
        }
    }
}