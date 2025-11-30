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
                    SELECT s.*, g.group_code, g.group_name, u.username 
                    FROM students s 
                    LEFT JOIN groups g ON s.group_id = g.id
                    LEFT JOIN users u ON s.user_id = u.id
                    WHERE s.id = ?
                ");
                $stmt->execute([$id]);
                $student = $stmt->fetch();
                echo json_encode(['success' => true, 'data' => $student]);
            } else {
                $stmt = $db->query("
                    SELECT s.*, g.group_code, g.group_name, u.username 
                    FROM students s 
                    LEFT JOIN groups g ON s.group_id = g.id
                    LEFT JOIN users u ON s.user_id = u.id
                    ORDER BY s.created_at DESC
                ");
                $students = $stmt->fetchAll();
                echo json_encode(['success' => true, 'data' => $students]);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $matricule = $data['matricule'] ?? '';
            $fullname = $data['fullname'] ?? '';
            $email = $data['email'] ?? '';
            $group_id = $data['group_id'] ?? null;
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';

            if (empty($matricule) || empty($fullname) || empty($username) || empty($password)) {
                throw new Exception('All required fields must be provided');
            }

            $db->beginTransaction();

            try {
                // Create user
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (username, password, role, fullname, email) VALUES (?, ?, 'student', ?, ?)");
                $stmt->execute([$username, $hashedPassword, $fullname, $email]);
                $userId = $db->lastInsertId();

                // Create student
                $stmt = $db->prepare("INSERT INTO students (user_id, matricule, fullname, email, group_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$userId, $matricule, $fullname, $email, $group_id]);
                $studentId = $db->lastInsertId();

                $db->commit();
                echo json_encode(['success' => true, 'student_id' => $studentId]);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id'] ?? 0;
            $matricule = $data['matricule'] ?? '';
            $fullname = $data['fullname'] ?? '';
            $email = $data['email'] ?? '';
            $group_id = $data['group_id'] ?? null;
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';

            if ($id <= 0) {
                throw new Exception('Invalid student ID');
            }

            if (empty($matricule) || empty($fullname)) {
                throw new Exception('Matricule and full name are required');
            }

            $db->beginTransaction();

            try {
                // Get user_id
                $stmt = $db->prepare("SELECT user_id FROM students WHERE id = ?");
                $stmt->execute([$id]);
                $student = $stmt->fetch();
                
                if (!$student) {
                    throw new Exception('Student not found');
                }

                // Update student
                $stmt = $db->prepare("UPDATE students SET matricule = ?, fullname = ?, email = ?, group_id = ? WHERE id = ?");
                $stmt->execute([$matricule, $fullname, $email ?: null, $group_id, $id]);

                // Update user
                if ($student['user_id']) {
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
                        $params[] = $student['user_id'];
                        $query = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE id = ?";
                        $stmt = $db->prepare($query);
                        $stmt->execute($params);
                    }
                }

                $db->commit();
                echo json_encode(['success' => true, 'message' => 'Student updated successfully']);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            break;

        case 'DELETE':
            $id = $_GET['id'] ?? 0;
            $db->beginTransaction();
            
            try {
                // Get user_id
                $stmt = $db->prepare("SELECT user_id FROM students WHERE id = ?");
                $stmt->execute([$id]);
                $student = $stmt->fetch();
                
                if ($student) {
                    // Delete student (user will be deleted via CASCADE or separately)
                    $stmt = $db->prepare("DELETE FROM students WHERE id = ?");
                    $stmt->execute([$id]);
                    
                    if ($student['user_id']) {
                        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
                        $stmt->execute([$student['user_id']]);
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






