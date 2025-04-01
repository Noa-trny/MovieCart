<?php
require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/config/database.php';

// Initialize cart if it doesn't exist
if (!isset($_SESSION[CART_SESSION_KEY])) {
    $_SESSION[CART_SESSION_KEY] = [];
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $_SESSION['flash_message'] = 'Invalid request';
        $_SESSION['flash_type'] = 'error';
        redirect('/cart.php');
    }

    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            // Add item to cart (requires login)
            if (!isLoggedIn()) {
                $_SESSION['flash_message'] = 'Please log in to add items to your cart';
                $_SESSION['flash_type'] = 'error';
                redirect('/login.php');
            }

            $movieId = filter_input(INPUT_POST, 'movie_id', FILTER_VALIDATE_INT);
            
            if ($movieId) {
                // Check if the movie exists
                $movie = fetchOne("SELECT id, title, price FROM movies WHERE id = ?", [$movieId]);
                
                if ($movie) {
                    // Add to cart
                    $_SESSION[CART_SESSION_KEY][] = $movieId;
                    $_SESSION['flash_message'] = "'{$movie['title']}' has been added to your cart";
                    $_SESSION['flash_type'] = 'success';
                }
            }
            
            // Redirect back to referring page or cart
            $referer = $_SERVER['HTTP_REFERER'] ?? SITE_URL . '/cart.php';
            header("Location: $referer");
            exit;
            
        case 'remove':
            $movieId = filter_input(INPUT_POST, 'movie_id', FILTER_VALIDATE_INT);
            
            if ($movieId) {
                // Find and remove the movie from the cart
                $key = array_search($movieId, $_SESSION[CART_SESSION_KEY]);
                if ($key !== false) {
                    unset($_SESSION[CART_SESSION_KEY][$key]);
                    // Reindex the array
                    $_SESSION[CART_SESSION_KEY] = array_values($_SESSION[CART_SESSION_KEY]);
                    
                    $_SESSION['flash_message'] = 'Item removed from cart';
                    $_SESSION['flash_type'] = 'success';
                }
            }
            
            redirect('/cart.php');
            break;
            
        case 'clear':
            // Empty the cart
            $_SESSION[CART_SESSION_KEY] = [];
            $_SESSION['flash_message'] = 'Your cart has been cleared';
            $_SESSION['flash_type'] = 'success';
            redirect('/cart.php');
            break;
            
        case 'checkout':
            // User must be logged in to checkout
            if (!isLoggedIn()) {
                $_SESSION['flash_message'] = 'Please log in to checkout';
                $_SESSION['flash_type'] = 'error';
                redirect('/login.php');
            }
            
            // Check if cart is empty
            if (empty($_SESSION[CART_SESSION_KEY])) {
                $_SESSION['flash_message'] = 'Your cart is empty';
                $_SESSION['flash_type'] = 'error';
                redirect('/cart.php');
            }
            
            try {
                // Begin transaction
                beginTransaction();
                
                // Get the current user ID
                $userId = $_SESSION['user_id'];
                
                // Calculate total amount
                $total = 0;
                $cartItems = [];
                
                foreach ($_SESSION[CART_SESSION_KEY] as $movieId) {
                    $movie = fetchOne("SELECT id, price FROM movies WHERE id = ?", [$movieId]);
                    if ($movie) {
                        $total += $movie['price'];
                        $cartItems[] = $movie;
                    }
                }
                
                // Create order
                $orderId = insert(
                    "INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'completed')",
                    [$userId, $total]
                );
                
                // Add order items
                foreach ($cartItems as $item) {
                    insert(
                        "INSERT INTO order_items (order_id, movie_id, price) VALUES (?, ?, ?)",
                        [$orderId, $item['id'], $item['price']]
                    );
                }
                
                // Commit transaction
                commitTransaction();
                
                // Clear cart after successful checkout
                $_SESSION[CART_SESSION_KEY] = [];
                
                $_SESSION['flash_message'] = 'Order completed successfully';
                $_SESSION['flash_type'] = 'success';
                redirect('/profile.php');
                
            } catch (Exception $e) {
                // Rollback transaction in case of error
                rollbackTransaction();
                
                $_SESSION['flash_message'] = 'Error processing your order. Please try again.';
                $_SESSION['flash_type'] = 'error';
                redirect('/cart.php');
            }
            
            break;
    }
}

// Get cart items for display
$cartItems = [];
$totalAmount = 0;

if (!empty($_SESSION[CART_SESSION_KEY])) {
    // Get unique movie IDs (in case there are duplicates)
    $uniqueMovieIds = array_unique($_SESSION[CART_SESSION_KEY]);
    
    // Prepare placeholders for SQL IN clause
    $placeholders = rtrim(str_repeat('?,', count($uniqueMovieIds)), ',');
    
    // Get movie details
    $movies = fetchAll(
        "SELECT m.id, m.title, m.price, m.poster_path, d.name as director_name 
         FROM movies m 
         LEFT JOIN directors d ON m.director_id = d.id 
         WHERE m.id IN ($placeholders)",
        $uniqueMovieIds
    );
    
    // Count occurrences of each movie
    $movieCounts = array_count_values($_SESSION[CART_SESSION_KEY]);
    
    // Prepare cart items with counts
    foreach ($movies as $movie) {
        $count = $movieCounts[$movie['id']];
        $itemTotal = $movie['price'] * $count;
        $totalAmount += $itemTotal;
        
        $cartItems[] = [
            'id' => $movie['id'],
            'title' => $movie['title'],
            'price' => $movie['price'],
            'director_name' => $movie['director_name'],
            'poster_path' => $movie['poster_path'],
            'quantity' => $count,
            'total' => $itemTotal
        ];
    }
}

$pageTitle = 'Your Cart';
ob_start();
?>

<div class="mb-8">
    <h1 class="text-3xl font-bold mb-2">Your Shopping Cart</h1>
    <p class="text-gray-600">Review your items before checkout</p>
</div>

<?php if (empty($cartItems)): ?>
    <div class="bg-white p-8 rounded-lg shadow-md text-center">
        <i class="fas fa-shopping-cart text-gray-300 text-5xl mb-4"></i>
        <h2 class="text-2xl font-semibold mb-2">Your cart is empty</h2>
        <p class="text-gray-600 mb-6">Looks like you haven't added any movies to your cart yet.</p>
        <a href="<?= SITE_URL ?>" class="inline-block bg-blue-500 hover:bg-blue-600 text-white py-2 px-6 rounded-lg font-semibold">
            Browse Movies
        </a>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cart Items -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 bg-gray-50 border-b">
                    <h2 class="text-lg font-semibold">Cart Items (<?= count($_SESSION[CART_SESSION_KEY]) ?>)</h2>
                </div>
                
                <ul class="divide-y divide-gray-200">
                    <?php foreach ($cartItems as $item): ?>
                        <li class="p-4 flex flex-col sm:flex-row">
                            <!-- Mobile view image and details stacked -->
                            <div class="flex sm:hidden mb-4 space-x-4">
                                <img src="<?= SITE_URL ?>/uploads/posters/<?= $item['poster_path'] ?? 'default.jpg' ?>" 
                                     alt="<?= sanitizeOutput($item['title']) ?>" 
                                     class="w-20 h-28 object-cover rounded">
                                     
                                <div class="flex-1">
                                    <h3 class="font-semibold">
                                        <a href="<?= SITE_URL ?>/movie.php?id=<?= $item['id'] ?>" class="hover:text-blue-600">
                                            <?= sanitizeOutput($item['title']) ?>
                                        </a>
                                    </h3>
                                    <p class="text-gray-600 text-sm">Directed by <?= sanitizeOutput($item['director_name']) ?></p>
                                    <p class="text-blue-600 font-semibold mt-1">$<?= number_format($item['price'], 2) ?></p>
                                </div>
                            </div>
                            
                            <!-- Desktop view side by side -->
                            <div class="hidden sm:flex items-center space-x-4 flex-1">
                                <img src="<?= SITE_URL ?>/uploads/posters/<?= $item['poster_path'] ?? 'default.jpg' ?>" 
                                     alt="<?= sanitizeOutput($item['title']) ?>" 
                                     class="w-16 h-24 object-cover rounded">
                                     
                                <div class="flex-1">
                                    <h3 class="font-semibold">
                                        <a href="<?= SITE_URL ?>/movie.php?id=<?= $item['id'] ?>" class="hover:text-blue-600">
                                            <?= sanitizeOutput($item['title']) ?>
                                        </a>
                                    </h3>
                                    <p class="text-gray-600 text-sm">Directed by <?= sanitizeOutput($item['director_name']) ?></p>
                                </div>
                                
                                <div class="text-right">
                                    <p class="font-semibold">$<?= number_format($item['price'], 2) ?></p>
                                </div>
                            </div>
                            
                            <!-- Remove button -->
                            <div class="mt-3 sm:mt-0 sm:ml-4 flex items-center">
                                <form action="<?= SITE_URL ?>/cart.php" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="movie_id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash-alt"></i> Remove
                                    </button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                
                <div class="p-4 bg-gray-50 border-t">
                    <form action="<?= SITE_URL ?>/cart.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                        <input type="hidden" name="action" value="clear">
                        <button type="submit" class="text-gray-500 hover:text-red-600 flex items-center">
                            <i class="fas fa-times mr-2"></i> Clear Cart
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div>
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
                <h2 class="text-lg font-semibold mb-4">Order Summary</h2>
                
                <div class="border-t border-b py-4 mb-4">
                    <div class="flex justify-between mb-2">
                        <span>Subtotal</span>
                        <span class="font-semibold">$<?= number_format($totalAmount, 2) ?></span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Tax</span>
                        <span>$0.00</span>
                    </div>
                </div>
                
                <div class="flex justify-between mb-6">
                    <span class="font-semibold">Total</span>
                    <span class="text-xl font-bold">$<?= number_format($totalAmount, 2) ?></span>
                </div>
                
                <form action="<?= SITE_URL ?>/cart.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <input type="hidden" name="action" value="checkout">
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold flex items-center justify-center">
                        <i class="fas fa-credit-card mr-2"></i> Proceed to Checkout
                    </button>
                </form>
                
                <p class="text-gray-500 text-sm text-center mt-4">
                    Secure checkout powered by MovieCart
                </p>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../src/views/layouts/main.php';
?> 