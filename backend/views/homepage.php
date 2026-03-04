<?php
session_start();

$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? ($_SESSION['user_name'] ?? 'User') : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusGigs - Micro Jobs for Students</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fc;
            color: #333;
        }
        .navbar {
            background: linear-gradient(90deg, #00274d, #0056b3);
            padding: 15px;
        }
        .navbar-brand {
            font-size: 1.8rem;
            font-weight: bold;
            color: #fff !important;
        }
        .navbar .nav-link {
            font-weight: bold;
            color: white !important;
        }
        .footer {
            background: linear-gradient(90deg, #00274d, #0056b3);
            color: white;
            text-align: center;
            padding: 25px 0;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">CampusGigs</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../../frontend/pages/Home Page.html">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../job_feed.php">Jobs</a>
                    </li>

                    <?php if ($is_logged_in): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../controllers/profile.php">Profile (<?php echo htmlspecialchars($user_name); ?>)</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../controllers/logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../../frontend/pages/Login.html">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../../frontend/pages/Sign up.html">Sign Up</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5 text-center">
        <h1>Welcome to CampusGigs</h1>
        <p class="lead">Find & Offer Micro Jobs on Campus</p>
        <a href="../job_feed.php" class="btn btn-primary btn-lg mt-3">Explore Job Feed</a>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2025 CampusGigs | All rights reserved</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<!-- Homepage base template -->

