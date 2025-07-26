<?php
// Include branch helper functions
require_once 'helpers/branches_helper.php';
require_once 'helpers/select2_helper.php';

// Include URL helper functions
require_once __DIR__ . '/../../includes/url_functions.php';

/**
 * Recupera un setting dal database (con caching statico).
 *
 * @param string $key Nome del setting.
 * @param mixed $default Valore di default se non trovato.
 * @return mixed
 */
function get_setting($key, $default = null) {
    static $settings = null;

    if ($settings === null) {
        // Includi Database se non già incluso
        if (!class_exists('Database')) {
            require_once __DIR__ . '/../../config/database.php'; // Percorso corretto al tuo file
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
        $settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }

    return $settings[$key] ?? $default;
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

function site_url($path = '') {
    $base_url = rtrim(get_setting('site_url', 'http://localhost'), '/');
    return $path ? $base_url . '/' . ltrim($path, '/') : $base_url;
}

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
    $message = TranslationManager::t($message); // Traduci chiave prima di salvare
    $_SESSION['notification'] = [
        'message' => $message,
        'type' => $type
    ];
}/**
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

/**
 * Restituisce il percorso assoluto di un file nel modulo Admin
 *
 * @param string $path Percorso relativo dentro il modulo
 * @param string|null $targetModule Nome del modulo (opzionale, default: modulo corrente)
 * @return string
 */
function admin_module_path($path = '', $targetModule = null) {
    global $module;
    $moduleName = $targetModule ?? $module;

    $base = get_setting('base_path', '/var/www/html') . "admin/modules/{$moduleName}/";
    return $base . ltrim($path, '/');
}

/**
 * Restituisce il percorso assoluto di un file nel modulo Public
 *
 * @param string $path Percorso relativo dentro il modulo
 * @param string|null $targetModule Nome del modulo (opzionale, default: modulo corrente)
 * @return string
 */
function public_module_path($path = '', $targetModule = null) {
    global $module;
    $moduleName = $targetModule ?? $module;

    $base = get_setting('base_path', '/var/www/html') . "public/modules/{$moduleName}/";
    return $base . ltrim($path, '/');
}

/**
 * Carica una vista dal modulo Admin
 *
 * @param string $view Nome della vista (es: 'settings/index')
 * @param array $data Dati da passare alla vista
 * @param string|null $targetModule Nome del modulo (opzionale)
 */
function load_admin_view($view, $data = [], $targetModule = null) {
    global $module;

    $moduleName = $targetModule ?? $module;

    // Convertiamo in percorso file
    $filePath = admin_module_path('views/' . $view . '.php', $moduleName);

    if (!file_exists($filePath)) {
        die("❌ View not found: $filePath");
    }

    // Estrae variabili dall'array $data
    extract($data);

    include $filePath;
}

/**
 * Carica una vista dal modulo Public
 *
 * @param string $view Nome della vista (es: 'home/index')
 * @param array $data Dati da passare alla vista
 * @param string|null $targetModule Nome del modulo (opzionale)
 */
function load_public_view($view, $data = [], $targetModule = null) {
    global $module;

    $moduleName = $targetModule ?? $module;

    $filePath = public_module_path('views/' . $view . '.php', $moduleName);

    if (!file_exists($filePath)) {
        die("❌ View not found: $filePath");
    }

    extract($data);

    include $filePath;
}

function get_available_languages_from_db($context = 'admin') {
    global $db;

    try {
        $stmt = $db->query("
            SELECT code, name, direction, is_active_admin, is_active_public
            FROM languages
            ORDER BY name
        ");
        $languages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Aggiunge i campi mancanti se non esistono
        foreach ($languages as &$lang) {
            if (!isset($lang['is_active_admin'])) {
                $lang['is_active_admin'] = 0;
            }
            if (!isset($lang['is_active_public'])) {
                $lang['is_active_public'] = 0;
            }
        }

        return $languages;
    } catch (PDOException $e) {
        error_log("Error fetching languages: " . $e->getMessage());
        return [];
    }
}
function get_default_admin_language_from_db() {
    global $db;
    $stmt = $db->query("SELECT code FROM languages WHERE is_active_admin = 1 LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['code'] ?? 'en';
}

function get_default_public_language_from_db() {
    global $db;
    $stmt = $db->query("SELECT code FROM languages WHERE is_active_public = 1 LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['code'] ?? 'en';
}

$view_hooks = [];

function add_view_hook($hook_name, $callback) {
    global $view_hooks;
    if (!isset($view_hooks[$hook_name])) {
        $view_hooks[$hook_name] = [];
    }
    $view_hooks[$hook_name][] = $callback;
}

function render_hook($hook_name, ...$args) {
    global $view_hooks;
    if (isset($view_hooks[$hook_name])) {
        foreach ($view_hooks[$hook_name] as $callback) {
            call_user_func_array($callback, $args);
        }
    }
}

$logic_hooks = [];

function add_logic_hook($hook_name, $callback) {
    global $logic_hooks;
    if (!isset($logic_hooks[$hook_name])) {
        $logic_hooks[$hook_name] = [];
    }
    $logic_hooks[$hook_name][] = $callback;
}

function run_logic_hook($hook_name, ...$args) {
    global $logic_hooks;
    if (isset($logic_hooks[$hook_name])) {
        foreach ($logic_hooks[$hook_name] as $callback) {
            call_user_func_array($callback, $args);
        }
    }
}

$global_hooks = [];

function add_global_hook($hook_name, $callback) {
    global $global_hooks;
    if (!isset($global_hooks[$hook_name])) {
        $global_hooks[$hook_name] = [];
    }
    $global_hooks[$hook_name][] = $callback;
}

function run_hook($hook_name, ...$args) {
    global $global_hooks;
    if (isset($global_hooks[$hook_name])) {
        foreach ($global_hooks[$hook_name] as $callback) {
            call_user_func_array($callback, $args);
        }
    }
}

function log_action($module, $table_name, $action, $record_id, $old_data = null, $new_data = null) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("
        INSERT INTO activity_logs (user_id, module, table_name, action, record_id, old_data, new_data)
        VALUES (:user_id, :module, :table_name, :action, :record_id, :old_data, :new_data)
    ");
    $stmt->execute([
        ':user_id' => $_SESSION['user_id'] ?? null,
        ':module' => $module,
        ':table_name' => $table_name,
        ':action' => $action,
        ':record_id' => $record_id,
        ':old_data' => $old_data ? json_encode($old_data) : null,
        ':new_data' => $new_data ? json_encode($new_data) : null
    ]);
}

function restore_action($log_id) {
    $db = Database::getInstance()->getConnection();

    // Usa il modello per recuperare il log
    $model = new ActivityLog();
    $log = $model->getLogById($log_id);

    if (!$log) {
        return ['success' => false, 'message' => TranslationManager::t('system.error.log_not_found')];
    }

    // Controlla che ci sia la tabella e i dati
    if (empty($log['table_name'])) {
        return ['success' => false, 'message' => TranslationManager::t('system.error.no_table_for_restore')];
    }

    // Azioni consentite per il restore
    if ($log['action'] !== 'update') {
        return ['success' => false, 'message' => TranslationManager::t('system.error.restore_not_allowed')];
    }

    // Decodifica old_data per ripristino
    $old_data = json_decode($log['old_data'], true);
    if (!$old_data || !is_array($old_data)) {
        return ['success' => false, 'message' => TranslationManager::t('system.error.no_data_to_restore')];
    }

    try {
        // Costruisci la query UPDATE
        $columns = array_keys($old_data);
        $set = implode(', ', array_map(fn($col) => "$col = ?", $columns));

        $stmt = $db->prepare("UPDATE {$log['table_name']} SET $set WHERE id = ?");
        $stmt->execute(array_merge(array_values($old_data), [$log['record_id']]));

        return ['success' => true, 'message' => TranslationManager::t('system.success.restore_done')];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}
?>