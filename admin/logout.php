<?php
require_once __DIR__ . '/../includes/session.php';

// Log the logout activity
if (is_logged_in()) {
    log_activity($_SESSION['user_id'], 'logout', 'User logged out');
}

// Destroy session
session_destroy();

// Clear session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to login page
redirect(get_setting('site_url', 'http://restaurant.com') . '/admin/login');
?>