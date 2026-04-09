<?php
// Disable error display for API responses to prevent corruption of JSON output
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once '../config/database.php';
require_once '../helpers/patient_photo_helper.php';

header('Content-Type: application/json');

function sendJson($success, $message, $extra = []) {
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message
    ], $extra));
    exit;
}

function calculateAgeFromDob(?string $dobString): ?int {
    if (empty($dobString)) {
        return null;
    }

    try {
        $dob = new DateTime($dobString);
        $today = new DateTime();
        return $today->diff($dob)->y;
    } catch (Exception $e) {
        return null;
    }
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
$aadhaar = preg_replace('/\D+/', '', $data['aadhaar'] ?? '');

$conn = getDBConnection();
if (!$conn) {
    sendJson(false, 'Database connection failed');
}

$patient = null;
$stmt = null;

if ($email !== '' && $password !== '') {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendJson(false, 'Please enter a valid email address');
    }

    $stmt = $conn->prepare("
        SELECT id, aadhaar_number, name, email, password, date_of_birth, age, gender, address, mobile_number, scheme
        FROM patients
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

    $patient = $result->fetch_assoc();

    if (!password_verify($password, $patient['password'])) {
        $stmt->close();
        $conn->close();
        sendJson(false, 'Invalid email or password');
    }
} elseif ($aadhaar !== '') {
    if (strlen($aadhaar) !== 12) {
        sendJson(false, 'Please enter a valid 12-digit Aadhaar number');
    }

    $stmt = $conn->prepare("
        SELECT id, aadhaar_number, name, email, date_of_birth, age, gender, address, mobile_number, scheme
        FROM patients
        WHERE aadhaar_number = ?
        LIMIT 1
    ");

    if (!$stmt) {
        sendJson(false, 'Query prepare failed: ' . $conn->error);
    }

    $stmt->bind_param("s", $aadhaar);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result || $result->num_rows !== 1) {
        $stmt->close();
        $conn->close();
        sendJson(false, 'Patient not found');
    }

    $patient = $result->fetch_assoc();
} else {
    $conn->close();
    sendJson(false, 'Email/password or Aadhaar is required for login');
}

$photoPath = function_exists('getPatientPhotoPath')
    ? getPatientPhotoPath($patient['aadhaar_number'] ?? '')
    : null;

$patient['photo_path'] = $photoPath;

if (empty($patient['age']) && !empty($patient['date_of_birth'])) {
    $patient['age'] = calculateAgeFromDob($patient['date_of_birth']);
}

unset($patient['password']);

if ($stmt) {
    $stmt->close();
}
$conn->close();

sendJson(true, 'Patient found successfully', [
    'patient' => $patient
]);
?>