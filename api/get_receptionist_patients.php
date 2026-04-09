<?php
// Disable error display for API responses to prevent corruption of JSON output
ini_set('display_errors', 0);
error_reporting(E_ALL);

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

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJson(false, 'Invalid request method');
}

if (!isset($_SESSION['receptionist_id'])) {
    sendJson(false, 'Please login first');
}

$receptionist_id = (int)$_SESSION['receptionist_id'];

$conn = getDBConnection();
if (!$conn) {
    sendJson(false, 'Database connection failed');
}

$stmt = $conn->prepare("
    SELECT 
        p.id,
        p.aadhaar_number,
        p.name,
        p.date_of_birth,
        p.age,
        p.gender,
        p.address,
        p.mobile_number,
        p.scheme,
        p.scheme_discount,
        p.cause,
        p.assigned_doctor_id,
        p.specialization,
        pr.registration_date,
        pr.status,
        d.doctor_name AS doctor_name
    FROM patient_registrations pr
    INNER JOIN patients p ON pr.patient_id = p.id
    LEFT JOIN doctors d ON p.assigned_doctor_id = d.id
    WHERE pr.receptionist_id = ?
    ORDER BY pr.registration_date DESC
");

if (!$stmt) {
    $error = $conn->error;
    $conn->close();
    sendJson(false, 'Prepare failed: ' . $error);
}

$stmt->bind_param("i", $receptionist_id);
$stmt->execute();
$result = $stmt->get_result();

$patients = [];
while ($row = $result->fetch_assoc()) {
    $patients[] = $row;
}

$stmt->close();
$conn->close();

sendJson(true, 'Patients fetched successfully', [
    'patients' => $patients,
    'count' => count($patients)
]);
?>