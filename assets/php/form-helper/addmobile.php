<?php
// assets/php/form-helper/addmobile.php
include '../../../config/dbconnect.php';

$billno = $_POST['billno'];
$company_name = $_POST['company_name'];
$supplier_name = $_POST['supplier_name'];
$mobile_number = $_POST['mobile_number'];
$address = $_POST['address'];
$bill_amount = $_POST['bill_amount'];
$brand_name = $_POST['brand_name'];
$model_name = $_POST['model_name'];
$ram = $_POST['ram'];
$storage = $_POST['storage'];
$color = $_POST['color'];
$buying = $_POST['buying'];
$selling = $_POST['selling'];
$condition = $_POST['condition'];
$trcsl = isset($_POST['trcsl']) ? 'Yes' : 'No';
$date = $_POST['date'];
$imei_numbers = explode("\n", trim($_POST['imei'])); // Splitting by line breaks
$imei_count = count($imei_numbers);

$response = ['status' => 'error', 'message' => 'Something went wrong.']; // Default response

$conn->begin_transaction();

try {
    // Check if supplier already exists
    $stmt = $conn->prepare("SELECT supplier_id FROM supplier WHERE supplier_name = ? AND company_name = ?");
    $stmt->bind_param("ss", $supplier_name, $company_name);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $supplier_id = $result->fetch_assoc()['supplier_id'];
    } else {
        // Insert new supplier
        $stmt = $conn->prepare("INSERT INTO supplier (supplier_name, company_name, mobile_number, address) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $supplier_name, $company_name, $mobile_number, $address);
        $stmt->execute();
        $supplier_id = $stmt->insert_id;
    }

    // Check if bill already exists
    $stmt = $conn->prepare("SELECT billid FROM bill WHERE billno = ?");
    $stmt->bind_param("s", $billno);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $billid = $result->fetch_assoc()['billid'];
    } else {
        // Insert new bill
        $stmt = $conn->prepare("INSERT INTO bill (billno, date, bill_amount, supplier_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $billno, $date, $bill_amount, $supplier_id);
        $stmt->execute();
        $billid = $stmt->insert_id;
    }

    // Check if brand and model already exist in variation_1
    $stmt = $conn->prepare("SELECT vid_1 FROM variation_1 WHERE brand = ? AND model = ? AND ram = ? AND storage = ? AND colour = ? AND trcsl = ?" );
    $stmt->bind_param("ssssss", $brand_name, $model_name, $ram, $storage, $color, $trcsl);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $vid_1 = $result->fetch_assoc()['vid_1'];
        // Update quantity1
        $stmt = $conn->prepare("UPDATE variation_1 SET quantity1 = quantity1 + ? WHERE vid_1 = ?");
        $stmt->bind_param("ii", $imei_count, $vid_1);
        $stmt->execute();
    } else {
        // Insert new variation_1
        $stmt = $conn->prepare("INSERT INTO variation_1 (brand, model, ram, storage, colour, trcsl, quantity1) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $brand_name, $model_name, $ram, $storage, $color, $trcsl, $imei_count);
        $stmt->execute();
        $vid_1 = $stmt->insert_id;
    }

    // Check if variation_2 exists
    $stmt = $conn->prepare("SELECT vid_2, quantity2 FROM variation_2 WHERE buying = ? AND selling = ? AND billid = ?");
    $stmt->bind_param("ddi", $buying, $selling, $billid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Record exists, update the quantity2
        $row = $result->fetch_assoc();
        $vid_2 = $row['vid_2'];
        $new_quantity2 = $row['quantity2'] + $imei_count;
    
        $update_stmt = $conn->prepare("UPDATE variation_2 SET quantity2 = ? WHERE vid_2 = ?");
        $update_stmt->bind_param("ii", $new_quantity2, $vid_2);
        $update_stmt->execute();
    } else {
        // No matching record, insert a new one
        $insert_stmt = $conn->prepare("INSERT INTO variation_2 (buying, selling, billid, quantity2) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("ddsi", $buying, $selling, $billid, $imei_count);
        $insert_stmt->execute();
        $vid_2 = $insert_stmt->insert_id;
    }

    // Insert each IMEI number into the mobile table as a new record
    $stmt = $conn->prepare("INSERT INTO mobile (imei, `condition`, status, vid_1, vid_2) VALUES (?, ?, 'In Stock', ?, ?)");

    
    foreach ($imei_numbers as $imei) {
        $imei = trim($imei); // Trim whitespace
        if (!empty($imei)) { // Ensure the IMEI is not empty
            $stmt->bind_param("ssii", $imei, $condition, $vid_1, $vid_2);
            $stmt->execute();
        }
    }

    // Commit transaction
    $conn->commit();

    // Prepare success response
    $response['status'] = 'success';
    $response['message'] = 'Mobile added successfully';
} catch (Exception $e) {
    // Rollback transaction
    $conn->rollback();
    // Prepare error response
    $response['message'] = 'Error: ' . $e->getMessage();
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
