<?php
$pageTitle = 'Panier';

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/utils/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn()) {
    setFlashMessage("Veuillez vous connecter pour accéder à votre panier.", "error");
    redirectTo(SITE_URL . '/login.php?redirect=' . urlencode(SITE_URL . '/cart.php'));
}

$cartItems = getCartItems();
$total = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_item'])) {
        $movieId = (int)$_POST['movie_id'];
        
        if (isset($_SESSION[CART_SESSION_KEY][$movieId])) {
            unset($_SESSION[CART_SESSION_KEY][$movieId]);
            setFlashMessage("L'article a été retiré du panier.", "success");
            redirectTo(SITE_URL . '/cart.php');
        }
    }
    
    if (isset($_POST['clear_cart'])) {
        $_SESSION[CART_SESSION_KEY] = [];
        setFlashMessage("Votre panier a été vidé.", "success");
        redirectTo(SITE_URL . '/cart.php');
    }
    
    if (isset($_POST['checkout'])) {
        redirectTo(SITE_URL . '/checkout.php');
    }
}

ob_start();
?>

<div class="cart-container">
    <h1 class="cart-title">Mon panier</h1>
    
    <?php if (empty($cartItems)): ?>
        <div class="cart-empty">
            <i class="fas fa-shopping-cart cart-empty-icon"></i>
            <h2 class="cart-empty-title">Votre panier est vide</h2>
            <p class="cart-empty-description">Parcourez notre catalogue pour trouver vos films préférés.</p>
            
            <a href="<?= SITE_URL ?>/index.php" class="btn btn-primary">
                Continuer mes achats
            </a>
        </div>
    <?php else: ?>
        <div class="cart-items">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Film</th>
                        <th>Prix</th>
                        <th>Sous-total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): 
                        $subtotal = $item['price'] * (isset($_SESSION[CART_SESSION_KEY][$item['id']]) ? $_SESSION[CART_SESSION_KEY][$item['id']] : 1);
                        $total += $subtotal;
                    ?>
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
                                            <a href="<?= SITE_URL ?>/movie.php?id=<?= $item['id'] ?>">
                                                <?= htmlspecialchars($item['title']) ?>
                                            </a>
                                        </h3>
                                        <?php if (isset($item['director_id']) && isset($item['director_name'])): ?>
                                            <p class="cart-product-director">
                                                Réalisé par 
                                                <a href="<?= SITE_URL ?>/director.php?id=<?= $item['director_id'] ?>" class="cart-director-link">
                                                    <?= htmlspecialchars($item['director_name']) ?>
                                                </a>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="cart-price"><?= number_format($item['price'], 2) ?> €</td>
                            <td class="cart-subtotal"><?= number_format($subtotal, 2) ?> €</td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="movie_id" value="<?= $item['id'] ?>">
                                    <button type="submit" name="remove_item" class="cart-remove">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <tr class="cart-total-row">
                        <td colspan="2" class="text-right">Total :</td>
                        <td class="cart-total-price"><?= number_format($total, 2) ?> €</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="cart-actions">
            <div class="cart-actions-left">
                <a href="<?= SITE_URL ?>/index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Continuer mes achats
                </a>
                
                <form method="POST" class="inline">
                    <button type="submit" name="clear_cart" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir vider votre panier ?')">
                        <i class="fas fa-trash"></i> Vider le panier
                    </button>
                </form>
            </div>
            
            <div class="cart-actions-right">
                <form method="POST" class="inline">
                    <button type="submit" name="checkout" class="btn btn-primary">
                        <i class="fas fa-credit-card"></i> Passer à la caisse
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../src/views/layouts/main.php';
?> 