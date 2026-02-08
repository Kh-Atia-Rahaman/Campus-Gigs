<?php
include __DIR__ . '/config/db.php';

$category = isset($_GET['category']) ? $_GET['category'] : '';

$sql = "SELECT * FROM jobs";
if ($category) {
    $sql .= " WHERE category = '$category'";
}

$result = $conn->query($sql);
$jobs = [];

while($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}

echo json_encode($jobs);
$conn->close();
?>
