<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $patient_id = $data['patient_id'] ?? 0;
    $records = $data['records'] ?? [];
    
    if (empty($patient_id)) {
        echo json_encode(['success' => false, 'message' => 'Patient ID required']);
        exit;
    }
    
    // Get default doctor ID (or use first available doctor)
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT id FROM doctors LIMIT 1");
    $stmt->execute();
    $result = $stmt->get_result();
    $doctor = $result->fetch_assoc();
    $doctor_id = $doctor['id'] ?? 1;
    $stmt->close();
    
    // Delete existing records for this patient and doctor to avoid duplicates
    $deleteStmt = $conn->prepare("DELETE FROM doctor_pages WHERE patient_id = ? AND doctor_id = ?");
    $deleteStmt->bind_param("ii", $patient_id, $doctor_id);
    $deleteStmt->execute();
    $deleteStmt->close();
    
    // Insert or update symptoms for each record
    foreach ($records as $record) {
        $symptoms = $record['symptoms'] ?? '';
        
        // Always insert new record (we deleted old ones above)
        $stmt = $conn->prepare("INSERT INTO doctor_pages (patient_id, doctor_id, symptoms, nurse_status) VALUES (?, ?, ?, 'pending')");
        $stmt->bind_param("iis", $patient_id, $doctor_id, $symptoms);
        
        if (!$stmt->execute()) {
            $stmt->close();
            $conn->close();
            echo json_encode(['success' => false, 'message' => 'Failed to save symptoms: ' . $conn->error]);
            exit;
        }
        $stmt->close();
    }
    
    $conn->close();
    
    echo json_encode(['success' => true, 'message' => 'Symptoms saved successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>

