<?php
header('Content-Type: application/json');
include '../../../../config/dbconnect.php';

// Query to retrieve the settings from the database
$sql = "SELECT * FROM settings LIMIT 1";
$result = $conn->query($sql);

$response = [
    'favicon_path' => 'uploads/default_favicon.png', // Default path if no image is set
    'logo_path' => 'uploads/default_logo.png',       // Default path if no image is set
    'company_name' => '',
    'address_line1' => '',
    'address_line2' => '',
    'city' => '',
    'mobile' => '',
    'landline' => '',
    'website' => '',
    'brand_color' => ''
];

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response['favicon_path'] = $row['favicon_path'] ?: 'uploads/default_favicon.png';
    $response['logo_path'] = $row['logo_path'] ?: 'uploads/default_logo.png';
    $response['company_name'] = $row['company_name'];
    $response['address_line1'] = $row['address_line1'];
    $response['address_line2'] = $row['address_line2'];
    $response['city'] = $row['city'];
    $response['mobile'] = $row['mobile'];
    $response['landline'] = $row['landline'];
    $response['website'] = $row['website'];
    $response['brand_color'] = $row['brand_color'];
}

$conn->close();
echo json_encode($response);
?>
