<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "huduma";

$conn = new mysqli($servername, $username, $password, $dbname);
$status_box = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $index_number = $_POST['index_number'];
  $result = $conn->query("SELECT * FROM applications WHERE index_number='$index_number'");
  
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $status = strtoupper($row['status']);

    // Choose color and icon based on status
    if ($status == "APPROVED") {
      $status_box = "<div class='alert success'><span class='icon'>✔</span> Congratulations! Your application is <strong>$status</strong>.</div>";
    } elseif ($status == "PENDING") {
      $status_box = "<div class='alert pending'><span class='icon'>⏳</span> Your application is still <strong>$status</strong>. Please check back later.</div>";
    } elseif ($status == "REJECTED") {
      $status_box = "<div class='alert error'><span class='icon'>❌</span> We regret to inform you that your application was <strong>$status</strong>.</div>";
    } else {
      $status_box = "<div class='alert neutral'><span class='icon'>ℹ️</span> Status: <strong>$status</strong>.</div>";
    }
  } else {
    $status_box = "<div class='alert error'><span class='icon'>⚠️</span> No application found with that index number.</div>";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Check Application Status</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

    body {
      font-family: 'Poppins', sans-serif;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0;
      background: linear-gradient(135deg, #fff8e1 25%, #ffe0b2 100%);
      background-image: radial-gradient(#ffd54f 1px, transparent 1px);
      background-size: 20px 20px;
      background-attachment: fixed;
      animation: gradientShift 16s ease infinite;
    }

    @keyframes gradientShift {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .container {
      background: rgba(255, 255, 255, 0.96);
      padding: 40px 50px;
      border-radius: 20px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      text-align: center;
      width: 400px;
      backdrop-filter: blur(8px);
      position: relative;
      z-index: 2;
    }

    h2 {
      margin-bottom: 25px;
      color: #4e342e;
      font-weight: 600;
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #5d4037;
    }

    input[type="text"] {
      width: 100%;
      padding: 12px;
      border: 2px solid #ffb300;
      border-radius: 8px;
      font-size: 15px;
      outline: none;
      margin-bottom: 20px;
      transition: 0.3s;
    }

    input[type="text"]:focus {
      border-color: #f57c00;
      box-shadow: 0 0 5px rgba(245, 124, 0, 0.4);
    }

    button {
      background-color: #ff9800;
      border: none;
      padding: 10px 25px;
      border-radius: 8px;
      color: white;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    button:hover {
      background-color: #e68900;
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(230, 137, 0, 0.3);
    }

    .alert {
      margin-top: 20px;
      padding: 15px 20px;
      border-radius: 10px;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 10px;
      justify-content: center;
      animation: fadeIn 0.6s ease;
    }

    .alert .icon {
      font-size: 22px;
    }

    .success {
      background-color: #e8f5e9;
      border: 1px solid #4caf50;
      color: #2e7d32;
    }

    .pending {
      background-color: #fff8e1;
      border: 1px solid #ffb300;
      color: #e65100;
    }

    .error {
      background-color: #ffebee;
      border: 1px solid #f44336;
      color: #c62828;
    }

    .neutral {
      background-color: #eceff1;
      border: 1px solid #90a4ae;
      color: #37474f;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); opacity: 1; }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Check Your Application Status</h2>
    <form method="POST">
      <label>Enter Index Number:</label>
      <input type="text" name="index_number" placeholder="e.g. 456789" required>
      <button type="submit">Check Status</button>
    </form>

    <?= $status_box ?>
  </div>
</body>
</html>
