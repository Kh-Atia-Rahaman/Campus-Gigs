<?php
session_start();
include __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../frontend/pages/Login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$name = $_POST['name'];
$email = $_POST['email'];
$university = $_POST['university'];
$skills = $_POST['skills'];

$stmt = $conn->prepare("UPDATE users SET name = ?, university_email = ?, university = ?, skills = ? WHERE id = ?");
$stmt->bind_param("ssssi", $name, $email, $university, $skills, $user_id);

if ($stmt->execute()) {
    header("Location: profile.php?updated=true");
} else {
    echo "Error updating profile: " . $stmt->error;
}
?>
