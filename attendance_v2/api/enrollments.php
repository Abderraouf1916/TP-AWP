<?php
error_reporting(0);
header('Content-Type: application/json');
require_once __DIR__ . '/../db_connect.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = getDBConnection();
    if (!$db) {
        throw new Exception('Database connection failed');
    }

    switch ($method) {
        case 'GET':
            $course_id = $_GET['course_id'] ?? null;
            $group_id = $_GET['group_id'] ?? null;
            
            if ($course_id) {
                // Get all students enrolled in a course (from all groups)
                // If group_id is provided, filter to show only that group (optional)
                if ($group_id) {
                    // Show students from specific group enrolled in course
                    $stmt = $db->prepare("
                        SELECT DISTINCT s.*, ce.group_id, g.group_code, g.group_name
                        FROM students s
                        INNER JOIN course_enrollments ce ON s.id = ce.student_id
                        INNER JOIN groups g ON ce.group_id = g.id
                        WHERE ce.course_id = ? AND ce.group_id = ?
                        ORDER BY s.fullname
                    ");
                    $stmt->execute([$course_id, $group_id]);
                } else {
                    // Show ALL students enrolled in course (from all groups)
                    $stmt = $db->prepare("
                        SELECT DISTINCT s.*, ce.group_id, g.group_code, g.group_name
                        FROM students s
                        INNER JOIN course_enrollments ce ON s.id = ce.student_id
                        INNER JOIN groups g ON ce.group_id = g.id
                        WHERE ce.course_id = ?
                        ORDER BY g.group_code, s.fullname
                    ");
                    $stmt->execute([$course_id]);
                }
                
                $students = $stmt->fetchAll();
                echo json_encode(['success' => true, 'data' => $students]);
            } else {
                throw new Exception('Course ID is required');
            }
            break;

        default:
            throw new Exception('Method not allowed');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

