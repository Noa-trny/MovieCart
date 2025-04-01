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

// Vérifier l'authentification (sauf pour certaines actions publiques)
$action = $_GET['action'] ?? '';
$publicActions = ['get_movie_price'];

if (!in_array($action, $publicActions) && !isLoggedIn()) {
    sendError('Authentification requise', 401);
}

// Récupérer l'ID utilisateur si connecté
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Traiter l'action demandée
switch ($action) {
    case 'get_purchases':
        try {
            $orders = fetchAll(
                "SELECT o.id as order_id, o.total_amount, o.created_at, o.status
                 FROM orders o
                 WHERE o.user_id = ?
                 ORDER BY o.created_at DESC",
                [$userId]
            );
            
            // Ajouter les films pour chaque commande
            foreach ($orders as &$order) {
                $order['items'] = fetchAll(
                    "SELECT oi.movie_id, m.title, oi.price, m.poster_path 
                     FROM order_items oi
                     JOIN movies m ON oi.movie_id = m.id
                     WHERE oi.order_id = ?",
                    [$order['order_id']]
                );
            }
            
            echo json_encode($orders);
        } catch (Exception $e) {
            sendError('Erreur lors de la récupération des achats: ' . $e->getMessage(), 500);
        }
        break;
        
    case 'verify_purchase':
        $movieId = filter_input(INPUT_GET, 'movie_id', FILTER_VALIDATE_INT);
        if (!$movieId) {
            sendError('ID de film invalide');
        }
        
        try {
            $purchase = fetchOne(
                "SELECT o.id as order_id, o.created_at as purchase_date
                 FROM orders o
                 JOIN order_items oi ON o.id = oi.order_id
                 WHERE o.user_id = ? AND oi.movie_id = ? AND o.status = 'completed'
                 ORDER BY o.created_at DESC
                 LIMIT 1",
                [$userId, $movieId]
            );
            
            if ($purchase) {
                echo json_encode([
                    'purchased' => true,
                    'order_id' => $purchase['order_id'],
                    'purchase_date' => $purchase['purchase_date']
                ]);
            } else {
                echo json_encode(['purchased' => false]);
            }
        } catch (Exception $e) {
            sendError('Erreur lors de la vérification de l\'achat: ' . $e->getMessage(), 500);
        }
        break;
        
    case 'add_to_cart':
        // Vérifier si la méthode est POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendError('Méthode non autorisée', 405);
        }
        
        // Vérifier le token CSRF
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            sendError('Token CSRF invalide', 403);
        }
        
        $movieId = filter_input(INPUT_POST, 'movie_id', FILTER_VALIDATE_INT);
        if (!$movieId) {
            sendError('ID de film invalide');
        }
        
        // Initialiser le panier si nécessaire
        if (!isset($_SESSION[CART_SESSION_KEY])) {
            $_SESSION[CART_SESSION_KEY] = [];
        }
        
        // Vérifier si le film existe
        $movie = fetchOne("SELECT id, title FROM movies WHERE id = ?", [$movieId]);
        if (!$movie) {
            sendError('Film non trouvé', 404);
        }
        
        // Ajouter au panier
        $_SESSION[CART_SESSION_KEY][] = $movieId;
        
        echo json_encode([
            'success' => true,
            'cart_count' => count($_SESSION[CART_SESSION_KEY])
        ]);
        break;
        
    case 'remove_from_cart':
        // Vérifier si la méthode est POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendError('Méthode non autorisée', 405);
        }
        
        // Vérifier le token CSRF
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            sendError('Token CSRF invalide', 403);
        }
        
        $movieId = filter_input(INPUT_POST, 'movie_id', FILTER_VALIDATE_INT);
        if (!$movieId) {
            sendError('ID de film invalide');
        }
        
        // Vérifier si le panier existe
        if (!isset($_SESSION[CART_SESSION_KEY])) {
            $_SESSION[CART_SESSION_KEY] = [];
        }
        
        // Chercher et supprimer du panier
        $key = array_search($movieId, $_SESSION[CART_SESSION_KEY]);
        if ($key !== false) {
            unset($_SESSION[CART_SESSION_KEY][$key]);
            $_SESSION[CART_SESSION_KEY] = array_values($_SESSION[CART_SESSION_KEY]); // Réindexer
        }
        
        echo json_encode([
            'success' => true,
            'cart_count' => count($_SESSION[CART_SESSION_KEY])
        ]);
        break;
        
    case 'checkout':
        // Vérifier si la méthode est POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendError('Méthode non autorisée', 405);
        }
        
        // Vérifier le token CSRF
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            sendError('Token CSRF invalide', 403);
        }
        
        // Vérifier si le panier existe et n'est pas vide
        if (!isset($_SESSION[CART_SESSION_KEY]) || empty($_SESSION[CART_SESSION_KEY])) {
            sendError('Le panier est vide', 400);
        }
        
        try {
            // Commencer la transaction
            beginTransaction();
            
            // Calculer le montant total
            $total = 0;
            $cartItems = [];
            
            foreach ($_SESSION[CART_SESSION_KEY] as $movieId) {
                $movie = fetchOne("SELECT id, price FROM movies WHERE id = ?", [$movieId]);
                if ($movie) {
                    $total += $movie['price'];
                    $cartItems[] = $movie;
                }
            }
            
            // Créer la commande
            $orderId = insert(
                "INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'completed')",
                [$userId, $total]
            );
            
            // Ajouter les films à la commande
            foreach ($cartItems as $item) {
                insert(
                    "INSERT INTO order_items (order_id, movie_id, price) VALUES (?, ?, ?)",
                    [$orderId, $item['id'], $item['price']]
                );
            }
            
            // Valider la transaction
            commitTransaction();
            
            // Vider le panier
            $_SESSION[CART_SESSION_KEY] = [];
            
            echo json_encode([
                'success' => true,
                'order_id' => $orderId,
                'total_amount' => $total
            ]);
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            rollbackTransaction();
            sendError('Erreur lors du paiement: ' . $e->getMessage(), 500);
        }
        break;
        
    case 'get_movie_price':
        $movieId = filter_input(INPUT_GET, 'movie_id', FILTER_VALIDATE_INT);
        if (!$movieId) {
            sendError('ID de film invalide');
        }
        
        try {
            $movie = fetchOne("SELECT id, title, price FROM movies WHERE id = ?", [$movieId]);
            if (!$movie) {
                sendError('Film non trouvé', 404);
            }
            
            echo json_encode([
                'id' => $movie['id'],
                'title' => $movie['title'],
                'price' => $movie['price']
            ]);
        } catch (Exception $e) {
            sendError('Erreur lors de la récupération du prix: ' . $e->getMessage(), 500);
        }
        break;
        
    default:
        sendError('Action non reconnue');
} 