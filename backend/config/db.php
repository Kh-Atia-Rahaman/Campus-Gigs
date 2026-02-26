<?php
$servername = "localhost";
$username = "root";  // default XAMPP user
$password = "";      // default is empty
$dbname = "campusgigs"; // standardized DB name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

// DB parameters standardized

// Initial db structure setup

