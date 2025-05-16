<?php
include '../../../config/dbconnect.php'; // Database connection

// Sanitize the input
$accessory_id = htmlspecialchars(trim($_GET['accessory_id']));

$response = [
    'exists' => false,
    'accessory_name' => '',
    'brand' => '',
    'color' => '',
    'other' => ''
];

try {
    // Check if the accessory ID exists in the database
    $stmt = $conn->prepare("SELECT accessory_name, brand, color, other FROM accessories WHERE accessory_id = ?");
    $stmt->bind_param("s", $accessory_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response['exists'] = true;
        $response['accessory_name'] = $row['accessory_name'];
        $response['brand'] = $row['brand'];
        $response['color'] = $row['color'];
        $response['other'] = $row['other'];
    }

    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode(['error' => 'Error fetching accessory details: ' . $e->getMessage()]);
}

$conn->close();
?>
