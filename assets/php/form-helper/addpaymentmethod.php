<?php
// Database connection
include '../../../config/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $warranty = $_POST['warranty'];
    $description = $_POST['description'];

    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO warranty (warranty, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $warranty, $description);

    if ($stmt->execute()) {
        // Prepare the response data
        $response = [
            'status' => 'success',
            'data' => [
                'w_id' => $conn->insert_id,
                'warranty' => $warranty,
                'description' => $description
            ]
        ];
    } else {
        $response = ['status' => 'error'];
    }

    // Send the JSON response
    echo json_encode($response);
}
?>
