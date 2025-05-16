<?php
// Include your database connection file
include '../../../config/dbconnect.php';

// Check if the request is coming through AJAX and if the brand parameter is set
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['brand'])) {
    $brand = $_GET['brand'];

    // Query to fetch all distinct models for the selected brand from the variation_1 table
    $query = "SELECT DISTINCT model FROM variation_1 WHERE brand = ? ORDER BY model ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $brand);
    $stmt->execute();
    $result = $stmt->get_result();

    // Initialize an array to hold the results
    $models = [];

    // Fetch the data and populate the models array
    while ($row = $result->fetch_assoc()) {
        $models[] = [
            'id' => $row['model'],
            'model' => $row['model']
        ];
    }

    // Return the data as JSON
    echo json_encode($models);
}
?>
