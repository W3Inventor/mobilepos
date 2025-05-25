<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../../../config/dbconnect.php';

header('Content-Type: application/json');

$accessory_id = $_GET['accessory_id'] ?? null;
$price_id = $_GET['price_id'] ?? null;

$response = ['status' => 'error', 'message' => 'Invalid input', 'data' => []];

if (!$accessory_id || !$price_id) {
    echo json_encode($response);
    exit;
}

$sql = "SELECT b.billid, b.billno, b.date, b.bill_amount, s.supplier_name
        FROM accessories_price ap
        JOIN bill b ON ap.billid = b.billid
        LEFT JOIN supplier s ON b.supplier_id = s.supplier_id
        WHERE ap.accessory_id = ? AND ap.id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    $response['message'] = 'Prepare failed: ' . $conn->error;
    echo json_encode($response);
    exit;
}

$stmt->bind_param("ii", $accessory_id, $price_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $bill = $result->fetch_assoc();

    // Get serial numbers associated with this price_id
    $serials_sql = "SELECT serial_number, status FROM serial_numbers WHERE accbill_id = ?";
    $serials_stmt = $conn->prepare($serials_sql);
    $serials_stmt->bind_param("i", $price_id);
    $serials_stmt->execute();
    $serials_result = $serials_stmt->get_result();

    $serials = [];
    while ($s = $serials_result->fetch_assoc()) {
        $serials[] = $s;
    }

    $bill['serials'] = $serials;

    $response = ['status' => 'success', 'data' => $bill];
} else {
    $response['message'] = 'No bill found for given accessory.';
}

$stmt->close();
$conn->close();

echo json_encode($response);
