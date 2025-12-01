<?php
require_once __DIR__ . '/../config/db.php';
session_start();

if ($_SESSION['user']['role'] !== 'admin') exit;

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);
    $name = trim($_POST['name']);

    $stmt = $conn->prepare("INSERT INTO attendance_system.courses (course_code, course_name) VALUES (?,?)");
    $stmt->bind_param("ss", $code, $name);

    $msg = $stmt->execute() ? "Course Added" : "Error: " . $stmt->error;
}

$cq = $conn->query("SELECT * FROM attendance_system.courses ORDER BY course_code");

include __DIR__ . '/../partials/header.php';
?>

<style>
    body {
        background: linear-gradient(110deg, #a063e0ff, #e0f2fe, #5da8f2ff);
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

    .course-item {
        background: #f9fafb;
        border-radius: 12px !important;
        padding: 15px 18px;
        margin-bottom: 8px;
        font-size: 16px;
        border: none;
        transition: 0.25s;
    }

    .course-item:hover {
        background: #eef2ff;
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
</style>

<div class="container py-4">

    <div class="page-card">

        <h3 class="fw-bold mb-4 text-primary">Manage Courses</h3>

        <?php if ($msg): ?>
            <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <form method="post" class="row g-3 mb-4">
            <div class="col-md-3">
                <input name="code" placeholder="Course Code" class="form-control" required>
            </div>

            <div class="col-md-6">
                <input name="name" placeholder="Course Name" class="form-control" required>
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary w-100">Add</button>
            </div>
        </form>

        <h5 class="mt-4 mb-3 fw-semibold text-secondary">Available Courses</h5>

        <ul class="list-group">
            <?php while ($c = $cq->fetch_assoc()): ?>
                <li class="list-group-item course-item">
                    <b><?= htmlspecialchars($c['course_code']) ?></b> — 
                    <?= htmlspecialchars($c['course_name']) ?>
                </li>
            <?php endwhile; ?>
        </ul>

    </div>
</div>


<?php include __DIR__ . '/../partials/footer.php'; ?>
