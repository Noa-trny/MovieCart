<?php
// Database connection configuration
require_once __DIR__ . '/config.php';

// Function to get database connection
function getDb() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Log error and display friendly message
            error_log("Database Connection Error: " . $e->getMessage());
            die("Could not connect to the database. Please try again later.");
        }
    }
    
    return $pdo;
}

// Helper function to execute queries with prepared statements
function query($sql, $params = []) {
    $db = getDb();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

// Get a single row from a query
function fetchOne($sql, $params = []) {
    $stmt = query($sql, $params);
    return $stmt->fetch();
}

// Get all rows from a query
function fetchAll($sql, $params = []) {
    $stmt = query($sql, $params);
    return $stmt->fetchAll();
}

// Insert data and return last insert ID
function insert($sql, $params = []) {
    $db = getDb();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $db->lastInsertId();
}

// Begin a transaction
function beginTransaction() {
    getDb()->beginTransaction();
}

// Commit a transaction
function commitTransaction() {
    getDb()->commit();
}

// Rollback a transaction
function rollbackTransaction() {
    getDb()->rollBack();
} 