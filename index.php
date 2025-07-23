<?php
/**
 * Front controller for handling pretty URLs
 */

// Enable debugging for troubleshooting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files
require_once __DIR__ . '/includes/functions.php';
require_once get_setting('base_path', '/var/www/html') . 'includes/globals.php';
require_once get_setting('base_path', '/var/www/html') . 'includes/session.php';

// Get the request URI
$request_uri = $_SERVER['REQUEST_URI'];
$query_string = $_SERVER['QUERY_STRING'] ?? '';

// Remove query string from the request URI if present
if (strpos($request_uri, '?') !== false) {
    $request_uri = substr($request_uri, 0, strpos($request_uri, '?'));
}

// Remove trailing slash if present
$request_uri = rtrim($request_uri, '/');

// Parse URL parts
$path_parts = explode('/', trim($request_uri, '/'));

// Debug output (remove this in production)
// error_log("Request URI: " . $request_uri);
// error_log("Path parts: " . print_r($path_parts, true));

// Handle admin routes
if (!empty($path_parts[0]) && $path_parts[0] === 'admin') {
    // If not logged in and not on login page, redirect to login
    if (!isset($_SESSION['user_id']) && (!isset($path_parts[1]) || $path_parts[1] !== 'login')) {
        header('Location: /admin/login');
        exit;
    }

    // Remove 'admin' from path parts
    array_shift($path_parts);
    
    // Default to index if no specific page requested
    $page = !empty($path_parts[0]) ? $path_parts[0] : 'index';
    
    // Handle login page specially
    if ($page === 'login') {
        require_once get_setting('base_path', '/var/www/html') . 'admin/login.php';
        exit;
    }
    
    // Handle logout page specially
    if ($page === 'logout') {
        require_once get_setting('base_path', '/var/www/html') . 'admin/logout.php';
        exit;
    }
    
    // Determine PHP file to include
    $file_path = get_setting('base_path', '/var/www/html') . 'admin/' . $page . '.php';

    // Set action and ID parameters if they exist in the URL
    if (isset($path_parts[1])) {
        $_GET['action'] = $path_parts[1];
        
        if (isset($path_parts[2])) {
            $_GET['id'] = $path_parts[2];
        }
    }
    
    // Include the appropriate file if it exists
    if (file_exists($file_path)) {
        require_once $file_path;
        exit;
    } else {
        // File not found, return 404
        header("HTTP/1.0 404 Not Found");
        if (function_exists('is_logged_in') && is_logged_in()) {
            include __DIR__ . '/views/admin/404.php';
        } else {
            echo "<h1>404 - Page Not Found</h1>";
            echo "<p>The requested admin page could not be found.</p>";
            echo "<p><a href='/admin'>Return to Admin Dashboard</a></p>";
        }
        exit;
    }
} else {
    // Handle public routes
    
    // Main site root shows homepage
    if (empty($path_parts[0])) {
        include __DIR__ . '/homepage.php';
        exit;
    }
    
    // Check if we should serve a static file
    $file_path = __DIR__ . $request_uri;
    if (file_exists($file_path) && !is_dir($file_path)) {
        $extension = pathinfo($file_path, PATHINFO_EXTENSION);
        
        // Set correct content type for common file types
        $content_types = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'ico' => 'image/x-icon',
        ];
        
        if (isset($content_types[$extension])) {
            header('Content-Type: ' . $content_types[$extension]);
        }
        
        readfile($file_path);
        exit;
    }
    
    // Handle other public routes (if needed)
    // ...
    
    // If no specific route matched, return 404
    header("HTTP/1.0 404 Not Found");
    include __DIR__ . '/views/404.php';
    exit;
}