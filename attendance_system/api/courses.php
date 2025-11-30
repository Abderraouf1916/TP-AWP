<?php
error_reporting(0); // Disable error display, we'll handle errors ourselves
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
        $student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
        
        if ($student_id > 0) {
            // Get courses enrolled by student
            $stmt = $conn->prepare("
                SELECT c.*, ce.group_id, g.group_code, g.group_name
                FROM courses c
                JOIN course_enrollments ce ON c.id = ce.course_id
                JOIN groups g ON ce.group_id = g.id
                WHERE ce.student_id = :student_id
                ORDER BY c.course_code
            ");
            $stmt->execute([':student_id' => $student_id]);
            $courses = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $courses]);
        } else {
            // Get all courses
            $stmt = $conn->query("SELECT * FROM courses ORDER BY course_code");
            $courses = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $courses]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}

