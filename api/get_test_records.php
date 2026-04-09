<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$department_id = $_GET['department_id'] ?? 0;

if (empty($department_id)) {
    echo json_encode(['success' => false, 'message' => 'Department ID required']);
    exit;
}

$conn = getDBConnection();

$stmt = $conn->prepare("SELECT tr.*, p.name as patient_name, p.aadhaar_number, p.age 
                       FROM test_records tr 
                       JOIN patients p ON tr.patient_id = p.id 
                       WHERE tr.test_department_id = ? AND tr.status = 'pending'
                       ORDER BY tr.uploaded_at DESC");
$stmt->bind_param("i", $department_id);
$stmt->execute();
$result = $stmt->get_result();

$records = [];
while ($row = $result->fetch_assoc()) {
    $records[] = [
        'id' => $row['id'],
        'patient_name' => $row['patient_name'],
        'aadhaar_number' => $row['aadhaar_number'],
        'age' => $row['age'],
        'test_name' => $row['test_name'],
        'report_file_path' => $row['report_file_path']
    ];
}

echo json_encode(['success' => true, 'records' => $records]);

$stmt->close();
$conn->close();
?>

