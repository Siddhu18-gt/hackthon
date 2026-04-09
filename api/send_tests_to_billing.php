<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $patient_id = $data['patient_id'] ?? 0;
    $billing_items = $data['billing_items'] ?? [];
    
    if (empty($patient_id) || empty($billing_items)) {
        echo json_encode(['success' => false, 'message' => 'Patient ID and billing items required']);
        exit;
    }
    
    $receptionist_id = $_SESSION['receptionist_id'] ?? 1; // Default to 1 if not in session
    
    $conn = getDBConnection();
    
    foreach ($billing_items as $item) {
        // Check if billing record already exists
        $checkStmt = $conn->prepare("SELECT id FROM billing WHERE patient_id = ? AND test_name = ? AND status = 'unpaid' AND item_type = 'test'");
        $checkStmt->bind_param("is", $patient_id, $item['test_name']);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $checkStmt->close();
        
        if ($checkResult->num_rows === 0) {
            // Insert new billing record with item_type = 'test'
            $insertStmt = $conn->prepare("INSERT INTO billing (patient_id, receptionist_id, item_type, test_name, amount, status) VALUES (?, ?, 'test', ?, ?, 'unpaid')");
            $insertStmt->bind_param("iisd", $patient_id, $receptionist_id, $item['test_name'], $item['amount']);
            $insertStmt->execute();
            $insertStmt->close();
        }
    }
    
    $conn->close();
    
    echo json_encode(['success' => true, 'message' => 'Tests added to billing successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>

