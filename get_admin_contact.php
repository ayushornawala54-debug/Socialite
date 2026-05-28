<?php
// Database Connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'db_socialmedia';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

$type = $_GET['type'] ?? '';

$query = "SELECT * FROM tbl_admin LIMIT 1";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    echo "<p class='text-danger'>Admin contact not found.</p>";
    exit;
}

if ($type === 'phone') {
    echo "<i class='bi bi-telephone-fill fs-4 text-primary'></i> 
          <span class='fs-5 fw-semibold ms-2'>" . htmlspecialchars($row['contactNumber']) . "</span>";
} elseif ($type === 'email') {
    echo "<i class='bi bi-envelope-fill fs-4 text-success'></i> 
          <span class='fs-5 fw-semibold ms-2'>" . htmlspecialchars($row['emailId']) . "</span>";
} else {
    echo "<p class='text-warning'>Invalid request.</p>";
}
?>
