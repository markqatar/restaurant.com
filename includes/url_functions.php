<?php
/**
 * URL helper functions for generating pretty URLs
 */

/**
 * Generate a URL for a admin page with optional action and parameters
 * 
 * @param string $page The admin page (e.g., 'users', 'products')
 * @param string|null $action Optional action (e.g., 'edit', 'delete')
 * @param int|string|null $id Optional ID for the action
 * @param array $params Optional additional parameters
 * @return string The generated URL
 */
function admin_url($page, $action = null, $id = null, $params = []) {
    // Handle the case where page is 'index'
    if ($page === 'index') {
        $base_url = '/admin';
    } else {
        $base_url = '/admin/' . $page;
    }
    
    if ($action !== null) {
        $base_url .= '/' . $action;
        
        if ($id !== null) {
            $base_url .= '/' . $id;
        }
    }
    
    // Add any additional parameters
    if (!empty($params)) {
        $query_params = [];
        foreach ($params as $key => $value) {
            $query_params[] = $key . '=' . urlencode($value);
        }
        $base_url .= '?' . implode('&', $query_params);
    }
    
    return $base_url;
}
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
 * Generate dynamic admin URL modules based
 */
function module_admin_url($path = '') {
    return url('/' . ltrim($path, '/'));
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