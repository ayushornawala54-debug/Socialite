<?php
$conn = new mysqli("localhost", "root", "", "db_socialmedia");

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $query = $_GET['q'];

    $stmt = $conn->prepare("SELECT id, username, profile_photo FROM tbl_register WHERE username LIKE ?");
    $searchTerm = "%$query%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<a href="timeline.php?user_id=' . $row['id'] . '" ...>';
            if (!empty($row['profile_photo'])) {
                echo '<img src="' . htmlspecialchars($row['profile_photo']) . '" class="object-cover w-9 h-9 rounded-full mr-3">';
            } else {
                echo '<img src="uploads/default.png" class="object-cover w-9 h-9 rounded-full mr-3">';
            }
            echo '<span>' . htmlspecialchars($row['username']) . '</span>';
            echo '</a>';
        }
    } else {
        echo '<div class="p-2 text-gray-500">No results found</div>';
    }

    $stmt->close();
}
?>
