<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "huduma";

$conn = new mysqli($servername, $username, $password, $dbname);

$id = $_GET['id'];
$status = $_GET['status'];

$sql = "UPDATE applications SET status=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $id);
$stmt->execute();

header("Location: admin.php");
?>
