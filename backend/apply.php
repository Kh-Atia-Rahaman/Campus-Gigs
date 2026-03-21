<?php
session_start();
include __DIR__ . '/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/pages/Login.html");
    exit();
}

$job_id = isset($_POST['job_id']) ? $_POST['job_id'] : 0;
$user_id = $_SESSION['user_id'];
$user_name = isset($_POST['user_name']) ? $_POST['user_name'] : (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '');
$user_email = isset($_POST['user_email']) ? $_POST['user_email'] : '';
$cover_letter = isset($_POST['cover_letter']) ? $_POST['cover_letter'] : '';

// Prepare SQL query to insert application
$sql = "INSERT INTO job_applications (job_id, user_id, user_name, user_email, cover_letter, status) VALUES (?, ?, ?, ?, ?, 'pending')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisss", $job_id, $user_id, $user_name, $user_email, $cover_letter);

if ($stmt->execute()) {
    header("Location: job_feed.php?application_status=success");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
