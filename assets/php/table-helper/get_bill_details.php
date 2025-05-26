<?php
include '../../../config/dbconnect.php';
header('Content-Type: application/json');

$accessory_id = $_GET['accessory_id'] ?? null;

$response = ['status' => 'error', 'message' => 'Invalid input', 'data' => []];
if (!$accessory_id) {
    echo json_encode($response);
    exit;
}

$sql = "SELECT 
            b.billid, b.billno, b.date, b.bill_amount,
            s.supplier_name,
            ap.selling AS selling_price,
            ap.buying AS buying_price,
            ap.quantity,
            ap.id AS price_id
        FROM accessories_price ap
        JOIN bill b ON ap.billid = b.billid
        LEFT JOIN supplier s ON b.supplier_id = s.supplier_id
        WHERE ap.accessory_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $accessory_id);
$stmt->execute();
$result = $stmt->get_result();

$bills = [];

while ($bill = $result->fetch_assoc()) {
    $price_id = $bill['price_id'];

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
    $bills[] = $bill;

    $serials_stmt->close();
}

$stmt->close();
$conn->close();

$response = ['status' => 'success', 'data' => $bills];
echo json_encode($response);
