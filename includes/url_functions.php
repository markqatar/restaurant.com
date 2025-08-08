<?php
/**
 * URL helper functions for generating pretty URLs
 */

/**
 * URL Helper Functions for Pretty URLs
 */

/**
 * Generate a pretty URL
 */
function url($path = '') {
    $baseUrl = rtrim(SITE_URL ?? 'http://localhost', '/');
    $path = ltrim($path, '/');
    $p21 = $baseUrl . '/' . $path;
        $p21 = $baseUrl . '/' . $path;

    return $baseUrl . '/' . $path;
}

/**
 * Get the current page with pretty URL format
 * 
 * @return string The current page name
 */
function get_current_page() {
    $request_uri = $_SERVER['REQUEST_URI'];
    $url_parts = explode('/', trim($request_uri, '/'));
    
    if ($url_parts[0] === 'admin' && isset($url_parts[1])) {
        return $url_parts[1];
    }
    
    return basename($_SERVER['SCRIPT_NAME'], '.php');
}