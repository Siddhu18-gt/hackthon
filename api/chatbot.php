<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$message = trim($data['message'] ?? '');

if ($message === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Empty message'
    ]);
    exit;
}

// 🔑 YOUR GEMINI API KEY
$apiKey = 'AIzaSyCJUthVLvu5Bc3-_fe4ctdOC3GeDdQ5GMo';

// Medixa system prompt
$systemPrompt = "You are Medixa AI, a smart hospital assistant chatbot.
You help users with:
- Patient registration
- Login help
- Medical report explanation
- Hospital services
- Government schemes

Be simple, helpful, and polite.
Do not give dangerous medical advice.
If emergency → tell user to contact doctor immediately.";

$fullPrompt = $systemPrompt . "\n\nUser: " . $message;

$payload = [
    "contents" => [
        [
            "parts" => [
                ["text" => $fullPrompt]
            ]
        ]
    ],
    "generationConfig" => [
        "temperature" => 0.7,
        "topP" => 0.95,
        "topK" => 40,
        "maxOutputTokens" => 1024
    ]
];

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo json_encode([
        'success' => false,
        'message' => curl_error($ch)
    ]);
    curl_close($ch);
    exit;
}

curl_close($ch);

$result = json_decode($response, true);

if ($httpCode !== 200) {
    // Fallback response if API key fails
    $fallbackResponses = [
        "patient registration" => "To register as a patient, visit the Receptionist Portal and provide your Aadhaar number. We'll verify your identity and create your account.",
        "login" => "Doctors, test departments, nurses, and receptionists can login with their email. Patients login with Aadhaar number (12 digits).",
        "bed" => "We have beds available at Karwar General Hospital (12 beds), Coastal City Hospital (8 beds), and Riverfront Medical Center (5 beds).",
        "appointment" => "Visit your doctor or test department portal to schedule appointments. Our system is available 24/7.",
        "report" => "Your medical reports are available in the Patient Portal. Doctors can view and create reports here.",
        "test" => "Common tests available: Blood work, USG, X-Ray, ECG, CT Scan, MRI, and more. Ask reception for details.",
        "medicine" => "Prescribed medicines are managed through the nurse portal. Your doctor will prescribe medications during consultation.",
        "billing" => "Billing is handled by the receptionist. Payment is required before sending tests to the lab.",
        "emergency" => "For emergencies, please contact the hospital directly or visit the nearest emergency room immediately.",
        "scheme" => "We support government health schemes like AYUSHMAN. Ask reception about available schemes and eligibility."
    ];
    
    $userMessageLower = strtolower($message);
    $reply = "Hello! I'm Medixa AI. I can help with patient registration, login assistance, hospital services, and more. How can I assist you?";
    
    foreach ($fallbackResponses as $keyword => $response_text) {
        if (strpos($userMessageLower, $keyword) !== false) {
            $reply = $response_text;
            break;
        }
    }
    
    echo json_encode([
        'success' => true,
        'reply' => $reply
    ]);
    exit;
}

$reply = 'No reply';
if (isset($result['candidates'][0]['output'])) {
    $reply = $result['candidates'][0]['output'];
} elseif (isset($result['candidates'][0]['content'][0]['text'])) {
    $reply = $result['candidates'][0]['content'][0]['text'];
} elseif (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
    $reply = $result['candidates'][0]['content']['parts'][0]['text'];
}

echo json_encode([
    'success' => true,
    'reply' => $reply
]);
?>