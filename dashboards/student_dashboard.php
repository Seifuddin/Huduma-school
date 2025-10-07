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

// Get student info
$sql = "SELECT id FROM admin WHERE username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$student_id = $student['id'] ?? "N/A";

// Generate initials
$initials = "";
if (!empty($username)) {
    $parts = explode(" ", $username);
    foreach ($parts as $p) {
        $initials .= strtoupper(substr($p, 0, 1));
    }
}

// Handle messages
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
            $message = "<p class='success'>Application submitted successfully!</p>";
        } else {
            $message = "<p class='error'>Error submitting application.</p>";
        }
    } else {
        $message = "<p class='error'>All fields are required.</p>";
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
            $passMessage = "<p class='success'>Password updated successfully!</p>";
        } else {
            $passMessage = "<p class='error'>Error updating password.</p>";
        }
    } else {
        $passMessage = "<p class='error'>Password cannot be empty.</p>";
    }
}

$result = $conn->query("SELECT * FROM applications ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #fffbeb;
        background-image: radial-gradient(rgba(245, 181, 79, 0.4) 1px, transparent 1px);
        background-size: 40px 40px;
        animation: moveBg 20s linear infinite;
    }

    @keyframes moveBg {
        from { background-position: 0 0; }
        to { background-position: 100px 100px; }
    }

    .sidebar {
        width: 230px;
        height: 100vh;
        background: #1e293b;
        color: white;
        position: fixed;
        top: 0;
        left: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-top: 25px;
        box-shadow: 3px 0 10px rgba(0,0,0,0.1);
    }

    .profile-icon {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: #facc15;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: bold;
        color: #1e293b;
        margin-bottom: 10px;
        border: 3px solid white;
        box-shadow: 0 0 15px rgba(0,0,0,0.2);
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .profile-icon:hover {
        transform: scale(1.05);
    }

    .sidebar h2 {
        font-size: 20px;
        margin-bottom: 25px;
        text-align: center;
        letter-spacing: 1px;
    }

    .sidebar a {
        color: white;
        text-decoration: none;
        background: rgba(255,255,255,0.1);
        padding: 12px 20px;
        width: 80%;
        text-align: center;
        border-radius: 8px;
        margin: 6px 0;
        transition: all 0.3s ease;
    }

    .sidebar a:hover {
        background: #fbbf24;
        color: #1e293b;
        transform: scale(1.05);
        font-weight: 600;
    }

    .content {
        margin-left: 250px;
        padding: 40px;
        max-width: 1100px;
    }

    h2 { color: #1e293b; margin-bottom: 10px; }
    h3 { color: #1e40af; margin-top: 30px; }

    .card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        margin-bottom: 25px;
    }

    form input[type="text"],
    form input[type="number"],
    form input[type="password"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
    }

    form button {
        background: #1e40af;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
    }

    form button:hover {
        background: #1d4ed8;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        margin-top: 15px;
    }

    th, td {
        border: 1px solid #e5e7eb;
        padding: 12px;
        text-align: center;
        font-size: 14px;
    }

    th {
        background: #facc15;
        color: #1e293b;
        font-weight: bold;
    }

    tr:nth-child(even) { background-color: #fff7e6; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }

    /* Popup styling */
    .profile-popup {
        display: none;
        position: absolute;
        top: 120px;
        left: 20px;
        background: white;
        color: #1e293b;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        padding: 15px;
        width: 190px;
        animation: fadeIn 0.3s ease;
        z-index: 100;
    }

    .profile-popup.active {
        display: block;
    }

    .profile-popup h4 {
        margin: 5px 0;
        font-size: 15px;
        color: #1e40af;
    }

    .profile-popup p {
        margin: 2px 0 8px;
        font-size: 14px;
    }

    .profile-popup a {
        display: block;
        text-decoration: none;
        color: #1e40af;
        padding: 6px 0;
        transition: color 0.3s;
    }

    .profile-popup a:hover {
        color: #f59e0b;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
</head>
<body>

<div class="sidebar">
    <div class="profile-icon" id="profileIcon"><?php echo $initials ?: "U"; ?></div>

    <div class="profile-popup" id="profilePopup">
        <h4>👤 Profile Info</h4>
        <p><strong>User:</strong> <?php echo htmlspecialchars($username); ?></p>
        <p><strong>ID:</strong> <?php echo htmlspecialchars($student_id); ?></p>
        <a href="#changePassword">Change Password</a>
        <a href="../index.html">Logout</a>
    </div>

    <h2>Student Panel</h2>
    <a href="student_dashboard.php">Dashboard</a>
    <a href="../check_status.php">Check Status</a>
    <a href="../index.html">Logout</a>
</div>

<div class="content">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <?php echo $message; ?>

    <div class="card">
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
    </div>

    <div class="card">
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
    </div>

    <div class="card" id="changePassword">
        <h3>Change Password</h3>
        <?php echo $passMessage; ?>
        <form method="post">
            <label>New Password:</label>
            <input type="password" name="new_password" required>
            <button type="submit" name="change_password">Update Password</button>
        </form>
    </div>
</div>

<script>
    const profileIcon = document.getElementById('profileIcon');
    const profilePopup = document.getElementById('profilePopup');

    profileIcon.addEventListener('click', () => {
        profilePopup.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
        if (!profileIcon.contains(e.target) && !profilePopup.contains(e.target)) {
            profilePopup.classList.remove('active');
        }
    });
</script>

</body>
</html>
