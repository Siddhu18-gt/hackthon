<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['test_department_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    $record_id = $_POST['record_id'] ?? 0;
    
    if (empty($record_id) || !isset($_FILES['report_file'])) {
        echo json_encode(['success' => false, 'message' => 'Record ID and file are required']);
        exit;
    }
    
    $upload_dir = '../uploads/test_reports/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file = $_FILES['report_file'];
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_exts = ['pdf', 'jpg', 'jpeg', 'png'];
    
    if (!in_array($file_ext, $allowed_exts)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only PDF, JPG, PNG allowed']);
        exit;
    }
    
    $file_name = 'report_' . $record_id . '_' . time() . '.' . $file_ext;
    $file_path = $upload_dir . $file_name;
    
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        $relative_path = 'uploads/test_reports/' . $file_name;
        
        $conn = getDBConnection();
        $stmt = $conn->prepare("UPDATE test_records SET report_file_path = ?, status = 'completed', uploaded_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("si", $relative_path, $record_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Report uploaded successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update database']);
        }
        
        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>

