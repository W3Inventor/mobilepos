<?php
include '../../../../config/dbconnect.php';
include '../payment-helper/send_sms.php';

header('Content-Type: application/json');

$repair_id = $_POST['repair_id'] ?? null;
$status = $_POST['status'] ?? null;
$actual_price = $_POST['actual_price'] ?? null;

$response = [];

if (!$repair_id || !$status) {
    echo json_encode(['error' => 'Invalid data']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE in_house_repair SET status = ?, actual_price = ? WHERE ir_id = ?");
    $stmt->bind_param("sdi", $status, $actual_price, $repair_id);
    $stmt->execute();

    // Log to history
    $history = $conn->prepare("INSERT INTO repair_status_history (repair_id, status) VALUES (?, ?)");
    $history->bind_param("is", $repair_id, $status);
    $history->execute();

    $response['success'] = 'Status updated successfully.';
} catch (Exception $e) {
    $response['error'] = 'Update failed: ' . $e->getMessage();
}

echo json_encode($response);

