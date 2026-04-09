<?php
// Disable error display for API responses to prevent corruption of JSON output
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once '../config/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            throw new Exception('Invalid JSON input');
        }
        
        $patient_id = intval($data['patient_id'] ?? 0);
        $doctor_id = intval($data['doctor_id'] ?? 0);
        
        // If no doctor_id in data, try session
        if (!$doctor_id && isset($_SESSION['doctor_id'])) {
            $doctor_id = intval($_SESSION['doctor_id']);
        }
        
        if (!$doctor_id) {
            throw new Exception('Doctor ID required');
        }
        
        if (!$patient_id) {
            throw new Exception('Patient ID required');
        }
        
        $records = $data['records'] ?? [];
        $conn = getDBConnection();
        if (!$conn) {
            throw new Exception('Database connection failed');
        }
        
        // Use a transaction
        $conn->begin_transaction();
        
        // Delete existing records for this patient and doctor
        $deleteStmt = $conn->prepare("DELETE FROM doctor_pages WHERE patient_id = ? AND doctor_id = ?");
        if (!$deleteStmt) {
            throw new Exception('Delete prepare failed: ' . $conn->error);
        }
        $deleteStmt->bind_param("ii", $patient_id, $doctor_id);
        $deleteStmt->execute();
        $deleteStmt->close();
        
        // If no records, just commit and return success
        if (empty($records)) {
            $conn->commit();
            $conn->close();
            echo json_encode(['success' => true, 'message' => 'Doctor page cleared successfully']);
            exit;
        }
        
        // Insert new records
        $stmt = $conn->prepare("INSERT INTO doctor_pages (patient_id, doctor_id, symptoms, cause, prescription, test, nurse_instructions, nurse_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception('Insert prepare failed: ' . $conn->error);
        }
        
        foreach ($records as $record) {
            $symptoms = $record['symptoms'] ?? '';
            $cause = $record['cause'] ?? '';
            $prescription = $record['prescription'] ?? '';
            $test = $record['test'] ?? '';
            $nurse_instructions = '';
            $nurse_status = $record['nurse_status'] ?? 'pending';
            
            if (isset($record['nurse_medicines']) && is_array($record['nurse_medicines'])) {
                $nurse_instructions = implode(', ', $record['nurse_medicines']);
            }
            
            $stmt->bind_param("iissssss", $patient_id, $doctor_id, $symptoms, $cause, $prescription, $test, $nurse_instructions, $nurse_status);
            if (!$stmt->execute()) {
                throw new Exception('Failed to save record: ' . $stmt->error);
            }
        }
        
        $stmt->close();
        $conn->commit();
        $conn->close();
        
        echo json_encode(['success' => true, 'message' => 'Doctor page saved successfully']);
        
    } catch (Exception $e) {
        if (isset($conn) && $conn) {
            $conn->rollback();
            $conn->close();
        }
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>

