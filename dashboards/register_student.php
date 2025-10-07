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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = $_POST['full_name'];
    $parent_name = $_POST['parent_name'];
    $parent_phone = $_POST['parent_phone'];
    $former_school = $_POST['former_school'];
    $index_number = $_POST['index_number'];
    $year_of_admission = $_POST['year_of_admission'];
    $date_of_birth = $_POST['date_of_birth'];
    $religion = $_POST['religion'];
    $county = $_POST['county'];

    // Handle passport upload
    $passport_photo = "";
    if (!empty($_FILES['passport_photo']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $target_file = $target_dir . basename($_FILES["passport_photo"]["name"]);
        move_uploaded_file($_FILES["passport_photo"]["tmp_name"], $target_file);
        $passport_photo = $target_file;
    }

    $stmt = $conn->prepare("INSERT INTO students (full_name, passport_photo, parent_name, parent_phone, former_school, index_number, year_of_admission, date_of_birth, religion, county) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $full_name, $passport_photo, $parent_name, $parent_phone, $former_school, $index_number, $year_of_admission, $date_of_birth, $religion, $county);

    if ($stmt->execute()) {
        $message = "<p class='success-msg'>Student registered successfully!</p>";
    } else {
        $message = "<p class='error-msg'>Error: " . $stmt->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register Student - Huduma Admin</title>
<style>
    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #fff8e1 25%, #ffe0b2 100%);
        min-height: 100vh;
        display: flex;
    }

    /* Sidebar (same as admin dashboard) */
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

    /* Content */
    .content {
        flex: 1;
        margin-left: 230px;
        padding: 30px;
    }

    h2 {
        color: #4e342e;
        margin-bottom: 20px;
    }

    .form-container {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        max-width: 700px;
        margin: auto;
    }

    form label {
        font-weight: 600;
        display: block;
        margin-top: 10px;
        color: #4e342e;
    }

    form input, form select {
        width: 100%;
        padding: 10px;
        border: 2px solid #ffe0b2;
        border-radius: 8px;
        margin-top: 5px;
        font-size: 15px;
        outline: none;
        transition: 0.3s;
    }

    form input:focus {
        border-color: #f57c00;
        box-shadow: 0 0 5px rgba(245,124,0,0.3);
    }

    form input[type="file"] {
        border: none;
    }

    button {
        background-color: #4e342e;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        cursor: pointer;
        margin-top: 20px;
        font-weight: bold;
        font-size: 16px;
        transition: background 0.3s;
    }

    button:hover {
        background-color: #6d4c41;
    }

    .success-msg { color: green; font-weight: bold; text-align: center; }
    .error-msg { color: red; font-weight: bold; text-align: center; }

    @media (max-width: 768px) {
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
    }
</style>
</head>
<body>

<!-- Sidebar (same as dashboard) -->
<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_home.php">🏠 Home</a>
    <a href="admin_dashboard.php">📋 Applications</a>
    <a href="register_student.php">➕ Add Student</a>
    <a href="view_students.php">📚 Reports</a>
    <a href="../index.html">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="content">
    <h2>Register a New Student</h2>
    <?= $message; ?>
    <div class="form-container">
        <form method="POST" enctype="multipart/form-data">
            <label>Full Name:</label>
            <input type="text" name="full_name" required>

            <label>Passport Photo:</label>
            <input type="file" name="passport_photo" accept="image/*">

            <label>Parent Name:</label>
            <input type="text" name="parent_name" required>

            <label>Parent Phone:</label>
            <input type="text" name="parent_phone" required>

            <label>Former School:</label>
            <input type="text" name="former_school">

            <label>Index Number:</label>
            <input type="text" name="index_number" required>

            <label>Year of Admission:</label>
            <input type="number" name="year_of_admission" min="2000" max="2100" required>

            <label>Date of Birth:</label>
            <input type="date" name="date_of_birth" required>

            <label>Religion:</label>
            <input type="text" name="religion">

            <label>County:</label>
            <input type="text" name="county">

            <button type="submit">Register Student</button>
        </form>
    </div>
</div>

</body>
</html>
