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
    <!-- Font Awesome for User Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            background:
                linear-gradient(135deg, rgba(255, 193, 7, 0.2), rgba(255, 171, 0, 0.15)),
                url('https://www.transparenttextures.com/patterns/sandpaper.png');
            background-color: #fff8e1;
            background-size: cover;
            background-attachment: fixed;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 380px;
            text-align: center;
            position: relative;
        }

        .user-icon {
            background: linear-gradient(135deg, #ffca28, #ffb300);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 40px;
            color: white;
            margin: 0 auto 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        }

        h2 {
            margin-bottom: 15px;
            color: #333;
        }

        p {
            margin: 8px 0;
            color: #444;
            font-size: 15px;
        }

        .status {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: capitalize;
        }

        .accepted {
            background-color: #d4edda;
            color: #155724;
        }

        .rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .buttons {
            margin-top: 25px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .btn {
            text-decoration: none;
            background: #ffb300;
            color: white;
            padding: 9px 18px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 14px;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: #ff8f00;
        }

        footer {
            position: absolute;
            bottom: 15px;
            font-size: 13px;
            color: #666;
            width: 100%;
            text-align: center;
        }

        @media (max-width: 500px) {
            .card {
                width: 90%;
                padding: 25px;
            }
        }
    </style>
</head>
<body>

<div class="card">
    <div class="user-icon">
        <i class="fas fa-user"></i>
    </div>
    <h2>Hello, <?= htmlspecialchars($_SESSION['username']); ?>!</h2>

    <?php if ($application): ?>
        <p>Your latest application:</p>
        <p><strong>Full Name:</strong> <?= htmlspecialchars($application['full_name']); ?></p>
        <p><strong>Index Number:</strong> <?= htmlspecialchars($application['index_number']); ?></p>
        <p><strong>Marks:</strong> <?= $application['marks']; ?></p>
        <p><strong>Status:</strong> 
            <span class="status <?= strtolower($application['status']); ?>">
                <?= ucfirst($application['status']); ?>
            </span>
        </p>
    <?php else: ?>
        <p>No applications found.</p>
    <?php endif; ?>

    <div class="buttons">
        <a href="student_dashboard.php" class="btn"><i class="fas fa-arrow-left"></i> Back</a>
        <a href="logout.php" class="btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<footer>
    © <?= date('Y'); ?> Huduma Secondary School | Student Portal
</footer>

</body>
</html>
