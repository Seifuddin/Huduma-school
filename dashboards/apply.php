<?php
session_start();
if (!isset($_SESSION['student_id'])) {
  header("Location: login.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = new mysqli("localhost", "root", "", "huduma");

    $student_id = $_SESSION['student_id'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $course = $_POST['course'];

    $stmt = $conn->prepare("INSERT INTO applications (student_id, full_name, email, course) VALUES (?,?,?,?)");
    $stmt->bind_param("isss", $student_id, $full_name, $email, $course);
    $stmt->execute();

    echo "<script>alert('Application submitted successfully!'); window.location='student_dashboard.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Apply for School</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
  <h2>School Application Form</h2>
  <form method="POST">
    <div class="mb-3">
      <label>Full Name</label>
      <input type="text" name="full_name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Course</label>
      <input type="text" name="course" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Submit Application</button>
  </form>
</body>
</html>
