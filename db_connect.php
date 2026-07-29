<?php
// ==========================================
// TEMPLATE: Database Connection
// ==========================================
// INSTRUCTIONS FOR TEAM MEMBERS:
// 1. Copy this file and rename the copy to: db_connect.php
// 2. Do NOT edit this example file directly.
// 3. Update the credentials in your local db_connect.php if your XAMPP password differs.
// ==========================================

$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = "";     // Default XAMPP password is empty
$dbname = "farmlend"; // Ensure this matches the database name in phpMyAdmin

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Optional: Set charset to utf8mb4 for proper character encoding
$conn->set_charset("utf8mb4");

?>