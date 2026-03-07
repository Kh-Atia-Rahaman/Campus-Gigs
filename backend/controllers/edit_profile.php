<?php
session_start();
include __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../frontend/pages/Login.html");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $university = $_POST['university'];
    $skills = $_POST['skills'];

    // Handle file upload
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
        $target_dir = __DIR__ . "/uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $avatar_name = basename($_FILES["avatar"]["name"]);
        $extension = pathinfo($avatar_name, PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    
        if (!in_array(strtolower($extension), $allowed)) {
            echo "<p class='text-danger'>❌ Invalid file type. Only JPG, PNG, or GIF allowed.</p>";
            exit;
        }
    
        $sanitized_name = preg_replace("/[^a-zA-Z0-9]/", "_", pathinfo($avatar_name, PATHINFO_FILENAME));
        $avatar_filename = time() . "_" . $sanitized_name . "." . $extension;
        $target_file = $target_dir . $avatar_filename;
    
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
            $stmt = $conn->prepare("UPDATE users SET name=?, university_email=?, university=?, skills=?, avatar=? WHERE id=?");
            $stmt->bind_param("sssssi", $name, $email, $university, $skills, $avatar_filename, $user_id);
            $stmt->execute();
        }
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, university_email=?, university=?, skills=? WHERE id=?");
        $stmt->bind_param("ssssi", $name, $email, $university, $skills, $user_id);
        $stmt->execute();
    }

    header("Location: profile.php");
    exit;
}

// Fetch current user data
$stmt = $conn->prepare("SELECT name, university_email, university, skills, avatar FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile - CampusGigs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-5">
    <div class="container" style="max-width: 600px;">
        <h2>Edit Your Profile</h2>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label>University Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['university_email'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label>University</label>
                <input type="text" name="university" class="form-control" value="<?php echo htmlspecialchars($user['university'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label>Skills (comma separated)</label>
                <input type="text" name="skills" class="form-control" value="<?php echo htmlspecialchars($user['skills'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label>Profile Picture</label><br>
                <input type="file" name="avatar" class="form-control">
            </div>
            <button class="btn btn-primary" type="submit">Save Changes</button>
            <a href="profile.php" class="btn btn-secondary ms-2">Cancel</a>
        </form>
    </div>
</body>
</html>

// Base profile editor

