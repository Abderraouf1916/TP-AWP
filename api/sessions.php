<?php
require_once '../db_connect.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

$conn = getDBConnection();

if ($conn === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

switch ($method) {
    case 'GET':
        // Get all sessions or a specific session
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $stmt = $conn->prepare("SELECT * FROM attendance_sessions WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $session = $stmt->fetch();
            
            if ($session) {
                echo json_encode(['success' => true, 'data' => $session]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Session not found']);
            }
        } else {
            $stmt = $conn->query("SELECT * FROM attendance_sessions ORDER BY date DESC, created_at DESC");
            $sessions = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $sessions]);
        }
        break;
        
    case 'POST':
        // Create a new session
        $data = json_decode(file_get_contents('php://input'), true);
        
        $course_id = isset($data['course_id']) ? trim($data['course_id']) : '';
        $group_id = isset($data['group_id']) ? trim($data['group_id']) : '';
        $professor_id = isset($data['professor_id']) ? trim($data['professor_id']) : '';
        $date = isset($data['date']) ? trim($data['date']) : date('Y-m-d');
        
        // Validation
        $errors = [];
        if (empty($course_id)) $errors[] = "Course ID is required.";
        if (empty($group_id)) $errors[] = "Group ID is required.";
        if (empty($professor_id)) $errors[] = "Professor ID is required.";
        if (empty($date)) $errors[] = "Date is required.";
        
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'errors' => $errors]);
            break;
        }
        
        try {
            $stmt = $conn->prepare("INSERT INTO attendance_sessions (course_id, group_id, date, opened_by, status) VALUES (:course_id, :group_id, :date, :opened_by, 'open')");
            $stmt->execute([
                ':course_id' => $course_id,
                ':group_id' => $group_id,
                ':date' => $date,
                ':opened_by' => $professor_id
            ]);
            
            $session_id = $conn->lastInsertId();
            echo json_encode(['success' => true, 'message' => 'Session created successfully', 'session_id' => $session_id]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;
        
    case 'PUT':
        // Update session (e.g., close it)
        $data = json_decode(file_get_contents('php://input'), true);
        
        $id = isset($data['id']) ? (int)$data['id'] : 0;
        $status = isset($data['status']) ? trim($data['status']) : '';
        
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid session ID']);
            break;
        }
        
        try {
            if ($status === 'closed') {
                $stmt = $conn->prepare("UPDATE attendance_sessions SET status = 'closed' WHERE id = :id");
                $stmt->execute([':id' => $id]);
                
                echo json_encode(['success' => true, 'message' => 'Session closed successfully']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid status']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        break;
}










