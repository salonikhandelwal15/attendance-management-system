<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Attendance Management System</title>

  <!-- Bootstrap -->
  <link href="../public/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    .glass-navbar {
        margin-top: 15px;
        width: 92%;
        margin-left: auto;
        margin-right: auto;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        border-radius: 18px;
        padding: 10px 25px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.25);
    }

    .glass-navbar a {
        color: white !important;
        text-decoration: none;
        font-weight: 500;
        padding: 6px 14px;
        border-radius: 12px;
        transition: 0.25s;
    }

    .glass-navbar a:hover {
        background: rgba(255, 255, 255, 0.18);
    }

    .glass-navbar .brand-text {
        font-size: 22px;
        font-weight: 600;
    }

    .logout-btn {
        border: 1px solid rgba(255,255,255,0.6);
        padding: 4px 14px;
        border-radius: 10px;
        background: transparent;
        transition: 0.3s;
        color: white !important;
        font-size: 14px;
    }

    .logout-btn:hover {
        background: rgba(255,255,255,0.25);
    }

</style>

</head>
<body>

<?php include __DIR__ . '/navbar.php'; ?>
