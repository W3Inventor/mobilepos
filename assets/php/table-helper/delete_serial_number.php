<?php
// Include the database connection file
include '../../../config/dbconnect.php';

// Initialize the response array
$response = ['status' => 'error', 'message' => 'Invalid request.'];

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the serial number from the POST request
    $serial_number = isset($_POST['serial_number']) ? trim($_POST['serial_number']) : '';

    // Log the received data for debugging purposes
    error_log("Received Serial Number: '$serial_number'");

    // Check if the serial number is valid
    if (!empty($serial_number)) {
        // Begin transaction
        $conn->begin_transaction();

        try {
            // Fetch the accessory_id and accbill_id related to the serial number
            $fetchQuery = "SELECT accessory_id, accbill_id FROM serial_numbers WHERE serial_number = ?";
            $fetchStmt = $conn->prepare($fetchQuery);
            $fetchStmt->bind_param("s", $serial_number);
            $fetchStmt->execute();
            $fetchResult = $fetchStmt->get_result();

            if ($fetchResult->num_rows > 0) {
                $row = $fetchResult->fetch_assoc();
                $accessory_id = $row['accessory_id'];
                $accbill_id = $row['accbill_id'];

                // Delete the specific serial number
                $deleteQuery = "DELETE FROM serial_numbers WHERE serial_number = ?";
                $deleteStmt = $conn->prepare($deleteQuery);
                $deleteStmt->bind_param("s", $serial_number);

                // Execute the delete statement
                if ($deleteStmt->execute()) {
                    if ($deleteStmt->affected_rows > 0) {
                        // Update the quantity in the accessories table
                        $updateAccessoryQuery = "UPDATE accessories SET quantity = quantity - 1 WHERE accessory_id = ?";
                        $updateAccessoryStmt = $conn->prepare($updateAccessoryQuery);
                        $updateAccessoryStmt->bind_param("i", $accessory_id);
                        $updateAccessoryStmt->execute();

                        // Update the quantity in the accessories_price table if necessary
                        $updatePriceQuery = "UPDATE accessories_price SET quantity = quantity - 1 WHERE accessory_id = ? AND id = ?";
                        $updatePriceStmt = $conn->prepare($updatePriceQuery);
                        $updatePriceStmt->bind_param("ii", $accessory_id, $accbill_id);
                        $updatePriceStmt->execute();

                        // Commit the transaction
                        $conn->commit();
                        $response = ['status' => 'success', 'message' => 'Serial number deleted and quantity updated successfully.'];
                    } else {
                        $response['message'] = 'No rows were affected. Check if the serial number exists.';
                        $conn->rollback();
                    }
                } else {
                    $response['message'] = 'Failed to execute the delete statement.';
                    $conn->rollback();
                }

                $deleteStmt->close();
            } else {
                $response['message'] = 'Serial number not found.';
                $conn->rollback();
            }

            $fetchStmt->close();
        } catch (Exception $e) {
            // Rollback on error
            $conn->rollback();
            $response['message'] = 'An error occurred during the operation.';
            error_log("Error during delete operation: " . $e->getMessage());
        }
    } else {
        // If data validation fails
        $response['message'] = 'Invalid serial number received.';
        error_log("Validation Error - Received invalid serial number: '$serial_number'");
    }
} else {
    // If the request method is not POST
    $response['message'] = 'Invalid request method.';
    error_log("Invalid Request Method: " . $_SERVER['REQUEST_METHOD']);
}

// Send the response back as JSON
header('Content-Type: application/json');
echo json_encode($response);

// Close the connection
$conn->close();
?>
