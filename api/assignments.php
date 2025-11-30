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
            // Get all assignments or filter by professor/course/group
            $professor_id = $_GET['professor_id'] ?? null;
            $course_id = $_GET['course_id'] ?? null;
            $group_id = $_GET['group_id'] ?? null;
            
            $query = "
                SELECT ca.*, 
                       p.fullname as professor_name, p.professor_code,
                       c.course_code, c.course_name,
                       g.group_code, g.group_name
                FROM course_assignments ca
                INNER JOIN professors p ON ca.professor_id = p.id
                INNER JOIN courses c ON ca.course_id = c.id
                INNER JOIN groups g ON ca.group_id = g.id
                WHERE 1=1
            ";
            $params = [];
            
            if ($professor_id) {
                $query .= " AND ca.professor_id = ?";
                $params[] = $professor_id;
            }
            if ($course_id) {
                $query .= " AND ca.course_id = ?";
                $params[] = $course_id;
            }
            if ($group_id) {
                $query .= " AND ca.group_id = ?";
                $params[] = $group_id;
            }
            
            $query .= " ORDER BY c.course_code, g.group_code";
            
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $assignments = $stmt->fetchAll();
            
            echo json_encode(['success' => true, 'data' => $assignments]);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $professor_id = $data['professor_id'] ?? null;
            $course_id = $data['course_id'] ?? null;
            $group_id = $data['group_id'] ?? null;

            if (!$professor_id || !$course_id || !$group_id) {
                throw new Exception('Professor, course, and group are required');
            }

            // Check if assignment already exists
            $stmt = $db->prepare("SELECT id FROM course_assignments WHERE professor_id = ? AND course_id = ? AND group_id = ?");
            $stmt->execute([$professor_id, $course_id, $group_id]);
            if ($stmt->fetch()) {
                throw new Exception('This assignment already exists');
            }

            $stmt = $db->prepare("INSERT INTO course_assignments (professor_id, course_id, group_id) VALUES (?, ?, ?)");
            $stmt->execute([$professor_id, $course_id, $group_id]);
            
            echo json_encode(['success' => true, 'assignment_id' => $db->lastInsertId()]);
            break;

        case 'DELETE':
            $id = $_GET['id'] ?? 0;
            if (!$id) {
                throw new Exception('Assignment ID is required');
            }

            $stmt = $db->prepare("DELETE FROM course_assignments WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true]);
            break;

        default:
            throw new Exception('Method not allowed');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}






