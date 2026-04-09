<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$patient_id = $_GET['patient_id'] ?? 0;

if (empty($patient_id)) {
    echo json_encode(['success' => false, 'message' => 'Patient ID required']);
    exit;
}

$conn = getDBConnection();

// Get nurse prescriptions from doctor_pages
$stmt = $conn->prepare("SELECT dp.id, dp.patient_id, dp.doctor_id, dp.nurse_instructions 
                       FROM doctor_pages dp 
                       WHERE dp.patient_id = ? AND dp.nurse_instructions IS NOT NULL AND dp.nurse_instructions != ''");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

$prescriptions = [];
while ($row = $result->fetch_assoc()) {
    // Parse nurse instructions (comma-separated medicines)
    $medicines = explode(',', $row['nurse_instructions']);
    
    foreach ($medicines as $medicine) {
        $medicine = trim($medicine);
        if (!empty($medicine)) {
            // Medicine format: "Medicine Name - Dosage" or just "Medicine Name"
            if (strpos($medicine, ' - ') !== false) {
                $parts = explode(' - ', $medicine, 2);
                $med_name = trim($parts[0]);
                $dosage = isset($parts[1]) ? trim($parts[1]) : 'As prescribed';
            } else {
                // Try to extract dosage from medicine string
                $parts = preg_split('/\s+(\d+)/', $medicine, 2, PREG_SPLIT_DELIM_CAPTURE);
                $med_name = trim($parts[0]);
                $dosage = isset($parts[1]) ? trim($parts[1]) : 'As prescribed';
            }
            
            // Check if already given
            $check_stmt = $conn->prepare("SELECT given_status FROM nurse_prescriptions WHERE patient_id = ? AND doctor_id = ? AND medicine_name = ?");
            $check_stmt->bind_param("iis", $row['patient_id'], $row['doctor_id'], $med_name);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            $given_status = false;
            $prescription_id = null;
            
            if ($check_result->num_rows > 0) {
                $presc = $check_result->fetch_assoc();
                $given_status = $presc['given_status'];
            } else {
                // Create prescription record
                $insert_stmt = $conn->prepare("INSERT INTO nurse_prescriptions (patient_id, doctor_id, medicine_name, dosage, given_status) VALUES (?, ?, ?, ?, 0)");
                $insert_stmt->bind_param("iiss", $row['patient_id'], $row['doctor_id'], $med_name, $dosage);
                $insert_stmt->execute();
                $prescription_id = $conn->insert_id;
                $insert_stmt->close();
            }
            
            $check_stmt->close();
            
            $prescriptions[] = [
                'id' => $prescription_id ?? 0,
                'medicine_name' => $med_name,
                'dosage' => $dosage,
                'given_status' => $given_status
            ];
        }
    }
}

echo json_encode(['success' => true, 'prescriptions' => $prescriptions]);

$stmt->close();
$conn->close();
?>

