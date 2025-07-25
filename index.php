<?php
/**
 * Front controller for handling pretty URLs
 */

// Enable debugging for troubleshooting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files
require_once __DIR__ . '/admin/includes/bootstrap.php';// Get the request URI
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

    if (!isset($_SESSION['user_id']) && (!isset($path_parts[1]) || $path_parts[1] !== 'login')) {
        header('Location: /admin/login');
        exit;
    }

    array_shift($path_parts); // rimuove "admin"

    // Modulo, controller, action, parametri
    $module = $path_parts[0] ?? 'dashboard';
    $controllerName = ucfirst($path_parts[1] ?? $module) . 'Controller';
    $action = $path_parts[2] ?? 'index';
    $params = array_slice($path_parts, 3);

    $controllerPath = __DIR__ . "/admin/modules/{$module}/controllers/{$controllerName}.php";

    if (file_exists($controllerPath)) {
        require_once $controllerPath;
        $controller = new $controllerName();
        
        if(method_exists($controller, $action)){
            call_user_func_array([$controller, $action], $params);
        } else {
            header("HTTP/1.0 404 Not Found");
            include __DIR__ . '/admin/layouts/404.php';
        }
    } else {
        header("HTTP/1.0 404 Not Found");
        include __DIR__ . '/admin/layouts/404.php';
    }

    exit;
} else {
    // Handle public routes
    
    // Main site root shows homepage
    if (empty($path_parts[0])) {
        include __DIR__ . '/public/index.php';
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