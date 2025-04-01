<?php
require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/config/database.php';

// Require login to access profile
requireLogin();

// Initialize variables
$pageTitle = 'My Profile';
$errors = [];
$success = '';

// Handle password change form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $errors[] = 'Invalid request';
    } else {
        // Get form data
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate input
        if (empty($currentPassword)) {
            $errors[] = 'Current password is required';
        }
        
        if (empty($newPassword)) {
            $errors[] = 'New password is required';
        } elseif (strlen($newPassword) < 8) {
            $errors[] = 'New password must be at least 8 characters long';
        }
        
        if ($newPassword !== $confirmPassword) {
            $errors[] = 'New passwords do not match';
        }
        
        // If no errors, check current password and update
        if (empty($errors)) {
            // Get current user data
            $user = fetchOne(
                "SELECT password FROM users WHERE id = ?", 
                [$_SESSION['user_id']]
            );
            
            // Verify current password
            if ($user && password_verify($currentPassword, $user['password'])) {
                // Hash new password
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                
                // Update password
                $updated = query(
                    "UPDATE users SET password = ? WHERE id = ?",
                    [$hashedPassword, $_SESSION['user_id']]
                );
                
                if ($updated) {
                    $success = 'Password updated successfully';
                } else {
                    $errors[] = 'Failed to update password';
                }
            } else {
                $errors[] = 'Current password is incorrect';
            }
        }
    }
}

// Get user data
$user = fetchOne(
    "SELECT username, email, created_at FROM users WHERE id = ?", 
    [$_SESSION['user_id']]
);

// Get purchase history
$purchases = fetchAll(
    "SELECT o.id, o.total_amount, o.created_at, o.status,
            m.id as movie_id, m.title, m.poster_path, oi.price
     FROM orders o
     JOIN order_items oi ON o.id = oi.order_id
     JOIN movies m ON oi.movie_id = m.id
     WHERE o.user_id = ?
     ORDER BY o.created_at DESC, o.id DESC",
    [$_SESSION['user_id']]
);

// Group purchases by order
$orders = [];
foreach ($purchases as $purchase) {
    $orderId = $purchase['id'];
    
    if (!isset($orders[$orderId])) {
        $orders[$orderId] = [
            'id' => $orderId,
            'total_amount' => $purchase['total_amount'],
            'created_at' => $purchase['created_at'],
            'status' => $purchase['status'],
            'items' => []
        ];
    }
    
    $orders[$orderId]['items'][] = [
        'movie_id' => $purchase['movie_id'],
        'title' => $purchase['title'],
        'poster_path' => $purchase['poster_path'],
        'price' => $purchase['price']
    ];
}

ob_start();
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- User Info and Password Change -->
    <div class="lg:col-span-1">
        <!-- User Info -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-xl font-semibold mb-4">Account Information</h2>
            
            <div class="flex items-center justify-center mb-6">
                <div class="bg-blue-500 text-white text-4xl w-24 h-24 rounded-full flex items-center justify-center">
                    <?= strtoupper(substr($user['username'], 0, 1)) ?>
                </div>
            </div>
            
            <div class="space-y-4">
                <div>
                    <h3 class="text-gray-500 text-sm">Username</h3>
                    <p class="font-semibold"><?= sanitizeOutput($user['username']) ?></p>
                </div>
                
                <div>
                    <h3 class="text-gray-500 text-sm">Email</h3>
                    <p class="font-semibold"><?= sanitizeOutput($user['email']) ?></p>
                </div>
                
                <div>
                    <h3 class="text-gray-500 text-sm">Member Since</h3>
                    <p class="font-semibold"><?= date('F j, Y', strtotime($user['created_at'])) ?></p>
                </div>
            </div>
        </div>
        
        <!-- Password Change Form -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Change Password</h2>
            
            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                    <ul class="list-disc list-inside">
                        <?php foreach ($errors as $error): ?>
                            <li><?= sanitizeOutput($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                    <?= sanitizeOutput($success) ?>
                </div>
            <?php endif; ?>
            
            <form action="<?= SITE_URL ?>/profile.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <input type="hidden" name="change_password" value="1">
                
                <div class="mb-4">
                    <label for="current_password" class="block text-gray-700 mb-2">Current Password</label>
                    <input type="password" id="current_password" name="current_password" 
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           required>
                </div>
                
                <div class="mb-4">
                    <label for="new_password" class="block text-gray-700 mb-2">New Password</label>
                    <input type="password" id="new_password" name="new_password" 
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           required>
                    <p class="text-sm text-gray-500 mt-1">Must be at least 8 characters</p>
                </div>
                
                <div class="mb-6">
                    <label for="confirm_password" class="block text-gray-700 mb-2">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" 
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           required>
                </div>
                
                <button type="submit" 
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg">
                    Update Password
                </button>
            </form>
        </div>
    </div>
    
    <!-- Purchase History -->
    <div class="lg:col-span-2">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-6">Purchase History</h2>
            
            <?php if (empty($orders)): ?>
                <div class="bg-gray-100 p-6 rounded-lg text-center">
                    <i class="fas fa-shopping-bag text-gray-300 text-5xl mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">No purchases yet</h3>
                    <p class="text-gray-600 mb-4">You haven't made any purchases yet.</p>
                    <a href="<?= SITE_URL ?>" class="inline-block bg-blue-500 hover:bg-blue-600 text-white py-2 px-6 rounded-lg">
                        Browse Movies
                    </a>
                </div>
            <?php else: ?>
                <div class="space-y-8">
                    <?php foreach ($orders as $order): ?>
                        <div class="border rounded-lg overflow-hidden">
                            <div class="bg-gray-50 p-4 border-b">
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                                    <div>
                                        <h3 class="font-semibold">Order #<?= $order['id'] ?></h3>
                                        <p class="text-gray-500 text-sm"><?= date('F j, Y', strtotime($order['created_at'])) ?></p>
                                    </div>
                                    <div class="mt-2 sm:mt-0">
                                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                                            <?= ucfirst($order['status']) ?>
                                        </span>
                                        <span class="ml-2 font-bold"><?= number_format($order['total_amount'], 2) ?> USD</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-4">
                                <h4 class="text-gray-500 text-sm mb-3">Purchased Items</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <?php foreach ($order['items'] as $item): ?>
                                        <div class="flex items-start space-x-4">
                                            <a href="<?= SITE_URL ?>/movie.php?id=<?= $item['movie_id'] ?>" class="flex-shrink-0">
                                                <img src="<?= SITE_URL ?>/uploads/posters/<?= $item['poster_path'] ?? 'default.jpg' ?>" 
                                                     alt="<?= sanitizeOutput($item['title']) ?>" 
                                                     class="w-16 h-24 object-cover rounded">
                                            </a>
                                            <div>
                                                <h4 class="font-semibold">
                                                    <a href="<?= SITE_URL ?>/movie.php?id=<?= $item['movie_id'] ?>" class="hover:text-blue-600">
                                                        <?= sanitizeOutput($item['title']) ?>
                                                    </a>
                                                </h4>
                                                <p class="text-gray-600 font-medium mt-1">$<?= number_format($item['price'], 2) ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../src/views/layouts/main.php';
?> 