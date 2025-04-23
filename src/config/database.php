<?php
require_once __DIR__ . '/config.php';

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
            error_log("Database Connection Error: " . $e->getMessage());
            die("Could not connect to the database. Please try again later.");
        }
    }
    
    return $pdo;
}

function query($sql, $params = []) {
    $db = getDb();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function fetchOne($sql, $params = []) {
    $stmt = query($sql, $params);
    return $stmt->fetch();
}

function fetchAll($sql, $params = []) {
    $stmt = query($sql, $params);
    return $stmt->fetchAll();
}

function insert($sql, $params = []) {
    $db = getDb();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $db->lastInsertId();
}

function beginTransaction() {
    getDb()->beginTransaction();
}

function commitTransaction() {
    getDb()->commit();
}

function rollbackTransaction() {
    getDb()->rollBack();
} 