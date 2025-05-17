<?php
include '../../../../config/dbconnect.php';
include '../payment-helper/send_sms.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// Collect and validate POST data
$repair_id = $_POST['repair_id'] ?? '';
$total_amount = $_POST['total_amount'] ?? '';
$parts = $_POST['parts'] ?? [];

if (empty($repair_id) || empty($parts) || !is_array($parts) || empty($total_amount)) {
    echo json_encode(['error' => 'Missing required fields.']);
    exit;
}

$part_names     = $parts['name']     ?? [];
$part_serials   = $parts['serial']   ?? [];
$part_warranties= $parts['warranty'] ?? [];
$part_qty       = $parts['qty']      ?? [];
$part_price     = $parts['price']    ?? [];

$conn->begin_transaction();

try {
    // 1. Insert new invoice
    $stmt = $conn->prepare("INSERT INTO repair_invoices (repair_id, status) VALUES (?, 'unpaid')");
    $stmt->bind_param("i", $repair_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to insert invoice: " . $stmt->error);
    }
    $invoice_id = $stmt->insert_id;
    $stmt->close();

    // 2. Process each part line
    $num_parts = count($part_names);
    for ($i = 0; $i < $num_parts; $i++) {
        $item_name = trim($part_names[$i] ?? '');
        $serial    = trim($part_serials[$i] ?? '');
        $warranty  = trim($part_warranties[$i] ?? '');
        $qty       = (int)($part_qty[$i] ?? 0);
        $price     = (float)($part_price[$i] ?? 0);

        if ($item_name === '' || $qty <= 0) {
            // Skip empty or invalid entries
            continue;
        }

        $accessory_id = null;

        // 2a. Try to find matching accessory by name
        $stmt = $conn->prepare("SELECT accessory_id FROM accessories WHERE item_name = ?");
        $stmt->bind_param("s", $item_name);
        $stmt->execute();
        $stmt->bind_result($aid);
        if ($stmt->fetch()) {
            // Accessory matched
            $accessory_id = $aid;
        }
        $stmt->close();

        // 2b. If matched, deduct stock and mark serial out of stock
        if ($accessory_id !== null) {
            $stmt = $conn->prepare("UPDATE accessories SET stock = stock - ? WHERE accessory_id = ?");
            $stmt->bind_param("ii", $qty, $accessory_id);
            if (!$stmt->execute()) {
                throw new Exception("Failed to update stock for accessory $accessory_id: " . $stmt->error);
            }
            $stmt->close();

            if ($serial !== '') {
                $stmt = $conn->prepare("UPDATE serial_numbers SET status = 'Out of Stock' WHERE serial_number = ?");
                $stmt->bind_param("s", $serial);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to update serial number $serial: " . $stmt->error);
                }
                $stmt->close();
            }
        }

        // 2c. Insert part line into repair_invoice_items
        if ($accessory_id !== null) {
            $stmt_item = $conn->prepare("
                INSERT INTO repair_invoice_items 
                  (invoice_id, accessory_id, item_name, serial_number, warranty, quantity, price) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt_item->bind_param("iisssid", 
                $invoice_id, $accessory_id, $item_name, $serial, $warranty, $qty, $price);
        } else {
            // Manual item (no accessory_id)
            $stmt_item = $conn->prepare("
                INSERT INTO repair_invoice_items 
                  (invoice_id, item_name, serial_number, warranty, quantity, price) 
                VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_item->bind_param("isssid", 
                $invoice_id, $item_name, $serial, $warranty, $qty, $price);
        }
        if (!$stmt_item->execute()) {
            throw new Exception("Failed to insert invoice item: " . $stmt_item->error);
        }
        $stmt_item->close();
    }

    // 3. Update in_house_repair record
    $ready_status = 'Ready to Pickup';
    $stmt = $conn->prepare("
        UPDATE in_house_repair 
        SET actual_price = ?, status = ?
        WHERE ir_id = ?");
    $stmt->bind_param("dsi", $total_amount, $ready_status, $repair_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to update in-house repair: " . $stmt->error);
    }
    $stmt->close();

    // 4. Insert into repair_status_history
    $stmt = $conn->prepare("
        INSERT INTO repair_status_history (repair_id, status) 
        VALUES (?, ?)");
    $stmt->bind_param("is", $repair_id, $ready_status);
    if (!$stmt->execute()) {
        throw new Exception("Failed to record status history: " . $stmt->error);
    }
    $stmt->close();

    // 5. Fetch customer name and mobile to send SMS
    $stmt = $conn->prepare("
        SELECT c.full_name, c.mobile_number
        FROM in_house_repair r
        JOIN customers c ON r.customer_id = c.customer_id
        WHERE r.ir_id = ?");
    $stmt->bind_param("i", $repair_id);
    $stmt->execute();
    $stmt->bind_result($customer_name, $customer_mobile);
    $stmt->fetch();
    $stmt->close();

    if (!empty($customer_name) && !empty($customer_mobile)) {
        $sms_message = "Hi $customer_name, your device repair (ID: #$repair_id) is ready for pickup. Final bill: LKR $total_amount. Thank you!";
        sendSms($customer_mobile, $sms_message);
    }

    // 6. Commit transaction and output success JSON
    $conn->commit();
    $conn->close();

    $redirect_url = "print_repair_invoice.php?invoice_id=$invoice_id";
    echo json_encode([
        'success'    => 'Invoice created successfully.',
        'invoice_id' => $invoice_id,
        'redirect'   => $redirect_url
    ]);
    exit;

} catch (Exception $e) {
    // Rollback on error and output JSON error
    $conn->rollback();
    $conn->close();
    echo json_encode(['error' => 'Transaction failed: ' . $e->getMessage()]);
    exit;
}
?>
