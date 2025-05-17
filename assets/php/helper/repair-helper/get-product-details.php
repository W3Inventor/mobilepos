<?php
// get-product-details.php
require '../../../../config/dbconnect.php';
header('Content-Type: application/json; charset=UTF-8');

if (!isset($_POST['accessory_id'])) {
    echo json_encode(['error' => 'No accessory ID provided']);
    exit;
}

$accessory_id = intval($_POST['accessory_id']);
$response = [
    'accessory_id' => $accessory_id,
    'prices' => [],
    'serials' => []
];

// Get latest selling price(s) from accessories_price
$price_stmt = $conn->prepare("SELECT selling FROM accessories_price WHERE accessory_id = ? ORDER BY id DESC LIMIT 3");
$price_stmt->bind_param("i", $accessory_id);
$price_stmt->execute();
$price_result = $price_stmt->get_result();
while ($row = $price_result->fetch_assoc()) {
    $response['prices'][] = $row['selling'];
}
$price_stmt->close();

// Get available serial numbers from serial_numbers
$serial_stmt = $conn->prepare("SELECT serial_number FROM serial_numbers WHERE accessory_id = ? AND status = 'In Stock'");
$serial_stmt->bind_param("i", $accessory_id);
$serial_stmt->execute();
$serial_result = $serial_stmt->get_result();
while ($row = $serial_result->fetch_assoc()) {
    $response['serials'][] = $row['serial_number'];
}
$serial_stmt->close();

echo json_encode($response);
