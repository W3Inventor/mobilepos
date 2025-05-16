<?php
// Include the database connection file
include '../../../config/dbconnect.php';

// Initialize the response array
$response = ['status' => 'error', 'message' => 'Invalid request.'];

// Start transaction if necessary
$conn->autocommit(FALSE); // Disable autocommit mode

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve parameters from POST request and validate them
    $new_serial = isset($_POST['new_serial']) ? trim($_POST['new_serial']) : '';
    $original_serial = isset($_POST['original_serial']) ? trim($_POST['original_serial']) : '';

    // Log the received data for debugging purposes
    error_log("Received Data - New Serial: '$new_serial', Original Serial: '$original_serial'");

    // Check if the received data is valid
    if (!empty($new_serial) && !empty($original_serial)) {
        // Correct the SQL query to update the serial number
        $query = "UPDATE serial_numbers SET serial_number = ? WHERE serial_number = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            // Bind the parameters
            $stmt->bind_param("ss", $new_serial, $original_serial);

            // Log the actual query being executed
            error_log("Executing Query: UPDATE serial_numbers SET serial_number = '$new_serial' WHERE serial_number = '$original_serial'");

            // Execute the statement
            if ($stmt->execute()) {
                // Check if any rows were affected
                if ($stmt->affected_rows > 0) {
                    $conn->commit(); // Commit the transaction
                    $response = ['status' => 'success', 'message' => 'Serial number updated successfully.'];
                } else {
                    $response['message'] = 'No rows were affected. Check if the serial number exists.';
                    error_log("No rows affected - New Serial: '$new_serial', Original Serial: '$original_serial'");
                }
            } else {
                $response['message'] = 'Failed to execute the update statement.';
                error_log("Execution Error: " . $stmt->error); // Log error details
            }
            $stmt->close();
        } else {
            $response['message'] = 'Failed to prepare the update statement.';
            error_log("Preparation Error: " . $conn->error); // Log error details
        }
    } else {
        // If data validation fails
        $response['message'] = 'Invalid data received. Check your inputs.';
        error_log("Validation Error - Received invalid data: New Serial: '$new_serial', Original Serial: '$original_serial'");
    }
} else {
    // If the request method is not POST
    $response['message'] = 'Invalid request method.';
    error_log("Invalid Request Method: " . $_SERVER['REQUEST_METHOD']);
}

// Send the response back as JSON
header('Content-Type: application/json');
echo json_encode($response);

// Rollback if there was an error
if ($response['status'] !== 'success') {
    $conn->rollback();
}

// Close the connection
$conn->close();
?>
