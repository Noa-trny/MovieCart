<?php
// WampServer specific configuration

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'moviecart');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site configuration
define('SITE_URL', 'http://localhost/MovieCart/public');
define('SITE_NAME', 'MovieCart');

// File upload paths
define('UPLOAD_PATH', __DIR__ . '/../../public/uploads');
define('POSTER_PATH', UPLOAD_PATH . '/posters');

// Session configuration
define('CART_SESSION_KEY', 'cart_items');

// Create upload directories if they don't exist
if (!file_exists(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0777, true);
}

if (!file_exists(POSTER_PATH)) {
    mkdir(POSTER_PATH, 0777, true);
}

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set default timezone
date_default_timezone_set('Europe/Paris'); 