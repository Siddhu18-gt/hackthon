<?php
require_once '../config/database.php';
require_once '../helpers/patient_photo_helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $aadhaar = preg_replace('/\s+/', '', $data['aadhaar'] ?? '');
    
    if (empty($aadhaar) || strlen($aadhaar) !== 12) {
        echo json_encode(['success' => false, 'message' => 'Invalid Aadhaar number']);
        exit;
    }
    
    $conn = getDBConnection();
    
    // Get patient
    $stmt = $conn->prepare("SELECT id, aadhaar_number, name, email, date_of_birth, age, gender, address, mobile_number, scheme, scheme_discount, cause, assigned_doctor_id, specialization, photo_path, created_at FROM patients WHERE aadhaar_number = ?");
    $stmt->bind_param("s", $aadhaar);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $patient = $result->fetch_assoc();

        if (empty($patient['photo_path'])) {
            $detectedPhoto = getPatientPhotoPath($patient['aadhaar_number'] ?? '');
            if ($detectedPhoto) {
                $patient['photo_path'] = $detectedPhoto;
                $updateStmt = $conn->prepare("UPDATE patients SET photo_path = ? WHERE id = ?");
                if ($updateStmt) {
                    $updateStmt->bind_param("si", $detectedPhoto, $patient['id']);
                    $updateStmt->execute();
                    $updateStmt->close();
                }
            }
        }
        
        // Get billing records
        $patient_id = $patient['id'];
        $stmt2 = $conn->prepare("SELECT * FROM billing WHERE patient_id = ?");
        $stmt2->bind_param("i", $patient_id);
        $stmt2->execute();
        $billingResult = $stmt2->get_result();
        
        $billing = [];
        while ($row = $billingResult->fetch_assoc()) {
            $billing[] = [
                'id' => $row['id'],
                'item_type' => $row['item_type'] ?? 'test',
                'test_name' => $row['test_name'] ?? '',
                'amount' => floatval($row['amount']),
                'status' => $row['status'],
                'scheme' => $row['scheme'] ?? '',
                'discount_percentage' => floatval($row['discount_percentage'] ?? 0),
                'discount_amount' => floatval($row['discount_amount'] ?? 0),
                'subtotal' => floatval($row['subtotal'] ?? 0),
                'total_amount' => floatval($row['total_amount'] ?? 0)
            ];
        }
        
        echo json_encode([
            'success' => true,
            'patient' => $patient,
            'billing' => $billing
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Patient not found']);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>

