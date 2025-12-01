<?php
require_once __DIR__ . '/../config/db.php';
session_start();
if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') { header('Location: /attendance-management-system/auth/login.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /attendance-management-system/student/dashboard.php'); exit; }

$uid = $_SESSION['user']['id'];
$session_id = (int)($_POST['session_id'] ?? 0);
$stars = (int)($_POST['stars'] ?? 0);

if ($stars < 1 || $stars > 5) { exit('Bad rating'); }

$q = $conn->prepare("SELECT cs.subject_id FROM attendance_system.class_sessions cs JOIN attendance_system.enrollments e ON e.subject_id=cs.subject_id AND e.student_id=? WHERE cs.id=?");
$q->bind_param('ii', $uid, $session_id);
$q->execute();
$ok = $q->get_result()->fetch_assoc();

if (!$ok) { http_response_code(403); exit('Forbidden'); }

$stmt = $conn->prepare("INSERT INTO attendance_system.ratings (session_id, student_id, stars) VALUES (?,?,?) ON DUPLICATE KEY UPDATE stars=VALUES(stars), rated_at=CURRENT_TIMESTAMP");
$stmt->bind_param('iii', $session_id, $uid, $stars);
$stmt->execute();

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/attendance-management-system/student/dashboard.php'));
exit;
