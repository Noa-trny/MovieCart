<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/utils/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn()) {
    setFlashMessage("Veuillez vous connecter pour finaliser votre achat.", "error");
    redirectTo(SITE_URL . '/login.php?redirect=' . urlencode(SITE_URL . '/cart.php'));
}

$cartItems = getCartItems();
if (empty($cartItems)) {
    setFlashMessage("Votre panier est vide.", "error");
    redirectTo(SITE_URL . '/cart.php');
}

function createOrder($userId, $cartItems) {
    $conn = connectDB();
    $totalAmount = getCartTotal();
    
    $conn->begin_transaction();
    
    try {
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'completed')");
        $stmt->bind_param("id", $userId, $totalAmount);
        $stmt->execute();
        $orderId = $conn->insert_id;
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, movie_id, price) VALUES (?, ?, ?)");
        foreach ($cartItems as $item) {
            $movieId = $item['id'];
            $price = $item['price'];
            $stmt->bind_param("iid", $orderId, $movieId, $price);
            $stmt->execute();
        }
        $conn->commit();
        $conn->close();
        clearCart();
        
        return $orderId;
    } catch (Exception $e) {
        $conn->rollback();
        $conn->close();
        
        return false;
    }
}

$userId = $_SESSION['user_id'];
$orderId = createOrder($userId, $cartItems);

if ($orderId) {
    setFlashMessage("Votre commande #" . $orderId . " a été traitée avec succès. Merci pour votre achat !", "success");
    redirectTo(SITE_URL . '/order-confirmation.php?order_id=' . $orderId);
} else {
    setFlashMessage("Une erreur est survenue lors du traitement de votre commande. Veuillez réessayer.", "error");
    redirectTo(SITE_URL . '/cart.php');
} 