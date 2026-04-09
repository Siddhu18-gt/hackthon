<?php
// Mock the POST request for testing the API
$_SERVER['REQUEST_METHOD'] = 'POST';

// Create a mock input file with test data
$test_data = [
    'hospital_name' => 'Test Hospital',
    'receptionist_name' => 'Test Receptionist',
    'email' => 'test_' . time() . '@medixa.com',
    'password' => 'password123',
    'confirm_password' => 'password123',
    'aadhaar' => '123456789012',
    'date_of_birth' => '1995-01-01',
    'gender' => 'Male',
    'address' => 'Test Address',
    'mobile' => '9876543210'
];

// Capture output
ob_start();
include 'api/register_receptionist.php';
$output = ob_get_clean();

// Check if registration was successful
$result = json_decode($output, true);
if ($result && $result['success']) {
    echo "SUCCESS: " . $result['message'] . "\n";
} else {
    echo "FAILED: " . ($result['message'] ?? 'Unknown error') . "\n";
    echo "Full output: " . $output . "\n";
}
?>
