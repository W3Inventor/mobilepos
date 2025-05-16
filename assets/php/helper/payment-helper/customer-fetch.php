<?php
// Include the database connection
include '../../../../config/dbconnect.php';  // Adjust the path based on your structure

// Get input values from the AJAX request
$nic = $conn->real_escape_string($_POST['nic'] ?? '');
$email = $conn->real_escape_string($_POST['email'] ?? '');
$mobile = $conn->real_escape_string($_POST['mobile'] ?? '');

// Initialize response array
$response = ['success' => false];

// Prepare the SQL query to search for customer details
$query = "
    SELECT nic, full_name, mobile_number, email, address 
    FROM customers 
    WHERE nic = ? OR email = ? OR mobile_number = ? 
    LIMIT 1
";

// Prepare the statement
$stmt = $conn->prepare($query);
$stmt->bind_param('sss', $nic, $email, $mobile);
$stmt->execute();
$result = $stmt->get_result();

// Check if a customer was found
if ($result->num_rows > 0) {
    $customer = $result->fetch_assoc();
    $response['success'] = true;
    $response['customer'] = $customer;
}

// Return the response as JSON
echo json_encode($response);

// Close the database connection
$conn->close();
?>
