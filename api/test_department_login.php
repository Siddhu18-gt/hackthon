<?php
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
    
    $stmt = $conn->prepare("SELECT id, department_name, email, password FROM test_departments WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $dept = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $dept['password'])) {
            $_SESSION['test_department_id'] = $dept['id'];
            $_SESSION['test_department_name'] = $dept['department_name'];
            $_SESSION['test_department_email'] = $dept['email'];
            
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'department' => [
                    'id' => $dept['id'],
                    'name' => $dept['department_name']
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

