<?php
include '../../../config/dbconnect.php'; // Include your database connection

// Capture data from the AJAX request
$accessory_id = $_POST['accessory_id'];
$price_id = $_POST['price_id'];
$brand = $_POST['brand'];
$accessory_name = $_POST['accessory_name'];
$buying_price = $_POST['buying_price'];
$selling_price = $_POST['selling_price'];
$quantity = $_POST['quantity'];

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
        $old_quantity = $row['quantity']; // Get the old quantity for adjustment calculation

        // Update the accessories_price table
        $updateQuery = "UPDATE accessories_price SET buying = ?, selling = ?, quantity = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ddii", $buying_price, $selling_price, $quantity, $price_id);

        if ($stmt->execute()) {
            // Calculate the quantity difference
            $quantity_difference = $quantity - $old_quantity;

            // Update the quantity in the accessories table
            $updateAccessoryQuery = "UPDATE accessories SET quantity = quantity + ? WHERE accessory_id = ?";
            $stmt = $conn->prepare($updateAccessoryQuery);
            $stmt->bind_param("ii", $quantity_difference, $accessory_id);
            $stmt->execute();

            // Commit the transaction
            $conn->commit();
            echo json_encode(['status' => 'success', 'message' => 'Accessory price updated successfully and quantity adjusted.']);
        } else {
            throw new Exception('Failed to update the accessory price.');
        }
    } else {
        throw new Exception('No record found with the specified Price ID. Update cannot be performed.');
    }
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();
?>
