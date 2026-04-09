<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$patient_id = $_GET['patient_id'] ?? 0;

if (empty($patient_id)) {
    echo json_encode(['success' => false, 'message' => 'Patient ID required']);
    exit;
}

$conn = getDBConnection();

// Get doctor page records
$stmt = $conn->prepare("SELECT * FROM doctor_pages WHERE patient_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

$records = [];
while ($row = $result->fetch_assoc()) {
    $nurse_medicines = [];
    if (!empty($row['nurse_instructions'])) {
        $nurse_medicines = explode(', ', $row['nurse_instructions']);
    }
    
    $records[] = [
        'id' => $row['id'],
        'symptoms' => $row['symptoms'] ?? '',
        'cause' => $row['cause'] ?? '',
        'prescription' => $row['prescription'] ?? '',
        'test' => $row['test'] ?? '',
        'nurse_instructions' => $row['nurse_instructions'] ?? '',
        'nurse_medicines' => $nurse_medicines,
        'nurse_status' => $row['nurse_status'] ?? 'pending',
        'created_at' => $row['created_at']
    ];
}

echo json_encode(['success' => true, 'records' => $records]);

$stmt->close();
$conn->close();
?>

