<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

<div class="glass-navbar">

    <!-- LEFT SIDE -->
    <div class="d-flex align-items-center">
        <a class="brand-text me-4" href="/attendance-management-system/public/index.php">
            AMS
        </a>

        <?php if (!empty($_SESSION['user'])): ?>

            <?php if ($_SESSION['user']['role'] === 'student'): ?>
                <a href="/attendance-management-system/student/dashboard.php">Dashboard</a>

            <?php elseif ($_SESSION['user']['role'] === 'teacher'): ?>
                <a href="/attendance-management-system/teacher/dashboard.php">Dashboard</a>

            <?php elseif ($_SESSION['user']['role'] === 'admin'): ?>
                <a href="/attendance-management-system/admin/dashboard.php">Admin Panel</a>

            <?php endif; ?>

        <?php endif; ?>
    </div>

    <!-- RIGHT SIDE -->
    <div class="d-flex align-items-center">

        <?php if (!empty($_SESSION['user'])): ?>

            <span class="text-white me-3">
                Hi, <?= htmlspecialchars($_SESSION['user']['name']) ?>
            </span>

            <a class="logout-btn"
               href="/attendance-management-system/auth/logout.php">
                Logout
            </a>

        <?php else: ?>

            <a class="logout-btn"
               href="/attendance-management-system/auth/login.php">
                Login
            </a>

        <?php endif; ?>

    </div>
</div>
