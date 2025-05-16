<?php
include '../../../config/dbconnect.php';

if (isset($_GET['id'])) {
    $pmethod_id = $_GET['id'];

    $query = "SELECT * FROM payment_methods WHERE pmethod_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $pmethod_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $payment_method = $result->fetch_assoc();
        echo json_encode($payment_method);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Payment method not found']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}

$conn->close();
?>
