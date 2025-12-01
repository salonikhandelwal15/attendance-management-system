<?php
require_once __DIR__ . '/../config/db.php';
session_start();
if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') { 
    header('Location: /attendance-management-system/auth/login.php'); 
    exit; 
}

$uid = $_SESSION['user']['id'];

$sql = "SELECT s.id, s.code, s.name,
  COUNT(cs.id) AS total_classes,
  SUM(CASE WHEN a.status='P' THEN 1 ELSE 0 END) AS presents,
  ROUND(100 * SUM(CASE WHEN a.status='P' THEN 1 ELSE 0 END) / NULLIF(COUNT(cs.id),0),2) AS percentage
FROM attendance_system.subjects s
JOIN attendance_system.enrollments e ON e.subject_id = s.id AND e.student_id = ?
LEFT JOIN attendance_system.class_sessions cs ON cs.subject_id = s.id
LEFT JOIN attendance_system.attendance a ON a.session_id = cs.id AND a.student_id = ?
GROUP BY s.id, s.code, s.name";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $uid, $uid);
$stmt->execute();
$result = $stmt->get_result();

include __DIR__ . '/../partials/header.php';
?>

<style>
    body {
        background: linear-gradient(110deg, #a063e0ff, #eaf3ff, #5da8f2ff);
        min-height: 100vh;
        font-family: 'Inter', sans-serif;
    }

    .page-title {
        font-weight: 700;
        font-size: 30px;
        color: #243447;
        margin-bottom: 25px;
        border-left: 6px solid #4b70f5;
        padding-left: 14px;
    }

    .attendance-card {
        background: rgba(255, 255, 255, 0.7);
        border-radius: 18px;
        padding: 20px;
        backdrop-filter: blur(12px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.10);
        transition: 0.3s ease;
        border: 1px solid rgba(200, 200, 255, 0.35);
    }

    .attendance-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 14px 32px rgba(0,0,0,0.15);
    }

    .subject-title {
        font-size: 20px;
        font-weight: 700;
        color: #1e2a38;
    }

    .stats-text {
        font-size: 14px;
        color: #5f6c7b;
    }

    .progress-ring {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.progress-ring svg {
    transform: rotate(-90deg);
}

.progress-ring circle {
    fill: none; 
    stroke-width: 9; 
    transform: rotate(-90deg); 
    transform-origin: 50% 50%; 
    stroke-linecap: round;
}

.progress-bg {
    stroke: #e2e8f0;
}

.progress-value {
    transition: stroke-dashoffset 0.5s ease-out;
}

.progress-text {
    position: absolute;
    font-weight: 700;
    font-size: 14px;
    color: #1e293b;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}


    .btn-details {
        font-weight: 600;
        text-decoration: none;
        padding: 6px 0;
        display: inline-block;
        color: #4b70f5;
    }

    .btn-details:hover {
        color: #2f55d4;
        text-decoration: underline;
    }
</style>

<div class="container py-5">

    <h3 class="page-title">Your Attendance</h3>

    <div class="row g-4">

        <?php while ($r = $result->fetch_assoc()): ?>
            <?php
$total_classes = (int)$r['total_classes'];
$presents = (int)$r['presents'];

// Percentage calculation
$pct = ($total_classes > 0) ? round(100 * $presents / $total_classes, 2) : 0;

$radius = 33;
$stroke = 9;
$circumference = 2 * M_PI * $radius;

// Ring color logic
if ($total_classes === 0 || $pct < 60) {
    $color = "#dc3545"; // red
} elseif ($pct >= 75) {
    $color = "#28a745"; // green
} else {
    $color = "#ffc107"; // yellow
}

$offset = $circumference * (1 - max($pct, 1) / 100);
?>


            <div class="col-md-6">
                <div class="attendance-card">

                    <div class="d-flex justify-content-between align-items-center">

                        <!-- LEFT TEXT -->
                        <div>
                            <div class="subject-title">
                                <?= htmlspecialchars($r['code']) ?> — <?= htmlspecialchars($r['name']) ?>
                            </div>
                            <div class="stats-text mt-1">
                                Classes: <?= (int)$r['total_classes'] ?> &nbsp; | &nbsp; Present: <?= (int)$r['presents'] ?>
                            </div>
                        </div>

                        <!-- RIGHT PROGRESS RING -->
                        <div class="progress-ring mt-3">
    <svg width="<?= ($radius + $stroke) * 2 ?>" height="<?= ($radius + $stroke) * 2 ?>">
        <circle class="progress-bg" 
                cx="<?= $radius + $stroke ?>" 
                cy="<?= $radius + $stroke ?>" 
                r="<?= $radius ?>" 
                stroke-width="<?= $stroke ?>">
        </circle>
        <circle class="progress-value" 
                cx="<?= $radius + $stroke ?>" 
                cy="<?= $radius + $stroke ?>" 
                r="<?= $radius ?>" 
                stroke="<?= $color ?>"
                stroke-dasharray="<?= $circumference ?>"
                stroke-dashoffset="<?= $offset ?>"
                stroke-linecap="round">
        </circle>
    </svg>
    <div class="progress-text"><?= $pct ?>%</div>
</div>


                    </div>

                    <a class="btn-details" 
                       href="/attendance-management-system/student/subject.php?subject_id=<?= (int)$r['id'] ?>">
                        View Details →
                    </a>

                </div>
            </div>

        <?php endwhile; ?>

    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
