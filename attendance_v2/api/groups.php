<?php
error_reporting(0);
header('Content-Type: application/json');
require_once __DIR__ . '/../db_connect.php';

try {
    $db = getDBConnection();
    if (!$db) {
        throw new Exception('Database connection failed');
    }

    $stmt = $db->query("SELECT * FROM groups ORDER BY group_code");
    $groups = $stmt->fetchAll();
    echo json_encode(['success' => true, 'data' => $groups]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}






