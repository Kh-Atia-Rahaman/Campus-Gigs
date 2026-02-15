<?php
session_start();
include __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../frontend/pages/Login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, university_email, university, skill, avatar FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['name'] ?? 'User'); ?> - Profile - CampusGigs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f1f5f9;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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
            position: relative;
            padding-bottom: 5px;
            transition: color 0.3s ease;
        }

        .profile-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            flex-grow: 1;
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            justify-content: space-between;
        }

        .profile-header img.profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #0056b3;
        }

        .profile-info {
            flex-grow: 1;
            margin-left: 20px;
        }

        .profile-info h2 {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
        }

        .btn-edit {
            padding: 10px 20px;
            background-color: #007bff;
            border: none;
            color: white;
            border-radius: 30px;
            font-size: 1rem;
            text-decoration: none;
        }

        .btn-edit:hover {
            background-color: #0056b3;
            color: white;
        }

        .stats-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .stat-box {
            flex: 1;
            text-align: center;
            background-color: #f1f5f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 0 10px;
        }

        .footer {
            background: linear-gradient(90deg, #00274d, #0056b3);
            color: white;
            text-align: center;
            padding: 25px 0;
            margin-top: auto;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="../../frontend/pages/Home Page.html">CampusGigs</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="../../frontend/pages/Home Page.html">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="../job_feed.php">Jobs</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Profile Section -->
<div class="profile-container">
    <div class="profile-header">
        <img src="uploads/<?php echo htmlspecialchars($user['avatar'] ?? 'default.jpg'); ?>" alt="User Photo" class="profile-pic" onerror="this.src='https://via.placeholder.com/120';">

        <div class="profile-info">
            <h2><?php echo htmlspecialchars($user['name'] ?? 'User'); ?></h2>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['university_email'] ?? ''); ?></p>
            <p><strong>University:</strong> <?php echo htmlspecialchars($user['university'] ?? 'Daffodil International University'); ?></p>
            <p><strong>Primary Skill:</strong> <?php echo htmlspecialchars($user['skill'] ?? 'Web Development'); ?></p>
        </div>
       
        <a href="edit_profile.php" class="btn-edit">Edit Profile</a>
    </div>

    <!-- Stats Section -->
    <div class="stats-container">
        <div class="stat-box">
            <h3>5</h3>
            <p>Completed Jobs</p>
        </div>
        <div class="stat-box">
            <h3>4.9/5</h3>
            <p>Average Rating</p>
        </div>
        <div class="stat-box">
            <h3>3</h3>
            <p>Active Jobs</p>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="footer">
    <p>© 2025 CampusGigs. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
