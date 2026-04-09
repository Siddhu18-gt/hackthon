<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$conn = getDBConnection();

$query = "SELECT id, nurse_name, email, aadhaar_number, date_of_birth, gender, address, mobile_number, created_at FROM nurses ORDER BY created_at DESC";
$result = $conn->query($query);

$nurses = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $nurses[] = $row;
    }
}

echo json_encode(['success' => true, 'nurses' => $nurses]);

$conn->close();
?>