<?php
// Disable error display for API responses to prevent corruption of JSON output
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required']);
        exit;
    }
    
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("SELECT id, doctor_name, doctor_id, email, password, specialization FROM doctors WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $doctor = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $doctor['password'])) {
            $_SESSION['doctor_id'] = $doctor['id'];
            $_SESSION['doctor_name'] = $doctor['doctor_name'];
            $_SESSION['doctor_specialization'] = $doctor['specialization'];
            $_SESSION['doctor_email'] = $doctor['email'];
            
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'doctor' => [
                    'id' => $doctor['id'],
                    'name' => $doctor['doctor_name'],
                    'specialization' => $doctor['specialization']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>

