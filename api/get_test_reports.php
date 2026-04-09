<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$patient_id = $_GET['patient_id'] ?? 0;

if (empty($patient_id)) {
    echo json_encode(['success' => false, 'message' => 'Patient ID required']);
    exit;
}

$conn = getDBConnection();

$stmt = $conn->prepare("SELECT tr.*, td.department_name FROM test_records tr 
                       JOIN test_departments td ON tr.test_department_id = td.id 
                       WHERE tr.patient_id = ? ORDER BY tr.uploaded_at DESC");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

$reports = [];
while ($row = $result->fetch_assoc()) {
    $reports[] = [
        'id' => $row['id'],
        'test_name' => $row['test_name'],
        'department_name' => $row['department_name'],
        'report_file_path' => $row['report_file_path'],
        'status' => $row['status'],
        'uploaded_at' => $row['uploaded_at']
    ];
}

echo json_encode(['success' => true, 'reports' => $reports]);

$stmt->close();
$conn->close();
?>

