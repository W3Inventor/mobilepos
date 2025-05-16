<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../../../../config/dbconnect.php';

$oldIMEI = $_POST['oldIMEI'] ?? '';
$newIMEI = $_POST['newIMEI'] ?? '';

if (empty($oldIMEI) || empty($newIMEI)) {
    echo json_encode(['success' => false, 'error' => 'Both old and new IMEI must be provided.']);
    exit;
}

$query = "UPDATE mobile SET imei = ? WHERE imei = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ss', $newIMEI, $oldIMEI);

$response = [];
if ($stmt->execute()) {
    $response['success'] = true;
} else {
    $response['success'] = false;
    $response['error'] = $stmt->error;
}

echo json_encode($response);
$stmt->close();
$conn->close();
