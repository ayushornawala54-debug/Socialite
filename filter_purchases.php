<?php
session_start();
include 'db.php';  // Database connection

$search_query = isset($_POST['search_query']) ? trim(mysqli_real_escape_string($conn, $_POST['search_query'])) : '';

$sql = "SELECT p.id, p.reference_code, p.supplier_id, p.warehouse_id, p.status, 
               p.grand_total, p.payment_type, p.created_at, 
               s.name AS supplier_name, w.name AS warehouse_name 
        FROM purchases p
        LEFT JOIN suppliers s ON p.supplier_id = s.id
        LEFT JOIN warehouses w ON p.warehouse_id = w.id
        WHERE 1";

if (!empty($search_query)) {
    $sql .= " AND (p.reference_code LIKE '%$search_query%' 
                    OR s.name LIKE '%$search_query%' 
                    OR w.name LIKE '%$search_query%' 
                    OR p.status LIKE '%$search_query%' 
                    OR p.payment_type LIKE '%$search_query%')";
}

$sql .= " ORDER BY p.created_at DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['reference_code']) . "</td>
                <td>" . htmlspecialchars($row['supplier_name'] ?? 'N/A') . "</td>
                <td>" . htmlspecialchars($row['warehouse_name'] ?? 'N/A') . "</td>
                <td>" . htmlspecialchars($row['status']) . "</td>
                <td>" . number_format($row['grand_total'], 2) . "</td>
                <td>" . htmlspecialchars($row['payment_type']) . "</td>
                <td>" . date("d-m-Y h:i A", strtotime($row['created_at'])) . "</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='7' class='text-center'>No results found</td></tr>";
}

$conn->close();
?>
