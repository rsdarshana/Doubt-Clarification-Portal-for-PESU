<?php
$servername = "localhost"; // MySQL server (usually localhost for XAMPP)
$username = "root";        // MySQL username (default for XAMPP is root)
$password = "";            // MySQL password (default for XAMPP is an empty string)
$dbname = "doubts";        // The name of your database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
