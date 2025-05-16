<?php
include '../../../../config/dbconnect.php';

header('Content-Type: application/json');

// Fetch the single row from smtp_settings
$sql = "SELECT * FROM smtp_settings LIMIT 1";
$result = $conn->query($sql);
$response = [];

if ($result && $result->num_rows > 0) {
    $response = $result->fetch_assoc();
} else {
    // Default empty values if no settings found
    $response = [
        'from_email' => '',
        'from_name' => '',
        'smtp_host' => '',
        'smtp_port' => '',
        'encryption' => 'ssl',
        'smtp_username' => '',
        'smtp_password' => ''
    ];
}

$conn->close();
echo json_encode($response);
?>
