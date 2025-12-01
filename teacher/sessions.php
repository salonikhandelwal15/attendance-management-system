<?php
require_once __DIR__ . '/../config/db.php';
session_start();

if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') { 
    header('Location: /attendance-management-system/auth/login.php'); 
    exit; 
}

$tid = $_SESSION['user']['id'];
$sid = (int)($_GET['subject_id'] ?? 0);

$chk = $conn->prepare("SELECT name, code FROM attendance_system.subjects WHERE id=? AND teacher_id=?");
$chk->bind_param('ii', $sid, $tid);
$chk->execute();
$subject = $chk->get_result()->fetch_assoc();

if (!$subject) { http_response_code(403); exit('Forbidden'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['class_date'] ?? '';
    $topic = trim($_POST['topic'] ?? '');
    $stmt = $conn->prepare("INSERT INTO attendance_system.class_sessions (subject_id, class_date, topic, created_by) VALUES (?,?,?,?)");
    $stmt->bind_param('issi', $sid, $date, $topic, $tid);
    $stmt->execute();
}

$sessions = $conn->prepare("SELECT id, class_date, topic FROM attendance_system.class_sessions WHERE subject_id=? ORDER BY class_date DESC");
$sessions->bind_param('i', $sid);
$sessions->execute();
$list = $sessions->get_result();

$students = $conn->prepare("SELECT u.id, u.roll_or_emp, u.name 
    FROM attendance_system.enrollments e 
    JOIN attendance_system.users u ON u.id=e.student_id 
    WHERE e.subject_id=? 
    ORDER BY u.roll_or_emp");
$students->bind_param('i', $sid);
$students->execute();
$roster = $students->get_result();

include __DIR__ . '/../partials/header.php';
?>

<style>
    body {
        background: linear-gradient(110deg, #a063e0ff, #eaf3ff, #5da8f2ff);
        min-height: 100vh;
    }

    .main-card {
        background: rgba(255, 255, 255, 0.58);
        backdrop-filter: blur(14px);
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        max-width: 950px;
        margin: 45px auto;
    }

    .sub-header {
        font-weight: 700;
        color: #2d2d4d;
        border-left: 5px solid #4b70f5;
        padding-left: 12px;
        margin-bottom: 25px;
    }

    .form-label {
        font-weight: 600;
        color: #333;
    }

    .form-control {
        border-radius: 12px;
        padding: 10px 15px;
        border: 1px solid #cbd5e1;
    }

    .btn-create {
        background: linear-gradient(135deg, #4b70f5, #6a5acd);
        border: none;
        padding: 10px 18px;
        border-radius: 12px;
        font-weight: 600;
    }

    .btn-create:hover {
        background: linear-gradient(135deg, #3c5de8, #5a4ac8);
    }

    .session-item {
        border-radius: 14px !important;
        padding: 18px;
        margin-bottom: 12px;
        background: rgba(240, 244, 255, 0.85);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(190, 200, 255, 0.4);
        transition: 0.25s ease-in-out;
        font-size: 16px;
    }

    .session-item:hover {
        transform: translateX(6px);
        background: rgba(220, 235, 255, 0.95);
        box-shadow: 0 8px 20px rgba(0,0,0,0.10);
    }

    .disabled-session {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
        background: rgba(200,200,200,0.35) !important;
        border: 1px solid rgba(160,160,160,0.4);
    }

    .student-box {
        background: rgba(255,255,255,0.55);
        border-radius: 16px;
        padding: 20px;
        margin-top: 10px;
        border: 1px solid rgba(220,220,255,0.4);
    }

    .student-box ul {
        padding-left: 18px;
    }

    .student-box li {
        padding: 6px 0;
        font-size: 15px;
        font-weight: 500;
    }
</style>

<div class="container">

    <div class="main-card">

        <!-- Subject Header -->
        <h3 class="sub-header">
            <?= htmlspecialchars($subject['code']) ?> — <?= htmlspecialchars($subject['name']) ?>
        </h3>

        <!-- Create Session Form -->
        <form method="post" class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label">Class Date</label>
                <input type="date" name="class_date" class="form-control" required>
            </div>

            <div class="col-md-5">
                <label class="form-label">Topic (optional)</label>
                <input type="text" name="topic" class="form-control" placeholder="Topic covered">
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-create w-100 text-white">Create Session</button>
            </div>
        </form>

        <!-- Session List -->
        <h5 class="fw-bold mb-3">Previous Sessions</h5>

        <div class="list-group mb-4">

            <?php 
            $today = date('Y-m-d');

            while ($cs = $list->fetch_assoc()):
                $classDate = $cs['class_date'];
                $isFuture = ($classDate > $today);
            ?>

                <?php if ($isFuture): ?>
                    <div class="list-group-item session-item disabled-session">
                        <strong><?= htmlspecialchars($classDate) ?></strong>
                        — <?= htmlspecialchars($cs['topic'] ?: 'No topic added') ?>
                        <div class="text-danger small mt-1">
                            Attendance can be marked on or after <?= htmlspecialchars($classDate) ?>
                        </div>
                    </div>

                <?php else: ?>
                    <a class="list-group-item session-item"
                       href="/attendance-management-system/teacher/mark.php?session_id=<?= (int)$cs['id'] ?>">
                        <strong><?= htmlspecialchars($classDate) ?></strong>
                        — <?= htmlspecialchars($cs['topic'] ?: 'No topic added') ?>
                    </a>
                <?php endif; ?>

            <?php endwhile; ?>

        </div>

        <!-- Student Roster -->
        <h5 class="fw-bold mb-2">Enrolled Students (<?= $roster->num_rows ?>)</h5>

        <div class="student-box">
            <ul>
                <?php while ($st = $roster->fetch_assoc()): ?>
                    <li>
                        <strong><?= htmlspecialchars($st['roll_or_emp']) ?></strong>
                        — <?= htmlspecialchars($st['name']) ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>

    </div>

</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
