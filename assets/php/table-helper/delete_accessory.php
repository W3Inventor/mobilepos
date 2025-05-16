<?php
include '../../../config/dbconnect.php'; // Include your database connection

// Capture data from the AJAX request
$accessory_id = $_POST['accessory_id'];
$price_id = $_POST['price_id'];

// Start a transaction to ensure atomicity
$conn->begin_transaction();

try {
    // Check if the price_id exists in the accessories_price table
    $checkQuery = "SELECT quantity FROM accessories_price WHERE id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $price_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $quantity_to_deduct = $row['quantity'];

        // Check for related serial numbers in the serial_numbers table
        $serialCheckQuery = "SELECT id FROM serial_numbers WHERE accbill_id = ?";
        $stmt = $conn->prepare($serialCheckQuery);
        $stmt->bind_param("i", $price_id);
        $stmt->execute();
        $serialResult = $stmt->get_result();

        if ($serialResult->num_rows > 0) {
            // Delete related serial numbers first
            $deleteSerialQuery = "DELETE FROM serial_numbers WHERE accbill_id = ?";
            $stmt = $conn->prepare($deleteSerialQuery);
            $stmt->bind_param("i", $price_id);
            $stmt->execute();
        }

        // Delete the row from the accessories_price table
        $deleteQuery = "DELETE FROM accessories_price WHERE id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $price_id);

        if ($stmt->execute()) {
            // Deduct the quantity from the accessories table
            $updateAccessoryQuery = "UPDATE accessories SET quantity = quantity - ? WHERE accessory_id = ?";
            $stmt = $conn->prepare($updateAccessoryQuery);
            $stmt->bind_param("ii", $quantity_to_deduct, $accessory_id);
            $stmt->execute();

            // Commit the transaction
            $conn->commit();
            echo json_encode(['status' => 'success', 'message' => 'Accessory price and related data deleted successfully, and quantity adjusted.']);
        } else {
            throw new Exception('Failed to delete the accessory price and related data.');
        }
    } else {
        throw new Exception('No record found with the specified Price ID. Delete operation cannot be performed.');
    }
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();
?>
