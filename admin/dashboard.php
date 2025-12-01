<?php
require_once __DIR__ . '/../config/db.php';
session_start();

if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /attendance-management-system/auth/login.php');
    exit;
}

include __DIR__ . '/../partials/header.php';
?>

<style>

    body {
        background: linear-gradient(135deg, #7f5af0, #42a5f5);
        min-height: 100vh;
    }

    .dashboard-wrapper {
        padding-top: 40px;
        padding-bottom: 60px;
    }

    .dash-card {
        border-radius: 22px;
        padding: 35px 25px;
        text-align: center;
        color: #fff;
        height: 100%;
        position: relative;
        overflow: hidden;
        cursor: pointer;
        transition: transform .35s ease, box-shadow .35s ease;
        backdrop-filter: blur(8px);
        box-shadow: 0 8px 22px rgba(0,0,0,0.12);
    }

    .dash-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 14px 35px rgba(0,0,0,0.18);
    }

    .dash-card .icon-box {
        font-size: 50px;
        margin-bottom: 20px;
        padding: 18px;
        border-radius: 50%;
        background: rgba(255,255,255,0.15);
        display: inline-flex;
        justify-content: center;
        align-items: center;
    }

    .dash-card h5 {
        font-size: 1.35rem;
        font-weight: 700;
        letter-spacing: .3px;
        margin-bottom: 12px;
    }

    .dash-card p {
        font-size: .9rem;
        opacity: .9;
        font-weight: 400;
        margin-bottom: 28px;
    }

    .dash-btn {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255,255,255,0.3);
        padding: 10px;
        border-radius: 10px;
        color: #fff;
        font-weight: 500;
        text-decoration: none;
    }

    .dash-btn:hover {
        background: rgba(0,0,0,0.15);
        color: #fff;
    }

    .bg-1 { background: linear-gradient(135deg, #4b79ff, #4bd6ff); }
    .bg-2 { background: linear-gradient(135deg, #FF5F6D, #FFC371); }
    .bg-3 { background: linear-gradient(135deg, #34D399, #06B6D4); }
    .bg-4 { background: linear-gradient(135deg, #A855F7, #6366F1); }
</style>


<div class="container dashboard-wrapper mt-5">
    <div class="row g-4">

        <!-- Create User -->
        <div class="col-md-6 col-lg-3">
            <div class="dash-card bg-1">
                <div class="icon-box">
                    <i class="bi bi-person-plus"></i>
                </div>
                <h5>Create User</h5>
                <p>Add new students or Teachers.</p>

                <a href="/attendance-management-system/admin/create_user.php"
                   class="dash-btn w-100">
                   Create
                </a>
            </div>
        </div>

        <!-- Manage Subjects -->
        <div class="col-md-6 col-lg-3">
            <div class="dash-card bg-2">
                <div class="icon-box">
                    <i class="bi bi-journal-text"></i>
                </div>
                <h5>Manage Subjects</h5>
                <p>Add, edit, or delete academic subjects.</p>

                <a href="/attendance-management-system/admin/subjects.php"
                   class="dash-btn w-100">
                    Manage
                </a>
            </div>
        </div>

        <!-- Manage Courses -->
        <div class="col-md-6 col-lg-3">
            <div class="dash-card bg-3">
                <div class="icon-box">
                    <i class="bi bi-grid"></i>
                </div>
                <h5>Manage Courses</h5>
                <p>View and modify available courses.</p>

                <a href="/attendance-management-system/admin/courses.php"
                   class="dash-btn w-100">
                    Manage
                </a>
            </div>
        </div>

        <!-- Assign Subjects -->
        <div class="col-md-6 col-lg-3">
            <div class="dash-card bg-4">
                <div class="icon-box">
                    <i class="bi bi-link-45deg"></i>
                </div>
                <h5>Assign Subjects</h5>
                <p>Link subjects to specific courses.</p>

                <a href="/attendance-management-system/admin/course_subjects.php"
                   class="dash-btn w-100">
                    Assign
                </a>
            </div>
        </div>

    </div>
</div>


<?php include __DIR__ . '/../partials/footer.php'; ?>
