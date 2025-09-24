<?php
session_start();

$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "huduma";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM admin WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] === 'admin') {
                header("Location: ../dashboards/admin_dashboard.php");
            } else {
                header("Location: ../dashboards/student_dashboard.php");
            }

            exit();
        } else {
            echo "<h3 style='color:red;text-align:center;'>Invalid password!</h3>";
            echo "<a href='login.html'>Try again</a>";
        }
    } else {
        echo "<h3 style='color:red;text-align:center;'>User not found!</h3>";
        echo "<a href='register.html'>Register here</a>";
    }

    $stmt->close();
}
$conn->close();
?>
