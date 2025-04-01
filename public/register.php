<?php
require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/config/database.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('/');
}

$pageTitle = 'Register';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $errors[] = 'Invalid request';
    } else {
        // Get and sanitize input
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate input
        if (empty($username)) {
            $errors[] = 'Username is required';
        } elseif (strlen($username) < 3 || strlen($username) > 50) {
            $errors[] = 'Username must be between 3 and 50 characters';
        }
        
        if (empty($email)) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }
        
        if (empty($password)) {
            $errors[] = 'Password is required';
        } elseif (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }
        
        // Check if username or email already exists
        if (empty($errors)) {
            $existingUser = fetchOne(
                "SELECT id FROM users WHERE username = ? OR email = ?",
                [$username, $email]
            );
            
            if ($existingUser) {
                $errors[] = 'Username or email already exists';
            }
        }
        
        // Register user if no errors
        if (empty($errors)) {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            try {
                $userId = insert(
                    "INSERT INTO users (username, email, password) VALUES (?, ?, ?)",
                    [$username, $email, $hashedPassword]
                );
                
                if ($userId) {
                    // Set session variables
                    $_SESSION['user_id'] = $userId;
                    $_SESSION['username'] = $username;
                    
                    // Set success message
                    $_SESSION['flash_message'] = 'Registration successful! Welcome to MovieCart.';
                    $_SESSION['flash_type'] = 'success';
                    
                    // Redirect to homepage
                    redirect('/');
                } else {
                    $errors[] = 'Failed to create account. Please try again.';
                }
            } catch (Exception $e) {
                $errors[] = 'An error occurred. Please try again.';
                error_log("Registration error: " . $e->getMessage());
            }
        }
    }
}

ob_start();
?>

<div class="max-w-md mx-auto">
    <h1 class="text-3xl font-bold mb-6">Create an Account</h1>
    
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
        <form action="<?= SITE_URL ?>/register.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="mb-4">
                <label for="username" class="block text-gray-700 mb-2">Username</label>
                <input type="text" id="username" name="username" 
                       value="<?= isset($username) ? sanitizeOutput($username) : '' ?>"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       required>
            </div>
            
            <div class="mb-4">
                <label for="email" class="block text-gray-700 mb-2">Email</label>
                <input type="email" id="email" name="email" 
                       value="<?= isset($email) ? sanitizeOutput($email) : '' ?>"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       required>
            </div>
            
            <div class="mb-4">
                <label for="password" class="block text-gray-700 mb-2">Password</label>
                <input type="password" id="password" name="password" 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       required>
                <p class="text-sm text-gray-500 mt-1">Must be at least 8 characters</p>
            </div>
            
            <div class="mb-6">
                <label for="confirm_password" class="block text-gray-700 mb-2">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       required>
            </div>
            
            <button type="submit" 
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg">
                Register
            </button>
        </form>
        
        <div class="mt-4 text-center">
            <p class="text-gray-600">
                Already have an account? 
                <a href="<?= SITE_URL ?>/login.php" class="text-blue-500 hover:underline">Login</a>
            </p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../src/views/layouts/main.php';
?> 