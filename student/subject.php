<?php
require_once __DIR__ . '/../config/db.php';
session_start();

if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') { 
    header('Location: /attendance-management-system/auth/login.php'); 
    exit; 
}

$uid = $_SESSION['user']['id'];
$sid = (int)($_GET['subject_id'] ?? 0);

// Handle rating submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['session_id'], $_POST['stars'])) {
    $session_id = (int)$_POST['session_id'];
    $stars = (int)$_POST['stars'];

    $chkRate = $conn->prepare("SELECT id FROM attendance_system.ratings WHERE session_id=? AND student_id=?");
    $chkRate->bind_param("ii", $session_id, $uid);
    $chkRate->execute();
    $result = $chkRate->get_result();

    if ($result->num_rows > 0) {
        $upd = $conn->prepare("UPDATE attendance_system.ratings SET stars=? WHERE session_id=? AND student_id=?");
        $upd->bind_param("iii", $stars, $session_id, $uid);
        $upd->execute();
    } else {
        $ins = $conn->prepare("INSERT INTO attendance_system.ratings (session_id, student_id, stars) VALUES (?, ?, ?)");
        $ins->bind_param("iii", $session_id, $uid, $stars);
        $ins->execute();
    }
    header("Location: ".$_SERVER['REQUEST_URI']);
    exit;
}

// Check enrollment
$chk = $conn->prepare("SELECT s.name, s.code FROM attendance_system.subjects s 
    JOIN attendance_system.enrollments e ON e.subject_id=s.id AND e.student_id=? 
    WHERE s.id=?");
$chk->bind_param('ii', $uid, $sid);
$chk->execute();
$subject = $chk->get_result()->fetch_assoc();

if (!$subject) { http_response_code(403); exit('Not enrolled'); }

$sql = "SELECT cs.id AS session_id, cs.class_date, cs.topic, a.status, r.stars
FROM attendance_system.class_sessions cs
LEFT JOIN attendance_system.attendance a ON a.session_id=cs.id AND a.student_id=?
LEFT JOIN attendance_system.ratings r ON r.session_id=cs.id AND r.student_id=?
WHERE cs.subject_id=? ORDER BY cs.class_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('iii', $uid, $uid, $sid);
$stmt->execute();
$rows = $stmt->get_result();

include __DIR__ . '/../partials/header.php';
?>

<style>
body {
    background: linear-gradient(110deg, #a063e0ff, #eaf3ff, #5da8f2ff);
    min-height: 100vh;
    padding-top: 20px;
}

.subject-card {
    background: rgba(255, 255, 255, 0.55);
    backdrop-filter: blur(12px);
    border-radius: 18px;
    padding: 25px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.10);
    max-width: 900px;
    margin: auto;
}

.subject-header {
    text-align: center;
    font-weight: 700;
    font-size: 28px;
    color: #344;
    margin-bottom: 20px;
}

.session-card {
    background: rgba(240, 244, 255, 0.85);
    backdrop-filter: blur(6px);
    border-radius: 16px;
    padding: 18px 22px;
    margin-bottom: 14px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: 0.25s ease-in-out;
}

.session-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 22px rgba(0,0,0,0.12);
}

.session-info {
    display: flex;
    flex-direction: column;
}

.session-info span {
    font-size: 15px;
    margin-bottom: 2px;
}

.badge-status {
    padding: 6px 12px;
    font-weight: 500;
    border-radius: 10px;
    font-size: 0.85rem;
}

.star-btn {
    margin: 2px;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    padding: 0;
    line-height: 1;
    font-weight: bold;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}

.star-btn:hover {
    transform: scale(1.2);
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

.star-form {
    display: flex;
    align-items: center;
}
</style>

<div class="container">
    <div class="subject-card mt-5">
        <h3 class="subject-header"><?= htmlspecialchars($subject['code']) ?> — <?= htmlspecialchars($subject['name']) ?></h3>

        <?php while ($r = $rows->fetch_assoc()): ?>
            <div class="session-card d-flex justify-content-between align-items-center">
    <div class="session-info">
        <span><strong>Date:</strong> <?= htmlspecialchars(date('d M Y', strtotime($r['class_date']))) ?></span>
        <span><strong>Topic:</strong> <?= htmlspecialchars($r['topic'] ?: '-') ?></span>
    </div>

    <div class="d-flex align-items-center gap-3">
        <!-- Attendance Badge -->
        <?php if ($r['status'] === 'P'): ?>
            <span class="badge bg-success badge-status">Present</span>
        <?php elseif ($r['status'] === 'A'): ?>
            <span class="badge bg-danger badge-status">Absent</span>
        <?php else: ?>
            <span class="badge bg-secondary badge-status">Not Marked</span>
        <?php endif; ?>

        <!-- Rating Stars -->
        <form method="POST" class="d-flex">
            <?php for ($s = 1; $s <= 5; $s++): ?>
                <button type="submit" name="stars" value="<?= $s ?>" 
                        class="btn btn-sm star-btn <?= ($r['stars'] == $s ? 'btn-primary' : 'btn-outline-primary') ?>">
                    <?= $s ?>★
                </button>
            <?php endfor; ?>
            <input type="hidden" name="session_id" value="<?= $r['session_id'] ?>">
        </form>
    </div>
</div>

        <?php endwhile; ?>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
