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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $admission_number = $_POST['admission_number'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $nationality = $_POST['nationality'];
    $class = $_POST['class'];

    // Check if a new photo was uploaded
    $photo_sql = "";
    $params = [$username, $full_name, $admission_number, $age, $gender, $nationality, $class, $id];
    $types = "sssisssi";

    if (!empty($_FILES['photo']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $photo = time() . "_" . basename($_FILES['photo']['name']);
        $targetFile = $targetDir . $photo;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
            $photo_sql = ", photo=?";
            $params = [$username, $full_name, $admission_number, $age, $gender, $nationality, $class, $photo, $id];
            $types = "sssissssi";
        }
    }

    $sql = "UPDATE student SET username=?, full_name=?, admission_number=?, age=?, gender=?, nationality=?, class=? $photo_sql WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?updated=1");
        exit();
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
