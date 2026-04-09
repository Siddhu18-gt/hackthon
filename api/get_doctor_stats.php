<?php
// Disable error display for API responses to prevent corruption of JSON output
ini_set('display_errors', 0);
error_reporting(E_ALL);

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

// Check if doctor is logged in
if (!isset($_SESSION['doctor_id'])) {
    sendJson(false, 'Please login first');
}

$doctor_id = (int)$_SESSION['doctor_id'];

$conn = getDBConnection();
if (!$conn) {
    sendJson(false, 'Database connection failed');
}

// Get total patients assigned to this doctor
$totalPatients = 0;
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM patients WHERE assigned_doctor_id = ?");
if ($stmt) {
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $totalPatients = $row['count'];
    }
    $stmt->close();
}

// Get today's registrations (registrations with today's date)
$todayPatients = 0;
$stmt = $conn->prepare("
    SELECT COUNT(*) as count 
    FROM patient_registrations pr
    JOIN patients p ON pr.patient_id = p.id
    WHERE p.assigned_doctor_id = ? AND DATE(pr.registration_date) = CURDATE()
");
if ($stmt) {
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $todayPatients = $row['count'];
    }
    $stmt->close();
}

// Get pending reports (nurse_status = 'pending' in doctor_pages for this doctor's patients)
$pendingReports = 0;
$stmt = $conn->prepare("
    SELECT COUNT(*) as count 
    FROM doctor_pages 
    WHERE doctor_id = ? AND nurse_status = 'pending'
");
if ($stmt) {
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $pendingReports = $row['count'];
    }
    $stmt->close();
}

// Get recent patients (last 5)
$recentPatients = [];
$stmt = $conn->prepare("
    SELECT p.id, p.name, p.aadhaar_number, p.gender, p.age, pr.registration_date
    FROM patient_registrations pr
    JOIN patients p ON pr.patient_id = p.id
    WHERE p.assigned_doctor_id = ?
    ORDER BY pr.registration_date DESC
    LIMIT 5
");
if ($stmt) {
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $recentPatients[] = $row;
    }
    $stmt->close();
}

$conn->close();

sendJson(true, 'Doctor stats fetched successfully', [
    'stats' => [
        'total_patients' => $totalPatients,
        'today_patients' => $todayPatients,
        'pending_reports' => $pendingReports
    ],
    'recent_patients' => $recentPatients
]);
?>