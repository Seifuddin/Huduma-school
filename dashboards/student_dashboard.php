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

// DB Connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];

// Fetch student details
$sql = "SELECT * FROM student WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

// Fetch courses (linked by student_id)
$sql_courses = "SELECT * FROM courses WHERE student_id = ?";
$stmt2 = $conn->prepare($sql_courses);
$stmt2->bind_param("i", $student['id']);
$stmt2->execute();
$courses = $stmt2->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    /* RESET */
    * {margin:0;padding:0;box-sizing:border-box;font-family:"Segoe UI",sans-serif;}
    body {display:flex;min-height:100vh;background:#f4f6f9;color:#333;}
    a {text-decoration:none;color:inherit;}

    /* SIDEBAR */
    .sidebar {
      width:250px;background:#2c3e50;color:#fff;position:fixed;top:0;left:0;bottom:0;
      display:flex;flex-direction:column;transition:width .3s;
    }
    .sidebar h2 {text-align:center;padding:20px;background:#1a252f;font-size:1.2rem;}
    .sidebar ul {list-style:none;flex:1;}
    .sidebar ul li {border-bottom:1px solid rgba(255,255,255,.1);}
    .sidebar ul li a, .sidebar ul li button {
      display:block;padding:15px 20px;color:#ddd;transition:.3s;width:100%;text-align:left;
      background:none;border:none;cursor:pointer;font-size:1rem;
    }
    .sidebar ul li a:hover, .sidebar ul li button:hover {background:#1abc9c;color:#fff;}
    .sidebar ul li i {margin-right:10px;}

    /* MAIN */
    .main {margin-left:250px;padding:20px;width:100%;transition:margin-left .3s;}
    .header {
      display:flex;justify-content:space-between;align-items:center;background:#fff;
      padding:15px 20px;border-radius:10px;margin-bottom:20px;
      box-shadow:0 2px 6px rgba(0,0,0,.1);
    }
    .header h1 {font-size:1.5rem;}
    .user-info {font-weight:bold;color:#1abc9c;}

    /* CARDS */
    .cards {
      display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
      gap:20px;margin-bottom:30px;
    }
    .card {
      background:#fff;padding:20px;border-radius:12px;text-align:center;
      box-shadow:0 2px 6px rgba(0,0,0,.1);
    }
    .card i {font-size:2rem;color:#1abc9c;margin-bottom:10px;}
    .card h3 {margin:10px 0;}

    /* SECTIONS */
    .section {margin-top:30px;}
    .section h2 {margin-bottom:15px;font-size:1.2rem;color:#2c3e50;}
    table {
      width:100%;border-collapse:collapse;background:#fff;border-radius:12px;
      overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,.1);
    }
    table th, table td {padding:12px 15px;text-align:left;}
    table th {background:#1abc9c;color:#fff;}
    table tr:nth-child(even) {background:#f9f9f9;}

    /* RESPONSIVE */
    @media (max-width:768px) {
      .sidebar {width:60px;}
      .sidebar h2, .sidebar ul li span {display:none;}
      .main {margin-left:60px;}
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <h2>Student Panel</h2>
    <ul>
      <li><a href="#"><i class="fas fa-home"></i><span> Dashboard</span></a></li>
      <li><a href="#"><i class="fas fa-user"></i><span> Profile</span></a></li>
      <li><a href="#"><i class="fas fa-book"></i><span> Courses</span></a></li>
      <li><a href="#"><i class="fas fa-file-alt"></i><span> Admission</span></a></li>
      <li><a href="#"><i class="fas fa-envelope"></i><span> Messages</span></a></li>
      <li><a href="#"><i class="fas fa-cog"></i><span> Settings</span></a></li>
      <li>
        <form action="logout.php" method="POST" style="margin:0;">
          <button type="submit"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></button>
        </form>
      </li>
    </ul>
  </aside>

  <!-- Main -->
  <div class="main">
    <!-- Header -->
    <div class="header">
      <h1>Welcome, <span class="user-info"><?php echo htmlspecialchars($student['fullname']); ?></span></h1>
      <p>Student Dashboard</p>
    </div>

    <!-- Cards -->
    <div class="cards">
      <div class="card">
        <i class="fas fa-check-circle"></i>
        <h3>Admission Status</h3>
        <p><?php echo htmlspecialchars($student['admission_status']); ?></p>
      </div>
      <div class="card">
        <i class="fas fa-calendar-alt"></i>
        <h3>Upcoming Events</h3>
        <p>Orientation - Oct 10, 2025</p>
      </div>
      <div class="card">
        <i class="fas fa-book-open"></i>
        <h3>Enrolled Courses</h3>
        <p><?php echo $courses->num_rows; ?> Active</p>
      </div>
      <div class="card">
        <i class="fas fa-bell"></i>
        <h3>Notifications</h3>
        <p>2 New</p>
      </div>
    </div>

    <!-- Profile -->
    <div class="section">
      <h2>Profile Summary</h2>
      <table>
        <tr><th>Full Name</th><td><?php echo htmlspecialchars($student['fullname']); ?></td></tr>
        <tr><th>Username</th><td><?php echo htmlspecialchars($student['username']); ?></td></tr>
        <tr><th>Email</th><td><?php echo htmlspecialchars($student['email']); ?></td></tr>
        <tr><th>Program</th><td><?php echo htmlspecialchars($student['program']); ?></td></tr>
        <tr><th>Year</th><td><?php echo htmlspecialchars($student['year']); ?></td></tr>
      </table>
    </div>

    <!-- Courses -->
    <div class="section">
      <h2>My Courses</h2>
      <table>
        <thead>
          <tr>
            <th>Course Code</th>
            <th>Course Name</th>
            <th>Instructor</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($courses->num_rows > 0): ?>
            <?php while ($course = $courses->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                <td><?php echo htmlspecialchars($course['instructor']); ?></td>
                <td><?php echo htmlspecialchars($course['status']); ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="4">No courses enrolled yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
<?php
$stmt->close();
$stmt2->close();
$conn->close();
?>
