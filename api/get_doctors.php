<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$specialization = $_GET['specialization'] ?? '';

$conn = getDBConnection();

if (!empty($specialization)) {
    $stmt = $conn->prepare("SELECT id, doctor_name, doctor_id, specialization FROM doctors WHERE specialization = ?");
    $stmt->bind_param("s", $specialization);
} else {
    $stmt = $conn->prepare("SELECT id, doctor_name, doctor_id, specialization FROM doctors");
}

$stmt->execute();
$result = $stmt->get_result();

$doctors = [];
while ($row = $result->fetch_assoc()) {
    $doctors[] = $row;
}

echo json_encode(['success' => true, 'doctors' => $doctors]);

$stmt->close();
$conn->close();
?>

