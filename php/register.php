<?php
session_start();

// Database connection
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "huduma";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    // Check if username already exists
    $check_sql = "SELECT * FROM admin WHERE username = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $check_result = $stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<h3 style='color:red;text-align:center;'>Username already exists. Please choose another.</h3>";
        echo "<a href='../register.html'>Go back</a>";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $insert_sql = "INSERT INTO admin (username, password, role) VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("sss", $username, $hashed_password, $role);

    if ($insert_stmt->execute()) {
        echo "<h3 style='color:green;text-align:center;'>Registration successful!</h3>";
        header("Refresh: 2; url=../login.html"); // Redirect after 2 seconds
        exit();
    } else {
        echo "<h3 style='color:red;text-align:center;'>Error: Could not register user.</h3>";
    }

    $stmt->close();
    $insert_stmt->close();
}

$conn->close();
?>
