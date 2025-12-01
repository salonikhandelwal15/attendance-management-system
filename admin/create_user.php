<?php
require_once __DIR__ . '/../config/db.php';
session_start();

if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /attendance-management-system/auth/login.php');
    exit;
}

$error = '';
$success = '';

// Fetch courses for dropdown
$courses = $conn->query("SELECT id, course_code, course_name FROM attendance_system.courses ORDER BY course_code");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $role = $_POST['role'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $roll = trim($_POST['roll'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $course_id = !empty($_POST['course_id']) ? (int)$_POST['course_id'] : null;

    if (!in_array($role, ['teacher', 'student'])) {
        $error = 'Invalid role';
    } else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);

        // Insert user
        $stmt = $conn->prepare("
            INSERT INTO attendance_system.users (roll_or_emp, name, email, password_hash, role, course_id)
            VALUES (?,?,?,?,?,?)
        ");
        $stmt->bind_param('sssssi', $roll, $name, $email, $hash, $role, $course_id);

        if ($stmt->execute()) {
            $newUserId = $stmt->insert_id;

            // Auto-enroll only if student
            if ($role === 'student' && $course_id) {

                // Get all subjects under this course
                $sub = $conn->prepare("
                    SELECT subject_id FROM attendance_system.course_subjects
                    WHERE course_id=?
                ");
                $sub->bind_param("i", $course_id);
                $sub->execute();
                $subjects = $sub->get_result();

                // Enroll student in all subjects
                $en = $conn->prepare("
                    INSERT IGNORE INTO attendance_system.enrollments (student_id, subject_id)
                    VALUES (?,?)
                ");
                
                while ($s = $subjects->fetch_assoc()) {
                    $en->bind_param("ii", $newUserId, $s['subject_id']);
                    $en->execute();
                }
            }

            $success = 'User created successfully';
        } else {
            $error = 'DB Error: ' . $stmt->error;
        }
    }
}

include __DIR__ . '/../partials/header.php';
?>

<style>
    body {
        background: linear-gradient(110deg, #a063e0ff, #e0f2fe, #5da8f2ff);
        min-height: 100vh;
    }

    .page-card {
        background: rgba(255, 255, 255, 0.55);
        backdrop-filter: blur(7px);
        border-radius: 22px;
        padding: 30px 35px;
        margin-top: 25px;
        box-shadow: 0 10px 35px rgba(0, 0, 0, 0.18);
    }

    .form-control, .form-select {
        border-radius: 10px;
        padding: 10px 14px;
    }

    .btn-success {
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 600;
    }
</style>

<div class="container py-4 mt-4">

    <div class="page-card">

        <h3 class="fw-bold mb-4 text-success">Create User (Admin)</h3>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="post" class="row g-3">

            <div class="col-md-3">
                <label class="form-label">Role</label>
                <select name="role" id="role" class="form-select" required>
                    <option value="teacher">Teacher</option>
                    <option value="student">Student</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">ID</label>
                <input name="roll" class="form-control" placeholder="Enter ID" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Name</label>
                <input name="name" class="form-control" placeholder="Enter Full Name" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" placeholder="Enter Email" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" placeholder="Enter Password" class="form-control" required>
            </div>

            <!-- COURSE FIELD -->
            <div class="col-md-4" id="course-field" style="display:none;">
                <label class="form-label">Select Course</label>
                <select name="course_id" class="form-select">
                    <option value="">-- Select Course --</option>
                    <?php while ($c = $courses->fetch_assoc()): ?>
                        <option value="<?= $c['id'] ?>">
                            <?= htmlspecialchars($c['course_code'] . ' - ' . $c['course_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <script>
                document.getElementById('role').addEventListener('change', function () {
                    document.getElementById('course-field').style.display =
                        this.value === 'student' ? 'block' : 'none';
                });
            </script>

            <div class="col-12 mt-4">
                <button class="btn btn-success px-4">Create</button>
            </div>

        </form>

    </div>
</div>


<?php include __DIR__ . '/../partials/footer.php'; ?>
