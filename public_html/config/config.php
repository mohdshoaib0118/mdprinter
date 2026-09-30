<?php
/**
 * MD Printers - System Configuration & Constants
 */

// Start output buffering immediately to prevent headers already sent warnings
if (ob_get_level() === 0) {
    ob_start();
}

// Start session securely if not active
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// Error reporting setting (Set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Base Paths
define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('MODELS_PATH', ROOT_PATH . '/models');
define('CONTROLLERS_PATH', ROOT_PATH . '/controllers');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('ADMIN_PATH', ROOT_PATH . '/admin');

// Autoload Composer Vendor Dependencies if available
if (file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require_once ROOT_PATH . '/vendor/autoload.php';
}

// Base URL calculation dynamically (works on XAMPP localhost and production domains)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$baseUrl = rtrim($protocol . $host . $scriptDir, '/');

// Fix base URL if running through front controller
if (substr($baseUrl, -6) === '/admin') {
    $baseUrl = substr($baseUrl, 0, -6);
}

define('SITE_URL', $baseUrl);
define('ADMIN_URL', SITE_URL . '/admin');

// Database Credentials
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'u225671252_md_printernew');
define('DB_USER', 'root');
define('DB_PASS', 'Dev@123');
define('DB_CHARSET', 'utf8mb4');

// Max upload size (50MB)
define('MAX_UPLOAD_SIZE', 50 * 1024 * 1024);
define('ALLOWED_EXTENSIONS', ['pdf', 'ai', 'psd', 'eps', 'tiff', 'zip', 'jpg', 'jpeg', 'png', 'webp', 'mp4', 'webm', 'ogg', 'mov', 'm4v']);
