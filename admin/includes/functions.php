<?php
// Common functions for the restaurant management system

// Include global settings first
require_once 'globals.php';

// Include branch helper functions
require_once 'branch_helpers.php';

// Include translation system
require_once 'translations.php';

// Include URL helper functions
require_once __DIR__ . '/../../includes/url_functions.php';

/**
 * Sanitize input data
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Generate random string
 */
function generate_random_string($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)))), 1, $length);
}

/**
 * Hash password
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Check user permission
 */
function has_permission($user_id, $module, $action) {
    global $db;
    
    try {
        // First check if user is super admin (user_id = 1)
        if ($user_id == 1) {
            return true;
        }
        
        // Get user's group ID first
        $stmt = $db->prepare("SELECT group_id FROM user_group_assignments WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user_group = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user_group) {
            return false; // User has no group assigned
        }
        
        // Check if the user's group has the required permission
        $stmt = $db->prepare("
            SELECT COUNT(*) as count 
            FROM permissions 
            WHERE group_id = ? AND module = ? AND action = ?
        ");
        $stmt->execute([$user_group['group_id'], $module, $action]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    } catch (PDOException $e) {
        // Log error and return false for security
        error_log("Permission check error: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Redirect function (moved to globals.php for better URL handling)
 */

/**
 * Format currency
 */
function format_currency($amount, $currency = 'USD') {
    return number_format($amount, 2) . ' ' . $currency;
}

/**
 * Format date
 */
function format_date($date, $format = 'Y-m-d H:i:s') {
    return date($format, strtotime($date));
}



/**
 * Generate barcode
 */
function generate_barcode($type, $id, $expiry_date = null) {
    $date_part = $expiry_date ? date('Ymd', strtotime($expiry_date)) : date('Ymd');
    return strtoupper($type) . str_pad($id, 6, '0', STR_PAD_LEFT) . $date_part;
}

/**
 * Upload file
 */
function upload_file($file, $upload_dir = 'uploads/') {
    if ($file['error'] !== 0) {
        return false;
    }
    
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_extension, $allowed_types)) {
        return false;
    }
    
    $filename = uniqid() . '.' . $file_extension;
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filename;
    }
    
    return false;
}

/**
 * Send notification
 */
function send_notification($message, $type = 'info') {
    $_SESSION['notification'] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * Set notification (alias for send_notification for compatibility)
 */
function set_notification($message, $type = 'success') {
    send_notification($message, $type);
}

/**
 * Get notification
 */
function get_notification() {
    if (isset($_SESSION['notification'])) {
        $notification = $_SESSION['notification'];
        unset($_SESSION['notification']);
        return $notification;
    }
    return null;
}

/**
 * Log activity
 */
function log_activity($user_id, $action, $details = '') {
    global $db;
    
    try {
        $stmt = $db->prepare("
            INSERT INTO activity_logs (user_id, action, details, ip_address, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$user_id, $action, $details, $_SERVER['REMOTE_ADDR']]);
    } catch (Exception $e) {
        // Log error silently
        error_log("Activity log error: " . $e->getMessage());
    }
}

/**
 * Generate order number
 */
function generate_order_number() {
    return 'ORD' . date('Ymd') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

/**
 * Calculate order total
 */
function calculate_order_total($subtotal, $discount = 0, $delivery_fee = 0, $tax_rate = 0) {
    $discount_amount = ($subtotal * $discount / 100);
    $after_discount = $subtotal - $discount_amount;
    $tax_amount = ($after_discount * $tax_rate / 100);
    $total = $after_discount + $delivery_fee + $tax_amount;
    
    return [
        'subtotal' => $subtotal,
        'discount_amount' => $discount_amount,
        'tax_amount' => $tax_amount,
        'delivery_fee' => $delivery_fee,
        'total' => $total
    ];
}

/**
 * Print receipt
 */
function print_receipt($order_id, $printer_ip) {
    // Implementation for thermal printer
    // This would require additional libraries like php-escpos-print
    // For now, we'll just log the print request
    error_log("Print request for order $order_id to printer $printer_ip");
}

// CSRF Token functions
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_token_field() {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Validate CSRF token (alias for verify_csrf_token for compatibility)
 */
function validate_csrf_token($token) {
    return verify_csrf_token($token);
}

function admin_module_path($path = '') {
    global $module;
    $base = get_setting('base_path', '/var/www/html') . "admin/modules/$module/";
    return $base . ltrim($path, '/');
}

function public_module_path($path = '') {
    global $module;
    $base = get_setting('base_path', '/var/www/html') . "public/modules/$module/";
    return $base . ltrim($path, '/');
}

?>