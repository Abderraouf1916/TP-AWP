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
        $student_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
        
        if ($student_id > 0) {
            $stmt = $conn->prepare("
                SELECT s.*, g.group_code, g.group_name
                FROM students s
                LEFT JOIN groups g ON s.group_id = g.id
                WHERE s.id = :id
            ");
            $stmt->execute([':id' => $student_id]);
            $student = $stmt->fetch();
            
            if ($student) {
                // Get enrolled courses
                $stmt = $conn->prepare("
                    SELECT c.*, ce.group_id, g.group_code
                    FROM course_enrollments ce
                    JOIN courses c ON ce.course_id = c.id
                    JOIN groups g ON ce.group_id = g.id
                    WHERE ce.student_id = :student_id
                ");
                $stmt->execute([':student_id' => $student_id]);
                $student['courses'] = $stmt->fetchAll();
                
                echo json_encode(['success' => true, 'data' => $student]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Student not found']);
            }
        } else if ($course_id > 0) {
            // Get students enrolled in a course
            $group_id = isset($_GET['group_id']) ? (int)$_GET['group_id'] : 0;
            
            if ($group_id > 0) {
                $stmt = $conn->prepare("
                    SELECT s.*, g.group_code
                    FROM students s
                    JOIN course_enrollments ce ON s.id = ce.student_id
                    JOIN groups g ON s.group_id = g.id
                    WHERE ce.course_id = :course_id AND ce.group_id = :group_id
                    ORDER BY s.fullname
                ");
                $stmt->execute([
                    ':course_id' => $course_id,
                    ':group_id' => $group_id
                ]);
            } else {
                $stmt = $conn->prepare("
                    SELECT s.*, g.group_code
                    FROM students s
                    JOIN course_enrollments ce ON s.id = ce.student_id
                    JOIN groups g ON s.group_id = g.id
                    WHERE ce.course_id = :course_id
                    ORDER BY s.fullname
                ");
                $stmt->execute([':course_id' => $course_id]);
            }
            
            $students = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $students]);
        } else {
            // Get all students
            $stmt = $conn->query("
                SELECT s.*, g.group_code, g.group_name
                FROM students s
                LEFT JOIN groups g ON s.group_id = g.id
                ORDER BY s.fullname
            ");
            $students = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $students]);
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        
        $matricule = isset($data['matricule']) ? trim($data['matricule']) : '';
        $fullname = isset($data['fullname']) ? trim($data['fullname']) : '';
        $email = isset($data['email']) ? trim($data['email']) : '';
        $group_id = isset($data['group_id']) ? (int)$data['group_id'] : 0;
        
        if (empty($matricule) || empty($fullname)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Matricule and fullname are required']);
            break;
        }
        
        try {
            $stmt = $conn->prepare("
                INSERT INTO students (matricule, fullname, email, group_id)
                VALUES (:matricule, :fullname, :email, :group_id)
            ");
            $stmt->execute([
                ':matricule' => $matricule,
                ':fullname' => $fullname,
                ':email' => $email ?: null,
                ':group_id' => $group_id > 0 ? $group_id : null
            ]);
            
            $student_id = $conn->lastInsertId();
            echo json_encode(['success' => true, 'student_id' => $student_id]);
        } catch (PDOException $e) {
            http_response_code(400);
            if ($e->getCode() == 23000) {
                echo json_encode(['success' => false, 'error' => 'Matricule already exists']);
            } else {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        }
        break;
        
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $student_id = isset($data['id']) ? (int)$data['id'] : 0;
        
        if ($student_id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid student ID']);
            break;
        }
        
        $fullname = isset($data['fullname']) ? trim($data['fullname']) : '';
        $email = isset($data['email']) ? trim($data['email']) : '';
        $group_id = isset($data['group_id']) ? (int)$data['group_id'] : 0;
        
        try {
            $stmt = $conn->prepare("
                UPDATE students 
                SET fullname = :fullname, email = :email, group_id = :group_id
                WHERE id = :id
            ");
            $stmt->execute([
                ':fullname' => $fullname,
                ':email' => $email ?: null,
                ':group_id' => $group_id > 0 ? $group_id : null,
                ':id' => $student_id
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Student updated']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;
        
    case 'DELETE':
        $student_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($student_id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid student ID']);
            break;
        }
        
        try {
            $stmt = $conn->prepare("DELETE FROM students WHERE id = :id");
            $stmt->execute([':id' => $student_id]);
            
            echo json_encode(['success' => true, 'message' => 'Student deleted']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
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

