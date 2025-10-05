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

// Handle approval or rejection
if (isset($_POST['action']) && isset($_POST['app_id'])) {
    $app_id = intval($_POST['app_id']);
    $action = $_POST['action'] === 'approve' ? 'accepted' : 'rejected';

    $stmt = $conn->prepare("UPDATE applications SET status=? WHERE id=?");
    $stmt->bind_param("si", $action, $app_id);

    if ($stmt->execute()) {
        $message = "<p style='color:green;'>Application $action successfully!</p>";
    } else {
        $message = "<p style='color:red;'>Error updating status.</p>";
    }
}

$result = $conn->query("SELECT * FROM applications ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body { margin:0; font-family:Arial, sans-serif; background:#f8f8f8; }
        .sidebar {
            width:200px; height:100vh; background:#222; color:white; position:fixed; top:0; left:0; padding-top:20px;
        }
        .sidebar h2 { text-align:center; margin-bottom:20px; }
        .sidebar a { display:block; color:white; padding:10px; text-decoration:none; }
        .sidebar a:hover { background:#444; }
        .content { margin-left:220px; padding:20px; }
        table { width:100%; border-collapse:collapse; background:white; margin-top:15px; }
        th, td { border:1px solid #ddd; padding:10px; text-align:center; }
        form { display:inline; }
        button { padding:6px 10px; border:none; border-radius:4px; color:white; cursor:pointer; }
        .approve { background:green; }
        .reject { background:red; }
        .approve:hover { background:#006400; }
        .reject:hover { background:#b30000; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="../index.html">Logout</a>
</div>

<div class="content">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <?php echo $message; ?>

    <h3>Pending Applications</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Index Number</th>
            <th>Marks</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= htmlspecialchars($row['index_number']) ?></td>
            <td><?= $row['marks'] ?></td>
            <td><?= ucfirst($row['status']) ?></td>
            <td>
                <?php if ($row['status'] == 'pending'): ?>
                    <form method="POST">
                        <input type="hidden" name="app_id" value="<?= $row['id'] ?>">
                        <button type="submit" name="action" value="approve" class="approve">Accept</button>
                        <button type="submit" name="action" value="reject" class="reject">Reject</button>
                    </form>
                <?php else: ?>
                    <em>No action</em>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
