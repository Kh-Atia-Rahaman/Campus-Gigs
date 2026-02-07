<?php
// fetch_jobs.php
include __DIR__ . '/config/db.php';

$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';

$sql = "SELECT title, description, category FROM jobs";
if (!empty($category)) {
    $sql .= " WHERE category = '$category'";
}
$sql .= " ORDER BY created_at DESC";

$result = $conn->query($sql);

$jobs = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $jobs[] = $row;
    }
}
header('Content-Type: application/json');
echo json_encode($jobs);
$conn->close();
?>
