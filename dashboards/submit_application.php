<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.html");
    exit();
}

$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "huduma";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];

// Get student id
$sql = "SELECT id FROM student WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

$course = $_POST['course'];

// Insert application
$sql_insert = "INSERT INTO applications (student_id, course_applied) VALUES (?, ?)";
$stmt2 = $conn->prepare($sql_insert);
$stmt2->bind_param("is", $student['id'], $course);
$stmt2->execute();

$stmt->close();
$stmt2->close();
$conn->close();

header("Location: student_dashboard.php");
exit();
?>
