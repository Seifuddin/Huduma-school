<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "huduma";

$conn = new mysqli($servername, $username, $password, $dbname);
$status_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $index_number = $_POST['index_number'];
  $result = $conn->query("SELECT * FROM applications WHERE index_number='$index_number'");
  
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $status_message = "Your application status: " . strtoupper($row['status']);
  } else {
    $status_message = "No application found with that index number.";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Check Application Status</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h2>Check Your Application Status</h2>
    <form method="POST">
      <label>Enter Index Number:</label>
      <input type="text" name="index_number" required>
      <button type="submit">Check Status</button>
    </form>

    <p style="margin-top: 15px; font-weight: bold;">
      <?= $status_message ?>
    </p>
  </div>
</body>
</html>
