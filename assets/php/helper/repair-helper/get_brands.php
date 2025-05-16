<?php
// Include your database connection file
include '../../../../config/dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Query to fetch all distinct brands from the in_house_repair table
    $query = "SELECT DISTINCT brand FROM in_house_repair ORDER BY brand ASC";
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
