<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="css/dashboard.css" />
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <h2 class="logo">School Admin</h2>
    <ul class="menu">
      <li class="active">Dashboard</li>
      <li>Students</li>
      <li>Classes</li>
      <li>Reports</li>
      <li>Settings</li>
      <li><a href="php/logout.php" class="logout">Logout</a></li>
    </ul>
  </aside>

  <!-- Main Content -->
  <main class="main">
    <!-- Top Navbar -->
    <header class="navbar">
      <h1>Welcome, <?php echo $_SESSION["admin"]; ?> 👋</h1>
    </header>

    <!-- Dashboard Cards -->
    <section class="cards">
      <div class="card">
        <h3>Total Students</h3>
        <p>325</p>
      </div>
      <div class="card">
        <h3>Classes</h3>
        <p>12</p>
      </div>
      <div class="card">
        <h3>Teachers</h3>
        <p>28</p>
      </div>
      <div class="card">
        <h3>Attendance Today</h3>
        <p>287</p>
      </div>
    </section>

    <!-- Student Table -->
    <section class="table-section">
      <h2>Recent Students</h2>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Class</th>
            <th>Age</th>
            <th>Guardian</th>
            <th>Contact</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1001</td>
            <td>Mary John</td>
            <td>Grade 6</td>
            <td>12</td>
            <td>Peter John</td>
            <td>0712345678</td>
          </tr>
          <tr>
            <td>1002</td>
            <td>James Kariuki</td>
            <td>Grade 8</td>
            <td>14</td>
            <td>Grace Wanjiru</td>
            <td>0798765432</td>
          </tr>
          <tr>
            <td>1003</td>
            <td>Amina Hassan</td>
            <td>Grade 7</td>
            <td>13</td>
            <td>Ali Hassan</td>
            <td>0700111222</td>
          </tr>
        </tbody>
      </table>
    </section>
  </main>

  <script src="js/dashboard.js"></script>
</body>
</html>
