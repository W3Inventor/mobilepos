<?php
// assets/php/helper/repair-helper/submit_repair_invoice.php
include '../../../../config/dbconnect.php';
include '../payment-helper/send_sms.php'; // for SMS sending

// Collect data
$repair_id    = $_POST['repair_id'] ?? 0;
$total_amount = $_POST['total_amount'] ?? 0;
$parts        = $_POST['parts'] ?? null;

if (!$repair_id || !$parts) {
    echo json_encode(['error'=>'Invalid data']); exit;
}

// Begin transaction
$conn->begin_transaction();
try {
    // 1. Insert into repair_invoices
    $stmt = $conn->prepare("INSERT INTO repair_invoices (repair_id, invoice_date, total_amount, status) VALUES (?, NOW(), ?, 'unpaid')");
    $stmt->bind_param("id", $repair_id, $total_amount);
    $stmt->execute();
    $invoice_id = $stmt->insert_id;
    $stmt->close();

    // 2. Insert invoice items and adjust inventory
    foreach ($parts['name'] as $idx => $name) {
        $serial = trim($parts['serial'][$idx]);
        $warranty = trim($parts['warranty'][$idx]);
        $qty = intval($parts['qty'][$idx]);
        $price = floatval($parts['price'][$idx]);

        // Attempt to find accessory ID by name (case-insensitive)
        $stmt = $conn->prepare("SELECT accessory_id FROM accessories WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->bind_result($accessory_id);
        $found = $stmt->fetch();
        $stmt->close();
        if (!$found) { $accessory_id = null; } 

        // Insert into repair_invoice_items
        $stmt = $conn->prepare("
          INSERT INTO repair_invoice_items (invoice_id, accessory_id, item_name, serial_number, warranty, quantity, price)
          VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssid", 
          $invoice_id, 
          $accessory_id, 
          $name, 
          $serial, 
          $warranty, 
          $qty, 
          $price
        );
        $stmt->execute();
        $stmt->close();

        // If accessory from stock, deduct inventory
        if ($accessory_id) {
            // If serial specified, mark it out of stock
            if ($serial !== '') {
                $s = $conn->prepare("UPDATE serial_numbers SET status = 'Out of Stock' WHERE serial_number = ?");
                $s->bind_param("s", $serial);
                $s->execute(); $s->close();
            } 
            // If no serial, you could decrement stock in accessories table here
            // (assuming an in-stock count is tracked)
            // e.g. UPDATE accessories SET stock = stock - ? WHERE accessory_id = ?
            // For brevity, omitted.
        }
    }

    // 3. Update in_house_repair actual_price and status (to Ready to Pickup)
    $stmt = $conn->prepare("UPDATE in_house_repair SET actual_price = ?, status = 'Ready to Pickup' WHERE ir_id = ?");
    $stmt->bind_param("di", $total_amount, $repair_id);
    $stmt->execute();
    $stmt->close();

    // 4. Log status history
    $stmt = $conn->prepare("INSERT INTO repair_status_history (repair_id, status) VALUES (?, 'Ready to Pickup')");
    $stmt->bind_param("i", $repair_id);
    $stmt->execute();
    $stmt->close();

    // 5. Send SMS to customer (reuse existing logic)
    $stmt = $conn->prepare("
      SELECT c.full_name, c.mobile_number 
        FROM in_house_repair r 
        JOIN customers c ON r.customer_id = c.customer_id 
       WHERE r.ir_id = ?");
    $stmt->bind_param("i", $repair_id);
    $stmt->execute();
    $stmt->bind_result($name, $mobile);
    $stmt->fetch();
    $stmt->close();
    $message = "Hi $name, your device repair (ID: $repair_id) is ready for pickup. Final bill: LKR " . number_format($total_amount,2) . ". Thank you!";
    sendSms($mobile, $message);

    // Commit all
    $conn->commit();
    echo json_encode(['success'=>'Invoice created successfully.', 'invoice_id'=>$invoice_id]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['error'=>'Invoice creation failed: '.$e->getMessage()]);
}
?>
