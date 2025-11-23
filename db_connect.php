<?php
require_once 'config.php';

/**
 * Establishes a connection to the database
 * @return PDO|null Returns PDO connection object on success, null on failure
 */
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        $conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        
        // Log successful connection
        error_log("Database connection successful: " . date('Y-m-d H:i:s'));
        
        return $conn;
    } catch (PDOException $e) {
        // Log error to file
        $error_message = "Database connection failed: " . $e->getMessage() . " - " . date('Y-m-d H:i:s') . "\n";
        error_log($error_message, 3, 'db_errors.log');
        
        // Return null on failure
        return null;
    }
}

