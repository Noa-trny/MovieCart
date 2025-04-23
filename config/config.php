<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('SITE_NAME', 'MovieCart');
define('SITE_URL', 'http://localhost/MovieCart/public/pages');
define('ASSETS_URL', 'http://localhost/MovieCart/public/assets');
define('PUBLIC_PATH', __DIR__ . '/../public');

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'moviecart');

define('CART_SESSION_KEY', 'cart_items');

function redirect($path) {
    header('Location: ' . SITE_URL . $path);
    exit;
}

function isLoggedIn() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['flash_message'] = 'Veuillez vous connecter pour accéder à cette page';
        $_SESSION['flash_type'] = 'error';
        redirect('/login.php');
    }
}

function sanitizeOutput($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?> 