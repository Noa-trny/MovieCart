<?php
require_once __DIR__ . '/../../config/config.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirectTo($url) {
    header("Location: $url");
    exit;
}

function setFlashMessage($message, $type = 'success') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function renderSearchForm() {
    $searchQuery = isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '';
    $output = '<form action="' . SITE_URL . '/search.php" method="GET">
        <div class="search-input-group">
            <input 
                type="text" 
                name="q" 
                placeholder="Rechercher un film..." 
                value="' . $searchQuery . '"
            >
            <button type="submit">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>';
    
    return $output;
}

function sanitizeInput($input) {
    $conn = connectDB();
    $sanitized = $conn->real_escape_string($input);
    $conn->close();
    return $sanitized;
}

function connectDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

function executeQuery($sql, $types = null, $params = []) {
    $conn = connectDB();
    
    if (!empty($params)) {
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            $conn->close();
            return false;
        }
        
        if ($types !== null) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    } else {
        $result = $conn->query($sql);
    }
    
    $conn->close();
    return $result;
}

function tableExists($tableName) {
    $conn = connectDB();
    $query = "SELECT 1 FROM information_schema.tables 
              WHERE table_schema = '" . DB_NAME . "' 
              AND table_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $tableName);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();
    $conn->close();
    return $exists;
}

function getMovieById($id) {
    $categoriesExist = tableExists('categories');
    
    $sql = "SELECT m.*, d.name as director_name";
    
    if ($categoriesExist) {
        $sql .= ", c.name as category_name";
    }
    
    $sql .= " FROM movies m 
              LEFT JOIN directors d ON m.director_id = d.id";
    
    if ($categoriesExist) {
        $sql .= " LEFT JOIN categories c ON m.category_id = c.id";
    }
    
    $sql .= " WHERE m.id = ?";
    
    $result = executeQuery($sql, "i", [$id]);
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

function getFeaturedMovies($limit = 6) {
    $conn = connectDB();
    $movies = [];
    $categoriesExist = tableExists('categories');
    
    // Vérifier si le champ 'featured' existe dans la table movies
    $checkFieldQuery = "SHOW COLUMNS FROM movies LIKE 'featured'";
    $fieldExists = $conn->query($checkFieldQuery);
    
    if ($fieldExists && $fieldExists->num_rows > 0) {
        // Le champ existe, on peut l'utiliser dans la requête
        $sql = "SELECT m.*, d.name as director_name";
        
        if ($categoriesExist) {
            $sql .= ", c.name as category_name";
        }
        
        $sql .= " FROM movies m 
                  LEFT JOIN directors d ON m.director_id = d.id";
        
        if ($categoriesExist) {
            $sql .= " LEFT JOIN categories c ON m.category_id = c.id";
        }
        
        $sql .= " WHERE m.featured = 1 
                  LIMIT ?";
        
        $result = executeQuery($sql, "i", [$limit]);
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $movies[] = $row;
            }
        }
    } else {
        // Le champ n'existe pas, on récupère simplement les films les plus récents
        $sql = "SELECT m.*, d.name as director_name";
        
        if ($categoriesExist) {
            $sql .= ", c.name as category_name";
        }
        
        $sql .= " FROM movies m 
                  LEFT JOIN directors d ON m.director_id = d.id";
        
        if ($categoriesExist) {
            $sql .= " LEFT JOIN categories c ON m.category_id = c.id";
        }
        
        $sql .= " ORDER BY m.id DESC 
                  LIMIT ?";
        
        $result = executeQuery($sql, "i", [$limit]);
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $movies[] = $row;
            }
        }
        
        // Afficher un avertissement pour l'administrateur
        if (isAdmin()) {
            setFlashMessage("Le champ 'featured' n'existe pas dans la table 'movies'. Veuillez mettre à jour votre base de données.", 'warning');
        }
    }
    
    $conn->close();
    return $movies;
}

function getMoviesByCategory($category, $limit = 10) {
    $sql = "SELECT * FROM movies WHERE category = ? LIMIT ?";
    $result = executeQuery($sql, "si", [$category, $limit]);
    
    $movies = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $movies[] = $row;
        }
    }
    
    return $movies;
}

function getMoviesByDirector($directorId) {
    $sql = "SELECT * FROM movies WHERE director_id = ?";
    $result = executeQuery($sql, "i", [$directorId]);
    
    $movies = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $movies[] = $row;
        }
    }
    
    return $movies;
}

function searchMovies($query) {
    $searchTerm = "%$query%";
    $categoriesExist = tableExists('categories');
    
    $sql = "SELECT m.*, d.name as director_name";
    
    if ($categoriesExist) {
        $sql .= ", c.name as category_name";
    }
    
    $sql .= " FROM movies m 
              LEFT JOIN directors d ON m.director_id = d.id";
    
    if ($categoriesExist) {
        $sql .= " LEFT JOIN categories c ON m.category_id = c.id";
    }
    
    $sql .= " WHERE m.title LIKE ? OR d.name LIKE ?";
    
    $result = executeQuery($sql, "ss", [$searchTerm, $searchTerm]);
    
    $movies = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $movies[] = $row;
        }
    }
    
    return $movies;
}

function isUserAuthenticated() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdmin() {
    if (!isUserAuthenticated()) {
        return false;
    }
    
    $conn = connectDB();
    $userId = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $isAdmin = (bool)$user['is_admin'];
    } else {
        $isAdmin = false;
    }
    
    $stmt->close();
    $conn->close();
    
    return $isAdmin;
}

function getCart() {
    if (!isset($_SESSION[CART_SESSION_KEY])) {
        $_SESSION[CART_SESSION_KEY] = [];
    }
    
    return $_SESSION[CART_SESSION_KEY];
}

function getCartItems() {
    if (!isset($_SESSION[CART_SESSION_KEY]) || empty($_SESSION[CART_SESSION_KEY])) {
        return [];
    }
    
    $conn = connectDB();
    $items = [];
    $categoriesExist = tableExists('categories');
    
    $movieIds = array_keys($_SESSION[CART_SESSION_KEY]);
    
    if (empty($movieIds)) {
        $conn->close();
        return [];
    }
    
    $placeholders = str_repeat('?,', count($movieIds) - 1) . '?';
    
    $sql = "SELECT m.*, d.name as director_name";
    
    if ($categoriesExist) {
        $sql .= ", c.name as category_name";
    }
    
    $sql .= " FROM movies m 
              LEFT JOIN directors d ON m.director_id = d.id";
    
    if ($categoriesExist) {
        $sql .= " LEFT JOIN categories c ON m.category_id = c.id";
    }
    
    $sql .= " WHERE m.id IN ($placeholders)";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        $conn->close();
        return [];
    }
    
    $types = str_repeat('i', count($movieIds));
    
    $bindParams = array($types);
    foreach ($movieIds as $key => $val) {
        $bindParams[] = &$movieIds[$key];
    }
    
    call_user_func_array(array($stmt, 'bind_param'), $bindParams);
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($movie = $result->fetch_assoc()) {
        $movie['quantity'] = $_SESSION[CART_SESSION_KEY][$movie['id']];
        
        if (!isset($movie['image_url']) && isset($movie['poster_path'])) {
            $movie['image_url'] = $movie['poster_path'];
        }
        
        if (empty($movie['image_url'])) {
            $movie['image_url'] = ASSETS_URL . '/images/movie-placeholder.jpg';
        }
        
        $items[] = $movie;
    }
    
    $stmt->close();
    $conn->close();
    
    return $items;
}

function addToCart($movieId, $quantity = 1) {
    if (!isset($_SESSION[CART_SESSION_KEY])) {
        $_SESSION[CART_SESSION_KEY] = [];
    }
    
    if (isset($_SESSION[CART_SESSION_KEY][$movieId])) {
        $_SESSION[CART_SESSION_KEY][$movieId] += $quantity;
    } else {
        $_SESSION[CART_SESSION_KEY][$movieId] = $quantity;
    }
    
    return true;
}

function removeFromCart($movieId) {
    if (isset($_SESSION[CART_SESSION_KEY][$movieId])) {
        unset($_SESSION[CART_SESSION_KEY][$movieId]);
        return true;
    }
    
    return false;
}

function updateCartItemQuantity($movieId, $quantity) {
    if (!isset($_SESSION[CART_SESSION_KEY])) {
        return false;
    }
    
    if ($quantity <= 0) {
        return removeFromCart($movieId);
    }
    
    $_SESSION[CART_SESSION_KEY][$movieId] = $quantity;
    return true;
}

function clearCart() {
    $_SESSION[CART_SESSION_KEY] = [];
    return true;
}

function getCartTotal() {
    if (!isset($_SESSION[CART_SESSION_KEY]) || empty($_SESSION[CART_SESSION_KEY])) {
        return 0;
    }
    
    $conn = connectDB();
    $total = 0;
    
    foreach ($_SESSION[CART_SESSION_KEY] as $movieId => $quantity) {
        $stmt = $conn->prepare("SELECT price FROM movies WHERE id = ?");
        $stmt->bind_param("i", $movieId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $movie = $result->fetch_assoc();
            $total += $movie['price'] * $quantity;
        }
        
        $stmt->close();
    }
    
    $conn->close();
    return $total;
}

function getCartItemCount() {
    if (!isset($_SESSION[CART_SESSION_KEY])) {
        return 0;
    }
    
    $count = 0;
    foreach ($_SESSION[CART_SESSION_KEY] as $quantity) {
        $count += $quantity;
    }
    
    return $count;
}

function getUserPurchases($userId) {
    $conn = connectDB();
    $movies = [];
    
    try {
        if (!tableExists('purchases')) {
            $conn->close();
            return [];
        }
        
        $categoriesExist = tableExists('categories');
        
        $sql = "SELECT m.*, d.name as director_name";
        
        if ($categoriesExist) {
            $sql .= ", c.name as category_name";
        }
        
        $sql .= " FROM purchases p
                 JOIN movies m ON p.movie_id = m.id
                 LEFT JOIN directors d ON m.director_id = d.id";
        
        if ($categoriesExist) {
            $sql .= " LEFT JOIN categories c ON m.category_id = c.id";
        }
        
        $sql .= " WHERE p.user_id = ?
                 ORDER BY p.purchase_date DESC";
        
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            $conn->close();
            return [];
        }
        
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $movies[] = $row;
        }
        
        $stmt->close();
    } catch (Exception $e) {
        // Log l'erreur mais on retourne simplement un tableau vide
        error_log("Erreur lors de la récupération des achats: " . $e->getMessage());
    }
    
    $conn->close();
    return $movies;
}
?> 