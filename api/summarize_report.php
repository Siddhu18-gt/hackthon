<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['doctor_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $patient_id = $data['patient_id'] ?? 0;
    $doctor_id = $_SESSION['doctor_id'];
    $summary = $data['summary'] ?? '';
    $medicines = $data['medicines'] ?? '';
    $ai_summary = $data['ai_summary'] ?? '';
    
    if (empty($patient_id)) {
        echo json_encode(['success' => false, 'message' => 'Patient ID required']);
        exit;
    }
    
    $conn = getDBConnection();
    
    // Check if report already exists
    $stmt = $conn->prepare("SELECT id FROM patient_reports WHERE patient_id = ? AND doctor_id = ?");
    $stmt->bind_param("ii", $patient_id, $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update existing report
        $stmt = $conn->prepare("UPDATE patient_reports SET summary = ?, medicines = ?, ai_summary = ?, finalized = 1 WHERE patient_id = ? AND doctor_id = ?");
        $stmt->bind_param("sssii", $summary, $medicines, $ai_summary, $patient_id, $doctor_id);
    } else {
        // Insert new report
        $stmt = $conn->prepare("INSERT INTO patient_reports (patient_id, doctor_id, summary, medicines, ai_summary, finalized) VALUES (?, ?, ?, ?, ?, 1)");
        $stmt->bind_param("iisss", $patient_id, $doctor_id, $summary, $medicines, $ai_summary);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Report summarized successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save report']);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>

