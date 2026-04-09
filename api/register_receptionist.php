<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once '../config/database.php';

header('Content-Type: application/json');

function sendJson($success, $message) {
    echo json_encode([
        'success' => $success,
        'message' => $message
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson(false, 'Invalid request method');
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    sendJson(false, 'Invalid JSON input');
}

$hospital_name = trim($data['hospital_name'] ?? '');
$receptionist_name = trim($data['receptionist_name'] ?? '');
$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');
$confirm_password = trim($data['confirm_password'] ?? '');
$aadhaar = preg_replace('/\D+/', '', $data['aadhaar'] ?? '');
$dob = trim($data['date_of_birth'] ?? '');
$gender = trim($data['gender'] ?? '');
$address = trim($data['address'] ?? '');
$mobile = preg_replace('/\D+/', '', $data['mobile'] ?? '');

if ($hospital_name === '' || $receptionist_name === '' || $email === '' || $password === '' || $confirm_password === '' || $aadhaar === '') {
    sendJson(false, 'All required fields must be filled');
}

if (strlen($aadhaar) !== 12) {
    sendJson(false, 'Invalid Aadhaar number');
}

if ($mobile !== '' && strlen($mobile) !== 10) {
    sendJson(false, 'Invalid mobile number');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendJson(false, 'Please enter a valid email address');
}

if ($password !== $confirm_password) {
    sendJson(false, 'Passwords do not match');
}

$conn = getDBConnection();

if (!$conn) {
    sendJson(false, 'Database connection failed');
}

$checkStmt = $conn->prepare("SELECT id FROM receptionists WHERE email = ? OR aadhaar_number = ?");
if (!$checkStmt) {
    sendJson(false, 'Query prepare failed: ' . $conn->error);
}

$checkStmt->bind_param("ss", $email, $aadhaar);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result && $result->num_rows > 0) {
    $checkStmt->close();
    $conn->close();
    sendJson(false, 'Email or Aadhaar already exists');
}
$checkStmt->close();

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("
    INSERT INTO receptionists
    (hospital_name, receptionist_name, email, password, aadhaar_number, date_of_birth, gender, address, mobile_number)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    sendJson(false, 'Database error: ' . $conn->error);
}

$stmt->bind_param(
    "sssssssss",
    $hospital_name,
    $receptionist_name,
    $email,
    $hashed_password,
    $aadhaar,
    $dob,
    $gender,
    $address,
    $mobile
);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    sendJson(true, 'Receptionist registered successfully');
} else {
    $error = $stmt->error;
    $stmt->close();
    $conn->close();
    sendJson(false, 'Registration failed: ' . $error);
}
?>