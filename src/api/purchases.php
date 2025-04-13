<?php
require_once __DIR__ . '/../../src/config/config.php';
require_once __DIR__ . '/../../src/config/database.php';

header('Content-Type: application/json');

function sendError($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit;
}

$action = $_GET['action'] ?? '';
$publicActions = ['get_movie_price'];

if (!in_array($action, $publicActions) && !isLoggedIn()) {
    sendError('Authentification requise', 401);
}

$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendError('Méthode non autorisée', 405);
        }
        
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            sendError('Token CSRF invalide', 403);
        }
        
        $movieId = filter_input(INPUT_POST, 'movie_id', FILTER_VALIDATE_INT);
        if (!$movieId) {
            sendError('ID de film invalide');
        }
        
        if (!isset($_SESSION[CART_SESSION_KEY])) {
            $_SESSION[CART_SESSION_KEY] = [];
        }
        
        $movie = fetchOne("SELECT id, title FROM movies WHERE id = ?", [$movieId]);
        if (!$movie) {
            sendError('Film non trouvé', 404);
        }
        
        $_SESSION[CART_SESSION_KEY][] = $movieId;
        
        echo json_encode([
            'success' => true,
            'cart_count' => count($_SESSION[CART_SESSION_KEY])
        ]);
        break;
        
    case 'remove_from_cart':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendError('Méthode non autorisée', 405);
        }
        
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            sendError('Token CSRF invalide', 403);
        }
        
        $movieId = filter_input(INPUT_POST, 'movie_id', FILTER_VALIDATE_INT);
        if (!$movieId) {
            sendError('ID de film invalide');
        }
        
        if (!isset($_SESSION[CART_SESSION_KEY])) {
            $_SESSION[CART_SESSION_KEY] = [];
        }
        
        $key = array_search($movieId, $_SESSION[CART_SESSION_KEY]);
        if ($key !== false) {
            unset($_SESSION[CART_SESSION_KEY][$key]);
            $_SESSION[CART_SESSION_KEY] = array_values($_SESSION[CART_SESSION_KEY]);
        }
        
        echo json_encode([
            'success' => true,
            'cart_count' => count($_SESSION[CART_SESSION_KEY])
        ]);
        break;
        
    case 'checkout':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendError('Méthode non autorisée', 405);
        }
        
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            sendError('Token CSRF invalide', 403);
        }
        
        if (!isset($_SESSION[CART_SESSION_KEY]) || empty($_SESSION[CART_SESSION_KEY])) {
            sendError('Le panier est vide', 400);
        }
        
        try {
            beginTransaction();
            
            $total = 0;
            $cartItems = [];
            
            foreach ($_SESSION[CART_SESSION_KEY] as $movieId) {
                $movie = fetchOne("SELECT id, price FROM movies WHERE id = ?", [$movieId]);
                if ($movie) {
                    $total += $movie['price'];
                    $cartItems[] = $movie;
                }
            }
            
            $orderId = insert(
                "INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'completed')",
                [$userId, $total]
            );
            
            foreach ($cartItems as $item) {
                insert(
                    "INSERT INTO order_items (order_id, movie_id, price) VALUES (?, ?, ?)",
                    [$orderId, $item['id'], $item['price']]
                );
            }
            
            commitTransaction();
            
            $_SESSION[CART_SESSION_KEY] = [];
            
            echo json_encode([
                'success' => true,
                'order_id' => $orderId,
                'total_amount' => $total
            ]);
        } catch (Exception $e) {
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