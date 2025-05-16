<?php
include '../../../config/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pmethod_id = $_POST['id'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    $cost_one = $_POST['cost-one'] ?? '';
    $cost_two = $_POST['cost-two'] ?? '';
    $payment_method_type = $_POST['payment_method_type'] ?? '';
    $reference = $_POST['reference'] ?? '';

    // Validate fields, allowing for "0" and handling cost with percentage
    if (trim($payment_method) === '' || trim($payment_method_type) === '' || trim($cost_one) === '' || trim($pmethod_id) === '') {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        exit;
    }

    // Remove '%' sign if present in costs
    $cost_one = str_replace('%', '', $cost_one); // Remove the '%' sign if present
    $cost_two = str_replace('%', '', $cost_two); // Remove the '%' sign if present

    // Validate that cost_one and cost_two are numeric
    if (!is_numeric($cost_one) || ($cost_two !== '' && !is_numeric($cost_two))) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Cost must be a valid number or percentage',
            'data' => [
                'payment_method' => $payment_method,
                'cost_one' => $cost_one,
                'cost_two' => $cost_two,
                'payment_method_type' => $payment_method_type,
                'reference' => $reference,
            ]
        ]);
        exit;
    }

    // Re-append the '%' sign if originally present
    if (strpos($_POST['cost-one'], '%') !== false) {
        $cost_one .= '%';
    }
    if (strpos($_POST['cost-two'], '%') !== false && $cost_two !== '') {
        $cost_two .= '%';
    }

    $query = "UPDATE payment_methods SET payment_method = ?, cost_one = ?, cost_two = ?, payment_method_type = ?, reference = ? WHERE pmethod_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssi", $payment_method, $cost_one, $cost_two, $payment_method_type, $reference, $pmethod_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update payment method']);
    }
    $stmt->close();
    $conn->close();
}
?>
