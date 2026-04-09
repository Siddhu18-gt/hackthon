<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['photo']) || !isset($_POST['aadhaar'])) {
        echo json_encode(['success' => false, 'message' => 'Photo and Aadhaar number are required']);
        exit;
    }
    
    $aadhaar = preg_replace('/\s+/', '', $_POST['aadhaar']);
    
    if (empty($aadhaar) || strlen($aadhaar) !== 12) {
        echo json_encode(['success' => false, 'message' => 'Invalid Aadhaar number']);
        exit;
    }
    
    $upload_dir = '../uploads/patient_photos/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file = $_FILES['photo'];
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (!in_array($file_ext, $allowed_exts)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF allowed']);
        exit;
    }
    
    $file_name = $aadhaar . '.' . $file_ext;
    $file_path = $upload_dir . $file_name;
    $relative_path = 'uploads/patient_photos/' . $file_name;
    
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        $conn = getDBConnection();
        
        // Update demo_aadhaar_data
        $stmt = $conn->prepare("UPDATE demo_aadhaar_data SET photo_path = ? WHERE aadhaar_number = ?");
        $stmt->bind_param("ss", $relative_path, $aadhaar);
        $stmt->execute();
        $stmt->close();
        
        // Update patients table if patient exists
        $stmt2 = $conn->prepare("UPDATE patients SET photo_path = ? WHERE aadhaar_number = ?");
        $stmt2->bind_param("ss", $relative_path, $aadhaar);
        $stmt2->execute();
        $stmt2->close();
        
        $conn->close();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Photo uploaded successfully',
            'photo_path' => $relative_path
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>

