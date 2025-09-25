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

$username = $_SESSION['username'];

// Get student ID
$sql = "SELECT id FROM admin WHERE username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$student_id = $student['id'];

// Get application history
$sql = "SELECT id, school_name, status FROM application WHERE student_id=? ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$applications = $stmt->get_result();

// Latest status
$latest_status = "No application yet.";
if ($applications->num_rows > 0) {
    $row = $applications->fetch_assoc();
    $latest_status = $row['status'];
}

// Handle new application submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['apply'])) {
    if ($latest_status === "pending" || $latest_status === "approved") {
        $message = "<p style='color:red;'>You cannot apply again while your application is $latest_status.</p>";
    } else {
        $sql = "INSERT INTO application (student_id) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $message = "<p style='color:green;'>Application submitted successfully!</p>";
        header("Location: student_dashboard.php");
        exit();
    }
}

// Handle password change
$passMessage = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $new_password = trim($_POST['new_password']);
    if (!empty($new_password)) {
        $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE admin SET password=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hashedPassword, $student_id);
        if ($stmt->execute()) {
            $passMessage = "<p style='color:green;'>Password updated successfully!</p>";
        } else {
            $passMessage = "<p style='color:red;'>Error updating password.</p>";
        }
    } else {
        $passMessage = "<p style='color:red;'>Password cannot be empty.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <style>
        body { margin:0; font-family: Arial, sans-serif; background:#f4f4f4; }
        .sidebar {
            width: 200px; height:100vh; background:#333; color:white; position:fixed; top:0; left:0; padding-top:20px;
        }
        .sidebar h2 { text-align:center; margin-bottom:20px; }
        .sidebar a {
            display:block; color:white; padding:12px; text-decoration:none; margin:4px 0;
        }
        .sidebar a:hover { background:#575757; }
        .content { margin-left:220px; padding:20px; }
        .message { margin:10px 0; }
        table { width:100%; border-collapse:collapse; margin-top:15px; background:white; }
        th, td { padding:10px; border:1px solid #ddd; text-align:center; }
        button { padding:10px 15px; background:blue; color:white; border:none; cursor:pointer; border-radius:4px; }
        form { margin-top:20px; background:white; padding:15px; border-radius:8px; width:350px; }
        input[type="password"] {
            width:100%; padding:8px; margin-bottom:10px; border:1px solid #ddd; border-radius:4px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Student Panel</h2>
    <a href="student_dashboard.php">Dashboard</a>
    <a href="#">Register</a>
    <a href="logout.php">Logout</a>
</div>

<div class="content">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <p><strong>Latest Application Status:</strong> <?php echo ucfirst($latest_status); ?></p>

    <?php echo $message; ?>

    <?php if ($latest_status === "No application yet." || $latest_status === "rejected") { ?>
        <form method="post">
            <button type="submit" name="apply">Apply Now</button>
        </form>
    <?php } ?>

    <h3>Your Application History</h3>
    <table>
        <tr>
            <th>Application ID</th>
            <th>School</th>
            <th>Status</th>
        </tr>
        <?php
        $applications->data_seek(0);
        if ($applications->num_rows > 0) {
            while($row = $applications->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['school_name']}</td>
                        <td>".ucfirst($row['status'])."</td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No applications yet.</td></tr>";
        }
        ?>
    </table>

    <h3>Change Password</h3>
    <?php echo $passMessage; ?>
    <form method="post">
        <label>New Password:</label>
        <input type="password" name="new_password" required>
        <button type="submit" name="change_password">Update Password</button>
    </form>
</div>

</body>
</html>
