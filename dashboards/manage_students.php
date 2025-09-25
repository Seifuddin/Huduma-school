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

// Handle delete student
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM admin WHERE id=? AND role='student'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Handle search filter
$search = "";
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = trim($_GET['search']);
    $searchLike = "%$search%";
    $sql = "SELECT id, username FROM admin WHERE role='student' AND username LIKE ? ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $searchLike);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT id, username FROM admin WHERE role='student' ORDER BY id DESC";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Students</title>
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
        table { 
            width: 100%; 
            border-collapse: collapse; 
            background: white; 
            margin-top: 15px; 
        }
        th, td { 
            padding: 10px; 
            border: 1px solid #ddd; 
            text-align: center; 
        }
        a.action { 
            padding: 5px 10px; 
            text-decoration: none; 
            border-radius: 4px; 
        }
        .delete { background: red; color: white; }
        h2 { margin-bottom: 10px; }
        .search-box { margin-bottom: 15px; }
        .search-box input[type="text"] { padding: 8px; width: 250px; }
        .search-box button { padding: 8px 12px; }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="manage_students.php">Manage Students</a>
        <a href="reports.php">Reports</a>
        <a href="#">Settings</a>
        <a class="logout" href="../logout.php">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h2>Manage Students</h2>

        <!-- Search box -->
        <form method="GET" class="search-box">
            <input type="text" name="search" placeholder="Search by username" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
            <a href="manage_students.php" style="padding:8px 12px; background:#555; color:white; text-decoration:none; border-radius:4px;">Reset</a>
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Student Username</th>
                <th>Action</th>
            </tr>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td>
                        <a class="action delete" href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="3">No students found.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
