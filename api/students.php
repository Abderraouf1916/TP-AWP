<?php
require_once '../db_connect.php';

header('Content-Type: application/json');

// Get the HTTP method
$method = $_SERVER['REQUEST_METHOD'];

$conn = getDBConnection();

if ($conn === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

switch ($method) {
    case 'GET':
        // Get all students or a specific student
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $stmt = $conn->prepare("SELECT * FROM students WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $student = $stmt->fetch();
            
            if ($student) {
                echo json_encode(['success' => true, 'data' => $student]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Student not found']);
            }
        } else {
            $stmt = $conn->query("SELECT * FROM students ORDER BY created_at DESC");
            $students = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $students]);
        }
        break;
        
    case 'POST':
        // Create a new student
        $data = json_decode(file_get_contents('php://input'), true);
        
        $fullname = isset($data['fullname']) ? trim($data['fullname']) : '';
        $matricule = isset($data['matricule']) ? trim($data['matricule']) : '';
        $group_id = isset($data['group_id']) ? trim($data['group_id']) : '';
        
        // Validation
        $errors = [];
        if (empty($fullname)) $errors[] = "Full name is required.";
        if (empty($matricule)) $errors[] = "Matricule is required.";
        if (empty($group_id)) $errors[] = "Group ID is required.";
        
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'errors' => $errors]);
            break;
        }
        
        try {
            $stmt = $conn->prepare("INSERT INTO students (fullname, matricule, group_id) VALUES (:fullname, :matricule, :group_id)");
            $stmt->execute([
                ':fullname' => $fullname,
                ':matricule' => $matricule,
                ':group_id' => $group_id
            ]);
            
            $student_id = $conn->lastInsertId();
            echo json_encode(['success' => true, 'message' => 'Student added successfully', 'id' => $student_id]);
        } catch (PDOException $e) {
            http_response_code(400);
            if ($e->getCode() == 23000) {
                echo json_encode(['success' => false, 'error' => 'A student with this matricule already exists']);
            } else {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        }
        break;
        
    case 'PUT':
        // Update a student
        $data = json_decode(file_get_contents('php://input'), true);
        
        $id = isset($data['id']) ? (int)$data['id'] : 0;
        $fullname = isset($data['fullname']) ? trim($data['fullname']) : '';
        $matricule = isset($data['matricule']) ? trim($data['matricule']) : '';
        $group_id = isset($data['group_id']) ? trim($data['group_id']) : '';
        
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid student ID']);
            break;
        }
        
        // Validation
        $errors = [];
        if (empty($fullname)) $errors[] = "Full name is required.";
        if (empty($matricule)) $errors[] = "Matricule is required.";
        if (empty($group_id)) $errors[] = "Group ID is required.";
        
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'errors' => $errors]);
            break;
        }
        
        try {
            $stmt = $conn->prepare("UPDATE students SET fullname = :fullname, matricule = :matricule, group_id = :group_id WHERE id = :id");
            $stmt->execute([
                ':fullname' => $fullname,
                ':matricule' => $matricule,
                ':group_id' => $group_id,
                ':id' => $id
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Student updated successfully']);
        } catch (PDOException $e) {
            http_response_code(400);
            if ($e->getCode() == 23000) {
                echo json_encode(['success' => false, 'error' => 'A student with this matricule already exists']);
            } else {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        }
        break;
        
    case 'DELETE':
        // Delete a student
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Student ID is required']);
            break;
        }
        
        $id = (int)$_GET['id'];
        
        try {
            $stmt = $conn->prepare("DELETE FROM students WHERE id = :id");
            $stmt->execute([':id' => $id]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Student deleted successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Student not found']);
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










