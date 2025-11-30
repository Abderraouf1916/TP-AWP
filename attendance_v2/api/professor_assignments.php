<?php
error_reporting(0);
header('Content-Type: application/json');
require_once __DIR__ . '/../db_connect.php';

try {
    $db = getDBConnection();
    if (!$db) {
        throw new Exception('Database connection failed');
    }

    $professor_id = $_GET['professor_id'] ?? null;
    $course_id = $_GET['course_id'] ?? null;

    if (!$professor_id) {
        throw new Exception('Professor ID is required');
    }

    $query = "
        SELECT DISTINCT g.*, c.id as course_id, c.course_code, c.course_name
        FROM groups g
        INNER JOIN course_assignments ca ON g.id = ca.group_id
        INNER JOIN courses c ON ca.course_id = c.id
        WHERE ca.professor_id = ?
    ";
    $params = [$professor_id];

    if ($course_id) {
        $query .= " AND ca.course_id = ?";
        $params[] = $course_id;
    }

    $query .= " ORDER BY c.course_code, g.group_code";

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $assignments = $stmt->fetchAll();

    echo json_encode(['success' => true, 'data' => $assignments]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}






