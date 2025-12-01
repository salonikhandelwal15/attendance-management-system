<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Education Landing Page</title>

    <!-- Bootstrap 5 -->
  <link href="../public/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body {
            margin: 0;
            font-family: "Poppins", sans-serif;
            background: linear-gradient(135deg, #4f46e5, #9333ea, #3b82f6);
            background-size: cover;
        }

        .main-card {
            background: white;
            border-radius: 30px;
            padding: 45px 50px;
            box-shadow: 0 20px 45px rgba(0,0,0,0.25);
            margin-top: 70px;
            width: 85%;
            margin-left: auto;
            margin-right: auto;
        }

        .wave-section {
            margin-top: 35px;  
        }

        .wave-section svg {
            width: 100%;
            display: block;
        }

        .title-lg {
            font-size: 48px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .illustration {
            width: 100%;
        }
    </style>
</head>

<body>

<div class="container">
    <div class="main-card">

        <div class="row align-items-center">

            <!-- Left Content -->
            <div class="col-md-6 mb-5 mb-md-0">

                <h1 class="title-lg text-primary">ATTENDANCE<br>MANAGEMENT<br>SYSTEM</h1>

                <p class="text-secondary">
                    Expand your knowledge, explore new skills, and learn at your own pace with a modern and engaging online learning experience. Education made simple, enjoyable, and accessible for everyone.
                </p>

                <a href="/attendance-management-system/auth/login.php" class="btn btn-outline-primary px-4 py-2 fw-semibold mt-3">
                    Explore More
                </a>
            </div>

            <!-- Right Image -->
            <div class="col-md-6 text-center">
                <img src="https://img.freepik.com/free-vector/online-learning-concept-illustration_114360-1005.jpg"
                     class="illustration" alt="img">
            </div>

        </div>

    </div>
</div>

<!-- Curved bottom white wave -->
<div class="wave-section">
    <svg viewBox="0 0 1440 320">
        <path fill="#ffffff" fill-opacity="1"
              d="M0,64L60,85.3C120,107,240,149,360,186.7C480,224,600,256,720,266.7C840,277,960,267,1080,245.3C1200,224,1320,192,1380,176L1440,160L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z">
        </path>
    </svg>
</div>

<script src="../public/assets/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>
