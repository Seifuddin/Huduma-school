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

$search = $_GET['search'] ?? '';
$year = $_GET['year'] ?? '';
$county = $_GET['county'] ?? '';

$sql = "SELECT * FROM students WHERE 1=1";

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $sql .= " AND (full_name LIKE '%$search%' OR parent_name LIKE '%$search%' OR index_number LIKE '%$search%')";
}

if (!empty($year)) {
    $year = $conn->real_escape_string($year);
    $sql .= " AND year_of_admission = '$year'";
}

if (!empty($county)) {
    $county = $conn->real_escape_string($county);
    $sql .= " AND county = '$county'";
}

$sql .= " ORDER BY id DESC";
$result = $conn->query($sql);

// Fetch distinct years and counties for filters
$years = $conn->query("SELECT DISTINCT year_of_admission FROM students ORDER BY year_of_admission DESC");
$counties = $conn->query("SELECT DISTINCT county FROM students ORDER BY county ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View All Students - Admin Panel</title>
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

    .content {
        flex: 1;
        margin-left: 230px;
        padding: 30px;
    }

    h2 {
        color: #4e342e;
        margin-bottom: 15px;
    }

    form.filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        background: #fff3e0;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .filter-form input,
    .filter-form select,
    .filter-form button {
        padding: 10px;
        font-size: 14px;
        border: 1px solid #ddd;
        border-radius: 6px;
    }

    .filter-form input {
        flex: 1;
    }

    .filter-form button {
        background-color: #ff9800;
        color: white;
        border: none;
        cursor: pointer;
        transition: background 0.3s;
    }

    .filter-form button:hover {
        background-color: #e68900;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border-radius: 10px;
        overflow: hidden;
    }

    th {
        background-color: #ff9800;
        color: white;
        padding: 12px;
        text-align: center;
        font-weight: bold;
    }

    td {
        padding: 10px;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }

    tr:hover {
        background-color: #fff3e0;
    }

    img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
    }

    .no-photo {
        font-style: italic;
        color: #757575;
    }

    @media (max-width: 768px) {
        .content {
            margin-left: 0;
        }

        .sidebar {
            display: none;
        }

        table {
            font-size: 12px;
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
    <h2>All Registered Students</h2>

    <form method="GET" class="filter-form">
        <input type="text" name="search" placeholder="Search by name, parent, or index..." value="<?= htmlspecialchars($search) ?>">
        <select name="year">
            <option value="">All Years</option>
            <?php while ($y = $years->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($y['year_of_admission']) ?>" <?= ($year == $y['year_of_admission']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($y['year_of_admission']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <select name="county">
            <option value="">All Counties</option>
            <?php while ($c = $counties->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($c['county']) ?>" <?= ($county == $c['county']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['county']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Filter</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Passport</th>
            <th>Full Name</th>
            <th>Parent Name</th>
            <th>Parent Phone</th>
            <th>Former School</th>
            <th>Index Number</th>
            <th>Year of Admission</th>
            <th>Date of Birth</th>
            <th>Religion</th>
            <th>County</th>
        </tr>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td>
                        <?php if ($row['passport_photo']): ?>
                            <img src="uploads/<?= htmlspecialchars($row['passport_photo']) ?>" alt="Passport">
                        <?php else: ?>
                            <span class="no-photo">No Photo</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= htmlspecialchars($row['parent_name']) ?></td>
                    <td><?= htmlspecialchars($row['parent_phone']) ?></td>
                    <td><?= htmlspecialchars($row['former_school']) ?></td>
                    <td><?= htmlspecialchars($row['index_number']) ?></td>
                    <td><?= htmlspecialchars($row['year_of_admission']) ?></td>
                    <td><?= htmlspecialchars($row['date_of_birth']) ?></td>
                    <td><?= htmlspecialchars($row['religion']) ?></td>
                    <td><?= htmlspecialchars($row['county']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="11" style="color:red;">No students found.</td></tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
