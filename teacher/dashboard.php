<?php
require_once __DIR__ . '/../config/db.php';
session_start();

if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') { 
    header('Location: /attendance-management-system/auth/login.php'); 
    exit; 
}

$tid = $_SESSION['user']['id'];

$stmt = $conn->prepare("SELECT id, code, name FROM attendance_system.subjects WHERE teacher_id=? ORDER BY code");
$stmt->bind_param('i', $tid);
$stmt->execute();
$subjects = $stmt->get_result();

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
        max-width: 850px;
        margin: auto;
    }

    .subject-header {
        text-align: center;
        font-weight: 700;
        font-size: 28px;
        color: #344;
        margin-bottom: 20px;
    }

    .list-group-item {
        border-radius: 14px !important;
        margin-bottom: 12px;
        padding: 12px;
        background: rgba(240, 244, 255, 0.85);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(180, 190, 255, 0.4);
        transition: 0.25s ease-in-out;
        font-size: 16px;
        font-weight: 500;
    }

    .list-group-item:hover {
        transform: translateY(-3px);
        background: rgba(220, 235, 255, 0.95);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }

    .badge-custom {
        background: linear-gradient(135deg, #4b70f5, #6a5acd);
        padding: 8px 14px;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
    }
</style>

<div class="container">

    <div class="subject-card mt-4">
        <h3 class="subject-header">Your Subjects</h3>

        <div class="list-group">
            <?php while ($s = $subjects->fetch_assoc()): ?>
                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                   href="/attendance-management-system/teacher/sessions.php?subject_id=<?= (int)$s['id'] ?>">

                    <span>
                        <strong class="text-primary"><?= htmlspecialchars($s['code']) ?></strong> — 
                        <?= htmlspecialchars($s['name']) ?>
                    </span>

                    <span class="badge-custom text-white">Manage</span>
                </a>
            <?php endwhile; ?>
        </div>
    </div>

</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
