<?php
// Database connection
include __DIR__ . '/../config/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize and collect form data
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $universityEmail = isset($_POST['universityEmail']) ? trim($_POST['universityEmail']) : '';
    $universityID = isset($_POST['universityID']) ? trim($_POST['universityID']) : '';
    $skill = isset($_POST['skills']) ? trim($_POST['skills']) : '';
    $mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword = isset($_POST['confirmPassword']) ? $_POST['confirmPassword'] : '';

    if (empty($name) || empty($universityEmail) || empty($password)) {
        die("Error: Please fill in all required fields.");
    }

    // Check if passwords match
    if ($password !== $confirmPassword) {
        die("Error: Passwords do not match.");
    }

    // Hash the password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE university_email = ?");
    $stmt->bind_param("s", $universityEmail);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        die("Error: An account with this university email already exists.");
    }
    $stmt->close();

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (name, university_email, university_id, skill, mobile, password_hash) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $universityEmail, $universityID, $skill, $mobile, $passwordHash);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href='../../frontend/pages/Login.html';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

// Initial signup handler

