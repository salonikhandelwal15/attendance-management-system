<?php
require_once __DIR__ . '/../config/db.php';
session_start();
if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { 
    header('Location: /attendance-management-system/auth/login.php'); 
    exit; 
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $teacher = (int)($_POST['teacher_id'] ?? 0);
    $s = $conn->prepare("INSERT INTO attendance_system.subjects (code, name, teacher_id) VALUES (?,?,?)");
    $s->bind_param('ssi', $code, $name, $teacher);
    $s->execute();
    $msg = $s->error ? 'Error: ' . $s->error : 'Added';
}

$tq = $conn->query("SELECT id, roll_or_emp, name FROM attendance_system.users WHERE role='teacher' ORDER BY roll_or_emp");
include __DIR__ . '/../partials/header.php';
?>

<style>
    body {
        background: linear-gradient(110deg, #a063e0ff, #e0f2fe, #5da8f2ff);
        background-size: cover;
        min-height: 100vh;
    }

    .page-card {
        background: rgba(224, 226, 252, 0.47);
        border-radius: 20px;
        padding: 30px 35px;
        margin-top: 30px;
        box-shadow: 0 10px 35px rgba(0,0,0,0.15);
    }

    .form-control, .form-select {
        border-radius: 10px;
        padding: 10px 14px;
    }

    .btn-primary {
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 600;
        background: #2563eb;
        border: none;
        transition: 0.25s;
    }

    .btn-primary:hover {
        background: #1d4ed8;
        box-shadow: 0 4px 10px rgba(0,0,0,0.25);
    }

    .subject-item {
        background: #f9fafb;
        border: none;
        border-radius: 12px !important;
        padding: 15px 18px;
        margin-bottom: 8px;
        transition: 0.25s;
        font-size: 16px;
    }

    .subject-item:hover {
        background: #eef2ff;
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .teacher-text {
        color: #6366f1;
        font-weight: 500;
    }
</style>

<div class="container py-4">
    <div class="page-card">

        <h3 class="fw-bold mb-4 text-primary">Manage Subjects</h3>

        <?php if ($msg): ?>
            <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <form method="post" class="row g-3 mb-4">
            <div class="col-md-2">
                <input name="code" class="form-control" placeholder="Code" required>
            </div>

            <div class="col-md-5">
                <input name="name" class="form-control" placeholder="Subject Name" required>
            </div>
            
            <!-- TEACHER FETCHED BY ID -->
            <div class="col-md-3">
                <select name="teacher_id" class="form-select">
                    <?php while ($tr = $tq->fetch_assoc()): ?>
                        <option value="<?= (int)$tr['id'] ?>">
                            <?= htmlspecialchars($tr['roll_or_emp'] . ' - ' . $tr['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary w-100">Add</button>
            </div>
        </form>

        <h5 class="mt-4 mb-3 fw-semibold text-secondary">Available Subjects</h5>

        <ul class="list-group">
            <?php
            $sq = $conn->query("
                SELECT s.id, s.code, s.name, u.name AS teacher
                FROM attendance_system.subjects s 
                JOIN attendance_system.users u 
                ON u.id = s.teacher_id 
                ORDER BY s.code
            ");

            while ($row = $sq->fetch_assoc()):
            ?>
                <li class="list-group-item subject-item">
                    <b><?= htmlspecialchars($row['code']) ?></b> - 
                    <?= htmlspecialchars($row['name']) ?>
                    <span class="teacher-text"> &nbsp; | &nbsp; <?= htmlspecialchars($row['teacher']) ?></span>
                </li>
            <?php endwhile; ?>
        </ul>

    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
