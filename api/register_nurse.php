<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $nurse_name = $data['nurse_name'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $aadhaar = preg_replace('/\D+/', '', $data['aadhaar'] ?? '');
    $dob = $data['date_of_birth'] ?? null;
    $gender = $data['gender'] ?? null;
    $address = $data['address'] ?? null;
    $mobile = preg_replace('/\D+/', '', $data['mobile'] ?? '');
    
    if (empty($nurse_name) || empty($email) || empty($password) || empty($aadhaar) || strlen($aadhaar) !== 12) {
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled with valid Aadhaar']);
        exit;
    }
    
    $conn = getDBConnection();
    
    // Check if email or Aadhaar already exists
    $stmt = $conn->prepare("SELECT id FROM nurses WHERE email = ? OR aadhaar_number = ?");
    $stmt->bind_param("ss", $email, $aadhaar);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email or Aadhaar already exists']);
        $stmt->close();
        $conn->close();
        exit;
    }
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO nurses (nurse_name, email, password, aadhaar_number, date_of_birth, gender, address, mobile_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $nurse_name, $email, $hashed_password, $aadhaar, $dob, $gender, $address, $mobile);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Nurse registered successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registration failed']);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>