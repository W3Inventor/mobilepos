<?php
include '../../../config/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pmethod_id = $_POST['id'];

    if (empty($pmethod_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid payment method ID']);
        exit;
    }

    $query = "DELETE FROM payment_methods WHERE pmethod_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $pmethod_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete payment method']);
    }
    $stmt->close();
    $conn->close();
}
?>
