<?php
// This file should be included at the very top of any page that might change language
// to prevent "headers already sent" errors

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include functions for redirect
if (!function_exists('redirect')) {
    require_once __DIR__ . '/../../includes/functions.php';
}

// Handle language change
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ar', 'it'])) {
    $_SESSION['lang'] = $_GET['lang'];
    $_SESSION['language'] = $_GET['lang'];
    
    // Redirect to remove lang parameter from URL
    $redirect_url = strtok($_SERVER["REQUEST_URI"], '?');
    
    // Preserve other query parameters if they exist
    $query = $_GET;
    unset($query['lang']);
    
    if (!empty($query)) {
        $redirect_url .= '?' . http_build_query($query);
    }
    
    // Perform the redirect
    header("Location: $redirect_url");
    exit();
}
?>