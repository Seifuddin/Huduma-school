<?php
$host = "localhost";  // database host
$user = "root";       // database username
$pass = "";           // database password
$dbname = "huduma"; // your database name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
