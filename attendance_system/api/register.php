<?php
error_reporting(0);
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../db_connect.php';
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        exit;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $username = isset($data['username']) ? trim($data['username']) : '';
    $password = isset($data['password']) ? trim($data['password']) : '';
    $role = isset($data['role']) ? trim($data['role']) : '';
    $fullname = isset($data['fullname']) ? trim($data['fullname']) : '';
    $email = isset($data['email']) ? trim($data['email']) : '';
    
    // Additional fields based on role
    $matricule = isset($data['matricule']) ? trim($data['matricule']) : '';
    $professor_code = isset($data['professor_code']) ? trim($data['professor_code']) : '';
    $group_id = isset($data['group_id']) ? (int)$data['group_id'] : 0;
    
    // Validation
    $errors = [];
    if (empty($username)) $errors[] = 'Username is required';
    if (empty($password)) $errors[] = 'Password is required';
    if (empty($role)) $errors[] = 'Role is required';
    if (empty($fullname)) $errors[] = 'Full name is required';
    
    if ($role === 'student' && empty($matricule)) {
        $errors[] = 'Matricule is required for students';
    }
    if ($role === 'professor' && empty($professor_code)) {
        $errors[] = 'Professor code is required for professors';
    }
    
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
    }
    
    if (!in_array($role, ['student', 'professor'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid role. Only student and professor can be registered']);
        exit;
    }
    
    $conn = getDBConnection();
    
    if ($conn === null) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit;
    }
    
    try {
        $conn->beginTransaction();
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Create user account
        $stmt = $conn->prepare("
            INSERT INTO users (username, password, role, fullname, email)
            VALUES (:username, :password, :role, :fullname, :email)
        ");
        $stmt->execute([
            ':username' => $username,
            ':password' => $hashedPassword,
            ':role' => $role,
            ':fullname' => $fullname,
            ':email' => $email ?: null
        ]);
        
        $user_id = $conn->lastInsertId();
        
        // Create profile based on role
        if ($role === 'student') {
            // Check if matricule already exists
            $stmt = $conn->prepare("SELECT id FROM students WHERE matricule = :matricule");
            $stmt->execute([':matricule' => $matricule]);
            if ($stmt->fetch()) {
                throw new Exception('Matricule already exists');
            }
            
            $stmt = $conn->prepare("
                INSERT INTO students (user_id, matricule, fullname, email, group_id)
                VALUES (:user_id, :matricule, :fullname, :email, :group_id)
            ");
            $stmt->execute([
                ':user_id' => $user_id,
                ':matricule' => $matricule,
                ':fullname' => $fullname,
                ':email' => $email ?: null,
                ':group_id' => $group_id > 0 ? $group_id : null
            ]);
            
            $profile_id = $conn->lastInsertId();
        } else if ($role === 'professor') {
            // Check if professor code already exists
            $stmt = $conn->prepare("SELECT id FROM professors WHERE professor_code = :professor_code");
            $stmt->execute([':professor_code' => $professor_code]);
            if ($stmt->fetch()) {
                throw new Exception('Professor code already exists');
            }
            
            $stmt = $conn->prepare("
                INSERT INTO professors (user_id, professor_code, fullname, email)
                VALUES (:user_id, :professor_code, :fullname, :email)
            ");
            $stmt->execute([
                ':user_id' => $user_id,
                ':professor_code' => $professor_code,
                ':fullname' => $fullname,
                ':email' => $email ?: null
            ]);
            
            $profile_id = $conn->lastInsertId();
        }
        
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => ucfirst($role) . ' registered successfully',
            'user_id' => $user_id,
            'profile_id' => $profile_id
        ]);
        
    } catch (PDOException $e) {
        $conn->rollBack();
        if ($e->getCode() == 23000) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Username already exists']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } catch (Exception $e) {
        $conn->rollBack();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}








