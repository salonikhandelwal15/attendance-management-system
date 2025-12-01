<?php
// __DIR__ ensures the include path is relative to this file
require_once __DIR__ . '/../config/db.php';

// Starts (or resumes) the PHP session so $_SESSION is available
session_start();

if ($_SESSION['user']['role'] !== 'admin') exit;

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = (int)$_POST['course_id'];
    $subject_id = (int)$_POST['subject_id'];

    // INSERT IGNORE means if a duplicate unique/primary key error occurs it ignores instead of failing
    $stmt = $conn->prepare("
        INSERT IGNORE INTO attendance_system.course_subjects (course_id, subject_id)
        VALUES (?,?)
    ");
    $stmt->bind_param("ii", $course_id, $subject_id);

    $msg = $stmt->execute() ? "Subject assigned successfully!" : "Error: " . $stmt->error;

    // Auto-enroll all students of the course into this subject
    $enroll = $conn->prepare("
        INSERT IGNORE INTO attendance_system.enrollments (student_id, subject_id)
        SELECT id AS student_id, ? AS subject_id
        FROM attendance_system.users
        WHERE course_id = ? AND role = 'student';
    ");
    $enroll->bind_param("ii", $subject_id, $course_id);
    $enroll->execute();
}

$courses = $conn->query("SELECT * FROM attendance_system.courses ORDER BY course_code");
$subjects = $conn->query("SELECT * FROM attendance_system.subjects ORDER BY code");

include __DIR__ . '/../partials/header.php';
?>

<style>
    body {
        background: linear-gradient(110deg, #a063e0ff, #e0f2fe, #5da8f2ff);
        min-height: 100vh;
    }

    .assign-card {
        background: rgba(224, 226, 252, 0.50);
        backdrop-filter: blur(15px);
        padding: 30px;
        border-radius: 18px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.10);
        max-width: 650px;
        margin: auto;
    }

    h4 {
        font-weight: 600;
        color: #334;
        text-align: center;
        margin-bottom: 25px;
    }

    label {
        font-weight: 600;
        color: #333;
    }

    .form-control, .form-select {
        padding: 12px;
        border-radius: 12px;
        font-weight: 500;
        border: 1px solid #c7d6ff;
        background: rgba(255, 255, 255, 0.7);
        box-shadow: 0px 2px 6px rgba(0,0,0,0.08);
        transition: 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #7a5af5;
        box-shadow: 0 0 0 3px rgba(122, 90, 245, 0.25);
    }

    .btn-custom {
        width: 100%;
        padding: 12px;
        font-weight: 600;
        background: #4b70f5;
        border-radius: 12px;
        border: none;
    }

    .btn-custom:hover {
        background: #365ff0;
    }

    .alert {
        border-radius: 12px;
        font-weight: 500;
    }
</style>

<div class="container">

    <div class="assign-card mt-5">

        <h4>Assign Subjects to Courses</h4>

        <?php if ($msg): ?>
            <div class="alert alert-info text-center"><?= $msg ?></div>
        <?php endif; ?>

        <form method="post" class="row g-4">

            <!-- Course Dropdown -->
            <div class="col-md-12">
                <label class="mb-1">Select Course</label>
                <select name="course_id" class="form-select" required>
                    <option value="">Choose Course</option>
                    <?php while ($c = $courses->fetch_assoc()): ?>

                        // htmlspecialchars() used to prevent XSS (Cross-Site Scripting)
                        <option value="<?= $c['id'] ?>">
                            <?= htmlspecialchars($c['course_code']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Subject Dropdown -->
            <div class="col-md-12">
                <label class="mb-1">Select Subject</label>
                <select name="subject_id" class="form-select" required>
                    <option value="">Choose Subject</option>
                    <?php while ($s = $subjects->fetch_assoc()): ?>
                        <option value="<?= $s['id'] ?>">
                            <?= htmlspecialchars($s['code'] . " - " . $s['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Assign Button -->
            <div class="col-md-12">
                <button class="btn btn-custom text-white">Assign</button>
            </div>

        </form>

    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
