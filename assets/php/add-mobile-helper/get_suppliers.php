<?php
// Include your database connection file
include '../../../config/dbconnect.php';

// Check if the request is coming through AJAX
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Query to fetch all suppliers from the supplier table
    $query = "SELECT DISTINCT supplier_name FROM supplier ORDER BY supplier_name ASC";
    $result = $conn->query($query);

    // Initialize an array to hold the results
    $suppliers = [];

    // Fetch the data and populate the suppliers array
    while ($row = $result->fetch_assoc()) {
        $suppliers[] = [
            'id' => $row['supplier_name'],
            'supplier_name' => $row['supplier_name']
        ];
    }

    // Return the data as JSON
    echo json_encode($suppliers);
}
?>
