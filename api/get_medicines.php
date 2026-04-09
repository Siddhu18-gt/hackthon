<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    require_once '../config/database.php';
    
    $conn = getDBConnection();
    
    // Check if table exists
    $checkTable = $conn->query("SHOW TABLES LIKE 'medicines_master'");
    if ($checkTable->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'Medicines table does not exist', 'medicines' => []]);
        exit;
    }
    
    $stmt = $conn->prepare("SELECT * FROM medicines_master ORDER BY medicine_name");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $medicines = [];
    while ($row = $result->fetch_assoc()) {
        $medicines[] = [
            'id' => $row['id'],
            'medicine_name' => $row['medicine_name'],
            'dosage' => $row['dosage'] ?? ''
        ];
    }
    
    $stmt->close();
    $conn->close();
    
    echo json_encode(['success' => true, 'medicines' => $medicines]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'medicines' => []]);
}
?>

