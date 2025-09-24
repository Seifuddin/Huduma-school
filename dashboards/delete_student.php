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

    // First fetch the photo path to delete the file if exists
    $result = $conn->query("SELECT photo FROM student WHERE id=" . (int)$id);
    if ($result && $row = $result->fetch_assoc()) {
        if (!empty($row['photo']) && file_exists("uploads/" . $row['photo'])) {
            unlink("uploads/" . $row['photo']);
        }
    }

    $stmt = $conn->prepare("DELETE FROM student WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?deleted=1");
        exit();
    } else {
        echo "Error deleting record: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
