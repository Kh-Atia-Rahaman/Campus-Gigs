<?php
// Database connection details
$servername = "sql104.infinityfree.com";
$username = "if0_42555672";
$password = "eC3M217yELD";
$dbname = "if0_42555672_campusgigs"; // Update if you used a different database name suffix

// Create a new connection using mysqli
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
