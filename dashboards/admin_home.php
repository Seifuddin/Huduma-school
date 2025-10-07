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

// Fetch statistics
$total_students = $conn->query("SELECT COUNT(*) AS count FROM students")->fetch_assoc()['count'];
$total_applications = $conn->query("SELECT COUNT(*) AS count FROM applications")->fetch_assoc()['count'];
$accepted = $conn->query("SELECT COUNT(*) AS count FROM applications WHERE status='accepted'")->fetch_assoc()['count'];
$pending = $conn->query("SELECT COUNT(*) AS count FROM applications WHERE status='pending'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Home - Huduma Secondary School</title>
<style>
    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #fff8e1 25%, #ffe0b2 100%);
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

    /* Main content */
    .content {
        flex: 1;
        margin-left: 230px;
        padding: 40px;
    }

    h1 {
        color: #4e342e;
        font-size: 28px;
        margin-bottom: 10px;
    }

    p {
        color: #6d4c41;
        font-size: 16px;
        margin-bottom: 40px;
    }

    /* Dashboard cards */
    .dashboard {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 25px;
    }

    .card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }

    .card h3 {
        font-size: 18px;
        color: #4e342e;
        margin-bottom: 10px;
    }

    .count {
        font-size: 28px;
        font-weight: bold;
        color: #f57c00;
    }

    .icon-box {
        font-size: 35px;
        margin-bottom: 10px;
        color: #ff9800;
    }

    /* Updates section */
    .updates {
        margin-top: 50px;
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .updates h2 {
        color: #4e342e;
        margin-bottom: 15px;
    }

    .updates ul {
        list-style: none;
        padding: 0;
    }

    .updates li {
        border-bottom: 1px solid #eee;
        padding: 10px 0;
        color: #5d4037;
    }

    .updates li:last-child {
        border-bottom: none;
    }

    @media(max-width: 768px) {
        .content {
            margin-left: 0;
            padding: 20px;
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
    <h1>Welcome, <?= htmlspecialchars($_SESSION['username']); ?> 👋</h1>
    <p>Manage Huduma Secondary School operations efficiently from your admin dashboard.</p>

    <div class="dashboard">
        <div class="card">
            <div class="icon-box">🎓</div>
            <h3>Total Students</h3>
            <p class="count" data-target="<?= $total_students; ?>">0</p>
        </div>

        <div class="card">
            <div class="icon-box">📄</div>
            <h3>Total Applications</h3>
            <p class="count" data-target="<?= $total_applications; ?>">0</p>
        </div>

        <div class="card">
            <div class="icon-box">✅</div>
            <h3>Accepted Students</h3>
            <p class="count" data-target="<?= $accepted; ?>">0</p>
        </div>

        <div class="card">
            <div class="icon-box">⏳</div>
            <h3>Pending Applications</h3>
            <p class="count" data-target="<?= $pending; ?>">0</p>
        </div>
    </div>

    <div class="updates">
        <h2>📢 Recent Updates</h2>
        <ul>
            <li>✔ New student registration form updated successfully.</li>
            <li>📈 Application approval system optimized for performance.</li>
            <li>🎉 <?= date("F j, Y"); ?> - Admin system running smoothly!</li>
        </ul>
    </div>
</div>

<script>
    // Counter animation
    const counters = document.querySelectorAll('.count');
    const speed = 200; // lower = faster

    counters.forEach(counter => {
        const animate = () => {
            const target = +counter.getAttribute('data-target');
            const count = +counter.innerText;
            const increment = Math.ceil(target / speed);

            if (count < target) {
                counter.innerText = count + increment;
                setTimeout(animate, 15);
            } else {
                counter.innerText = target;
            }
        };
        animate();
    });
</script>

</body>
</html>
