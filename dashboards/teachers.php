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

// Handle teacher registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_teacher'])) {
    $full_name   = $conn->real_escape_string($_POST['full_name']);
    $phone       = $conn->real_escape_string($_POST['phone']);
    $email       = $conn->real_escape_string($_POST['email']);
    $gender      = $conn->real_escape_string($_POST['gender']);
    $nationality = $conn->real_escape_string($_POST['nationality']);
    $photo       = null;

    // Handle photo upload
    if (!empty($_FILES['photo']['name'])) {
        $targetDir = __DIR__ . "/uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES["photo"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFilePath)) {
            $photo = $fileName;
        }
    }

    $sql = "INSERT INTO teachers (full_name, phone, email, gender, nationality, photo, created_at) 
            VALUES ('$full_name','$phone','$email','$gender','$nationality','$photo',NOW())";

    if ($conn->query($sql)) {
        $success = "Teacher registered successfully.";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// Fetch teachers
$teachers = $conn->query("SELECT * FROM teachers ORDER BY created_at DESC");
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Teachers Management</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <style>
    .teacher-photo {
      width: 48px;
      height: 48px;
      object-fit: cover;
      border-radius: 50%;
    }
  </style>
</head>
<body class="p-4">
  <div class="container">
    <h1 class="mb-4 text-center">👨‍🏫 Teacher Registration</h1>

    <?php if (!empty($success)): ?>
      <div class="alert alert-success"><?php echo $success; ?></div>
    <?php elseif (!empty($error)): ?>
      <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Registration Form -->
    <div class="card mb-4">
      <div class="card-header">Add New Teacher</div>
      <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Full Name</label>
              <input type="text" name="full_name" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone Number</label>
              <input type="text" name="phone" class="form-control" required>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Gender</label>
              <select name="gender" class="form-select" required>
                <option value="">Select</option>
                <option>Male</option>
                <option>Female</option>
                <option>Other</option>
              </select>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Nationality</label>
              <input type="text" name="nationality" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Photo</label>
              <input type="file" name="photo" class="form-control">
            </div>
          </div>

          <button type="submit" name="register_teacher" class="btn btn-primary">Register Teacher</button>
        </form>
      </div>
    </div>

    <!-- Teachers List -->
    <div class="card">
      <div class="card-header">Registered Teachers</div>
      <div class="card-body table-responsive">
        <table class="table table-bordered table-hover align-middle">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Photo</th>
              <th>Full Name</th>
              <th>Phone</th>
              <th>Email</th>
              <th>Gender</th>
              <th>Nationality</th>
              <th>Joined</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($teachers && $teachers->num_rows > 0): ?>
              <?php while($teacher = $teachers->fetch_assoc()): ?>
                <tr>
                  <td><?php echo (int)$teacher['id']; ?></td>
                  <td>
                    <?php if (!empty($teacher['photo']) && file_exists(__DIR__ . '/uploads/' . $teacher['photo'])): ?>
                      <img src="uploads/<?php echo htmlspecialchars($teacher['photo']); ?>" class="teacher-photo" alt="photo">
                    <?php else: ?>
                      <span class="text-muted small">No Photo</span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo htmlspecialchars($teacher['full_name']); ?></td>
                  <td><?php echo htmlspecialchars($teacher['phone']); ?></td>
                  <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                  <td><?php echo htmlspecialchars($teacher['gender']); ?></td>
                  <td><?php echo htmlspecialchars($teacher['nationality']); ?></td>
                  <td><?php echo htmlspecialchars($teacher['created_at']); ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="8" class="text-center text-muted">No teachers registered yet.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
