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

// Handle student registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_student'])) {
    $username    = $conn->real_escape_string($_POST['username']);
    $full_name   = $conn->real_escape_string($_POST['full_name']);
    $admission   = $conn->real_escape_string($_POST['admission_number']);
    $age         = $conn->real_escape_string($_POST['age']);
    $gender      = $conn->real_escape_string($_POST['gender']);
    $nationality = $conn->real_escape_string($_POST['nationality']);
    $class       = $conn->real_escape_string($_POST['class']);
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

    $sql = "INSERT INTO student (username, full_name, admission_number, age, gender, nationality, class, photo, created_at) 
            VALUES ('$username','$full_name','$admission','$age','$gender','$nationality','$class','$photo',NOW())";

    if ($conn->query($sql)) {
        $success = "Student registered successfully.";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// Fetch students
$students = $conn->query("SELECT * FROM student ORDER BY created_at DESC");
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Students</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <style>
    .student-photo {
      width: 48px;
      height: 48px;
      object-fit: cover;
      border-radius: 50%;
    }
  </style>
</head>
<body class="bg-light">
<div class="container py-4">
  <h2 class="text-center mb-4">👨‍🎓 Student Registration</h2>

  <?php if (!empty($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
  <?php elseif (!empty($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
  <?php endif; ?>

  <!-- Registration Form -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header">Add New Student</div>
    <div class="card-body">
      <form method="POST" enctype="multipart/form-data">
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" required>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Admission No.</label>
            <input type="text" name="admission_number" class="form-control" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Age</label>
            <input type="number" name="age" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-select">
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
            <input type="text" name="nationality" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Class</label>
            <input type="text" name="class" class="form-control">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Photo</label>
          <input type="file" name="photo" class="form-control">
        </div>

        <button type="submit" name="register_student" class="btn btn-primary">Register Student</button>
      </form>
    </div>
  </div>

  <!-- Students Table -->
  <div class="card shadow-sm">
    <div class="card-header">Registered Students</div>
    <div class="card-body table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-primary">
          <tr>
            <th>ID</th>
            <th>Photo</th>
            <th>Username</th>
            <th>Full Name</th>
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
                    <img src="uploads/<?php echo htmlspecialchars($student['photo']); ?>" class="student-photo" alt="photo">
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
              <td colspan="10" class="text-center text-muted">No students registered yet.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
