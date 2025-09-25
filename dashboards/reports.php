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

// Fetch report data
$total = $conn->query("SELECT COUNT(*) AS count FROM application")->fetch_assoc()['count'];
$pending = $conn->query("SELECT COUNT(*) AS count FROM application WHERE status='pending'")->fetch_assoc()['count'];
$approved = $conn->query("SELECT COUNT(*) AS count FROM application WHERE status='approved'")->fetch_assoc()['count'];
$rejected = $conn->query("SELECT COUNT(*) AS count FROM application WHERE status='rejected'")->fetch_assoc()['count'];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Reports - Admin Panel</title>
    <style>
        body {
            margin: 0; 
            font-family: Arial, sans-serif; 
            background: #f4f4f4;
        }
        .sidebar {
            height: 100vh;
            width: 220px;
            position: fixed;
            top: 0;
            left: 0;
            background: #333;
            color: white;
            padding-top: 20px;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 12px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background: #575757;
        }
        .logout {
            background: red;
            text-align: center;
            margin-top: 20px;
            border-radius: 5px;
        }
        .content {
            margin-left: 240px;
            padding: 20px;
        }
        h2 { margin-bottom: 20px; }
        .card {
            background: white;
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .card h3 {
            margin: 0;
            color: #333;
        }
        .card p {
            font-size: 24px;
            color: #555;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="#">Manage Students</a>
        <a href="reports.php">Reports</a>
        <a href="#">Settings</a>
        <a class="logout" href="../logout.php">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h2>Application Reports</h2>

        <div class="card">
            <h3>Total Applications</h3>
            <p><?php echo $total; ?></p>
        </div>

        <div class="card">
            <h3>Pending Applications</h3>
            <p><?php echo $pending; ?></p>
        </div>

        <div class="card">
            <h3>Approved Applications</h3>
            <p><?php echo $approved; ?></p>
        </div>

        <div class="card">
            <h3>Rejected Applications</h3>
            <p><?php echo $rejected; ?></p>
        </div>
    </div>

</body>
</html>
