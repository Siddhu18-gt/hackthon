<?php
// Disable error display for API responses to prevent corruption of JSON output
ini_set('display_errors', 0);
error_reporting(E_ALL);

session_start();
require_once '../config/database.php';
require_once '../helpers/specialization_helper.php';

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

$aadhaar = preg_replace('/\D+/', '', $data['aadhaar'] ?? '');
$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');
$confirmPassword = trim($data['confirm_password'] ?? '');
$dob = trim($data['date_of_birth'] ?? '');
$age = intval($data['age'] ?? 0);
$gender = trim($data['gender'] ?? '');
$address = trim($data['address'] ?? '');
$mobile = preg_replace('/\D+/', '', trim($data['mobile'] ?? ''));
$scheme = trim($data['scheme'] ?? '');
$scheme_discount = floatval($data['scheme_discount'] ?? 0);
$cause = trim($data['cause'] ?? '');
$specialization = trim($data['specialization'] ?? '');

if (strlen($aadhaar) !== 12) {
    sendJson(false, 'Invalid Aadhaar number');
}

if ($name === '' || $email === '' || $password === '' || $confirmPassword === '' || $dob === '' || $age <= 0 || $gender === '' || $address === '' || $mobile === '') {
    sendJson(false, 'All fields are required');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendJson(false, 'Please enter a valid email address');
}

if (strlen($mobile) !== 10) {
    sendJson(false, 'Invalid mobile number');
}

if ($password !== $confirmPassword) {
    sendJson(false, 'Passwords do not match');
}

$conn = getDBConnection();
if (!$conn) {
    sendJson(false, 'Database connection failed');
}

$conn->begin_transaction();

try {
    $checkStmt = $conn->prepare("SELECT id FROM patients WHERE aadhaar_number = ? OR email = ?");
    if (!$checkStmt) {
        throw new Exception('Check query prepare failed: ' . $conn->error);
    }

    $checkStmt->bind_param("ss", $aadhaar, $email);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result && $result->num_rows > 0) {
        $checkStmt->close();
        throw new Exception('Patient already exists');
    }
    $checkStmt->close();

    $assignedDoctorId = null;

    if ($specialization !== '') {
        $doctorStmt = $conn->prepare("SELECT id FROM doctors WHERE specialization = ? LIMIT 1");
        if ($doctorStmt) {
            $doctorStmt->bind_param("s", $specialization);
            $doctorStmt->execute();
            $doctorResult = $doctorStmt->get_result();
            if ($doctorResult && $doctorResult->num_rows > 0) {
                $doctor = $doctorResult->fetch_assoc();
                $assignedDoctorId = (int)$doctor['id'];
            }
            $doctorStmt->close();
        }
    }

    if ($assignedDoctorId === null) {
        $doctorQuery = $conn->query("SELECT id FROM doctors ORDER BY id ASC LIMIT 1");
        if ($doctorQuery && $doctorQuery->num_rows > 0) {
            $doctor = $doctorQuery->fetch_assoc();
            $assignedDoctorId = (int)$doctor['id'];
        }
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        INSERT INTO patients
        (aadhaar_number, name, email, password, date_of_birth, age, gender, address, mobile_number, scheme, scheme_discount, cause, assigned_doctor_id, specialization)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        throw new Exception('Insert query prepare failed: ' . $conn->error);
    }

    $stmt->bind_param(
        "sssssissssdsis",
        $aadhaar,
        $name,
        $email,
        $hashedPassword,
        $dob,
        $age,
        $gender,
        $address,
        $mobile,
        $scheme,
        $scheme_discount,
        $cause,
        $assignedDoctorId,
        $specialization
    );

    if (!$stmt->execute()) {
        throw new Exception('Patient insert failed: ' . $stmt->error);
    }

    $patientId = $stmt->insert_id;
    $stmt->close();

    if (isset($_SESSION['receptionist_id'])) {
        $receptionistId = (int)$_SESSION['receptionist_id'];

        $regStmt = $conn->prepare("
            INSERT INTO patient_registrations (patient_id, receptionist_id, registration_date, status)
            VALUES (?, ?, NOW(), 'registered')
        ");

        if (!$regStmt) {
            throw new Exception('Registration link prepare failed: ' . $conn->error);
        }

        $regStmt->bind_param("ii", $patientId, $receptionistId);

        if (!$regStmt->execute()) {
            throw new Exception('Patient registration tracking failed: ' . $regStmt->error);
        }

        $regStmt->close();
    }

    $conn->commit();
    $conn->close();

    sendJson(true, 'Patient registered successfully', [
        'patient_id' => $patientId,
        'assigned_doctor_id' => $assignedDoctorId
    ]);
} catch (Exception $e) {
    $conn->rollback();
    $conn->close();
    sendJson(false, $e->getMessage());
}
?>