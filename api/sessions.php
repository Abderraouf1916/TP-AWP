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
            $course_id = $_GET['course_id'] ?? null;
            $group_id = $_GET['group_id'] ?? null;
            $session_id = $_GET['session_id'] ?? null;
            
            if ($session_id) {
                // Get single session with details
                $stmt = $db->prepare("
                    SELECT s.*, c.course_code, c.course_name, g.group_code, g.group_name, p.fullname as professor_name
                    FROM attendance_sessions s
                    INNER JOIN courses c ON s.course_id = c.id
                    INNER JOIN groups g ON s.group_id = g.id
                    INNER JOIN professors p ON s.professor_id = p.id
                    WHERE s.id = ?
                ");
                $stmt->execute([$session_id]);
                $session = $stmt->fetch();
                echo json_encode(['success' => true, 'data' => $session]);
            } else {
                // Build query based on filters
                $query = "
                    SELECT s.*, c.course_code, c.course_name, g.group_code, g.group_name, p.fullname as professor_name
                    FROM attendance_sessions s
                    INNER JOIN courses c ON s.course_id = c.id
                    INNER JOIN groups g ON s.group_id = g.id
                    INNER JOIN professors p ON s.professor_id = p.id
                    WHERE 1=1
                ";
                $params = [];
                
                if ($professor_id) {
                    $query .= " AND s.professor_id = ?";
                    $params[] = $professor_id;
                }
                if ($course_id) {
                    $query .= " AND s.course_id = ?";
                    $params[] = $course_id;
                }
                if ($group_id) {
                    $query .= " AND s.group_id = ?";
                    $params[] = $group_id;
                }
                
                $query .= " ORDER BY s.session_date DESC, s.created_at DESC";
                
                $stmt = $db->prepare($query);
                $stmt->execute($params);
                $sessions = $stmt->fetchAll();
                echo json_encode(['success' => true, 'data' => $sessions]);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $course_id = $data['course_id'] ?? null;
            $group_id = $data['group_id'] ?? null;
            $professor_id = $data['professor_id'] ?? null;
            $session_date = $data['session_date'] ?? date('Y-m-d');

            if (!$course_id || !$group_id || !$professor_id) {
                throw new Exception('Course, group, and professor are required');
            }

            // Verify that the professor is assigned to this course and group
            $stmt = $db->prepare("
                SELECT id FROM course_assignments 
                WHERE professor_id = ? AND course_id = ? AND group_id = ?
            ");
            $stmt->execute([$professor_id, $course_id, $group_id]);
            if (!$stmt->fetch()) {
                throw new Exception('You are not assigned to this course and group combination');
            }

            $stmt = $db->prepare("
                INSERT INTO attendance_sessions (course_id, group_id, professor_id, session_date, status)
                VALUES (?, ?, ?, ?, 'open')
            ");
            $stmt->execute([$course_id, $group_id, $professor_id, $session_date]);
            
            echo json_encode(['success' => true, 'session_id' => $db->lastInsertId()]);
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $session_id = $data['session_id'] ?? null;
            $status = $data['status'] ?? null;

            if (!$session_id || !$status) {
                throw new Exception('Session ID and status are required');
            }

            $stmt = $db->prepare("UPDATE attendance_sessions SET status = ? WHERE id = ?");
            $stmt->execute([$status, $session_id]);
            
            echo json_encode(['success' => true]);
            break;

        default:
            throw new Exception('Method not allowed');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

