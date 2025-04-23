<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../utils/functions.php';

function registerUser($username, $email, $password, $confirmPassword) {
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        return ['success' => false, 'message' => 'All fields are required'];
    }
    
    if ($password !== $confirmPassword) {
        return ['success' => false, 'message' => 'Passwords do not match'];
    }
    
    if (strlen($password) < 8) {
        return ['success' => false, 'message' => 'Password must be at least 8 characters long'];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Invalid email format'];
    }
    
    $conn = connectDB();
    
    $username = sanitizeInput($username);
    $email = sanitizeInput($email);
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $stmt->close();
        $conn->close();
        return ['success' => false, 'message' => 'Username or email already exists'];
    }
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $username, $email, $hashedPassword);
    
    if ($stmt->execute()) {
        $userId = $conn->insert_id;
        $stmt->close();
        $conn->close();
        
        session_start();
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        
        return ['success' => true, 'message' => 'Registration successful', 'user_id' => $userId];
    } else {
        $stmt->close();
        $conn->close();
        return ['success' => false, 'message' => 'Registration failed: ' . $conn->error];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    $result = registerUser($username, $email, $password, $confirmPassword);
    
    if ($result['success']) {
        header('Location: /index.php');
        exit;
    } else {
        $error = $result['message'];
    }
}
?> 