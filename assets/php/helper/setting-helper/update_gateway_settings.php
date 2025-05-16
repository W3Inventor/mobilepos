<?php
include '../../../../config/dbconnect.php';

$response = ['success' => false, 'error' => ''];

// Get form data
$userId = $_POST['user_id'] ?? null;
$userPassword = $_POST['user_password'] ?? null;

// Check required fields
if (!$userId || !$userPassword) {
    $response['error'] = 'Required fields are missing or invalid.';
    echo json_encode($response);
    exit;
}

// Fetch current settings to update rather than inserting a new row
$sql = "SELECT * FROM textit_gateway_settings LIMIT 1";
$result = $conn->query($sql);
$currentSettings = $result->fetch_assoc();

// Prepare SQL for update or insert
$sql = $currentSettings ?
    "UPDATE textit_gateway_settings SET user_id=?, user_password=?, updated_at=NOW() WHERE id=?" :
    "INSERT INTO textit_gateway_settings (user_id, user_password) VALUES (?, ?)";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    $response['error'] = 'Prepare statement failed: ' . $conn->error;
    echo json_encode($response);
    exit;
}

// Bind parameters
if ($currentSettings) {
    $stmt->bind_param("ssi", $userId, $userPassword, $currentSettings['id']);
} else {
    $stmt->bind_param("ss", $userId, $userPassword);
}

// Execute statement
if ($stmt->execute()) {
    $response['success'] = true;
} else {
    $response['error'] = 'Statement execution failed: ' . $stmt->error;
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>
