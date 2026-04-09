<?php
require_once '../config/database.php';
require_once '../helpers/patient_photo_helper.php';

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ob_start();

header('Content-Type: application/json; charset=utf-8');

function outputJson(array $payload): void {
    if (ob_get_length()) {
        ob_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload);
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

function prepareStatement($conn, $query) {
    mysqli_report(MYSQLI_REPORT_OFF);

    try {
        $stmt = $conn->prepare($query);
    } catch (mysqli_sql_exception $e) {
        error_log('fetch_aadhaar prepare exception: ' . $e->getMessage() . ' query: ' . trim(preg_replace('/\s+/', ' ', $query)));
        return null;
    }

    if (!$stmt) {
        error_log('fetch_aadhaar prepare failed: ' . $conn->error . ' query: ' . trim(preg_replace('/\s+/', ' ', $query)));
    }
    return $stmt;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    outputJson(['success' => false, 'message' => 'Invalid request method']);
}

$data = json_decode(file_get_contents('php://input'), true);
$aadhaar = $data['aadhaar'] ?? '';
$aadhaar = preg_replace('/\D+/', '', $aadhaar);

if (empty($aadhaar) || strlen($aadhaar) !== 12) {
    outputJson(['success' => false, 'message' => 'Enter valid Aadhaar']);
}

$conn = getDBConnection();
$otp = '413256';


// =====================
// 1) CHECK PATIENTS TABLE
// =====================
if ($patientStmt = prepareStatement($conn, "
    SELECT aadhaar_number, name, date_of_birth, age, gender, address, mobile_number, scheme 
    FROM patients 
    WHERE aadhaar_number = ? 
    LIMIT 1
")) {
    $patientStmt->bind_param("s", $aadhaar);
    $patientStmt->execute();
    $result = $patientStmt->get_result();

    if ($result && $result->num_rows === 1) {
        $patient = $result->fetch_assoc();

        $photoPath = getPatientPhotoPath($aadhaar);

        outputJson([
            'success' => true,
            'is_new' => false,
            'data' => [
                'name' => $patient['name'],
                'date_of_birth' => $patient['date_of_birth'],
                'age' => $patient['age'] ?: calculateAgeFromDob($patient['date_of_birth']),
                'gender' => $patient['gender'],
                'address' => $patient['address'],
                'mobile_number' => $patient['mobile_number'],
                'scheme' => $patient['scheme'],
                'photo_path' => $photoPath
            ],
            'otp' => $otp
        ]);
    }
}


// =====================
// 2) CHECK DOCTORS TABLE
// =====================
if ($doctorStmt = prepareStatement($conn, "
    SELECT aadhaar_number, doctor_name AS name, date_of_birth, gender, address, mobile_number 
    FROM doctors 
    WHERE aadhaar_number = ? 
    LIMIT 1
")) {
    $doctorStmt->bind_param("s", $aadhaar);
    $doctorStmt->execute();
    $result = $doctorStmt->get_result();

    if ($result && $result->num_rows === 1) {
        $doctor = $result->fetch_assoc();

        $photoPath = getPatientPhotoPath($aadhaar);

        outputJson([
            'success' => true,
            'is_new' => false,
            'data' => [
                'name' => $doctor['name'],
                'date_of_birth' => $doctor['date_of_birth'],
                'age' => calculateAgeFromDob($doctor['date_of_birth']),
                'gender' => $doctor['gender'],
                'address' => $doctor['address'],
                'mobile_number' => $doctor['mobile_number'],
                'scheme' => null,
                'photo_path' => $photoPath
            ],
            'otp' => $otp
        ]);
    }
}

// =====================
// 3) CHECK NURSES TABLE
// =====================
if ($nurseStmt = prepareStatement($conn, "
    SELECT aadhaar_number, nurse_name AS name, date_of_birth, gender, address, mobile_number 
    FROM nurses 
    WHERE aadhaar_number = ? 
    LIMIT 1
")) {
    $nurseStmt->bind_param("s", $aadhaar);
    $nurseStmt->execute();
    $result = $nurseStmt->get_result();

    if ($result && $result->num_rows === 1) {
        $nurse = $result->fetch_assoc();

        $photoPath = getPatientPhotoPath($aadhaar);

        outputJson([
            'success' => true,
            'is_new' => false,
            'data' => [
                'name' => $nurse['name'],
                'date_of_birth' => $nurse['date_of_birth'],
                'age' => calculateAgeFromDob($nurse['date_of_birth']),
                'gender' => $nurse['gender'],
                'address' => $nurse['address'],
                'mobile_number' => $nurse['mobile_number'],
                'scheme' => null,
                'photo_path' => $photoPath
            ],
            'otp' => $otp
        ]);
    }
}

// =====================
// 4) CHECK RECEPTIONISTS TABLE
// =====================
if ($receptionistStmt = prepareStatement($conn, "
    SELECT aadhaar_number, receptionist_name AS name, date_of_birth, gender, address, mobile_number 
    FROM receptionists 
    WHERE aadhaar_number = ? 
    LIMIT 1
")) {
    $receptionistStmt->bind_param("s", $aadhaar);
    $receptionistStmt->execute();
    $result = $receptionistStmt->get_result();

    if ($result && $result->num_rows === 1) {
        $receptionist = $result->fetch_assoc();

        $photoPath = getPatientPhotoPath($aadhaar);

        outputJson([
            'success' => true,
            'is_new' => false,
            'data' => [
                'name' => $receptionist['name'],
                'date_of_birth' => $receptionist['date_of_birth'],
                'age' => calculateAgeFromDob($receptionist['date_of_birth']),
                'gender' => $receptionist['gender'],
                'address' => $receptionist['address'],
                'mobile_number' => $receptionist['mobile_number'],
                'scheme' => null,
                'photo_path' => $photoPath
            ],
            'otp' => $otp
        ]);
    }
}

// =====================
// 5) CHECK DEMO TABLE
// =====================
if ($demoStmt = prepareStatement($conn, "
    SELECT aadhaar_number, name, date_of_birth, gender, address, mobile_number, scheme 
    FROM demo_aadhaar_data 
    WHERE aadhaar_number = ? 
    LIMIT 1
")) {
    $demoStmt->bind_param("s", $aadhaar);
    $demoStmt->execute();
    $result = $demoStmt->get_result();

    if ($result && $result->num_rows === 1) {
        $demo = $result->fetch_assoc();

        $photoPath = getPatientPhotoPath($aadhaar);

        outputJson([
            'success' => true,
            'is_new' => false,
            'data' => [
                'name' => $demo['name'],
                'date_of_birth' => $demo['date_of_birth'],
                'age' => calculateAgeFromDob($demo['date_of_birth']),
                'gender' => $demo['gender'],
                'address' => $demo['address'],
                'mobile_number' => $demo['mobile_number'],
                'scheme' => $demo['scheme'],
                'photo_path' => $photoPath
            ],
            'otp' => $otp
        ]);
    }
}


// =====================
// 3) NEW AADHAAR
// =====================
outputJson([
    'success' => true,
    'is_new' => true,
    'data' => [
        'name' => '',
        'date_of_birth' => '',
        'age' => '',
        'gender' => '',
        'address' => '',
        'mobile_number' => '',
        'scheme' => null,
        'photo_path' => null
    ],
    'otp' => $otp,
    'message' => 'New Aadhaar → enter details manually'
]);

$conn->close();
?>