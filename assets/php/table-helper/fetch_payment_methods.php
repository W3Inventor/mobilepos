<?php
include '../../../config/dbconnect.php';

$query = "SELECT * FROM payment_methods";
$result = $conn->query($query);

$payment_methods = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $payment_methods[] = $row;
    }
}

echo json_encode($payment_methods);

$conn->close();
?>
