<?php
include '../../../config/dbconnect.php';
header('Content-Type: application/json');

$price_id = $_POST['price_id'] ?? null;
$price = $_POST['price'] ?? null;

if (!$price_id || !is_numeric($price)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

$stmt = $conn->prepare("UPDATE accessories_price SET selling = ? WHERE id = ?");
$stmt->bind_param("di", $price, $price_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Price updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update price']);
}

$stmt->close();
$conn->close();
