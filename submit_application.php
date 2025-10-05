<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "huduma";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$full_name = $_POST['full_name'];
$index_number = $_POST['index_number'];
$marks = $_POST['marks'];

$sql = "INSERT INTO applications (full_name, index_number, marks) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $full_name, $index_number, $marks);

if ($stmt->execute()) {
  echo "Application submitted successfully!";
} else {
  echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
