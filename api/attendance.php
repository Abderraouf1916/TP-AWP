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
            $session_id = $_GET['session_id'] ?? null;
            $student_id = $_GET['student_id'] ?? null;
            
            if ($session_id) {
                // Get attendance records for a session
                $stmt = $db->prepare("
                    SELECT ar.*, s.matricule, s.fullname as student_name
                    FROM attendance_records ar
                    INNER JOIN students s ON ar.student_id = s.id
                    WHERE ar.session_id = ?
                    ORDER BY s.fullname
                ");
                $stmt->execute([$session_id]);
                $records = $stmt->fetchAll();
                echo json_encode(['success' => true, 'data' => $records]);
            } elseif ($student_id) {
                // Get attendance records for a student
                $stmt = $db->prepare("
                    SELECT ar.*, sas.session_date, c.course_code, c.course_name, g.group_code
                    FROM attendance_records ar
                    INNER JOIN attendance_sessions sas ON ar.session_id = sas.id
                    INNER JOIN courses c ON sas.course_id = c.id
                    INNER JOIN groups g ON sas.group_id = g.id
                    WHERE ar.student_id = ?
                    ORDER BY sas.session_date DESC
                ");
                $stmt->execute([$student_id]);
                $records = $stmt->fetchAll();
                echo json_encode(['success' => true, 'data' => $records]);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $session_id = $data['session_id'] ?? null;
            $records = $data['records'] ?? [];

            if (!$session_id || empty($records)) {
                throw new Exception('Session ID and attendance records are required');
            }

            $db->beginTransaction();
            
            try {
                // Delete existing records for this session
                $stmt = $db->prepare("DELETE FROM attendance_records WHERE session_id = ?");
                $stmt->execute([$session_id]);

                // Insert new records
                $stmt = $db->prepare("
                    INSERT INTO attendance_records (session_id, student_id, status, justification)
                    VALUES (?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE status = VALUES(status), justification = VALUES(justification)
                ");
                
                foreach ($records as $record) {
                    $stmt->execute([
                        $session_id,
                        $record['student_id'],
                        $record['status'],
                        $record['justification'] ?? null
                    ]);
                }
                
                $db->commit();
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            break;

        case 'PUT':
            // Update single attendance record (for justification)
            $data = json_decode(file_get_contents('php://input'), true);
            $session_id = $data['session_id'] ?? null;
            $student_id = $data['student_id'] ?? null;
            $justification = $data['justification'] ?? null;
            $justification_pdf = $data['justification_pdf'] ?? null;

            if (!$session_id || !$student_id) {
                throw new Exception('Session ID and student ID are required');
            }

            $stmt = $db->prepare("
                UPDATE attendance_records 
                SET justification = ?, justification_pdf = ?, status = CASE WHEN ? IS NOT NULL THEN 'justified' ELSE status END
                WHERE session_id = ? AND student_id = ?
            ");
            $stmt->execute([$justification, $justification_pdf, $justification, $session_id, $student_id]);
            
            echo json_encode(['success' => true]);
            break;

        default:
            throw new Exception('Method not allowed');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}






