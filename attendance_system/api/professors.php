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
            $professor_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            if ($professor_id > 0) {
                $stmt = $conn->prepare("
                    SELECT p.*, u.username, u.email as user_email
                    FROM professors p
                    JOIN users u ON p.user_id = u.id
                    WHERE p.id = :id
                ");
                $stmt->execute([':id' => $professor_id]);
                $professor = $stmt->fetch();
                
                if ($professor) {
                    echo json_encode(['success' => true, 'data' => $professor]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'error' => 'Professor not found']);
                }
            } else {
                $stmt = $conn->query("
                    SELECT p.*, u.username, u.email as user_email
                    FROM professors p
                    JOIN users u ON p.user_id = u.id
                    ORDER BY p.fullname
                ");
                $professors = $stmt->fetchAll();
                echo json_encode(['success' => true, 'data' => $professors]);
            }
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $professor_id = isset($data['id']) ? (int)$data['id'] : 0;
            
            if ($professor_id <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid professor ID']);
                break;
            }
            
            $fullname = isset($data['fullname']) ? trim($data['fullname']) : '';
            $email = isset($data['email']) ? trim($data['email']) : '';
            
            if (empty($fullname)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Full name is required']);
                break;
            }
            
            try {
                // Get user_id first
                $stmt = $conn->prepare("SELECT user_id FROM professors WHERE id = :id");
                $stmt->execute([':id' => $professor_id]);
                $prof = $stmt->fetch();
                
                if (!$prof) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'error' => 'Professor not found']);
                    break;
                }
                
                // Update professor
                $stmt = $conn->prepare("
                    UPDATE professors 
                    SET fullname = :fullname, email = :email
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':fullname' => $fullname,
                    ':email' => $email ?: null,
                    ':id' => $professor_id
                ]);
                
                // Update user
                $stmt = $conn->prepare("
                    UPDATE users 
                    SET fullname = :fullname, email = :email
                    WHERE id = :user_id
                ");
                $stmt->execute([
                    ':fullname' => $fullname,
                    ':email' => $email ?: null,
                    ':user_id' => $prof['user_id']
                ]);
                
                echo json_encode(['success' => true, 'message' => 'Professor updated successfully']);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            break;
            
        case 'DELETE':
            $professor_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            if ($professor_id <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid professor ID']);
                break;
            }
            
            try {
                // Get user_id first
                $stmt = $conn->prepare("SELECT user_id FROM professors WHERE id = :id");
                $stmt->execute([':id' => $professor_id]);
                $prof = $stmt->fetch();
                
                if (!$prof) {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'error' => 'Professor not found']);
                    break;
                }
                
                // Delete professor (user will be deleted via CASCADE or we can delete manually)
                $stmt = $conn->prepare("DELETE FROM professors WHERE id = :id");
                $stmt->execute([':id' => $professor_id]);
                
                // Delete user if no foreign key constraint
                $stmt = $conn->prepare("DELETE FROM users WHERE id = :user_id");
                $stmt->execute([':user_id' => $prof['user_id']]);
                
                echo json_encode(['success' => true, 'message' => 'Professor deleted successfully']);
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








