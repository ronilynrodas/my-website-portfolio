<?php
// Database Configuration
$host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "ibiza_subdivision";

// Create connection
$conn = new mysqli($host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");
?>
