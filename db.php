<?php
// Database connection file for CNN Clone
// Database: rsk9_rsk9_4
// User: rsk9_rsk9_4
// Password: 654321#
 
$host = 'localhost';
$dbname = 'rsk9_rsk9_4';
$username = 'rsk9_rsk9_4';
$password = '654321#';
 
// Create connection
$conn = new mysqli($host, $username, $password, $dbname);
 
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
 
// Set charset to utf8mb4
$conn->set_charset("utf8mb4");
 
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
 
?>
 
 
