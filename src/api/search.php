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

// Récupérer et valider les paramètres
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($query)) {
    sendError('Terme de recherche requis');
}

// Limiter la recherche à un certain nombre de résultats
$limit = isset($_GET['limit']) ? filter_var($_GET['limit'], FILTER_VALIDATE_INT, ['options' => ['default' => 20, 'min_range' => 1, 'max_range' => 50]]) : 20;

// Effectuer la recherche
$pdo = getDbConnection();

// Préparation des termes de recherche pour la requête SQL
$searchTerm = '%' . $query . '%';

$stmt = $pdo->prepare("
    SELECT 
        m.id, m.title, m.release_year, m.duration, m.price, m.poster_path, m.description,
        d.name as director_name, d.id as director_id,
        c.name as category_name, c.id as category_id, c.slug as category_slug,
        a.name as actor_name, a.id as actor_id
    FROM movies m
    LEFT JOIN directors d ON m.director_id = d.id
    LEFT JOIN categories c ON m.category_id = c.id
    LEFT JOIN movie_actors ma ON m.id = ma.movie_id
    LEFT JOIN actors a ON ma.actor_id = a.id
    WHERE m.title LIKE ? 
       OR d.name LIKE ? 
       OR a.name LIKE ?
    ORDER BY 
        CASE 
            WHEN m.title LIKE ? THEN 1
            WHEN d.name LIKE ? THEN 2
            WHEN a.name LIKE ? THEN 3
            ELSE 4
        END,
        m.title
    LIMIT ?
");

$stmt->execute([
    $searchTerm, 
    $searchTerm, 
    $searchTerm, 
    $searchTerm, 
    $searchTerm, 
    $searchTerm, 
    $limit
]);

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organiser les résultats pour éviter les doublons
$movies = [];
$actorsByMovie = [];

foreach ($results as $row) {
    $movieId = $row['id'];
    
    // Si ce film n'a pas encore été ajouté à notre tableau de résultats
    if (!isset($movies[$movieId])) {
        $movies[$movieId] = [
            'id' => $movieId,
            'title' => $row['title'],
            'release_year' => $row['release_year'],
            'duration' => $row['duration'],
            'price' => $row['price'],
            'poster_path' => $row['poster_path'],
            'description' => $row['description'],
            'director' => [
                'id' => $row['director_id'],
                'name' => $row['director_name']
            ],
            'category' => [
                'id' => $row['category_id'],
                'name' => $row['category_name'],
                'slug' => $row['category_slug']
            ]
        ];
        $actorsByMovie[$movieId] = [];
    }
    
    // Ajouter l'acteur au film s'il n'est pas déjà présent
    if ($row['actor_id'] && !in_array($row['actor_id'], array_column($actorsByMovie[$movieId], 'id'))) {
        $actorsByMovie[$movieId][] = [
            'id' => $row['actor_id'],
            'name' => $row['actor_name']
        ];
    }
}

// Ajouter les acteurs à chaque film
foreach ($movies as $movieId => &$movie) {
    $movie['actors'] = $actorsByMovie[$movieId];
}

// Convertir l'array associatif en tableau simple pour le JSON
$moviesArray = array_values($movies);

// Renvoyer les résultats
echo json_encode([
    'query' => $query,
    'count' => count($moviesArray),
    'movies' => $moviesArray
]); 