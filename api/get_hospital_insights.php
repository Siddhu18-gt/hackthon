<?php
// Medixa Insights API - Real-time hospital metrics
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

$conn = getDBConnection();
if (!$conn) {
    sendJson(false, 'Database connection failed');
}

// 1. Total Patients (from receptionist entries)
$totalPatients = 0;
$res = $conn->query("SELECT COUNT(*) as count FROM patients");
if ($row = $res->fetch_assoc()) $totalPatients = $row['count'];

// 2. Doctor Availability
$totalDoctors = 0;
$res = $conn->query("SELECT COUNT(*) as count FROM doctors");
if ($row = $res->fetch_assoc()) $totalDoctors = $row['count'];

// 3. Bed Management (Logic: Let's assume a total of 100 beds for demo)
$totalBeds = 100;
$bedsOccupied = 0;
// We check doctor_pages for is_admitted = TRUE
$res = $conn->query("SELECT COUNT(*) as count FROM doctor_pages WHERE is_admitted = 1");
if ($row = $res->fetch_assoc()) $bedsOccupied = $row['count'];
$bedsRemaining = $totalBeds - $bedsOccupied;

// 4. Business & Growth (Revenue from billing)
$totalRevenue = 0;
$res = $conn->query("SELECT SUM(total_amount) as total FROM billing WHERE status = 'paid'");
if ($row = $res->fetch_assoc()) $totalRevenue = (float)$row['total'];

// 5. Growth (New patients in last 7 days)
$newPatientsLastWeek = 0;
$res = $conn->query("SELECT COUNT(*) as count FROM patients WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
if ($row = $res->fetch_assoc()) $newPatientsLastWeek = $row['count'];

$conn->close();

sendJson(true, 'Insights fetched successfully', [
    'metrics' => [
        'patients' => $totalPatients,
        'doctors' => $totalDoctors,
        'beds' => [
            'total' => $totalBeds,
            'occupied' => $bedsOccupied,
            'remaining' => $bedsRemaining
        ],
        'revenue' => number_format($totalRevenue, 2),
        'growth' => $newPatientsLastWeek
    ]
]);
?>