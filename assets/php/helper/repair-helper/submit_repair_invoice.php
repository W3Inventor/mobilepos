<?php
include '../../../../config/dbconnect.php';
include '../payment-helper/send_sms.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// Collect and validate POST data
$repair_id     = $_POST['repair_id'] ?? '';
$total_amount  = $_POST['total_amount'] ?? '';
$parts         = $_POST['parts'] ?? [];

if (empty($repair_id) || empty($parts) || !is_array($parts) || !is_numeric($total_amount)) {
    echo json_encode(['error' => 'Missing or invalid required fields.']);
    exit;
}

$repair_id     = (int) $repair_id;
$total_amount  = (float) $total_amount;

$part_names      = $parts['name']     ?? [];
$part_serials    = $parts['serial']   ?? [];
$part_warranties = $parts['warranty'] ?? [];
$part_qty        = $parts['qty']      ?? [];
$part_price      = $parts['price']    ?? [];

$conn->begin_transaction();

try {
    // 1. Insert new invoice with total_amount
    $stmt = $conn->prepare("INSERT INTO repair_invoices (repair_id, status, total_amount) VALUES (?, 'unpaid', ?)");
    $stmt->bind_param("id", $repair_id, $total_amount);
    if (!$stmt->execute()) {
        throw new Exception("Failed to insert invoice: " . $stmt->error);
    }
    $invoice_id = $stmt->insert_id;
    $stmt->close();

    // 2. Process each part line
    $num_parts = count($part_names);
    for ($i = 0; $i < $num_parts; $i++) {
        $raw_id   = trim($part_names[$i] ?? '');
        $serial   = trim($part_serials[$i] ?? '');
        $warranty = trim($part_warranties[$i] ?? '');
        $qty      = (int)($part_qty[$i] ?? 0);
        $price    = (float)($part_price[$i] ?? 0);

        $accessory_id = null;
        $item_name = '';
        $serial_from_id = '';

        if (str_starts_with($raw_id, 'static')) {
            $item_name = 'Repairing Cost';
        } elseif (strpos($raw_id, '||') !== false) {
            [$accessory_id, $serial_from_id] = explode('||', $raw_id);
            $accessory_id = is_numeric($accessory_id) ? (int)$accessory_id : null;

            if ($accessory_id !== null) {
                $stmt = $conn->prepare("SELECT accessory_name FROM accessories WHERE accessory_id = ?");
                $stmt->bind_param("i", $accessory_id);
                $stmt->execute();
                $stmt->bind_result($fetched_name);
                if ($stmt->fetch()) {
                    $item_name = $fetched_name;
                }
                $stmt->close();
            }
        } else {
            $item_name = $raw_id;
        }

        $serial = $serial ?: $serial_from_id;

        if ($item_name === '' || $qty <= 0) continue;

        // 2b. If matched, update stock and serial number
        if ($accessory_id !== null) {
            // Update accessories stock
            $stmt = $conn->prepare("UPDATE accessories SET quantity = quantity - ? WHERE accessory_id = ?");
            $stmt->bind_param("ii", $qty, $accessory_id);
            if (!$stmt->execute()) {
                throw new Exception("Failed to update stock in accessories: " . $stmt->error);
            }
            $stmt->close();

            // Update accessories_price stock
            $stmt = $conn->prepare("UPDATE accessories_price SET quantity = quantity - ? WHERE accessory_id = ?");
            $stmt->bind_param("ii", $qty, $accessory_id);
            if (!$stmt->execute()) {
                throw new Exception("Failed to update stock in accessories_price: " . $stmt->error);
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

        // 2c. Insert into repair_invoice_items
        if ($accessory_id !== null) {
            $stmt = $conn->prepare("INSERT INTO repair_invoice_items 
                (invoice_id, accessory_id, item_name, serial_number, warranty, quantity, price)
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iisssid", $invoice_id, $accessory_id, $item_name, $serial, $warranty, $qty, $price);
        } else {
            $stmt = $conn->prepare("INSERT INTO repair_invoice_items 
                (invoice_id, item_name, serial_number, warranty, quantity, price)
                VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssid", $invoice_id, $item_name, $serial, $warranty, $qty, $price);
        }

        if (!$stmt->execute()) {
            throw new Exception("Failed to insert invoice item: " . $stmt->error);
        }
        $stmt->close();
    }

    // 3. Update in_house_repair
    $status = 'Ready to Pickup';
    $stmt = $conn->prepare("UPDATE in_house_repair SET actual_price = ?, status = ? WHERE ir_id = ?");
    $stmt->bind_param("dsi", $total_amount, $status, $repair_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to update in-house repair: " . $stmt->error);
    }
    $stmt->close();

    // 4. Insert into status history
    $stmt = $conn->prepare("INSERT INTO repair_status_history (repair_id, status) VALUES (?, ?)");
    $stmt->bind_param("is", $repair_id, $status);
    if (!$stmt->execute()) {
        throw new Exception("Failed to record status history: " . $stmt->error);
    }
    $stmt->close();

    // 5. Get customer details and send SMS
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
        $message = "Hi $customer_name, your repair (ID: #$repair_id) is ready. Total: LKR $total_amount.";
        sendSms($customer_mobile, $message);
    }

    $conn->commit();
    echo json_encode([
        'success' => 'Invoice created successfully.',
        'invoice_id' => $invoice_id,
        'redirect' => "print_repair_invoice.php?invoice_id=$invoice_id"
    ]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['error' => 'Transaction failed: ' . $e->getMessage()]);
}
?>
