<?php
include '../../../../config/dbconnect.php';
header('Content-Type: application/json');

$repair_id = $_POST['repair_id'] ?? null;
$status = $_POST['status'] ?? '';

if (!$repair_id || !$status) {
    echo json_encode(['error' => 'Missing data']);
    exit;
}

// Update status in in_house_repair
$stmt = $conn->prepare("UPDATE in_house_repair SET status = ? WHERE ir_id = ?");
$stmt->bind_param("si", $status, $repair_id);
if (!$stmt->execute()) {
    echo json_encode(['error' => 'DB update failed']);
    exit;
}
$stmt->close();

// Insert into repair_status_history
$stmt2 = $conn->prepare("INSERT INTO repair_status_history (repair_id, status) VALUES (?, ?)");
$stmt2->bind_param("is", $repair_id, $status);
$stmt2->execute();
$stmt2->close();

echo json_encode(['success' => 'Status updated successfully.']);
