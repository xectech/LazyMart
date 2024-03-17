<?php
$servername = "localhost";
$username = "root";
$password = ""; // Modify this if you have set a password for your database
$dbname = "linkify";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
