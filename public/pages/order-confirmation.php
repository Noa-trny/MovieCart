<?php
$pageTitle = 'Confirmation de commande';
$customCss = '/css/orders.css';
$additionalCss = '/css/cart.css';

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/utils/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn()) {
    setFlashMessage("Veuillez vous connecter pour accéder à cette page.", "error");
    redirectTo(SITE_URL . '/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    setFlashMessage("Commande introuvable.", "error");
    redirectTo(SITE_URL . '/index.php');
}

$orderId = (int)$_GET['order_id'];
$userId = $_SESSION['user_id'];

function getOrderDetails($orderId, $userId) {
    $conn = connectDB();
    $order = null;
    $items = [];
    
    $stmt = $conn->prepare("
        SELECT o.*, DATE_FORMAT(o.created_at, '%d/%m/%Y à %H:%i') as formatted_date 
        FROM orders o 
        WHERE o.id = ? AND o.user_id = ?
    ");
    $stmt->bind_param("ii", $orderId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $conn->close();
        return null;
    }
    
    $order = $result->fetch_assoc();
    
    $stmt = $conn->prepare("
        SELECT oi.*, m.title, m.poster_path, d.name as director_name
        FROM order_items oi
        JOIN movies m ON oi.movie_id = m.id
        LEFT JOIN directors d ON m.director_id = d.id
        WHERE oi.order_id = ?
    ");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($item = $result->fetch_assoc()) {
        $items[] = $item;
    }
    
    $conn->close();
    
    return [
        'order' => $order,
        'items' => $items
    ];
}

$orderDetails = getOrderDetails($orderId, $userId);

if (!$orderDetails) {
    setFlashMessage("Commande introuvable.", "error");
    redirectTo(SITE_URL . '/index.php');
}

ob_start();
?>

<div class="cart-container">
    <h1 class="cart-title">Confirmation de commande</h1>
    
    <div class="flash-message flash-success">
        <p><i class="fas fa-check-circle"></i> Votre commande a été traitée avec succès !</p>
    </div>
    
    <div class="order-details">
        <div class="order-summary">
            <h2 class="order-section-title">Récapitulatif de la commande</h2>
            <div class="order-info">
                <p><strong>Numéro de commande :</strong> #<?= $orderDetails['order']['id'] ?></p>
                <p><strong>Date :</strong> <?= $orderDetails['order']['formatted_date'] ?></p>
                <p><strong>Statut :</strong> <span class="order-status-completed">Complétée</span></p>
                <p><strong>Total :</strong> <?= number_format($orderDetails['order']['total_amount'], 2) ?> €</p>
            </div>
        </div>
        
        <h2 class="order-section-title">Articles commandés</h2>
        <div class="cart-items">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Film</th>
                        <th>Prix</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderDetails['items'] as $item): ?>
                        <tr>
                            <td>
                                <div class="cart-product">
                                    <img 
                                        src="<?= isset($item['poster_path']) ? $item['poster_path'] : ASSETS_URL . '/images/movie-placeholder.jpg' ?>" 
                                        alt="<?= htmlspecialchars($item['title']) ?>" 
                                        class="cart-product-image"
                                    >
                                    <div>
                                        <h3 class="cart-product-title">
                                            <a href="<?= SITE_URL ?>/movie.php?id=<?= $item['movie_id'] ?>">
                                                <?= htmlspecialchars($item['title']) ?>
                                            </a>
                                        </h3>
                                        <?php if (isset($item['director_name'])): ?>
                                            <p class="cart-product-director">
                                                Réalisé par <?= htmlspecialchars($item['director_name']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="cart-price"><?= number_format($item['price'], 2) ?> €</td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <tr class="cart-total-row">
                        <td class="text-right">Total :</td>
                        <td class="cart-total-price"><?= number_format($orderDetails['order']['total_amount'], 2) ?> €</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="order-actions">
            <a href="<?= SITE_URL ?>/index.php" class="btn btn-primary">
                <i class="fas fa-home"></i> Retour à l'accueil
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../src/views/layouts/main.php';
?> 