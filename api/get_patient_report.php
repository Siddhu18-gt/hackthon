<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$patient_id = $_GET['patient_id'] ?? 0;

if (empty($patient_id)) {
    echo json_encode(['success' => false, 'message' => 'Patient ID required']);
    exit;
}

$conn = getDBConnection();

$stmt = $conn->prepare("SELECT * FROM patient_reports WHERE patient_id = ? ORDER BY report_date DESC LIMIT 1");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $report = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'report' => [
            'summary' => $report['summary'] ?? '',
            'medicines' => $report['medicines'] ?? '',
            'ai_summary' => $report['ai_summary'] ?? '',
            'finalized' => $report['finalized'] ?? false
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'No report found']);
}

$stmt->close();
$conn->close();
?>

