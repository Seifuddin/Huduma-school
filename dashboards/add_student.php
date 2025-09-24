<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $admission_number = $_POST['admission_number'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $nationality = $_POST['nationality'];
    $class = $_POST['class'];

    // Handle photo upload
    $photo = null;
    if (!empty($_FILES['photo']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $photo = time() . "_" . basename($_FILES['photo']['name']);
        $targetFile = $targetDir . $photo;

        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
            $photo = null; // fallback if upload fails
        }
    }

    $stmt = $conn->prepare("INSERT INTO student (username, full_name, admission_number, age, gender, nationality, photo, class) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssissss", $username, $full_name, $admission_number, $age, $gender, $nationality, $photo, $class);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?success=1");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>
