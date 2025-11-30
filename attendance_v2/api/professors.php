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
            $id = $_GET['id'] ?? null;
            
            if ($id) {
                $stmt = $db->prepare("
                    SELECT p.*, u.username 
                    FROM professors p 
                    LEFT JOIN users u ON p.user_id = u.id
                    WHERE p.id = ?
                ");
                $stmt->execute([$id]);
                $professor = $stmt->fetch();
                echo json_encode(['success' => true, 'data' => $professor]);
            } else {
                $stmt = $db->query("
                    SELECT p.*, u.username 
                    FROM professors p 
                    LEFT JOIN users u ON p.user_id = u.id
                    ORDER BY p.created_at DESC
                ");
                $professors = $stmt->fetchAll();
                echo json_encode(['success' => true, 'data' => $professors]);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $professor_code = $data['professor_code'] ?? '';
            $fullname = $data['fullname'] ?? '';
            $email = $data['email'] ?? '';
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';

            if (empty($professor_code) || empty($fullname) || empty($username) || empty($password)) {
                throw new Exception('All required fields must be provided');
            }

            $db->beginTransaction();

            try {
                // Create user
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (username, password, role, fullname, email) VALUES (?, ?, 'professor', ?, ?)");
                $stmt->execute([$username, $hashedPassword, $fullname, $email]);
                $userId = $db->lastInsertId();

                // Create professor
                $stmt = $db->prepare("INSERT INTO professors (user_id, professor_code, fullname, email) VALUES (?, ?, ?, ?)");
                $stmt->execute([$userId, $professor_code, $fullname, $email]);
                $professorId = $db->lastInsertId();

                $db->commit();
                echo json_encode(['success' => true, 'professor_id' => $professorId]);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id'] ?? 0;
            $professor_code = $data['professor_code'] ?? '';
            $fullname = $data['fullname'] ?? '';
            $email = $data['email'] ?? '';
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';

            if ($id <= 0) {
                throw new Exception('Invalid professor ID');
            }

            if (empty($professor_code) || empty($fullname)) {
                throw new Exception('Professor code and full name are required');
            }

            $db->beginTransaction();

            try {
                // Get user_id
                $stmt = $db->prepare("SELECT user_id FROM professors WHERE id = ?");
                $stmt->execute([$id]);
                $professor = $stmt->fetch();
                
                if (!$professor) {
                    throw new Exception('Professor not found');
                }

                // Update professor
                $stmt = $db->prepare("UPDATE professors SET professor_code = ?, fullname = ?, email = ? WHERE id = ?");
                $stmt->execute([$professor_code, $fullname, $email ?: null, $id]);

                // Update user
                if ($professor['user_id']) {
                    $updateFields = [];
                    $params = [];
                    
                    if (!empty($username)) {
                        $updateFields[] = "username = ?";
                        $params[] = $username;
                    }
                    
                    if (!empty($fullname)) {
                        $updateFields[] = "fullname = ?";
                        $params[] = $fullname;
                    }
                    
                    if (isset($email)) {
                        $updateFields[] = "email = ?";
                        $params[] = $email ?: null;
                    }
                    
                    if (!empty($password)) {
                        $updateFields[] = "password = ?";
                        $params[] = password_hash($password, PASSWORD_DEFAULT);
                    }
                    
                    if (!empty($updateFields)) {
                        $params[] = $professor['user_id'];
                        $query = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE id = ?";
                        $stmt = $db->prepare($query);
                        $stmt->execute($params);
                    }
                }

                $db->commit();
                echo json_encode(['success' => true, 'message' => 'Professor updated successfully']);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            break;

        case 'DELETE':
            $id = $_GET['id'] ?? 0;
            $db->beginTransaction();
            
            try {
                $stmt = $db->prepare("SELECT user_id FROM professors WHERE id = ?");
                $stmt->execute([$id]);
                $professor = $stmt->fetch();
                
                if ($professor) {
                    $stmt = $db->prepare("DELETE FROM professors WHERE id = ?");
                    $stmt->execute([$id]);
                    
                    if ($professor['user_id']) {
                        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
                        $stmt->execute([$professor['user_id']]);
                    }
                }
                
                $db->commit();
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            break;

        default:
            throw new Exception('Method not allowed');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}






