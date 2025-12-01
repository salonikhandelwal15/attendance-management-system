<?php
require_once __DIR__ . '/../config/db.php';
session_start();

// Create error variable always (fixes your warning)
$error = $error ?? '';

// Redirect if already logged in
if (!empty($_SESSION['user'])) {
    $r = $_SESSION['user']['role'];
    if ($r === 'admin') header('Location: /attendance-management-system/admin/dashboard.php');
    elseif ($r === 'teacher') header('Location: /attendance-management-system/teacher/dashboard.php');
    else header('Location: /attendance-management-system/student/dashboard.php');
    exit;
}

// Login Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id   = trim($_POST['id'] ?? '');
    $pass = $_POST['password'] ?? '';

    $stmt = $conn->prepare("
        SELECT id, name, password_hash, role 
        FROM attendance_system.users 
        WHERE (roll_or_emp = ? OR email = ?)
        LIMIT 1
    ");
    $stmt->bind_param('ss', $id, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $u = $result->fetch_assoc();

    if ($u && password_verify($pass, $u['password_hash'])) {

        $_SESSION['user'] = [
            'id'   => $u['id'],
            'name' => $u['name'],
            'role' => $u['role']
        ];

        if ($u['role'] === 'admin') header('Location: /attendance-management-system/admin/dashboard.php');
        elseif ($u['role'] === 'teacher') header('Location: /attendance-management-system/teacher/dashboard.php');
        else header('Location: /attendance-management-system/student/dashboard.php');
        exit;
    }

    $error = "Invalid credentials! Please try again.";
}

?>


<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Login</title>

  <!-- Bootstrap 5 CSS -->
  <link href="../public/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Google font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root{
      --left-bg: #1f1a65;        
      --accent: #ff4d6d;        
      --right-bg: #f3f6fa;     
      --card-bg: #ffffff;
      --input-bg: rgba(255,255,255,0.85);
    }

    html,body { height: 100%; }
    body{
      font-family: "Poppins", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      margin:0;
      background: linear-gradient(135deg, #a18cff, #6e8bff, #9ecbff);
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
    }

    .login-wrap{
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      padding: 48px 24px;
    }

    .login-panel {
      width: 100%;
      max-width: 1100px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 18px 50px rgba(14,30,60,0.12);
    }

    .left-panel{
      background: linear-gradient(160deg, var(--left-bg), #4b39d8);
      color: #fff;
      padding: 48px;
      position: relative;
      display:flex;
      align-items:center;
      justify-content:center;
    }

    .left-panel::before{
      content: '';
      position: absolute;
      right: -40px;
      top: -40px;
      width: 220px;
      height: 220px;
      background: rgba(255,255,255,0.03);
      border-bottom-left-radius: 180px;
      transform: rotate(15deg);
      pointer-events:none;
    }

    .dots {
      position: absolute;
      left: 28px;
      top: 28px;
      width: 56px;
      height: 56px;
      background-image: radial-gradient(rgba(255,255,255,0.08) 1px, transparent 1px);
      background-size: 8px 8px;
      border-radius: 8px;
      opacity: 0.9;
    }

    .ill-box {
      width: 95%;
      max-width: 420px;
      display:flex;
      align-items:center;
      justify-content:center;
    }

    .ill-box img {
      width: 100%;
      height: auto;
      border-radius: 22px;
      box-shadow: 0 12px 30px rgba(11,15,35,0.25);
      background: rgba(255,255,255,0.03);
    }

    .right-panel{
      background: var(--right-bg);
      padding: 48px 56px;
      display:flex;
      flex-direction:column;
      justify-content:center;
    }

    .brand {
      text-align:center;
      margin-bottom: 18px;
    }

    .brand .logo {
      display:inline-flex;
      align-items:center;
      gap:10px;
      font-weight:800;
      color: #16213e;
    }

    .logo .badge {
      width:40px;
      height:36px;
      border-radius:8px;
      display:inline-grid;
      place-items:center;
      background: linear-gradient(135deg,#4f46e5,#7c3aed);
      color:white;
      font-weight:800;
      box-shadow: 0 6px 18px rgba(79,70,229,0.28);
    }

    .welcome {
      text-align:center;
      margin-bottom: 28px;
      color: #243b55;
    }

    .welcome h2 {
      margin:0;
      font-size: 22px;
      font-weight:800;
      letter-spacing: .3px;
      color:#0f172a;
    }

    .welcome p {
      margin:0;
      margin-top:6px;
      color:#475569;
      font-size:14px;
    }

    .login-card {
      background: var(--card-bg);
      border-radius: 14px;
      padding: 22px;
      box-shadow: 0 10px 28px rgba(12,20,60,0.06);
      width: 450px;
      margin: 0 auto;
    }

    .form-group label {
      font-size:12px;
      font-weight:700;
      color:#334155;
      letter-spacing: .4px;
    }

    .form-control.custom {
      background: var(--input-bg);
      border: none;
      border-radius: 14px;
      padding: 14px 16px;
      box-shadow: inset 0 -1px 0 rgba(15,23,42,0.03);
      font-weight:600;
      color:#0f172a;
    }

    .form-control.custom::placeholder {
      color: #94a3b8;
      font-weight:500;
    }

    .forgot {
      font-size:13px;
      text-decoration:none;
      color:#64748b;
      float:right;
      margin-top:6px;
    }

    .login-btn {
      display:block;
      width: 100%;
      border-radius: 12px;
      padding: 12px 18px;
      font-weight:800;
      color: #f3f4f7ff;
      background: linear-gradient(160deg, var(--left-bg), #4b39d8);
      border: none;
      margin-top: 18px;
      box-shadow: 0 10px 24px rgba(12,20,60,0.08);
    }

    .small-muted {
      text-align:center;
      margin-top:12px;
      color:#94a3b8;
      font-size:13px;
    }

    @media (max-width: 880px){
      .login-panel { grid-template-columns: 1fr; }
      .left-panel { padding: 30px; min-height: 260px; }
      .right-panel { padding: 28px; }
      .ill-box img { border-radius: 16px; }
      .login-card { margin-top: 14px; }
    }
  </style>
</head>
<body>

  <div class="login-wrap">
    <div class="login-panel">

      <!-- LEFT: Form -->
      <div class="left-panel">
        <div class="dots" aria-hidden="true"></div>
        <div class="ill-box">
          <img src="../public/assets/images/LOGIN.jpg" alt="img">
        </div>
      </div>

      <!-- RIGHT: Form -->
      <div class="right-panel">
        <div class="brand">
          <div class="logo">
            <span class="badge">AMS</span>
            <span style="font-size:18px;color:#0f172a;">ATTENDANCE MANAGEMENT SYSTEM</span>
          </div>
        </div>

        <div class="welcome">
          <h2>Welcome Back !</h2>
          <p>Enter your credentials to access your dashboard</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="login-card">
          <form action="#" method="post" novalidate>
                    <!-- ID -->
                    <div class="mb-4">
                        <label class="form-label">ID</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text" name="id" class="form-control" placeholder="Enter Your ID" required>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" name="password" class="form-control" placeholder="Enter Your Password" required>
                        </div>
                    </div>

            <button class="login-btn" type="submit">Log In</button>
            <div class="text-center mt-3">
        <a href="/attendance-management-system/public/index.php" 
           style="font-size:14px; color:#475569; text-decoration:none; font-weight:600;">
            ← Back to Home
        </a>
    </div>
          </form>

        </div>

      </div>
    </div>
  </div>

  <!-- bootstrap JS (optional) -->
<script src="../public/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>


