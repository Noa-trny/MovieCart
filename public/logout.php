<?php
require_once __DIR__ . '/../src/config/config.php';

// Clear session data
session_unset();
session_destroy();

// Start a new session for the flash message
session_start();

// Set logout message
$_SESSION['flash_message'] = 'You have been logged out successfully';
$_SESSION['flash_type'] = 'success';

// Redirect to homepage
redirect('/'); 