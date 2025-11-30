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
    case 'POST':
        // Mark attendance for a session
        $data = json_decode(file_get_contents('php://input'), true);
        
        $session_id = isset($data['session_id']) ? (int)$data['session_id'] : 0;
        $attendance_data = isset($data['attendance']) ? $data['attendance'] : [];
        
        if ($session_id <= 0 || empty($attendance_data)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing required data']);
            break;
        }
        
        try {
            $conn->beginTransaction();
            
            // Delete existing records for this session
            $stmt = $conn->prepare("DELETE FROM attendance_records WHERE session_id = :session_id");
            $stmt->execute([':session_id' => $session_id]);
            
            // Insert new records
            $stmt = $conn->prepare("
                INSERT INTO attendance_records (session_id, student_id, status, justification)
                VALUES (:session_id, :student_id, :status, :justification)
            ");
            
            foreach ($attendance_data as $record) {
                $stmt->execute([
                    ':session_id' => $session_id,
                    ':student_id' => (int)$record['student_id'],
                    ':status' => $record['status'],
                    ':justification' => isset($record['justification']) ? $record['justification'] : null
                ]);
            }
            
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Attendance marked successfully']);
        } catch (PDOException $e) {
            $conn->rollBack();
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;
        
    case 'PUT':
        // Update justification for a student's attendance
        $data = json_decode(file_get_contents('php://input'), true);
        
        $record_id = isset($data['record_id']) ? (int)$data['record_id'] : 0;
        $justification = isset($data['justification']) ? trim($data['justification']) : '';
        
        if ($record_id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid record ID']);
            break;
        }
        
        try {
            $stmt = $conn->prepare("
                UPDATE attendance_records 
                SET justification = :justification, status = 'justified'
                WHERE id = :id
            ");
            $stmt->execute([
                ':justification' => $justification,
                ':id' => $record_id
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Justification submitted']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;
        
    case 'GET':
        // Get attendance summary
        $student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
        $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
        $group_id = isset($_GET['group_id']) ? (int)$_GET['group_id'] : 0;
        
        if ($student_id > 0 && $course_id > 0) {
            // Get student attendance for a course
            $stmt = $conn->prepare("
                SELECT ar.*, s.session_date, s.status as session_status,
                       c.course_code, c.course_name, g.group_code
                FROM attendance_records ar
                JOIN attendance_sessions s ON ar.session_id = s.id
                JOIN courses c ON s.course_id = c.id
                JOIN groups g ON s.group_id = g.id
                WHERE ar.student_id = :student_id AND s.course_id = :course_id
                ORDER BY s.session_date DESC
            ");
            $stmt->execute([
                ':student_id' => $student_id,
                ':course_id' => $course_id
            ]);
            $records = $stmt->fetchAll();
            
            echo json_encode(['success' => true, 'data' => $records]);
        } else if ($course_id > 0 && $group_id > 0) {
            // Get attendance summary for a course/group
            $stmt = $conn->prepare("
                SELECT s.id as session_id, s.session_date,
                       COUNT(ar.id) as total_students,
                       SUM(CASE WHEN ar.status = 'present' THEN 1 ELSE 0 END) as present_count,
                       SUM(CASE WHEN ar.status = 'absent' THEN 1 ELSE 0 END) as absent_count,
                       SUM(CASE WHEN ar.status = 'justified' THEN 1 ELSE 0 END) as justified_count
                FROM attendance_sessions s
                LEFT JOIN attendance_records ar ON s.id = ar.session_id
                WHERE s.course_id = :course_id AND s.group_id = :group_id
                GROUP BY s.id
                ORDER BY s.session_date DESC
            ");
            $stmt->execute([
                ':course_id' => $course_id,
                ':group_id' => $group_id
            ]);
            $summary = $stmt->fetchAll();
            
            echo json_encode(['success' => true, 'data' => $summary]);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
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

