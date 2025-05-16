<?php
include '../../../config/dbconnect.php'; // Include your database connection

header('Content-Type: application/json');

$accessory_id = $_POST['accessory_id'];
$price_id = $_POST['price_id'];

// Check the number of serial numbers associated with the specific price entry
$query = "SELECT COUNT(*) AS min_quantity FROM serial_numbers WHERE accessory_id = ? AND accbill_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $accessory_id, $price_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['status' => 'success', 'min_quantity' => $row['min_quantity']]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch minimum quantity.']);
}

$stmt->close();
$conn->close();
?>
