<?php
error_reporting(0);
header('Content-Type: application/json');
require_once __DIR__ . '/../db_connect.php';

try {
    $db = getDBConnection();
    if (!$db) {
        throw new Exception('Database connection failed');
    }

    $course_id = $_GET['course_id'] ?? null;
    $group_id = $_GET['group_id'] ?? null;
    $professor_id = $_GET['professor_id'] ?? null;

    $query = "
        SELECT 
            s.id as student_id,
            s.matricule,
            s.fullname,
            COUNT(ar.id) as total_sessions,
            SUM(CASE WHEN ar.status = 'present' THEN 1 ELSE 0 END) as present_count,
            SUM(CASE WHEN ar.status = 'absent' THEN 1 ELSE 0 END) as absent_count,
            SUM(CASE WHEN ar.status = 'justified' THEN 1 ELSE 0 END) as justified_count,
            ROUND(SUM(CASE WHEN ar.status = 'present' THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(ar.id), 0), 2) as attendance_rate
        FROM students s
        INNER JOIN course_enrollments ce ON s.id = ce.student_id
        LEFT JOIN attendance_sessions asess ON ce.course_id = asess.course_id AND ce.group_id = asess.group_id
        LEFT JOIN attendance_records ar ON asess.id = ar.session_id AND s.id = ar.student_id
        WHERE 1=1
    ";
    
    $params = [];
    
    if ($course_id) {
        $query .= " AND ce.course_id = ?";
        $params[] = $course_id;
    }
    if ($group_id) {
        $query .= " AND ce.group_id = ?";
        $params[] = $group_id;
    }
    if ($professor_id) {
        $query .= " AND asess.professor_id = ?";
        $params[] = $professor_id;
    }
    
    $query .= " GROUP BY s.id, s.matricule, s.fullname ORDER BY s.fullname";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $statistics = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'data' => $statistics]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}






