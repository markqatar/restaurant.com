<?php
// Global settings and configuration
require_once __DIR__ . '/../models/Setting.php';

// Initialize settings
$settingModel = new Setting();
$GLOBALS['site_settings'] = $settingModel->getAll();

// Global helper functions for settings
function get_setting($key, $default = null) {
    return isset($GLOBALS['site_settings'][$key]) ? $GLOBALS['site_settings'][$key] : $default;
}

function site_url($path = '') {
    $base_url = rtrim(get_setting('site_url', 'http://localhost'), '/');
    return $path ? $base_url . '/' . ltrim($path, '/') : $base_url;
}

function site_name() {
    return get_setting('site_name', 'Restaurant Management System');
}

function site_logo() {
    $logo_path = get_setting('logo_path');
    if ($logo_path && file_exists(__DIR__ . '/../uploads/' . $logo_path)) {
        return site_url('uploads/' . $logo_path);
    }
    return null;
}

// Fix redirect function to use absolute URLs
function redirect($url) {
    // If URL doesn't start with http, make it relative to site_url
    if (!preg_match('/^https?:\/\//', $url)) {
        $url = site_url($url);
    }
    header("Location: $url");
    exit();
}