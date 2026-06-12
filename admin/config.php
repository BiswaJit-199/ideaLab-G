<?php
/**
 * SECURITY ENHANCED CONFIGURATION FILE
 * 
 * Architecture: Modular, Secure, and Performance-Optimized.
 */

// 1. Enforce strict session security BEFORE session starts
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Strict');
    
    // In production, also enforce: ini_set('session.cookie_secure', 1);
    session_start();
}

// 2. Define Core System Constants
define('ADMIN_USERNAME', 'admin');
// Securely store hashed password. Default: Admin123
define('ADMIN_PASSWORD_HASH', '$2y$10$sMpLtBkYMMosxt3KjCTI3uUXtltSSwpUSU6PyDiAkfndqo3xVfiBO'); 
define('ADMIN_TITLE', 'ideaLab Admin Panel');

// File paths
define('PROJECT_ROOT', dirname(__DIR__));
define('UPLOADS_DIR', PROJECT_ROOT . '/assets/');
define('DATA_DIR', PROJECT_ROOT . '/data/');

// Ensure directories exist with secure permissions
if (!is_dir(UPLOADS_DIR)) {
    mkdir(UPLOADS_DIR, 0755, true);
}
if (!is_dir(DATA_DIR)) {
    mkdir(DATA_DIR, 0755, true);
}

/**
 * CSRF Protection - Generate unique secure token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * CSRF Protection - Validate submitted token
 */
function validateCSRFToken($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Check if admin is authenticated
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Force login check and protect admin pages from unauthorized access
 */
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Safely logout and destroy session
 */
function logout() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header('Location: login.php');
    exit();
}

/**
 * Secure sanitation helper to mitigate XSS (Cross Site Scripting)
 */
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
?>