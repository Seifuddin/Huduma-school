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
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch messages
$messages = $conn->query("SELECT * FROM messages ORDER BY created_at DESC");

// Fetch students (all requested fields)
$students = $conn->query("SELECT id, username, full_name, admission_number, age, gender, nationality, photo, class, created_at FROM student ORDER BY created_at DESC");

// Fetch statistics
$total_students = $conn->query("SELECT COUNT(*) AS total FROM student")->fetch_assoc()['total'] ?? 0;
$total_messages = $conn->query("SELECT COUNT(*) AS total FROM messages")->fetch_assoc()['total'] ?? 0;

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      background: #f8f9fa;
      font-family: Arial, sans-serif;
      display: flex;
    }
    /* Sidebar */
    .sidebar {
      width: 240px;
      height: 100vh;
      background: #343a40;
      color: #fff;
      position: fixed;
      top: 0; left: 0;
      padding-top: 20px;
    }
    .sidebar h2 {
      text-align: center;
      font-size: 1.2rem;
      margin-bottom: 30px;
      color: #0d6efd;
    }
    .sidebar ul {
      list-style: none;
      padding: 0;
    }
    .sidebar ul li {
      margin: 15px 0;
    }
    .sidebar ul li a, .sidebar ul li form button {
      display: block;
      padding: 12px 20px;
      color: #ddd;
      text-decoration: none;
      transition: 0.3s;
      border: none;
      background: none;
      width: 100%;
      text-align: left;
    }
    .sidebar ul li a:hover, .sidebar ul li form button:hover {
      background: #0d6efd;
      color: #fff;
    }
    .sidebar ul li i {
      margin-right: 10px;
    }
    /* Main content */
    .main {
      margin-left: 240px;
      padding: 20px;
      flex: 1;
    }
    .card {
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    .modal-header {
      background: #0d6efd;
      color: white;
    }
    .table thead {
      background: #0d6efd;
      color: white;
    }
    .student-photo {
      width: 48px;
      height: 48px;
      object-fit: cover;
      border-radius: 50%;
    }
    @media (max-width: 768px) {
      .sidebar { width: 70px; }
      .sidebar h2, .sidebar ul li span { display: none; }
      .main { margin-left: 70px; }
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <h2>Admin Panel</h2>
    <ul>
      <li><a href="#"><i class="fas fa-home"></i><span> Dashboard</span></a></li>
      <li><a href="students.php"><i class="fas fa-user-graduate"></i><span> Students</span></a></li>
      <li><a href="#"><i class="fas fa-book"></i><span> Courses</span></a></li>
      <li><a href="teachers.php"><i class="fas fa-chalkboard-teacher"></i><span> Teachers</span></a></li>
      <li><a href="#"><i class="fas fa-envelope"></i><span> Messages</span></a></li>
      <li><a href="#"><i class="fas fa-cog"></i><span> Settings</span></a></li>
      <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span> Logout</span></a></li>
    </ul>
  </aside>

  <!-- Main Content -->
  <div class="main">
    <h1 class="mb-4 text-center">📊 Admin Dashboard</h1>

    <!-- Stats -->
    <div class="row mb-4">
      <div class="col-md-6">
        <div class="card text-center p-3">
          <h4>Total Students</h4>
          <p class="fs-3 fw-bold text-primary"><?php echo (int)$total_students; ?></p>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card text-center p-3">
          <h4>Total Messages</h4>
          <p class="fs-3 fw-bold text-success"><?php echo (int)$total_messages; ?></p>
        </div>
      </div>
    </div>

    <!-- Students -->
    <div class="card mb-4">
      <div class="card-header fw-bold">👩‍🎓 Registered Students</div>
      <div class="card-body">
        <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Photo</th>
              <th>Username</th>
              <th>Fullname</th>
              <th>Admission No.</th>
              <th>Age</th>
              <th>Gender</th>
              <th>Nationality</th>
              <th>Class</th>
              <th>Joined</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($students && $students->num_rows > 0): ?>
              <?php while($student = $students->fetch_assoc()): ?>
                <tr>
                  <td><?php echo (int)$student['id']; ?></td>
                  <td>
                    <?php if (!empty($student['photo']) && file_exists(__DIR__ . '/uploads/' . $student['photo'])): ?>
                      <img src="/pages/uploads/<?php echo htmlspecialchars($student['photo']); ?>" alt="photo" class="student-photo">
                    <?php elseif (!empty($student['photo'])): ?>
                      <!-- if uploads/ isn't in pages folder, fallback to relative path -->
                      <img src="uploads/<?php echo htmlspecialchars($student['photo']); ?>" alt="photo" class="student-photo">
                    <?php else: ?>
                      <span class="text-muted small">No Photo</span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo htmlspecialchars($student['username']); ?></td>
                  <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                  <td><?php echo htmlspecialchars($student['admission_number']); ?></td>
                  <td><?php echo htmlspecialchars($student['age']); ?></td>
                  <td><?php echo htmlspecialchars($student['gender']); ?></td>
                  <td><?php echo htmlspecialchars($student['nationality']); ?></td>
                  <td><?php echo htmlspecialchars($student['class']); ?></td>
                  <td><?php echo htmlspecialchars($student['created_at']); ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="10" class="text-center text-muted">No students found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
        </div>
      </div>
    </div>

    <!-- Messages -->
    <div class="card">
      <div class="card-header fw-bold">✉️ Student Messages</div>
      <div class="card-body">
        <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Sender</th>
              <th>Email</th>
              <th>Subject</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          <?php if ($messages && $messages->num_rows > 0): ?>
            <?php while($msg = $messages->fetch_assoc()): ?>
              <tr>
                <td><?php echo (int)$msg['id']; ?></td>
                <td><?php echo htmlspecialchars($msg['sender_name']); ?></td>
                <td><?php echo htmlspecialchars($msg['sender_email']); ?></td>
                <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                <td><?php echo htmlspecialchars($msg['created_at']); ?></td>
                <td>
                  <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewMsg<?php echo (int)$msg['id']; ?>">View</button>
                  <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteMsg<?php echo (int)$msg['id']; ?>">Delete</button>
                </td>
              </tr>

              <!-- View Modal -->
              <div class="modal fade" id="viewMsg<?php echo (int)$msg['id']; ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Message from <?php echo htmlspecialchars($msg['sender_name']); ?></h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <p><strong>Email:</strong> <?php echo htmlspecialchars($msg['sender_email']); ?></p>
                      <p><strong>Subject:</strong> <?php echo htmlspecialchars($msg['subject']); ?></p>
                      <p><strong>Message:</strong></p>
                      <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                      <p><small>Sent at: <?php echo htmlspecialchars($msg['created_at']); ?></small></p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Delete Modal -->
              <div class="modal fade" id="deleteMsg<?php echo (int)$msg['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title text-danger">Delete Message?</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      Are you sure you want to delete this message from <strong><?php echo htmlspecialchars($msg['sender_name']); ?></strong>?
                    </div>
                    <div class="modal-footer">
                      <form method="POST" action="delete_message.php">
                        <input type="hidden" name="id" value="<?php echo (int)$msg['id']; ?>">
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                      </form>
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                  </div>
                </div>
              </div>

            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center text-muted">No messages found.</td>
            </tr>
          <?php endif; ?>
          </tbody>
        </table>
        </div>
      </div>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
