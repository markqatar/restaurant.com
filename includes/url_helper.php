<?php
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
 * Generate admin URL
 */
function admin_url($path = '') {
    return url('admin/' . ltrim($path, '/'));
}

/**
 * Generate dynamic admin URL modules based
 */
function module_admin_url($path = '') {
    return url('admin/modules/' . ltrim($path, '/'));
}

/**
 * Redirect to a pretty URL
 */
function redirect_to($path) {
    header('Location: ' . url($path));
    exit();
}

/**
 * Get current URL path
 */
function current_path() {
    return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
}

/**
 * Check if current path matches
 */
function is_current_path($path) {
    return current_path() === '/' . ltrim($path, '/');
}

/**
 * Generate breadcrumb navigation
 */
function breadcrumb($items = []) {
    if (empty($items)) return '';
    
    $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    
    foreach ($items as $key => $item) {
        if ($key === array_key_last($items)) {
            // Last item - active
            $html .= '<li class="breadcrumb-item active" aria-current="page">' . htmlspecialchars($item['title']) . '</li>';
        } else {
            // Regular item with link
            $html .= '<li class="breadcrumb-item"><a href="' . htmlspecialchars($item['url']) . '">' . htmlspecialchars($item['title']) . '</a></li>';
        }
    }
    
    $html .= '</ol></nav>';
    
    return $html;
}