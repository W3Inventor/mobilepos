<?php
include '../../../../config/dbconnect.php';

$response = ['success' => false, 'error' => ''];

// Get form data with default fallback values
$fromEmail = $_POST['from_email'] ?? null;
$fromName = $_POST['from_name'] ?? null;
$smtpHost = $_POST['smtp_host'] ?? null;
$smtpPort = $_POST['smtp_port'] ?? null;
$encryption = $_POST['encryption'] ?? null;
$smtpUsername = $_POST['smtp_username'] ?? null;
$smtpPassword = $_POST['smtp_password'] ?? null;

// Check required fields are not null
if (!$fromEmail || !$fromName || !$smtpHost || !$smtpPort || !$smtpUsername || !$smtpPassword) {
    $response['error'] = 'Required fields are missing or invalid.';
    echo json_encode($response);
    exit;
}

// Fetch current settings to update instead of inserting a new row
$sql = "SELECT * FROM smtp_settings LIMIT 1";
$result = $conn->query($sql);

if ($result === false) {
    $response['error'] = 'Database query failed: ' . $conn->error;
    echo json_encode($response);
    exit;
}

$currentSettings = $result->fetch_assoc();

$sql = $currentSettings ?
    "UPDATE smtp_settings SET from_email=?, from_name=?, smtp_host=?, smtp_port=?, encryption=?, smtp_username=?, smtp_password=? WHERE id=?" :
    "INSERT INTO smtp_settings (from_email, from_name, smtp_host, smtp_port, encryption, smtp_username, smtp_password) VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    $response['error'] = 'Prepare statement failed: ' . $conn->error;
    echo json_encode($response);
    exit;
}

if ($currentSettings) {
    $stmt->bind_param("sssssssi", $fromEmail, $fromName, $smtpHost, $smtpPort, $encryption, $smtpUsername, $smtpPassword, $currentSettings['id']);
} else {
    $stmt->bind_param("sssssss", $fromEmail, $fromName, $smtpHost, $smtpPort, $encryption, $smtpUsername, $smtpPassword);
}

if ($stmt->execute()) {
    $response['success'] = true;
} else {
    $response['error'] = 'Statement execution failed: ' . $stmt->error;
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>
