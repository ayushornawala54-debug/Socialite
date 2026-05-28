<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'db_socialmedia';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

if (isset($_POST['search']) && isset($_POST['table']) && isset($_POST['column'])) {
    $search = $conn->real_escape_string($_POST['search']);
    $table = $conn->real_escape_string($_POST['table']);
    $column = $conn->real_escape_string($_POST['column']);

    $query = "SELECT * FROM $table WHERE $column LIKE '%$search%'";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                echo "<td>$value</td>";
            }
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='100%'>No results found</td></tr>";
    }
}
?>
