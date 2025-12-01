<?php
require_once __DIR__ . '/../config/db.php';
session_start();

if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') { 
    header('Location: /attendance-management-system/auth/login.php'); 
    exit; 
}

$tid = $_SESSION['user']['id'];
$session_id = (int)($_GET['session_id'] ?? 0);

// Fetch class session
$stmt = $conn->prepare("
    SELECT cs.id, cs.class_date, cs.subject_id, s.name, s.code 
    FROM attendance_system.class_sessions cs 
    JOIN attendance_system.subjects s ON s.id=cs.subject_id 
    WHERE cs.id=? AND s.teacher_id=?
");
$stmt->bind_param('ii', $session_id, $tid);
$stmt->execute();
$session = $stmt->get_result()->fetch_assoc();

if (!$session) { 
    http_response_code(403); 
    exit('Forbidden'); 
}

// Fetch student list 
$stmt2 = $conn->prepare("
    SELECT u.id, u.roll_or_emp, u.name, a.status 
    FROM attendance_system.enrollments e 
    JOIN attendance_system.users u ON u.id=e.student_id 
    LEFT JOIN attendance_system.attendance a 
        ON a.session_id=? AND a.student_id=u.id 
    WHERE e.subject_id=? 
    ORDER BY u.roll_or_emp
");
$stmt2->bind_param('ii', $session_id, $session['subject_id']);
$stmt2->execute();
$list = $stmt2->get_result();

include __DIR__ . '/../partials/header.php';

// Read success popup message
$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);
?>

<style>
    body {
        background: linear-gradient(110deg, #a063e0ff, #eaf3ff, #5da8f2ff);
        min-height: 100vh;
    }

    .attendance-card {
        background: rgba(255, 255, 255, 0.60);
        backdrop-filter: blur(14px);
        border-radius: 20px;
        padding: 40px 45px;
        box-shadow: 0px 10px 35px rgba(0,0,0,0.15);
        max-width: 1050px;
        margin: 45px auto;
    }

    .page-title {
        font-weight: 700;
        color: #2d2d4d;
        margin-bottom: 25px;
        border-left: 6px solid #4b70f5;
        padding-left: 12px;
    }

    table {
        background: rgba(255,255,255,0.75);
        border-radius: 15px !important;
        overflow: hidden;
    }

    table thead {
        background: #4b70f5;
        color: white;
    }

    table tbody tr {
        transition: 0.25s;
    }

    table tbody tr:hover {
        background: rgba(150,180,255,0.25);
        transform: scale(1.01);
    }

    .form-select {
        border-radius: 10px;
    }

    .btn-save {
        background: linear-gradient(135deg, #4b70f5, #6a5acd);
        border: none;
        padding: 10px 18px;
        border-radius: 12px;
        font-weight: 600;
    }

    .btn-save:hover {
        background: linear-gradient(135deg, #3c59e8, #5a49cd);
    }

    .btn-back {
        border-radius: 12px;
        font-weight: 600;
    }

    .table td, .table th {
        padding: 0.75rem 1.5rem !important;
    }
</style>

<div class="container">

    <!-- Success popup -->
    <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="attendance-card">

        <h3 class="page-title">
            Mark Attendance — <?= htmlspecialchars($session['code']) ?>
            <span class="text-muted" style="font-size: 18px;">
                (<?= htmlspecialchars($session['class_date']) ?>)
            </span>
        </h3>

        <form action="/attendance-management-system/teacher/save_attendance.php" method="post">
            <input type="hidden" name="session_id" value="<?= (int)$session_id ?>">

            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 90px;">Roll</th>
                        <th>Name</th>
                        <th style="width: 160px;">Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($st = $list->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($st['roll_or_emp']) ?></strong></td>
                            <td><?= htmlspecialchars($st['name']) ?></td>
                            <td>
                                <select name="status[<?= (int)$st['id'] ?>]" class="form-select">
                                    <option value="P" <?= ($st['status'] === 'P') ? 'selected' : '' ?>>Present</option>
                                    <option value="A" <?= ($st['status'] === 'A') ? 'selected' : '' ?>>Absent</option>
                                </select>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <div class="mt-4 d-flex gap-2">
                <button class="btn btn-save text-white">Save Attendance</button>
                <a class="btn btn-outline-secondary btn-back"
                   href="/attendance-management-system/teacher/sessions.php?subject_id=<?= (int)$session['subject_id'] ?>">
                    Back
                </a>
            </div>

        </form>

    </div>

</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
