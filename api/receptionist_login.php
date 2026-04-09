<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

function sendJson($success, $message, $extra = []) {
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message
    ], $extra));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson(false, 'Invalid request method');
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    sendJson(false, 'Invalid JSON input');
}

$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

if ($email === '' || $password === '') {
    sendJson(false, 'Email and password are required');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendJson(false, 'Please enter a valid email address');
}

$conn = getDBConnection();

if (!$conn) {
    sendJson(false, 'Database connection failed');
}

$stmt = $conn->prepare("
    SELECT id, hospital_name, receptionist_name, email, password
    FROM receptionists
    WHERE email = ?
    LIMIT 1
");

if (!$stmt) {
    sendJson(false, 'Query prepare failed: ' . $conn->error);
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows !== 1) {
    $stmt->close();
    $conn->close();
    sendJson(false, 'Invalid email or password');
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user['password'])) {
    $stmt->close();
    $conn->close();
    sendJson(false, 'Invalid email or password');
}

$_SESSION['receptionist_id'] = $user['id'];
$_SESSION['receptionist_name'] = $user['receptionist_name'];
$_SESSION['hospital_name'] = $user['hospital_name'];
$_SESSION['email'] = $user['email'];

$stmt->close();
$conn->close();

sendJson(true, 'Login successful', [
    'user' => [
        'id' => $user['id'],
        'name' => $user['receptionist_name'],
        'hospital' => $user['hospital_name']
    ]
]);
?>