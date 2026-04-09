<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if receptionist is logged in
    if (!isset($_SESSION['receptionist_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $patient_id = $data['patient_id'] ?? 0;
    $billing_items = $data['billing_items'] ?? [];
    $scheme = $data['scheme'] ?? '';
    $subtotal = $data['subtotal'] ?? 0;
    $discount_percentage = $data['discount_percentage'] ?? 0;
    $discount_amount = $data['discount_amount'] ?? 0;
    $total_amount = $data['total_amount'] ?? 0;
    
    if (empty($billing_items)) {
        echo json_encode(['success' => false, 'message' => 'No billing items provided']);
        exit;
    }
    
    $conn = getDBConnection();
    $receptionist_id = $_SESSION['receptionist_id'];
    
    // Delete existing unpaid billing for this patient (keep paid ones)
    $stmt = $conn->prepare("DELETE FROM billing WHERE patient_id = ? AND status = 'unpaid'");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $stmt->close();
    
    // Insert new billing records
    $stmt = $conn->prepare("INSERT INTO billing (patient_id, receptionist_id, item_type, test_name, amount, status, scheme, discount_percentage, discount_amount, subtotal, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $billing_ids = [];
    foreach ($billing_items as $item) {
        $item_type = $item['item_type'] ?? 'test';
        $test_name = $item['test_name'] ?? ($item_type === 'consultation' ? 'Doctor Consultation' : '');
        $amount = $item['amount'] ?? 0;
        $status = $item['status'] ?? 'unpaid';
        
        $stmt->bind_param("iissdsddddd", $patient_id, $receptionist_id, $item_type, $test_name, $amount, $status, $scheme, $discount_percentage, $discount_amount, $subtotal, $total_amount);
        $stmt->execute();
        $billing_ids[] = $conn->insert_id;
        
        // If status is paid and item_type is test, create test record for test departments
        if ($status === 'paid' && $item_type === 'test') {
            // Get department from tests_master table
            $testStmt = $conn->prepare("SELECT department FROM tests_master WHERE test_name = ? LIMIT 1");
            $testStmt->bind_param("s", $test_name);
            $testStmt->execute();
            $testResult = $testStmt->get_result();
            $dept_name = '';
            
            if ($testResult->num_rows > 0) {
                $testRow = $testResult->fetch_assoc();
                $dept_name = $testRow['department'];
            } else {
                // Fallback: Find test department by test name
                $test_name_lower = strtolower($test_name);
                
                if (strpos($test_name_lower, 'blood') !== false || strpos($test_name_lower, 'electrolytes') !== false || 
                    strpos($test_name_lower, 'pt/inr') !== false || strpos($test_name_lower, 'lipid') !== false || 
                    strpos($test_name_lower, 'thyroid') !== false) {
                    $dept_name = 'Blood';
                } elseif (strpos($test_name_lower, 'urine') !== false || strpos($test_name_lower, 'pregnancy') !== false) {
                    $dept_name = 'Urine';
                } elseif (strpos($test_name_lower, 'usg') !== false || strpos($test_name_lower, 'ultrasound') !== false || 
                          strpos($test_name_lower, 'doppler') !== false || strpos($test_name_lower, 'echo') !== false) {
                    $dept_name = 'USG';
                } elseif (strpos($test_name_lower, 'ecg') !== false) {
                    $dept_name = 'ECG';
                } elseif (strpos($test_name_lower, 'x-ray') !== false || strpos($test_name_lower, 'xray') !== false || 
                          strpos($test_name_lower, 'chest') !== false) {
                    $dept_name = 'X-Ray';
                } elseif (strpos($test_name_lower, 'ct') !== false) {
                    $dept_name = 'CT Scan';
                } elseif (strpos($test_name_lower, 'mri') !== false) {
                    $dept_name = 'MRI';
                }
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
    }
    
    $stmt->close();
    $conn->close();
    
    echo json_encode(['success' => true, 'message' => 'Billing saved successfully', 'billing_ids' => $billing_ids]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>

