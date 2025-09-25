<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "huduma");

$id = $_GET['id'];
$status = $_GET['status'];

$stmt = $conn->prepare("UPDATE applications SET status=? WHERE id=?");
$stmt->bind_param("si", $status, $id);
$stmt->execute();

header("Location: dashboard.php");
exit();
