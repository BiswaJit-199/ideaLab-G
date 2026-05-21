<?php
// ADMIN CONFIGURATION FILE
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'Admin123'); // Change this to something strong!
define('ADMIN_TITLE', 'ideaLab Admin Panel');
define('PROJECT_ROOT', dirname(dirname(__FILE__)));
define('UPLOADS_DIR', PROJECT_ROOT . '/assets/');
define('DATA_DIR', PROJECT_ROOT . '/data/');

// Create uploads directory if it doesn't exist
if (!is_dir(UPLOADS_DIR)) {
    mkdir(UPLOADS_DIR, 0755, true);
}

// Simple session management
session_start();

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function logout() {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Check if user is logged in (redirect if not)
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}
?>
