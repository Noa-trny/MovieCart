<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/utils/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn()) {
    setFlashMessage('Vous devez être connecté pour ajouter des articles au panier.', 'error');
    redirectTo(SITE_URL . '/pages/login.php');
}

$movieId = isset($_POST['movie_id']) ? (int)$_POST['movie_id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($movieId <= 0) {
    setFlashMessage('Film invalide.', 'error');
    redirectTo(SITE_URL . '/pages/index.php');
}

if ($quantity <= 0) {
    $quantity = 1;
}

$movie = getMovieById($movieId);
if (!$movie) {
    setFlashMessage('Le film demandé n\'existe pas.', 'error');
    redirectTo(SITE_URL . '/pages/index.php');
}

if (addToCart($movieId, $quantity)) {
    setFlashMessage($movie['title'] . ' a été ajouté à votre panier.', 'success');
}

$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : SITE_URL . '/pages/cart.php');
redirectTo($redirect);
?> 