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
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT * FROM applications WHERE full_name=? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$application = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Application Status</title>
    <style>
        body { font-family:Arial, sans-serif; background:#f9f9f9; text-align:center; padding-top:80px; }
        .card { background:white; display:inline-block; padding:20px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.1); width:400px; }
        .status { font-weight:bold; font-size:1.2em; }
        .accepted { color:green; }
        .rejected { color:red; }
        .pending { color:orange; }
    </style>
</head>
<body>

<div class="card">
    <h2>Hello, <?= htmlspecialchars($_SESSION['username']); ?>!</h2>
    <?php if ($application): ?>
        <p>Your latest application:</p>
        <p>Full Name: <strong><?= htmlspecialchars($application['full_name']); ?></strong></p>
        <p>Index Number: <strong><?= htmlspecialchars($application['index_number']); ?></strong></p>
        <p>Marks: <strong><?= $application['marks']; ?></strong></p>
        <p>Status: 
            <span class="status <?= strtolower($application['status']); ?>">
                <?= ucfirst($application['status']); ?>
            </span>
        </p>
    <?php else: ?>
        <p>No applications found.</p>
    <?php endif; ?>
</div>

</body>
</html>
