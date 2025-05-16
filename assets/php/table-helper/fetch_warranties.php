<?php
// Database connection
include '../../../config/dbconnect.php';

// Fetch all warranty records
$query = "SELECT * FROM warranty";
$result = $conn->query($query);

$warranties = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $warranties[] = $row;
    }
}

// Return data as JSON
echo json_encode($warranties);
?>
