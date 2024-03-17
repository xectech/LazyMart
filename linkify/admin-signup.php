<?php
// Connect to database (replace with actual database connection code)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "linkify";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    // Hash the password before storing (for security)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert admin data into database
    $sql = "INSERT INTO admins (email, username, password) VALUES ('$email', '$username', '$hashed_password')";

    if ($conn->query($sql) === TRUE) {
        header("Location: admin-login.html");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
