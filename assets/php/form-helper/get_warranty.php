<?php
include '../../../config/dbconnect.php';

if (isset($_GET['id'])) {
    $warrantyId = $_GET['id'];

    $query = "SELECT * FROM warranty WHERE w_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $warrantyId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Warranty not found.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
