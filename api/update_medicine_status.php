<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['nurse_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $prescription_id = $data['prescription_id'] ?? 0;
    $given_status = $data['given_status'] ?? false;
    $nurse_id = $_SESSION['nurse_id'];
    
    if (empty($prescription_id)) {
        echo json_encode(['success' => false, 'message' => 'Prescription ID required']);
        exit;
    }
    
    $conn = getDBConnection();
    
    // Get prescription details to find patient_id and doctor_id
    $getStmt = $conn->prepare("SELECT patient_id, doctor_id FROM nurse_prescriptions WHERE id = ?");
    $getStmt->bind_param("i", $prescription_id);
    $getStmt->execute();
    $prescResult = $getStmt->get_result();
    $prescData = $prescResult->fetch_assoc();
    $getStmt->close();
    
    if (!$prescData) {
        echo json_encode(['success' => false, 'message' => 'Prescription not found']);
        $conn->close();
        exit;
    }
    
    $given_at = $given_status ? date('Y-m-d H:i:s') : null;
    
    // Update nurse_prescriptions table
    $stmt = $conn->prepare("UPDATE nurse_prescriptions SET given_status = ?, given_at = ?, nurse_id = ? WHERE id = ?");
    $stmt->bind_param("isii", $given_status, $given_at, $nurse_id, $prescription_id);
    $stmt->execute();
    $stmt->close();
    
    // Update doctor_pages table nurse_status
    $nurseStatus = $given_status ? 'done' : 'pending';
    $updateStmt = $conn->prepare("UPDATE doctor_pages SET nurse_status = ? WHERE patient_id = ? AND doctor_id = ?");
    $updateStmt->bind_param("sii", $nurseStatus, $prescData['patient_id'], $prescData['doctor_id']);
    $updateStmt->execute();
    $updateStmt->close();
    
    $conn->close();
    
    echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>

