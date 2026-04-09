<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $billing_id = $data['billing_id'] ?? 0;
    $status = $data['status'] ?? 'unpaid';
    
    if (empty($billing_id)) {
        echo json_encode(['success' => false, 'message' => 'Billing ID required']);
        exit;
    }
    
    $conn = getDBConnection();
    
    // Get billing record
    $stmt = $conn->prepare("SELECT patient_id, test_name, item_type FROM billing WHERE id = ?");
    $stmt->bind_param("i", $billing_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $billing = $result->fetch_assoc();
    $stmt->close();
    
    if (!$billing) {
        echo json_encode(['success' => false, 'message' => 'Billing record not found']);
        $conn->close();
        exit;
    }
    
    // Update billing status
    $updateStmt = $conn->prepare("UPDATE billing SET status = ? WHERE id = ?");
    $updateStmt->bind_param("si", $status, $billing_id);
    $updateStmt->execute();
    $updateStmt->close();
    
    // If status changed to paid and item_type is test, send to test department
    if ($status === 'paid' && $billing['item_type'] === 'test') {
        $patient_id = $billing['patient_id'];
        $test_name = $billing['test_name'];
        
        // Get department from tests_master table
        $testStmt = $conn->prepare("SELECT department FROM tests_master WHERE test_name = ? LIMIT 1");
        $testStmt->bind_param("s", $test_name);
        $testStmt->execute();
        $testResult = $testStmt->get_result();
        $dept_name = '';
        
        if ($testResult->num_rows > 0) {
            $testRow = $testResult->fetch_assoc();
            $dept_name = $testRow['department'];
        }
        
        $testStmt->close();
        
        if (!empty($dept_name)) {
            // Get test department ID
            $dept_stmt = $conn->prepare("SELECT id FROM test_departments WHERE department_name = ?");
            $dept_stmt->bind_param("s", $dept_name);
            $dept_stmt->execute();
            $dept_result = $dept_stmt->get_result();
            
            if ($dept_result->num_rows > 0) {
                $dept = $dept_result->fetch_assoc();
                $test_dept_id = $dept['id'];
                
                // Check if test record already exists
                $check_stmt = $conn->prepare("SELECT id FROM test_records WHERE patient_id = ? AND test_department_id = ? AND test_name = ?");
                $check_stmt->bind_param("iis", $patient_id, $test_dept_id, $test_name);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                
                if ($check_result->num_rows === 0) {
                    // Create test record
                    $test_stmt = $conn->prepare("INSERT INTO test_records (patient_id, test_department_id, test_name, status) VALUES (?, ?, ?, 'pending')");
                    $test_stmt->bind_param("iis", $patient_id, $test_dept_id, $test_name);
                    $test_stmt->execute();
                    $test_stmt->close();
                }
                
                $check_stmt->close();
            }
            
            $dept_stmt->close();
        }
    }
    
    $conn->close();
    
    echo json_encode(['success' => true, 'message' => 'Billing status updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>

