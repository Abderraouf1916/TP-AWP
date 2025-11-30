<?php
error_reporting(0);
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../db_connect.php';
    
    $method = $_SERVER['REQUEST_METHOD'];
    $conn = getDBConnection();

    if ($conn === null) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit;
    }

    switch ($method) {
    case 'GET':
        $stmt = $conn->query("SELECT * FROM groups ORDER BY group_code");
        $groups = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $groups]);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}

