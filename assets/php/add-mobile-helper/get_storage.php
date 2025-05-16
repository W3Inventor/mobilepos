<?php
// Include your database connection file
include '../../../config/dbconnect.php';

// Check if the request is coming through AJAX and if the necessary parameters are set
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['brand']) && isset($_GET['model'])) {
    $brand = $_GET['brand'];
    $model = $_GET['model'];

    // Query to fetch all distinct storage options for the selected brand and model from the variation_1 table
    $query = "SELECT DISTINCT storage FROM variation_1 WHERE brand = ? AND model = ? ORDER BY storage ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $brand, $model);
    $stmt->execute();
    $result = $stmt->get_result();

    // Initialize an array to hold the results
    $storage_options = [];

    // Fetch the data and populate the storage_options array
    while ($row = $result->fetch_assoc()) {
        $storage_options[] = [
            'id' => $row['storage'],
            'storage' => $row['storage']
        ];
    }

    // Return the data as JSON
    echo json_encode($storage_options);
}
?>
