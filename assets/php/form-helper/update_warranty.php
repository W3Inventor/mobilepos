<?php
include '../../../config/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $warrantyId = $_POST['id'];
    $warranty = $_POST['warranty'];
    $description = isset($_POST['description']) ? $_POST['description'] : '';

    $query = "UPDATE warranty SET warranty = ?, description = ? WHERE w_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $warranty, $description, $warrantyId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update warranty.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
