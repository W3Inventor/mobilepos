<?php
include '../../../config/dbconnect.php';
header('Content-Type: application/json');

$price_id = $_POST['price_id'] ?? null;
$new_quantity = $_POST['quantity'] ?? null;

if (!$price_id || !is_numeric($new_quantity)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // Fetch the current quantity and accessory_id
    $stmt = $conn->prepare("SELECT quantity, accessory_id FROM accessories_price WHERE id = ?");
    $stmt->bind_param("i", $price_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("No record found for given price_id");
    }

    $data = $result->fetch_assoc();
    $old_quantity = $data['quantity'];
    $accessory_id = $data['accessory_id'];
    $stmt->close();

    // Calculate quantity difference
    $difference = $new_quantity - $old_quantity;

    // Update accessories_price table
    $stmt = $conn->prepare("UPDATE accessories_price SET quantity = ? WHERE id = ?");
    $stmt->bind_param("ii", $new_quantity, $price_id);
    $stmt->execute();
    $stmt->close();

    // Update accessories table
    $stmt = $conn->prepare("UPDATE accessories SET quantity = quantity + ? WHERE accessory_id = ?");
    $stmt->bind_param("ii", $difference, $accessory_id);
    $stmt->execute();
    $stmt->close();

    // Commit transaction
    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Quantity updated successfully']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Transaction failed: ' . $e->getMessage()]);
}

$conn->close();
