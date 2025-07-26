<?php
require_once __DIR__ . '/../includes/session.php';

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
redirect(get_setting('site_url', 'http://localhost') . '/admin/login');
?>