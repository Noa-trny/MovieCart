<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/utils/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn()) {
    setFlashMessage('Vous devez être connecté pour gérer votre panier.', 'error');
    redirectTo(SITE_URL . '/pages/login.php');
}

$movieId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($movieId <= 0) {
    setFlashMessage('Film invalide.', 'error');
    redirectTo(SITE_URL . '/pages/cart.php');
}

if (removeFromCart($movieId)) {
    setFlashMessage('Le film a été retiré de votre panier.', 'success');
} else {
    setFlashMessage('Le film n\'était pas dans votre panier.', 'error');
}

$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : SITE_URL . '/pages/cart.php');
redirectTo($redirect);
?> 