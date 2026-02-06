<?php
// Database connection details
$servername = "localhost";
$username = "root";      // MySQL username
$password = "";          // MySQL password
$dbname = "campusgigs";  // Database name

// Create a new connection using mysqli
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

// DB parameters standardized
