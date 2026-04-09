<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $patient_id = $data['patient_id'] ?? 0;
    $tests = $data['tests'] ?? [];
    
    if (empty($patient_id) || empty($tests)) {
        echo json_encode(['success' => false, 'message' => 'Patient ID and tests required']);
        exit;
    }
    
    $conn = getDBConnection();
    
    foreach ($tests as $test) {
        // Find test department by department name
        $deptStmt = $conn->prepare("SELECT id FROM test_departments WHERE department_name = ? LIMIT 1");
        $deptStmt->bind_param("s", $test['department']);
        $deptStmt->execute();
        $deptResult = $deptStmt->get_result();
        $dept = $deptResult->fetch_assoc();
        $deptStmt->close();
        
        if ($dept) {
            // Check if record already exists
            $checkStmt = $conn->prepare("SELECT id FROM test_records WHERE patient_id = ? AND test_department_id = ? AND test_name = ?");
            $checkStmt->bind_param("iis", $patient_id, $dept['id'], $test['test_name']);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            $checkStmt->close();
            
            if ($checkResult->num_rows === 0) {
                // Insert new test record
                $insertStmt = $conn->prepare("INSERT INTO test_records (patient_id, test_department_id, test_name, status) VALUES (?, ?, ?, 'pending')");
                $insertStmt->bind_param("iis", $patient_id, $dept['id'], $test['test_name']);
                $insertStmt->execute();
                $insertStmt->close();
            }
        }
    }
    
    $conn->close();
    
    echo json_encode(['success' => true, 'message' => 'Tests sent to departments successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>

