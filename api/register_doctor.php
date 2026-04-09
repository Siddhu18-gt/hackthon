<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $doctor_name = $data['doctor_name'] ?? '';
    $doctor_id = $data['doctor_id'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $specialization = $data['specialization'] ?? '';
    $aadhaar = preg_replace('/\D+/', '', $data['aadhaar'] ?? '');
    $dob = $data['date_of_birth'] ?? null;
    $gender = $data['gender'] ?? null;
    $address = $data['address'] ?? null;
    $mobile = preg_replace('/\D+/', '', $data['mobile'] ?? '');
    
    if (empty($doctor_name) || empty($doctor_id) || empty($email) || empty($password) || empty($specialization) || empty($aadhaar) || strlen($aadhaar) !== 12) {
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled with valid Aadhaar']);
        exit;
    }
    
    $conn = getDBConnection();
    
    // Check if doctor ID, email, or Aadhaar already exists
    $stmt = $conn->prepare("SELECT id FROM doctors WHERE doctor_id = ? OR email = ? OR aadhaar_number = ?");
    $stmt->bind_param("sss", $doctor_id, $email, $aadhaar);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Doctor ID, Email, or Aadhaar already exists']);
        $stmt->close();
        $conn->close();
        exit;
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new doctor
    $stmt = $conn->prepare("INSERT INTO doctors (doctor_name, doctor_id, email, password, specialization, aadhaar_number, date_of_birth, gender, address, mobile_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $doctor_name, $doctor_id, $email, $hashed_password, $specialization, $aadhaar, $dob, $gender, $address, $mobile);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Doctor registered successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registration failed']);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>

