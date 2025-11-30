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
        $professor_id = isset($_GET['professor_id']) ? (int)$_GET['professor_id'] : 0;
        $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
        $session_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($session_id > 0) {
            // Get single session with attendance records
            $stmt = $conn->prepare("
                SELECT s.*, c.course_code, c.course_name, g.group_code, g.group_name,
                       p.fullname as professor_name
                FROM attendance_sessions s
                JOIN courses c ON s.course_id = c.id
                JOIN groups g ON s.group_id = g.id
                JOIN professors p ON s.professor_id = p.id
                WHERE s.id = :id
            ");
            $stmt->execute([':id' => $session_id]);
            $session = $stmt->fetch();
            
            if ($session) {
                // Get attendance records
                $stmt = $conn->prepare("
                    SELECT ar.*, st.fullname, st.matricule
                    FROM attendance_records ar
                    JOIN students st ON ar.student_id = st.id
                    WHERE ar.session_id = :session_id
                    ORDER BY st.fullname
                ");
                $stmt->execute([':session_id' => $session_id]);
                $session['attendance_records'] = $stmt->fetchAll();
                
                echo json_encode(['success' => true, 'data' => $session]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Session not found']);
            }
        } else if ($professor_id > 0) {
            // Get sessions by professor, grouped by course
            $stmt = $conn->prepare("
                SELECT s.*, c.course_code, c.course_name, g.group_code, g.group_name,
                       COUNT(ar.id) as attendance_count
                FROM attendance_sessions s
                JOIN courses c ON s.course_id = c.id
                JOIN groups g ON s.group_id = g.id
                LEFT JOIN attendance_records ar ON s.id = ar.session_id
                WHERE s.professor_id = :professor_id
                GROUP BY s.id
                ORDER BY s.session_date DESC, c.course_code
            ");
            $stmt->execute([':professor_id' => $professor_id]);
            $sessions = $stmt->fetchAll();
            
            // Group by course
            $grouped = [];
            foreach ($sessions as $session) {
                $course_id = $session['course_id'];
                if (!isset($grouped[$course_id])) {
                    $grouped[$course_id] = [
                        'course_id' => $course_id,
                        'course_code' => $session['course_code'],
                        'course_name' => $session['course_name'],
                        'sessions' => []
                    ];
                }
                $grouped[$course_id]['sessions'][] = $session;
            }
            
            echo json_encode(['success' => true, 'data' => array_values($grouped)]);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'professor_id or id required']);
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        
        $course_id = isset($data['course_id']) ? (int)$data['course_id'] : 0;
        $group_id = isset($data['group_id']) ? (int)$data['group_id'] : 0;
        $professor_id = isset($data['professor_id']) ? (int)$data['professor_id'] : 0;
        $session_date = isset($data['session_date']) ? $data['session_date'] : date('Y-m-d');
        
        if ($course_id <= 0 || $group_id <= 0 || $professor_id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            break;
        }
        
        try {
            $stmt = $conn->prepare("
                INSERT INTO attendance_sessions (course_id, group_id, professor_id, session_date, status)
                VALUES (:course_id, :group_id, :professor_id, :session_date, 'open')
            ");
            $stmt->execute([
                ':course_id' => $course_id,
                ':group_id' => $group_id,
                ':professor_id' => $professor_id,
                ':session_date' => $session_date
            ]);
            
            $session_id = $conn->lastInsertId();
            echo json_encode(['success' => true, 'session_id' => $session_id]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;
        
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $session_id = isset($data['id']) ? (int)$data['id'] : 0;
        $status = isset($data['status']) ? $data['status'] : '';
        
        if ($session_id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid session ID']);
            break;
        }
        
        if ($status === 'closed') {
            $stmt = $conn->prepare("UPDATE attendance_sessions SET status = 'closed' WHERE id = :id");
            $stmt->execute([':id' => $session_id]);
            echo json_encode(['success' => true, 'message' => 'Session closed']);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid status']);
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

