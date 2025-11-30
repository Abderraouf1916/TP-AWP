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
            $professor_id = $_GET['professor_id'] ?? null;
            $student_id = $_GET['student_id'] ?? null;
            
            if ($professor_id) {
                // Get courses for a professor
                $stmt = $db->prepare("
                    SELECT DISTINCT c.* 
                    FROM courses c
                    INNER JOIN course_assignments ca ON c.id = ca.course_id
                    WHERE ca.professor_id = ?
                    ORDER BY c.course_code
                ");
                $stmt->execute([$professor_id]);
                $courses = $stmt->fetchAll();
                echo json_encode(['success' => true, 'data' => $courses]);
            } elseif ($student_id) {
                // Get courses for a student
                $stmt = $db->prepare("
                    SELECT DISTINCT c.*, ce.group_id, g.group_code, g.group_name,
                           (SELECT p.fullname FROM professors p 
                            INNER JOIN course_assignments ca ON p.id = ca.professor_id
                            WHERE ca.course_id = c.id AND ca.group_id = ce.group_id LIMIT 1) as professor_name
                    FROM courses c
                    INNER JOIN course_enrollments ce ON c.id = ce.course_id
                    LEFT JOIN groups g ON ce.group_id = g.id
                    WHERE ce.student_id = ?
                    ORDER BY c.course_code
                ");
                $stmt->execute([$student_id]);
                $courses = $stmt->fetchAll();
                echo json_encode(['success' => true, 'data' => $courses]);
            } else {
                // Get all courses
                $stmt = $db->query("SELECT * FROM courses ORDER BY course_code");
                $courses = $stmt->fetchAll();
                echo json_encode(['success' => true, 'data' => $courses]);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $course_code = $data['course_code'] ?? '';
            $course_name = $data['course_name'] ?? '';
            $description = $data['description'] ?? '';

            if (empty($course_code) || empty($course_name)) {
                throw new Exception('Course code and name are required');
            }

            $stmt = $db->prepare("INSERT INTO courses (course_code, course_name, description) VALUES (?, ?, ?)");
            $stmt->execute([$course_code, $course_name, $description]);
            
            echo json_encode(['success' => true, 'course_id' => $db->lastInsertId()]);
            break;

        case 'DELETE':
            $id = $_GET['id'] ?? 0;
            $stmt = $db->prepare("DELETE FROM courses WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        default:
            throw new Exception('Method not allowed');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}






