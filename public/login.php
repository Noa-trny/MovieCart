<?php
require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/config/database.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('/');
}

$pageTitle = 'Login';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $errors[] = 'Invalid request';
    } else {
        // Get login credentials
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        
        // Validate input
        if (empty($email)) {
            $errors[] = 'Email is required';
        }
        
        if (empty($password)) {
            $errors[] = 'Password is required';
        }
        
        // If no errors, attempt login
        if (empty($errors)) {
            // Get user by email
            $user = fetchOne(
                "SELECT id, username, email, password, created_at FROM users WHERE email = ?",
                [$email]
            );
            
            // Verify password if user exists
            if ($user && password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // Success message
                $_SESSION['flash_message'] = 'Login successful. Welcome back!';
                $_SESSION['flash_type'] = 'success';
                
                // Redirect to home page or intended destination
                $redirect = $_SESSION['redirect_after_login'] ?? '/';
                unset($_SESSION['redirect_after_login']);
                redirect($redirect);
            } else {
                $errors[] = 'Invalid email or password';
            }
        }
    }
}

ob_start();
?>

<div class="max-w-md mx-auto">
    <h1 class="text-3xl font-bold mb-6">Login to Your Account</h1>
    
    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                    <li><?= sanitizeOutput($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="bg-white p-6 rounded-lg shadow-md">
        <form action="<?= SITE_URL ?>/login.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="mb-4">
                <label for="email" class="block text-gray-700 mb-2">Email</label>
                <input type="email" id="email" name="email" 
                       value="<?= isset($email) ? sanitizeOutput($email) : '' ?>"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       required>
            </div>
            
            <div class="mb-6">
                <label for="password" class="block text-gray-700 mb-2">Password</label>
                <input type="password" id="password" name="password" 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       required>
            </div>
            
            <button type="submit" 
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg">
                Login
            </button>
        </form>
        
        <div class="mt-4 text-center">
            <p class="text-gray-600">
                Don't have an account? 
                <a href="<?= SITE_URL ?>/register.php" class="text-blue-500 hover:underline">Register</a>
            </p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../src/views/layouts/main.php';
?> 