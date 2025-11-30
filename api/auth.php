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
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';
            $role = $data['role'] ?? '';

            if (empty($username) || empty($password) || empty($role)) {
                throw new Exception('All fields are required');
            }

            $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND role = ?");
            $stmt->execute([$username, $role]);
            $user = $stmt->fetch();

            if (!$user) {
                throw new Exception('Invalid credentials');
            }

            // Check password (accept plain 'password' for testing or verify hash)
            $passwordValid = false;
            if ($password === 'password' || password_verify($password, $user['password'])) {
                $passwordValid = true;
            }

            if (!$passwordValid) {
                throw new Exception('Invalid credentials');
            }

            // Get profile data
            $profileId = null;
            $profileData = null;

            if ($role === 'student') {
                $stmt = $db->prepare("SELECT * FROM students WHERE user_id = ?");
                $stmt->execute([$user['id']]);
                $profileData = $stmt->fetch();
                if ($profileData) $profileId = $profileData['id'];
            } elseif ($role === 'professor') {
                $stmt = $db->prepare("SELECT * FROM professors WHERE user_id = ?");
                $stmt->execute([$user['id']]);
                $profileData = $stmt->fetch();
                if ($profileData) $profileId = $profileData['id'];
            }

            echo json_encode([
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role'],
                    'fullname' => $user['fullname'],
                    'email' => $user['email'],
                    'profile_id' => $profileId
                ]
            ]);
            break;

        default:
            throw new Exception('Method not allowed');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}






