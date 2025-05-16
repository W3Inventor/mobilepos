<?php
// Include your database connection file
include '../../../config/dbconnect.php';

// Check if the request is coming through AJAX and if the necessary parameters are set
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['brand']) && isset($_GET['model'])) {
    $brand = $_GET['brand'];
    $model = $_GET['model'];

    // Query to fetch all distinct RAM options for the selected brand and model from the variation_1 table
    $query = "SELECT DISTINCT ram FROM variation_1 WHERE brand = ? AND model = ? ORDER BY ram ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $brand, $model);
    $stmt->execute();
    $result = $stmt->get_result();

    // Initialize an array to hold the results
    $ram_options = [];

    // Fetch the data and populate the ram_options array
    while ($row = $result->fetch_assoc()) {
        $ram_options[] = [
            'id' => $row['ram'],
            'ram' => $row['ram']
        ];
    }

    // Return the data as JSON
    echo json_encode($ram_options);
}
?>
