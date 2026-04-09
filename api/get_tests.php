<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    require_once '../config/database.php';
    
    $conn = getDBConnection();
    
    // Check if table exists
    $checkTable = $conn->query("SHOW TABLES LIKE 'tests_master'");
    if ($checkTable->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'Tests table does not exist', 'tests' => []]);
        exit;
    }
    
    $stmt = $conn->prepare("SELECT * FROM tests_master ORDER BY test_name");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $tests = [];
    while ($row = $result->fetch_assoc()) {
        $tests[] = [
            'id' => $row['id'],
            'test_name' => $row['test_name'],
            'cost' => floatval($row['cost']),
            'department' => $row['department']
        ];
    }
    
    $stmt->close();
    $conn->close();
    
    echo json_encode(['success' => true, 'tests' => $tests]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'tests' => []]);
}
?>

