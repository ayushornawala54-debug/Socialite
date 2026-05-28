<?php
$conn = new mysqli("localhost", "root", "", "db_socialmedia");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function getCounts($table, $column, $conn) {
    $current_month = date('Y-m');
    $last_month = date('Y-m', strtotime("-1 month"));

    // Current month
    $stmt1 = $conn->prepare("SELECT COUNT(*) AS count FROM $table WHERE DATE_FORMAT($column, '%Y-%m') = ?");
    $stmt1->bind_param("s", $current_month);
    $stmt1->execute();
    $result1 = $stmt1->get_result()->fetch_assoc();
    $current = $result1['count'];

    // Last month
    $stmt2 = $conn->prepare("SELECT COUNT(*) AS count FROM $table WHERE DATE_FORMAT($column, '%Y-%m') = ?");
    $stmt2->bind_param("s", $last_month);
    $stmt2->execute();
    $result2 = $stmt2->get_result()->fetch_assoc();
    $previous = $result2['count'];

    // Percentage growth
    $percentage = ($previous == 0) ? 100 : (($current - $previous) / $previous) * 100;

    return [
        'current' => $current,
        'percentage' => round($percentage, 1)
    ];
}

$user_stats = getCounts("tbl_register", "reg_date", $conn);
$sub_stats = getCounts("tbl_subscription", "subscribed_on", $conn);

echo json_encode([
    "users" => $user_stats,
    "subscriptions" => $sub_stats
]);

$conn->close();
?>
