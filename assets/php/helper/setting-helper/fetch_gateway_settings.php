<?php
include '../../../../config/dbconnect.php';

// Prepare the response with default empty values
$response = [
    'user_id' => '',
    'user_password' => ''
];

// Fetch the saved settings from the database, if any
$sql = "SELECT * FROM textit_gateway_settings LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    // If a row exists, populate the response with the saved settings
    $row = $result->fetch_assoc();
    $response['user_id'] = $row['user_id'];
    $response['user_password'] = $row['user_password'];
}

// Close the database connection
$conn->close();

// Return the response in JSON format
echo json_encode($response);
?>
