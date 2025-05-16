<?php
include '../../../config/dbconnect.php'; // Database connection

// Function to sanitize input data
function sanitizeInput($data) {
    return htmlspecialchars(trim($data));
}

// Fetch and sanitize form data
$bill_no = sanitizeInput($_POST['billno']);
$bill_amount = sanitizeInput($_POST['bill_amount']);
$company_name = sanitizeInput($_POST['company_name']);
$supplier_name = sanitizeInput($_POST['supplier_name']);
$mobile_number = sanitizeInput($_POST['mobile_number']);
$address = sanitizeInput($_POST['address']);
$accessory_id = sanitizeInput($_POST['accessory_id']);
$brand_name = sanitizeInput($_POST['brand_name']);
$accessory_name = sanitizeInput($_POST['accessory_name']);
$color = sanitizeInput($_POST['color']);
$other_variation = sanitizeInput($_POST['other']);
$serial_numbers = explode("\n", sanitizeInput($_POST['serial_numbers'])); // Split serial numbers by lines
$buying_price = sanitizeInput($_POST['buying']);
$selling_price = sanitizeInput($_POST['selling']);
$quantity = count($serial_numbers); // Count the number of serial numbers

try {
    // Start a transaction using mysqli
    $conn->autocommit(false); // Disable autocommit mode to start the transaction

    // Check if supplier exists, if not insert
    $stmt = $conn->prepare("SELECT supplier_id FROM supplier WHERE supplier_name = ? AND company_name = ?");
    $stmt->bind_param("ss", $supplier_name, $company_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $supplier = $result->fetch_assoc();
    
    if (!$supplier) {
        $stmt = $conn->prepare("INSERT INTO supplier (supplier_name, company_name, mobile_number, address) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $supplier_name, $company_name, $mobile_number, $address);
        $stmt->execute();
        $supplier_id = $conn->insert_id;
    } else {
        $supplier_id = $supplier['supplier_id'];
    }

    // Check if bill exists, if not insert
    $stmt = $conn->prepare("SELECT billid FROM bill WHERE billno = ?");
    $stmt->bind_param("s", $bill_no);
    $stmt->execute();
    $result = $stmt->get_result();
    $bill = $result->fetch_assoc();
    
    if (!$bill) {
        $stmt = $conn->prepare("INSERT INTO bill (billno, bill_amount, supplier_id, date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdss", $bill_no, $bill_amount, $supplier_id, date('Y-m-d'));
        $stmt->execute();
        $billid = $conn->insert_id;
    } else {
        $billid = $bill['billid'];
    }

    // Check accessory barcode length
    if (strlen($accessory_id) < 3) {
        throw new Exception('Accessory ID must be at least 3 characters long.');
    }

    // Check if the accessory exists and update the quantity if it does
    $stmt = $conn->prepare("SELECT quantity FROM accessories WHERE accessory_id = ?");
    $stmt->bind_param("s", $accessory_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $accessory = $result->fetch_assoc();

    if ($accessory) {
        // If accessory exists, update the quantity
        $new_quantity = $accessory['quantity'] + $quantity; // Sum new quantity
        $stmt = $conn->prepare("UPDATE accessories SET quantity = ? WHERE accessory_id = ?");
        $stmt->bind_param("is", $new_quantity, $accessory_id);
        $stmt->execute();
    } else {
        // Insert new accessory details
        $stmt = $conn->prepare("INSERT INTO accessories (accessory_id, accessory_name, brand, color, other, quantity) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $accessory_id, $accessory_name, $brand_name, $color, $other_variation, $quantity);
        $stmt->execute();
    }

    // Check if a matching row exists in accessories_price
    $stmt = $conn->prepare("SELECT id, quantity FROM accessories_price WHERE buying = ? AND selling = ? AND billid = ? AND supplier_id = ? AND accessory_id = ?");
    $stmt->bind_param("ddiis", $buying_price, $selling_price, $billid, $supplier_id, $accessory_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $price = $result->fetch_assoc();

    if ($price) {
        // If matching row exists, update the quantity
        $new_price_quantity = $price['quantity'] + $quantity;
        $stmt = $conn->prepare("UPDATE accessories_price SET quantity = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_price_quantity, $price['id']);
        $stmt->execute();
        $accbill_id = $price['id'];
    } else {
        // Insert accessory price details
        $stmt = $conn->prepare("INSERT INTO accessories_price (buying, selling, billid, supplier_id, accessory_id, quantity) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ddisii", $buying_price, $selling_price, $billid, $supplier_id, $accessory_id, $quantity);
        $stmt->execute();
        $accbill_id = $conn->insert_id;
    }

    // Insert serial numbers
    foreach ($serial_numbers as $serial) {
        $serial = trim($serial); // Remove whitespace
        if (!empty($serial)) {
            $stmt = $conn->prepare("INSERT INTO serial_numbers (serial_number, accessory_id, status, accbill_id) VALUES (?, ?, 'In Stock', ?)");
            $stmt->bind_param("sii", $serial, $accessory_id, $accbill_id);
            $stmt->execute();
        }
    }

    // Commit the transaction
    $conn->commit();
    $conn->autocommit(true); // Enable autocommit mode again

    // Return success response
    echo json_encode(['status' => 'success', 'message' => 'Accessory added successfully.']);
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    $conn->autocommit(true); // Enable autocommit mode again
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>
