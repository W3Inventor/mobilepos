<?php
// Include your database connection file
include '../../../config/dbconnect.php';

// Check if the request is coming through AJAX
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Query to fetch all companies from the supplier table
    $query = "SELECT DISTINCT company_name FROM supplier ORDER BY company_name ASC";
    $result = $conn->query($query);

    // Initialize an array to hold the results
    $companies = [];

    // Fetch the data and populate the companies array
    while ($row = $result->fetch_assoc()) {
        $companies[] = [
            'id' => $row['company_name'],
            'company_name' => $row['company_name']
        ];
    }

    // Return the data as JSON
    echo json_encode($companies);
}
