<?php
// Session management
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Session configuration (only if session not already started)
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_lifetime', 0);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 0 for HTTP testing
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_samesite', 'Strict');
}

// Regenerate session ID periodically
if (!isset($_SESSION['last_regeneration'])) {
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 1800) { // 30 minutes
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// Include database and functions
require_once __DIR__ . '/functions.php';

// Initialize database connection
try {
    $database = Database::getInstance();
    $db = $database->getConnection();
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Set default language
if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = 'en';
}

// Include site configuration
require_once __DIR__ . '/../config/site.php';

// CSRF token generation
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}



/**
 * Get CSRF token
 */
function get_csrf_token() {
    return $_SESSION['csrf_token'] ?? '';
}
?>