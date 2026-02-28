<?php
include __DIR__ . '/../config/db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // Prepare SQL to fetch user using correct column names
    $stmt = $conn->prepare("SELECT id, name, password_hash FROM users WHERE university_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password_hash'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: ../../frontend/pages/Home Page.html");
            exit;
        } else {
            echo "<script>alert('Incorrect password!'); window.location.href='../../frontend/pages/Login.html';</script>";
        }
    } else {
        echo "<script>alert('No user found with that email!'); window.location.href='../../frontend/pages/Login.html';</script>";
    }
}
?>

// Navigation redirects updated

// Initial login handler

