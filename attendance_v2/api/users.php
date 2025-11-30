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
                $stmt = $db->prepare("SELECT id, username, role, fullname, email, created_at FROM users WHERE id = ?");
                $stmt->execute([$id]);
                $user = $stmt->fetch();
                echo json_encode(['success' => true, 'data' => $user]);
            } else {
                $stmt = $db->query("SELECT id, username, role, fullname, email, created_at FROM users ORDER BY created_at DESC");
                $users = $stmt->fetchAll();
                echo json_encode(['success' => true, 'data' => $users]);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';
            $role = $data['role'] ?? '';
            $fullname = $data['fullname'] ?? '';
            $email = $data['email'] ?? '';

            if (empty($username) || empty($password) || empty($role) || empty($fullname)) {
                throw new Exception('All required fields must be provided');
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $db->prepare("INSERT INTO users (username, password, role, fullname, email) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$username, $hashedPassword, $role, $fullname, $email]);
            
            $userId = $db->lastInsertId();
            echo json_encode(['success' => true, 'user_id' => $userId]);
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id'] ?? 0;
            $username = $data['username'] ?? '';
            $fullname = $data['fullname'] ?? '';
            $email = $data['email'] ?? '';
            $role = $data['role'] ?? '';
            $password = $data['password'] ?? '';

            if ($id <= 0) {
                throw new Exception('Invalid user ID');
            }

            if (empty($username) || empty($fullname)) {
                throw new Exception('Username and full name are required');
            }

            try {
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
                
                if (!empty($role)) {
                    $updateFields[] = "role = ?";
                    $params[] = $role;
                }
                
                if (!empty($password)) {
                    $updateFields[] = "password = ?";
                    $params[] = password_hash($password, PASSWORD_DEFAULT);
                }
                
                if (!empty($updateFields)) {
                    $params[] = $id;
                    $query = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE id = ?";
                    $stmt = $db->prepare($query);
                    $stmt->execute($params);
                }

                echo json_encode(['success' => true, 'message' => 'User updated successfully']);
            } catch (Exception $e) {
                throw $e;
            }
            break;

        case 'DELETE':
            $id = $_GET['id'] ?? 0;
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        default:
            throw new Exception('Method not allowed');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}






