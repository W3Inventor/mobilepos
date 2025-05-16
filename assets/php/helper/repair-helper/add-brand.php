<?php
include '../../../../config/dbconnect.php';

$brand = $conn->real_escape_string($_POST['brand'] ?? '');

$response = ['success' => false, 'error' => ''];

if (!empty($brand)) {
    // Insert the new brand into the in_house_repair table
    $query = "INSERT INTO in_house_repair (brand) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $brand);

    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['error'] = 'Failed to add brand: ' . $stmt->error;
    }

    $stmt->close();
} else {
    $response['error'] = 'Invalid brand name.';
}

echo json_encode($response);

$conn->close();
?>
