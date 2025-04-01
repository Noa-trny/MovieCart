<?php
require_once __DIR__ . '/../../src/config/config.php';
require_once __DIR__ . '/../../src/config/database.php';

// Assurer que la réponse est en JSON
header('Content-Type: application/json');

// Fonction pour envoyer une réponse d'erreur
function sendError($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit;
}

$action = $_GET['action'] ?? 'get_all';

// Traiter l'action demandée
switch ($action) {
    case 'get_all':
        // Récupérer toutes les catégories
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("SELECT id, name, slug, description FROM categories ORDER BY name");
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['categories' => $categories]);
        break;
        
    case 'get_movies':
        // Récupérer les films d'une catégorie spécifique
        $categoryId = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
        $slug = filter_input(INPUT_GET, 'slug', FILTER_SANITIZE_STRING);
        
        if (!$categoryId && !$slug) {
            sendError('ID de catégorie ou slug requis');
        }
        
        $pdo = getDbConnection();
        
        // Préparation de la requête selon les paramètres fournis
        if ($categoryId) {
            $stmt = $pdo->prepare("
                SELECT 
                    m.id, m.title, m.release_year, m.duration, m.price, m.poster_path, m.description,
                    d.name as director_name,
                    c.name as category_name, c.slug as category_slug
                FROM movies m
                LEFT JOIN directors d ON m.director_id = d.id
                LEFT JOIN categories c ON m.category_id = c.id
                WHERE m.category_id = ?
                ORDER BY m.title
            ");
            $stmt->execute([$categoryId]);
        } else {
            $stmt = $pdo->prepare("
                SELECT 
                    m.id, m.title, m.release_year, m.duration, m.price, m.poster_path, m.description,
                    d.name as director_name,
                    c.name as category_name, c.slug as category_slug
                FROM movies m
                LEFT JOIN directors d ON m.director_id = d.id
                LEFT JOIN categories c ON m.category_id = c.id
                WHERE c.slug = ?
                ORDER BY m.title
            ");
            $stmt->execute([$slug]);
        }
        
        $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Si la catégorie existe mais n'a pas de films
        if (empty($movies) && $slug) {
            // Vérifier si la catégorie existe
            $checkStmt = $pdo->prepare("SELECT id, name, description FROM categories WHERE slug = ?");
            $checkStmt->execute([$slug]);
            $category = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($category) {
                echo json_encode([
                    'category' => $category,
                    'movies' => []
                ]);
                exit;
            }
        }
        
        // Si des films ont été trouvés, extraire les infos de catégorie du premier film
        if (!empty($movies)) {
            $category = [
                'id' => $categoryId ?: $movies[0]['id'],
                'name' => $movies[0]['category_name'],
                'slug' => $movies[0]['category_slug']
            ];
            
            // Nettoyer les résultats pour éliminer les données redondantes
            foreach ($movies as &$movie) {
                unset($movie['category_slug']);
            }
            
            echo json_encode([
                'category' => $category,
                'movies' => $movies
            ]);
        } else {
            sendError('Catégorie non trouvée', 404);
        }
        break;
        
    default:
        sendError('Action non reconnue');
} 