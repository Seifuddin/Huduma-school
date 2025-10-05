<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "huduma";

$conn = new mysqli($servername, $username, $password, $dbname);
$result = $conn->query("SELECT * FROM applications");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h2>Admin Dashboard</h2>
    <table border="1" width="100%">
      <tr>
        <th>ID</th>
        <th>Full Name</th>
        <th>Index Number</th>
        <th>Marks</th>
        <th>Status</th>
        <th>Action</th>
      </tr>

      <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['full_name'] ?></td>
        <td><?= $row['index_number'] ?></td>
        <td><?= $row['marks'] ?></td>
        <td><?= $row['status'] ?></td>
        <td>
          <a href="update_status.php?id=<?= $row['id'] ?>&status=accepted">Accept</a> |
          <a href="update_status.php?id=<?= $row['id'] ?>&status=rejected">Reject</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>
</body>
</html>
