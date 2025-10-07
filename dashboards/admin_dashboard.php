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
        $message = "<p class='success-msg'>Application $action successfully!</p>";
    } else {
        $message = "<p class='error-msg'>Error updating status.</p>";
    }
}

$result = $conn->query("SELECT * FROM applications ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - Huduma School</title>
<style>
    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #fff8e1 25%, #ffe0b2 100%);
        background-image: radial-gradient(#ffd54f 1px, transparent 1px);
        background-size: 20px 20px;
        min-height: 100vh;
        display: flex;
    }

    /* Sidebar */
    .sidebar {
        width: 230px;
        background: #4e342e;
        color: white;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        padding: 25px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        box-shadow: 2px 0 10px rgba(0,0,0,0.2);
    }

    .sidebar h2 {
        margin: 10px 0;
        font-size: 20px;
        text-align: center;
    }

    .sidebar .icon {
        background: #ffcc80;
        color: #4e342e;
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 35px;
        margin-bottom: 15px;
        box-shadow: 0 3px 6px rgba(0,0,0,0.2);
    }

    .sidebar a {
        display: block;
        color: white;
        text-decoration: none;
        padding: 10px 0;
        text-align: center;
        width: 100%;
        border-radius: 6px;
        transition: background 0.3s;
    }

    .sidebar a:hover {
        background: #6d4c41;
    }

    /* Main Content */
    .content {
        flex: 1;
        margin-left: 230px;
        padding: 30px;
    }

    h2 {
        color: #4e342e;
    }

    .success-msg { color: green; font-weight: bold; }
    .error-msg { color: red; font-weight: bold; }

    /* Search bar */
    .search-container {
        margin: 15px 0;
        display: flex;
        justify-content: flex-end;
    }

    .search-container input {
        padding: 8px 10px;
        width: 250px;
        border-radius: 6px;
        border: 2px solid #ffb300;
        outline: none;
        transition: 0.3s;
    }

    .search-container input:focus {
        border-color: #f57c00;
        box-shadow: 0 0 5px rgba(245,124,0,0.3);
    }

    /* Table */
    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border-radius: 8px;
        overflow: hidden;
    }

    th, td {
        padding: 12px;
        text-align: center;
        border-bottom: 1px solid #eee;
    }

    th {
        background: #ffe0b2;
        color: #4e342e;
        font-weight: 600;
    }

    tr:hover {
        background-color: #fff8e1;
    }

    .badge {
        padding: 5px 10px;
        border-radius: 12px;
        font-weight: bold;
        text-transform: capitalize;
    }

    .badge.pending { background: #ffeaa7; color: #b9770e; }
    .badge.accepted { background: #c8e6c9; color: #2e7d32; }
    .badge.rejected { background: #ffcdd2; color: #c62828; }

    button {
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
    }

    .approve { background: #4caf50; }
    .reject { background: #f44336; }

    .approve:hover { background: #388e3c; }
    .reject:hover { background: #d32f2f; }

    @media(max-width: 768px) {
        .sidebar {
            width: 100%;
            height: auto;
            flex-direction: row;
            justify-content: space-between;
        }

        .content {
            margin-left: 0;
            margin-top: 120px;
        }

        table {
            font-size: 14px;
        }
    }
</style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_home.php">🏠 Home</a>
    <a href="admin_dashboard.php">📋 Applications</a>
    <a href="register_student.php">➕ Add Student</a>
    <a href="view_students.php">📚 Reports</a>
    <a href="../index.html">🚪 Logout</a>
</div>

<div class="content">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['username']); ?>!</h2>
    <?= $message; ?>

    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search by name or index number...">
    </div>

    <table id="applicationsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Index Number</th>
                <th>Marks</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['index_number']) ?></td>
                <td><?= $row['marks'] ?></td>
                <td>
                    <span class="badge <?= strtolower($row['status']); ?>">
                        <?= ucfirst($row['status']); ?>
                    </span>
                </td>
                <td>
                    <?php if ($row['status'] == 'pending'): ?>
                        <form method="POST" style="display:inline;">
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
        </tbody>
    </table>
</div>

<script>
    // Simple search functionality
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('applicationsTable');
    searchInput.addEventListener('keyup', function() {
        const filter = searchInput.value.toLowerCase();
        const rows = table.getElementsByTagName('tr');
        for (let i = 1; i < rows.length; i++) {
            const name = rows[i].cells[1].textContent.toLowerCase();
            const index = rows[i].cells[2].textContent.toLowerCase();
            rows[i].style.display = name.includes(filter) || index.includes(filter) ? '' : 'none';
        }
    });
</script>

</body>
</html>
