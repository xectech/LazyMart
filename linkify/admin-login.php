<?php
session_start();
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
    $password = $_POST["password"];

    // Check if the entered email matches the admin email
    if ($email == 'xectech34@xectech.com') {
        // Retrieve hashed password from database
        $sql = "SELECT id, password FROM admins WHERE email='$email'";
        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $hashed_password = $row["password"];

            // Verify password
            if (password_verify($password, $hashed_password)) {
                $_SESSION["admin"] = true;
                header("Location: admin-panel.php");
                exit();
            } else {
                $error = "Invalid email or password";
            }
        } else {
            $error = "Invalid email or password";
        }
    } else {
        // Display message and offer a paid plan
        $message = "You are not authorized to access the admin panel. Subscribe to our paid plan for full access at $499/month.";
    }
}

$conn->close();
?>
