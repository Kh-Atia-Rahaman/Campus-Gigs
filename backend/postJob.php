<?php
include __DIR__ . '/config/db.php';

// Get data from POST request safely
$category = isset($_POST['category']) ? $_POST['category'] : '';
$budget = isset($_POST['budget']) ? $_POST['budget'] : 0;
$deadline = isset($_POST['deadline']) ? $_POST['deadline'] : '';
$time = isset($_POST['time']) ? $_POST['time'] : '';
$description = isset($_POST['description']) ? $_POST['description'] : '';
$location = isset($_POST['location']) ? $_POST['location'] : 'Campus';

// Use prepared statements to insert into database
$stmt = $conn->prepare("INSERT INTO jobs (category, budget, deadline, time, description, location) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sdssss", $category, $budget, $deadline, $time, $description, $location);

if ($stmt->execute()) {
    header("Location: job_feed.php?posted=true");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
