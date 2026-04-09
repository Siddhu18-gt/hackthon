<?php
require_once 'config/database.php';
header('Content-Type: application/json');

$conn = getDBConnection();
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$result = $conn->query("DESCRIBE doctor_pages");
if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Table doctor_pages not found or error: ' . $conn->error]);
} else {
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row;
    }
    echo json_encode(['success' => true, 'columns' => $columns]);
}
$conn->close();
?>