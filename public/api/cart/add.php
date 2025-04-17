<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/utils/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn()) {
    echo json_encode([
        'success' => false,
        'message' => 'Vous devez être connecté pour ajouter des articles au panier.'
    ]);
    exit;
}

$movieId = isset($_POST['movie_id']) ? (int)$_POST['movie_id'] : 0;

if ($movieId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Film invalide.'
    ]);
    exit;
}

$movie = getMovieById($movieId);
if (!$movie) {
    echo json_encode([
        'success' => false,
        'message' => 'Le film demandé n\'existe pas.'
    ]);
    exit;
}

if (addToCart($movieId, 1)) {
    echo json_encode([
        'success' => true,
        'message' => $movie['title'] . ' a été ajouté à votre panier.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Une erreur est survenue lors de l\'ajout au panier.'
    ]);
} 