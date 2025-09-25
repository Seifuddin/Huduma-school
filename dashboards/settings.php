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

$message = "";

// Admin updates their own settings
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_admin'])) {
    $new_username = trim($_POST['username']);
    $new_password = trim($_POST['password']);
    $admin_id = $_SESSION['id']; 

    if (!empty($new_username) && !empty($new_password)) {
        $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE admin SET username=?, password=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $new_username, $hashedPassword, $admin_id);
        if ($stmt->execute()) {
            $_SESSION['username'] = $new_username;
            $message = "Admin settings updated successfully!";
        } else {
            $message = "Error updating admin settings.";
        }
    } else {
        $message = "Both fields are required.";
    }
}

// Admin changes a student password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_student_password'])) {
    $student_id = intval($_POST['student_id']);
    $new_student_password = trim($_POST['student_password']);

    if (!empty($student_id) && !empty($new_student_password)) {
        $hashedPassword = password_hash($new_student_password, PASSWORD_DEFAULT);
        $sql = "UPDATE admin SET password=? WHERE id=? AND role='student'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hashedPassword, $student_id);
        if ($stmt->execute()) {
            $message = "Student password updated successfully!";
        } else {
            $message = "Error updating student password.";
        }
    } else {
        $message = "Please select a student and enter a new password.";
    }
}

// Admin edits student details (username)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_student_details'])) {
    $student_id = intval($_POST['student_id']);
    $new_student_username = trim($_POST['student_username']);

    if (!empty($student_id) && !empty($new_student_username)) {
        $sql = "UPDATE admin SET username=? WHERE id=? AND role='student'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_student_username, $student_id);
        if ($stmt->execute()) {
            $message = "Student details updated successfully!";
        } else {
            $message = "Error updating student details.";
        }
    } else {
        $message = "Please select a student and enter a new username.";
    }
}

// Admin deletes a student account
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_student'])) {
    $student_id = intval($_POST['student_id']);
    if (!empty($student_id)) {
        $sql = "DELETE FROM admin WHERE id=? AND role='student'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $student_id);
        if ($stmt->execute()) {
            $message = "Student account deleted successfully!";
        } else {
            $message = "Error deleting student account.";
        }
    } else {
        $message = "Please select a student to delete.";
    }
}

// Fetch current admin data
$admin_id = $_SESSION['id'];
$sql = "SELECT username FROM admin WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$adminData = $result->fetch_assoc();

// Fetch students for dropdowns
$students = $conn->query("SELECT id, username FROM admin WHERE role='student'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Settings</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f4f4f4; }
        .sidebar {
            height: 100vh; width: 220px; position: fixed; top: 0; left: 0;
            background: #333; color: white; padding-top: 20px;
        }
        .sidebar h2 { text-align: center; margin-bottom: 20px; }
        .sidebar a { display: block; color: white; padding: 12px; text-decoration: none; }
        .sidebar a:hover { background: #575757; }
        .logout { background: red; text-align: center; margin-top: 20px; border-radius: 5px; }
        .content { margin-left: 240px; padding: 20px; }
        h2, h3 { margin-bottom: 15px; }
        form { background: white; padding: 20px; border-radius: 5px; width: 400px; margin-bottom: 30px; }
        label { display: block; margin: 10px 0 5px; }
        input[type="text"], input[type="password"], select {
            width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px;
        }
        button {
            padding: 10px 15px; background: green; color: white; border: none; border-radius: 4px;
        }
        .delete-btn { background: red; }
        .message { margin-top: 10px; color: blue; }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="manage_students.php">Manage Students</a>
        <a href="reports.php">Reports</a>
        <a href="settings.php">Settings</a>
        <a class="logout" href="../logout.php">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h2>Account Settings</h2>
        <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>

        <!-- Admin Settings -->
        <form method="POST">
            <h3>Update Admin Account</h3>
            <input type="hidden" name="update_admin" value="1">
            <label for="username">Change Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($adminData['username']); ?>" required>

            <label for="password">Change Password:</label>
            <input type="password" name="password" placeholder="Enter new password" required>

            <button type="submit">Update</button>
        </form>

        <!-- Change Student Password -->
        <form method="POST">
            <h3>Change Student Password</h3>
            <input type="hidden" name="update_student_password" value="1">
            <label for="student_id">Select Student:</label>
            <select name="student_id" required>
                <option value="">-- Select Student --</option>
                <?php 
                $students->data_seek(0); 
                while ($row = $students->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['username']); ?></option>
                <?php endwhile; ?>
            </select>

            <label for="student_password">New Password:</label>
            <input type="password" name="student_password" placeholder="Enter new password" required>

            <button type="submit">Update Password</button>
        </form>

        <!-- Edit Student Details -->
        <form method="POST">
            <h3>Edit Student Details</h3>
            <input type="hidden" name="update_student_details" value="1">
            <label for="student_id">Select Student:</label>
            <select name="student_id" required>
                <option value="">-- Select Student --</option>
                <?php 
                $students->data_seek(0); 
                while ($row = $students->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['username']); ?></option>
                <?php endwhile; ?>
            </select>

            <label for="student_username">New Username:</label>
            <input type="text" name="student_username" placeholder="Enter new username" required>

            <button type="submit">Update Details</button>
        </form>

        <!-- Delete Student Account -->
        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this student account? This action cannot be undone.');">
            <h3>Delete Student Account</h3>
            <input type="hidden" name="delete_student" value="1">
            <label for="student_id">Select Student:</label>
            <select name="student_id" required>
                <option value="">-- Select Student --</option>
                <?php 
                $students->data_seek(0); 
                while ($row = $students->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['username']); ?></option>
                <?php endwhile; ?>
            </select>

            <button type="submit" class="delete-btn">Delete Student</button>
        </form>

    </div>
</body>
</html>
