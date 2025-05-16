<?php
// Include your database connection file
include '../../../config/dbconnect.php';

// Check if the request is coming through AJAX
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Query to fetch all distinct brands from the brand table
    $query = "SELECT DISTINCT brand FROM variation_1 ORDER BY brand ASC";
    $result = $conn->query($query);

    // Initialize an array to hold the results
    $brands = [];

    // Fetch the data and populate the brands array
    while ($row = $result->fetch_assoc()) {
        $brands[] = [
            'id' => $row['brand'],
            'brand' => $row['brand']
        ];
    }

    // Return the data as JSON
    echo json_encode($brands);
}
?>
