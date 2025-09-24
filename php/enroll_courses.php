<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'student') {
    header("Location: login.html");
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

// Handle enrollment
if (isset($_POST['enroll'])) {
    $course_id = intval($_POST['course_id']);
    $stmt = $conn->prepare("INSERT INTO student_courses (student_username, course_id) VALUES (?, ?)");
    $stmt->bind_param("si", $username, $course_id);
    $stmt->execute();
    $stmt->close();
}

// Handle dropping
if (isset($_POST['drop'])) {
    $course_id = intval($_POST['course_id']);
    $stmt = $conn->prepare("DELETE FROM student_courses WHERE student_username=? AND course_id=?");
    $stmt->bind_param("si", $username, $course_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch all available courses
$all_courses = $conn->query("SELECT * FROM courses ORDER BY course_code ASC");

// Fetch enrolled courses
$stmt = $conn->prepare("SELECT c.id, c.course_code, c.course_name, c.instructor, sc.status 
                        FROM student_courses sc 
                        JOIN courses c ON sc.course_id = c.id 
                        WHERE sc.student_username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$enrolled_courses = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Course Enrollment</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f4f6f9;
      color: #333;
      display: flex;
    }
    .sidebar {
      width: 240px;
      background: #2c3e50;
      color: #fff;
      height: 100vh;
      padding: 20px 0;
      position: fixed;
    }
    .sidebar h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    .sidebar ul {
      list-style: none;
      padding: 0;
    }
    .sidebar ul li {
      margin: 15px 0;
    }
    .sidebar ul li a {
      color: #ddd;
      text-decoration: none;
      padding: 10px 20px;
      display: flex;
      align-items: center;
      transition: 0.3s;
    }
    .sidebar ul li a:hover {
      background: #34495e;
      border-radius: 6px;
    }
    .sidebar ul li i {
      margin-right: 10px;
    }
    .main {
      margin-left: 240px;
      padding: 20px;
      width: calc(100% - 240px);
    }
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    .header h1 {
      font-size: 1.6rem;
    }
    .section {
      margin-top: 20px;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 12px;
      text-align: left;
    }
    th {
      background: #2c3e50;
      color: white;
    }
    tr:nth-child(even) {
      background: #f9f9f9;
    }
    button {
      padding: 6px 12px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: 0.3s;
    }
    .enroll-btn {
      background: #27ae60;
      color: white;
    }
    .enroll-btn:hover {
      background: #1e8449;
    }
    .drop-btn {
      background: #e74c3c;
      color: white;
    }
    .drop-btn:hover {
      background: #c0392b;
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <h2>Student Panel</h2>
    <ul>
      <li><a href="student_dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
      <li><a href="enroll_courses.php"><i class="fas fa-book"></i> Enroll Courses</a></li>
      <li>
        <form action="logout.php" method="POST" style="margin:0;">
          <button type="submit" style="background:none;border:none;color:#ddd;padding:15px 20px;width:100%;text-align:left;cursor:pointer;">
            <i class="fas fa-sign-out-alt"></i> Logout
          </button>
        </form>
      </li>
    </ul>
  </aside>

  <!-- Main -->
  <div class="main">
    <div class="header">
      <h1>Course Enrollment</h1>
    </div>

    <!-- Enrolled Courses -->
    <div class="section">
      <h2>My Enrolled Courses</h2>
      <table>
        <thead>
          <tr>
            <th>Course Code</th>
            <th>Course Name</th>
            <th>Instructor</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($enrolled_courses->num_rows > 0): ?>
            <?php while ($course = $enrolled_courses->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                <td><?php echo htmlspecialchars($course['instructor']); ?></td>
                <td><?php echo htmlspecialchars($course['status']); ?></td>
                <td>
                  <form method="POST">
                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                    <button type="submit" name="drop" class="drop-btn">Drop</button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="5">You have not enrolled in any courses yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Available Courses -->
    <div class="section">
      <h2>Available Courses</h2>
      <table>
        <thead>
          <tr>
            <th>Course Code</th>
            <th>Course Name</th>
            <th>Instructor</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($all_courses->num_rows > 0): ?>
            <?php while ($course = $all_courses->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                <td><?php echo htmlspecialchars($course['instructor']); ?></td>
                <td>
                  <form method="POST">
                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                    <button type="submit" name="enroll" class="enroll-btn">Enroll</button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="4">No courses available.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
