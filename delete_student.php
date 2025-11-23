<?php
require_once 'db_connect.php';

// Get student ID from URL
$student_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($student_id <= 0) {
    header('Location: list_students.php');
    exit;
}

$conn = getDBConnection();

if ($conn !== null) {
    try {
        // Check if student exists
        $stmt = $conn->prepare("SELECT * FROM students WHERE id = :id");
        $stmt->execute([':id' => $student_id]);
        $student = $stmt->fetch();
        
        if ($student) {
            // Delete student
            $stmt = $conn->prepare("DELETE FROM students WHERE id = :id");
            $stmt->execute([':id' => $student_id]);
            
            header('Location: list_students.php?deleted=1');
            exit;
        } else {
            header('Location: list_students.php?error=notfound');
            exit;
        }
    } catch (PDOException $e) {
        header('Location: list_students.php?error=' . urlencode($e->getMessage()));
        exit;
    }
} else {
    header('Location: list_students.php?error=connection');
    exit;
}

