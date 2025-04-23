<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../utils/functions.php';

function loginUser($username, $password) {
    session_start();
    
    if (empty($username) || empty($password)) {
        return ['success' => false, 'message' => 'Username and password are required'];
    }
    
    $conn = connectDB();
    $username = sanitizeInput($username);
    
    $stmt = $conn->prepare("SELECT id, username, password, email FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        $conn->close();
        return ['success' => false, 'message' => 'Invalid username or password'];
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        
        return [
            'success' => true, 
            'message' => 'Login successful', 
            'user_id' => $user['id'],
            'username' => $user['username']
        ];
    } else {
        return ['success' => false, 'message' => 'Invalid username or password'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $result = loginUser($username, $password);
    
    if ($result['success']) {
        header('Location: /index.php');
        exit;
    } else {
        $error = $result['message'];
    }
}
?> 