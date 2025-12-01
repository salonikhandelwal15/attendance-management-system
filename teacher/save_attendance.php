<?php
require_once __DIR__ . '/../config/db.php';
session_start();

if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') { 
    header('Location: /attendance-management-system/auth/login.php'); 
    exit; 
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
    header('Location: /attendance-management-system/teacher/dashboard.php'); 
    exit; 
}

$tid = $_SESSION['user']['id'];
$session_id = (int)($_POST['session_id'] ?? 0);

$own = $conn->prepare("
    SELECT s.teacher_id, cs.subject_id 
    FROM attendance_system.class_sessions cs 
    JOIN attendance_system.subjects s ON s.id=cs.subject_id 
    WHERE cs.id=?
");
$own->bind_param('i', $session_id);
$own->execute();
$o = $own->get_result()->fetch_assoc();

if (!$o || (int)$o['teacher_id'] !== (int)$tid) { 
    http_response_code(403); 
    exit('Forbidden'); 
}

$statuses = $_POST['status'] ?? [];
$conn->begin_transaction();

try {
    $stmt = $conn->prepare("
        INSERT INTO attendance_system.attendance 
            (session_id, student_id, status, marked_by) 
        VALUES (?,?,?,?)
        ON DUPLICATE KEY UPDATE 
            status=VALUES(status),
            marked_at=CURRENT_TIMESTAMP,
            marked_by=VALUES(marked_by)
    ");

    foreach ($statuses as $student_id => $status) {
        $student_id = (int)$student_id;
        $status = ($status === 'A') ? 'A' : 'P';
        $stmt->bind_param('iisi', $session_id, $student_id, $status, $tid);
        $stmt->execute();
    }

    $conn->commit();

    $_SESSION['success'] = "Attendance saved successfully!";

} catch (Exception $e) {
    $conn->rollback();
    exit('Error: ' . $e->getMessage());
}

// Redirect back to the mark page
header('Location: /attendance-management-system/teacher/mark.php?session_id=' . $session_id);
exit;
