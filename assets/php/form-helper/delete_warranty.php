<?php
include '../../../config/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $warrantyId = $_POST['id'];

    $query = "DELETE FROM warranty WHERE w_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $warrantyId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete warranty.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
