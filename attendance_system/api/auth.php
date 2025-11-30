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
    
    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Username and password are required']);
        exit;
    }
    
    $conn = getDBConnection();
    
    if ($conn === null) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit;
    }
    
    // Find user by username and role
    $stmt = $conn->prepare("
        SELECT u.*, 
               CASE 
                   WHEN u.role = 'student' THEN s.id
                   WHEN u.role = 'professor' THEN p.id
                   ELSE NULL
               END as profile_id
        FROM users u
        LEFT JOIN students s ON u.id = s.user_id AND u.role = 'student'
        LEFT JOIN professors p ON u.id = p.user_id AND u.role = 'professor'
        WHERE u.username = :username AND u.role = :role
    ");
    $stmt->execute([
        ':username' => $username,
        ':role' => $role
    ]);
    
    $user = $stmt->fetch();
    
    if (!$user) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Invalid username or password']);
        exit;
    }
    
    // Verify password
    // Accept both hashed password and plain 'password' for testing
    $passwordValid = false;
    if (password_verify($password, $user['password'])) {
        $passwordValid = true;
    } else if ($password === 'password' && $user['password'] === '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi') {
        // Allow plain 'password' for test users
        $passwordValid = true;
    }
    
    if ($passwordValid) {
        // Return user data
        echo json_encode([
            'success' => true,
            'data' => [
                'user_id' => $user['profile_id'] ?: $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
                'fullname' => $user['fullname']
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Invalid username or password']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}

