<?php
include '../../../config/dbconnect.php'; // Include your database connection

// Function to fetch supplier ID by supplier name
function getSupplierId($conn, $supplierName) {
    $stmt = $conn->prepare("SELECT supplier_id FROM supplier WHERE supplier_name = ?");
    $stmt->bind_param("s", $supplierName);
    $stmt->execute();
    $result = $stmt->get_result();
    $supplier = $result->fetch_assoc();
    return $supplier ? $supplier['supplier_id'] : null;
}

// Function to fetch bill ID by bill number
function getBillId($conn, $billNo) {
    $stmt = $conn->prepare("SELECT billid FROM bill WHERE billno = ?");
    $stmt->bind_param("s", $billNo);
    $stmt->execute();
    $result = $stmt->get_result();
    $bill = $result->fetch_assoc();
    return $bill ? $bill['billid'] : null;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $billNo = $_POST['billno'];
    $billAmount = $_POST['bill_amount'];
    $companyName = $_POST['company_name'];
    $supplierName = $_POST['supplier_name'];
    $mobileNumber = $_POST['mobile_number'];
    $address = $_POST['address'];
    $date = $_POST['date'];
    $brandName = $_POST['brand_name'];
    $accessoryName = $_POST['accessory_name'];
    $barcode = $_POST['accessory_id'];
    $quantity = $_POST['quantity'];
    $color = $_POST['color'];
    $other = $_POST['other'];
    $buyingPrice = $_POST['buying'];
    $sellingPrice = $_POST['selling'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Check if supplier already exists
        $supplierId = getSupplierId($conn, $supplierName);

        // If supplier does not exist, insert it
        if (!$supplierId) {
            $stmt = $conn->prepare("INSERT INTO supplier (supplier_name, company_name, mobile_number, address) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $supplierName, $companyName, $mobileNumber, $address);
            $stmt->execute();
            $supplierId = $conn->insert_id; // Get the inserted supplier_id
        }

        // Check if the bill already exists
        $billId = getBillId($conn, $billNo);

        // If bill does not exist, insert it
        if (!$billId) {
            $stmt = $conn->prepare("INSERT INTO bill (billno, bill_amount, date, supplier_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sdsi", $billNo, $billAmount, $date, $supplierId);
            $stmt->execute();
            $billId = $conn->insert_id; // Get the inserted billid
        }

        // Check if the barcode already exists in the accessories table
        $stmt = $conn->prepare("SELECT quantity FROM accessories WHERE accessory_id = ?");
        $stmt->bind_param("i", $barcode);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Barcode exists, update the quantity by adding the new quantity
            $existingAccessory = $result->fetch_assoc();
            $newQuantity = $existingAccessory['quantity'] + $quantity;

            // Update the quantity in the accessories table
            $stmt = $conn->prepare("UPDATE accessories SET quantity = ? WHERE accessory_id = ?");
            $stmt->bind_param("ii", $newQuantity, $barcode);
            $stmt->execute();
        } else {
            // Insert the new accessory details if barcode does not exist
            $stmt = $conn->prepare("INSERT INTO accessories (accessory_id, accessory_name, brand, color, other, quantity) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssi", $barcode, $accessoryName, $brandName, $color, $other, $quantity);
            $stmt->execute();
        }

        // Check if the same buying, selling, billid, supplier_id, and accessory_id already exist in accessories_price
        $stmt = $conn->prepare("SELECT id, quantity FROM accessories_price WHERE buying = ? AND selling = ? AND billid = ? AND supplier_id = ? AND accessory_id = ?");
        $stmt->bind_param("ddiii", $buyingPrice, $sellingPrice, $billId, $supplierId, $barcode);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Row exists, update the quantity
            $existingPrice = $result->fetch_assoc();
            $newPriceQuantity = $existingPrice['quantity'] + $quantity;

            // Update the quantity in the accessories_price table
            $stmt = $conn->prepare("UPDATE accessories_price SET quantity = ? WHERE id = ?");
            $stmt->bind_param("ii", $newPriceQuantity, $existingPrice['id']);
            $stmt->execute();
        } else {
            // Insert the price details for the accessory if no matching row exists
            $stmt = $conn->prepare("INSERT INTO accessories_price (buying, selling, billid, supplier_id, accessory_id, quantity) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ddiiii", $buyingPrice, $sellingPrice, $billId, $supplierId, $barcode, $quantity);
            $stmt->execute();
        }

        // Commit the transaction
        $conn->commit();

        // Respond with success message
        echo json_encode(['status' => 'success', 'message' => 'Accessory added successfully!']);
    } catch (Exception $e) {
        // Rollback transaction if any error occurs
        $conn->rollback();

        // Respond with error message
        echo json_encode(['status' => 'error', 'message' => 'Error occurred: ' . $e->getMessage()]);
    }
}

$conn->close();
?>
