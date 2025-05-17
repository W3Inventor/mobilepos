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

    // Send SMS if Ready to Pickup
    if ($status === 'Ready to Pickup') {
        $query = $conn->prepare("SELECT c.full_name, c.mobile_number FROM in_house_repair r JOIN customers c ON r.customer_id = c.customer_id WHERE r.ir_id = ?");
        $query->bind_param("i", $repair_id);
        $query->execute();
        $query->bind_result($name, $mobile);
        $query->fetch();
        $query->close();

        $message = "Hi $name, your repair (ID: $repair_id) is ready for pickup. Final bill: LKR " . number_format($actual_price, 2) . ". Thank you!";
        $sms_result = sendSms($mobile, $message);
    }

    $response['success'] = 'Status updated successfully.';
} catch (Exception $e) {
    $response['error'] = 'Update failed: ' . $e->getMessage();
}

echo json_encode($response);

