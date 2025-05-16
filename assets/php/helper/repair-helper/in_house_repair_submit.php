<?php
include '../../../../config/dbconnect.php';

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
$response = ["success" => false, "error" => ""];

try {
    // Collect form data
    $nic = $_POST['nic'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $mobile_number = $_POST['mobile_number'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';
    $imei = $_POST['imei'] ?? '';
    $brand = $_POST['brand_name'] ?? '';
    $phone_model = $_POST['phone_model'] ?? '';
    $reason = $_POST['reason'] ?? '';
    $estimate_price = $_POST['estimate_amount'] ?? 0;
    $uploadedImagePaths = $_POST['uploaded_image_paths'] ?? '';

    // Process uploaded image paths
    $image_urls = array_filter(array_map('trim', explode(',', $uploadedImagePaths)));
    $images_column = !empty($image_urls) ? implode(',', $image_urls) : null;

    // Begin transaction
    $conn->begin_transaction();

    // Step 1: Check if customer exists
    $stmt = $conn->prepare("SELECT customer_id FROM customers WHERE nic = ? OR email = ? OR mobile_number = ? LIMIT 1");
    $stmt->bind_param("sss", $nic, $email, $mobile_number);
    $stmt->execute();
    $stmt->bind_result($customer_id);
    $stmt->fetch();
    $stmt->close();

    // Step 2: Insert new customer if not found
    if (empty($customer_id)) {
        $stmt = $conn->prepare("INSERT INTO customers (nic, full_name, mobile_number, email, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nic, $full_name, $mobile_number, $email, $address);

        if (!$stmt->execute()) {
            throw new Exception("Failed to insert customer details: " . $stmt->error);
        }

        $customer_id = $stmt->insert_id;
        $stmt->close();
    }

    // Step 3: Insert repair record
    $technician_id = null;
    $status = 'Pending';

    $stmt = $conn->prepare("INSERT INTO in_house_repair (imei, brand, model, reason, images, estimate_price, technician_id, status, customer_id)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssdsis", $imei, $brand, $phone_model, $reason, $images_column, $estimate_price, $technician_id, $status, $customer_id);

    if (!$stmt->execute()) {
        throw new Exception("Failed to insert repair details: " . $stmt->error);
    }

    $conn->commit();
    $response["success"] = true;

} catch (Exception $e) {
    $conn->rollback();
    $response["error"] = $e->getMessage();
}

echo json_encode($response);
$conn->close();
