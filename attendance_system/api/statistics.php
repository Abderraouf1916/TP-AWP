<?php
error_reporting(0);
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../db_connect.php';
    
    $conn = getDBConnection();

if ($conn === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Get overall statistics
$stats = [];

    // Total students
    $stmt = $conn->query("SELECT COUNT(*) as count FROM students");
    $stats['total_students'] = $stmt->fetch()['count'];

    // Total courses
    $stmt = $conn->query("SELECT COUNT(*) as count FROM courses");
    $stats['total_courses'] = $stmt->fetch()['count'];

    // Total sessions
    $stmt = $conn->query("SELECT COUNT(*) as count FROM attendance_sessions");
    $stats['total_sessions'] = $stmt->fetch()['count'];

    // Attendance rate
    $stmt = $conn->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present,
            SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent,
            SUM(CASE WHEN status = 'justified' THEN 1 ELSE 0 END) as justified
        FROM attendance_records
    ");
    $attendance = $stmt->fetch();
    $stats['attendance'] = $attendance;
    if ($attendance['total'] > 0) {
        $stats['attendance_rate'] = round(($attendance['present'] / $attendance['total']) * 100, 2);
    } else {
        $stats['attendance_rate'] = 0;
    }

    // Attendance by course
    $stmt = $conn->query("
        SELECT c.course_code, c.course_name,
               COUNT(ar.id) as total,
               SUM(CASE WHEN ar.status = 'present' THEN 1 ELSE 0 END) as present
        FROM courses c
        LEFT JOIN attendance_sessions s ON c.id = s.course_id
        LEFT JOIN attendance_records ar ON s.id = ar.session_id
        GROUP BY c.id
        ORDER BY c.course_code
    ");
    $stats['by_course'] = $stmt->fetchAll();

    // Attendance by group
    $stmt = $conn->query("
        SELECT g.group_code, g.group_name,
               COUNT(ar.id) as total,
               SUM(CASE WHEN ar.status = 'present' THEN 1 ELSE 0 END) as present
        FROM groups g
        LEFT JOIN attendance_sessions s ON g.id = s.group_id
        LEFT JOIN attendance_records ar ON s.id = ar.session_id
        GROUP BY g.id
        ORDER BY g.group_code
    ");
    $stats['by_group'] = $stmt->fetchAll();

echo json_encode(['success' => true, 'data' => $stats]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}

