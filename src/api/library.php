<?php
require_once __DIR__ . '/../../src/config/config.php';
require_once __DIR__ . '/../../src/config/database.php';
require_once __DIR__ . '/../models/Library.php';

header('Content-Type: application/json');

function sendError($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit;
}

if (!isLoggedIn()) {
    sendError('Authentification requise', 401);
}

$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? 'get_library';

$library = new Library($userId);

switch ($action) {
    case 'get_library':
        try {
            $movies = $library->getMovies();
            echo json_encode($movies);
        } catch (Exception $e) {
            sendError('Erreur lors de la récupération des films: ' . $e->getMessage(), 500);
        }
        break;
        
    case 'get_movie':
        $movieId = filter_input(INPUT_GET, 'movie_id', FILTER_VALIDATE_INT);
        if (!$movieId) {
            sendError('ID de film invalide');
        }
        
        try {
            $movie = $library->getMovie($movieId);
            if (!$movie) {
                sendError('Film non trouvé ou non acheté', 404);
            }
            echo json_encode($movie);
        } catch (Exception $e) {
            sendError('Erreur lors de la récupération du film: ' . $e->getMessage(), 500);
        }
        break;
        
    default:
        sendError('Action non reconnue');
} 