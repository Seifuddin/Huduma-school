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

// Get student ID from the admin table
$sql = "SELECT id FROM admin WHERE username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$student_id = $student['id'] ?? null;

$message = "";
$passMessage = "";

// Handle new application submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['apply'])) {
    $full_name = trim($_POST['full_name']);
    $index_number = trim($_POST['index_number']);
    $marks = intval($_POST['marks']);

    if (!empty($full_name) && !empty($index_number) && $marks > 0) {
        $stmt = $conn->prepare("INSERT INTO applications (full_name, index_number, marks) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $full_name, $index_number, $marks);
        if ($stmt->execute()) {
            $message = "<p style='color:green;'>Application submitted successfully!</p>";
        } else {
            $message = "<p style='color:red;'>Error submitting application.</p>";
        }
    } else {
        $message = "<p style='color:red;'>All fields are required.</p>";
    }
}

// Handle password change
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

// Fetch all applications (for history)
$result = $conn->query("SELECT * FROM applications ORDER BY id DESC");
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
        table { width:100%; border-collapse:collapse; margin-top:15px; background:white; }
        th, td { padding:10px; border:1px solid #ddd; text-align:center; }
        form { margin-top:20px; background:white; padding:15px; border-radius:8px; width:400px; }
        input[type="text"], input[type="number"], input[type="password"] {
            width:100%; padding:8px; margin-bottom:10px; border:1px solid #ddd; border-radius:4px;
        }
        button {
            padding:10px 15px; background:blue; color:white; border:none; cursor:pointer; border-radius:4px;
        }
        button:hover { background:#0056b3; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Student Panel</h2>
    <a href="student_dashboard.php">Dashboard</a>
    <a href="../check_status.php">Check Status</a>
    <a href="../index.html">Logout</a>
</div>

<div class="content">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>

    <?php echo $message; ?>

    <!-- Application Form -->
    <h3>Submit Application</h3>
    <form method="POST">
        <label>Full Name:</label>
        <input type="text" name="full_name" required>

        <label>Index Number:</label>
        <input type="text" name="index_number" required>

        <label>Marks:</label>
        <input type="number" name="marks" required>

        <button type="submit" name="apply">Submit Application</button>
    </form>

    <!-- Application History -->
    <h3>Your Application History</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Index Number</th>
            <th>Marks</th>
            <th>Status</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['full_name']}</td>
                        <td>{$row['index_number']}</td>
                        <td>{$row['marks']}</td>
                        <td>".ucfirst($row['status'])."</td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No applications yet.</td></tr>";
        }
        ?>
    </table>

    <!-- Password Update -->
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
