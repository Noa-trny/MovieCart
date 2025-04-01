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

$action = $_GET['action'] ?? '';

// Traiter l'action demandée
switch ($action) {
    case 'login':
        // Vérifier si la méthode est POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendError('Méthode non autorisée', 405);
        }
        
        // Récupérer les données JSON du corps de la requête
        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);
        
        if (!$data) {
            // Essayer de récupérer les données depuis $_POST
            $data = $_POST;
        }
        
        // Valider les entrées
        $email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = $data['password'] ?? '';
        
        if (!$email || empty($password)) {
            sendError('Email et mot de passe requis');
        }
        
        // Vérifier les identifiants
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("SELECT id, email, password, first_name, last_name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user || !password_verify($password, $user['password'])) {
            sendError('Identifiants incorrects', 401);
        }
        
        // Créer la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        
        // Générer un nouveau token CSRF
        generateCSRFToken();
        
        echo json_encode([
            'success' => true,
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'name' => $user['first_name'] . ' ' . $user['last_name']
            ],
            'csrf_token' => $_SESSION['csrf_token']
        ]);
        break;
        
    case 'register':
        // Vérifier si la méthode est POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendError('Méthode non autorisée', 405);
        }
        
        // Récupérer les données JSON du corps de la requête
        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);
        
        if (!$data) {
            // Essayer de récupérer les données depuis $_POST
            $data = $_POST;
        }
        
        // Valider les entrées
        $email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = $data['password'] ?? '';
        $firstName = trim($data['first_name'] ?? '');
        $lastName = trim($data['last_name'] ?? '');
        
        if (!$email || empty($password) || empty($firstName) || empty($lastName)) {
            sendError('Tous les champs sont requis');
        }
        
        if (strlen($password) < 8) {
            sendError('Le mot de passe doit contenir au moins 8 caractères');
        }
        
        // Vérifier si l'email existe déjà
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            sendError('Cet email est déjà utilisé');
        }
        
        // Créer l'utilisateur
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO users (email, password, first_name, last_name, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$email, $hashedPassword, $firstName, $lastName]);
            
            $userId = $pdo->lastInsertId();
            
            // Créer la session
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $firstName . ' ' . $lastName;
            
            // Générer un nouveau token CSRF
            generateCSRFToken();
            
            echo json_encode([
                'success' => true,
                'user' => [
                    'id' => $userId,
                    'email' => $email,
                    'name' => $firstName . ' ' . $lastName
                ],
                'csrf_token' => $_SESSION['csrf_token']
            ]);
        } catch (PDOException $e) {
            sendError('Erreur lors de la création du compte');
        }
        break;
        
    case 'logout':
        // Détruire la session
        session_destroy();
        
        echo json_encode(['success' => true]);
        break;
        
    case 'get_profile':
        // Vérifier l'authentification
        if (!isLoggedIn()) {
            sendError('Authentification requise', 401);
        }
        
        $userId = $_SESSION['user_id'];
        
        // Récupérer les informations de l'utilisateur
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("
            SELECT id, email, first_name, last_name, created_at 
            FROM users 
            WHERE id = ?
        ");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            sendError('Utilisateur non trouvé', 404);
        }
        
        // Masquer les informations sensibles
        unset($user['password']);
        
        echo json_encode(['user' => $user]);
        break;
        
    case 'update_password':
        // Vérifier l'authentification
        if (!isLoggedIn()) {
            sendError('Authentification requise', 401);
        }
        
        // Vérifier si la méthode est POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            sendError('Méthode non autorisée', 405);
        }
        
        // Vérifier le token CSRF
        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);
        
        if (!$data) {
            // Essayer de récupérer les données depuis $_POST
            $data = $_POST;
        }
        
        if (!isset($data['csrf_token']) || !validateCSRFToken($data['csrf_token'])) {
            sendError('Token CSRF invalide', 403);
        }
        
        $userId = $_SESSION['user_id'];
        $currentPassword = $data['current_password'] ?? '';
        $newPassword = $data['new_password'] ?? '';
        
        if (empty($currentPassword) || empty($newPassword)) {
            sendError('Tous les champs sont requis');
        }
        
        if (strlen($newPassword) < 8) {
            sendError('Le nouveau mot de passe doit contenir au moins 8 caractères');
        }
        
        // Vérifier le mot de passe actuel
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user || !password_verify($currentPassword, $user['password'])) {
            sendError('Mot de passe actuel incorrect');
        }
        
        // Mettre à jour le mot de passe
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashedPassword, $userId]);
            
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            sendError('Erreur lors de la mise à jour du mot de passe');
        }
        break;
        
    default:
        sendError('Action non reconnue');
} 