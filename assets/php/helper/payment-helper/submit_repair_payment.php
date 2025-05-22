
<?php
session_start();

header('Content-Type: application/json');

include '../../../../config/dbconnect.php';
include 'send_sms.php';
include 'send_email.php';
include '../setting-helper/settings_helper.php';

$log_file = __DIR__ . '/payment_error_log.txt';
function logDebug($message) {
    global $log_file;
    @file_put_contents($log_file, date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
}

// base URL for QR and invoice
$domain = $_SERVER['HTTP_HOST'];
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? "https" : "http";
$base_url = $protocol . "://" . $domain;





try {

    $settings = getSettings();
    $companyName = $settings['company_name'];
    $address_line1 = $settings['address_line1'];
    $address_line2 = $settings['address_line2'];
    $city = $settings['city'];
    $brand_color = $settings['brand_color'] ?? '#4CAF50';
    $website = $settings['website'];
    $contact_phone = $settings['mobile'];
    $logo_path = $settings['logo_path'];
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) throw new Exception('Invalid JSON input.');
    $repair_id = $data['repair_id'] ?? null;
    $repairNumber = 'Repair-' . str_pad($repair_id, 6, '0', STR_PAD_LEFT);
    // Fetch IMEI, brand, model, reason, estimate_price for this repair
    $repair_stmt = $conn->prepare("SELECT imei, brand, model, reason, estimate_price FROM in_house_repair WHERE ir_id = ?");
    $repair_stmt->bind_param("i", $repair_id);
    $repair_stmt->execute();
    $repair_stmt->bind_result($imei, $brand, $model, $reason, $estimate_price);
    $repair_stmt->fetch();
    $repair_stmt->close();


    $customer    = $data['customer'] ?? [];
    $payment     = $data['payment'] ?? [];
    $cart_items  = $data['cart_items'] ?? [];

    if (!$repair_id || empty($customer) || empty($payment) || empty($cart_items)) {
        throw new Exception("Missing required input fields.");
    }

    if (!isset($_SESSION['user_id'])) {
        throw new Exception("User session missing.");
    }

    $bill_type = $data['bill_type'] ?? 'print';


    $payment = array_merge([
        'bill_amount' => 0.00,
        'payable_amount' => 0.00,
        'cash_payment' => 0.00,
        'card_payment' => 0.00,
        'payment_cost_1' => 0.00,
        'payment_cost_2' => 0.00,
        'reference' => '',
        'method' => 'unknown',
        
    ], $payment);

    $payment['bill_type'] = $bill_type;

    $conn->begin_transaction();

    $invoice_token = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
    $stmt = $conn->prepare("UPDATE repair_invoices SET token = ?, status = 'paid' WHERE repair_id = ?");
    $stmt->bind_param("si", $invoice_token, $repair_id);
    $stmt->execute();
    $stmt->close();


    $stmt = $conn->prepare("INSERT INTO sales (
        customer_id, bill_amount, payable_amount, payment_method,
        payment_1, payment_2, payment_cost_1, payment_cost_2, reference,
        sale_date, user_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
    $stmt->bind_param(
        "iddddddssi",
        $customer['id'],
        $payment['bill_amount'],
        $payment['payable_amount'],
        $payment['method'],
        $payment['cash_payment'],
        $payment['card_payment'],
        $payment['payment_cost_1'],
        $payment['payment_cost_2'],
        $payment['reference'],
        $_SESSION['user_id']
    );
    $stmt->execute();
    $sale_id = $stmt->insert_id;
    $stmt->close();

    foreach ($cart_items as $item) {
        $item['serial_number'] = $item['serial_number'] ?? '';
        $item['warranty'] = $item['warranty'] ?? '';
        $item['discount'] = $item['discount'] ?? 0.00;
        $item['after_discount_price'] = $item['after_discount_price'] ?? $item['price'];
        $item['profit'] = $item['profit'] ?? 0.00;

        if ($item['type'] === 'accessory') {
            $buying_price = 0.00;
            $bp_stmt = $conn->prepare("SELECT buying FROM accessories_price WHERE accessory_id = ? ORDER BY id DESC LIMIT 1");
            $bp_stmt->bind_param("i", $item['id']);
            $bp_stmt->execute();
            $bp_stmt->bind_result($buying_price);
            $bp_stmt->fetch();
            $bp_stmt->close();

            $stmt = $conn->prepare("INSERT INTO sale_accessories (
                sale_id, accessory_id, item_name, serial_number, warranty,
                buying_price, selling_price, discount, after_discount_price, profit
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "iisssddddd",
                $sale_id,
                $item['id'],
                $item['item_name'],
                $item['serial_number'],
                $item['warranty'],
                $buying_price,
                $item['price'],
                $item['discount'],
                $item['after_discount_price'],
                $item['profit']
            );
            $stmt->execute();
            $stmt->close();

        } else {
            $stmt = $conn->prepare("INSERT INTO repair_revenue (repair_id, item_name, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isid", $repair_id, $item['item_name'], $item['quantity'], $item['price']);
            $stmt->execute();
            $stmt->close();
        }
    }

    // ✅ Update in_house_repair status to "Paid & Pickup"
    $stmt = $conn->prepare("UPDATE in_house_repair SET status = 'Paid & Pickup' WHERE ir_id = ?");
    $stmt->bind_param("i", $repair_id);
    $stmt->execute();
    $stmt->close();

    // ✅ Log status history
    $stmt = $conn->prepare("INSERT INTO repair_status_history (repair_id, status) VALUES (?, 'Paid & Pickup')");
    $stmt->bind_param("i", $repair_id);
    $stmt->execute();
    $stmt->close();


    $conn->commit();

    $response = ['success' => 'Payment processed successfully.'];

    if ($payment['bill_type'] === 'print') {
            $response['redirect'] = "assets/php/helper/repair-helper/repair_invoice.php?repair_id=$repair_id";
    }

    if ($payment['bill_type'] === 'sms' && !empty($customer['mobile_number'])) {
        $message = "Thank you for payment! View your invoice here: {$base_url}/view-repair-invoice.php?token={$invoice_token}";
        $result = sendSms($customer['mobile_number'], $message);
        if (strpos($result['error'] ?? '', 'OK:1') !== false) {
            $response['sms_success'] = 'SMS sent successfully.';
        } else {
            $response['sms_error'] = $result['error'] ?? 'Failed to send SMS.';
        }
    }

    if ($payment['bill_type'] === 'email' && !empty($customer['email'])) {
        // Prepare email subject and initial HTML structure
        $subject = "Repair Invoice $repairNumber from $companyName";
        $body = "<!DOCTYPE html><html><head><meta charset='UTF-8'>";
        $body .= "<style>
            @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');
            body {
                font-family: 'Roboto', sans-serif;
                color: #333333;
                margin: 0;
                padding: 0;
                background-color: #f4f4f4;
            }
            .invoice-container {
                max-width: 800px;
                margin: 30px auto;
                padding: 30px;
                background-color: #ffffff;
                border-radius: 8px;
                box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            }
            .company-details {
                text-align: center;
                margin-bottom: 30px;
            }
            .company-details img {
                max-width: 150px;
                margin-bottom: 20px;
            }
            .company-details h1 {
                color: #000000;
                margin-bottom: 5px;
                font-size: 24px;
            }
            .company-details p {
                margin: 2px;
                font-size: 14px;
                color: #777777;
            }
            .invoice-header {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 30px;
            }
            .invoice-header .invoice-to,
            .invoice-header .invoice-details {
                width: 48%;
            }
            .invoice-header .invoice-details {
                text-align: right;
            }
            .invoice-header .invoice-details p {
                margin: 2px;
                font-size: 14px;
            }
            .invoice-header .invoice-details p span {
                font-weight: 500;
                color: #555555;
            }
            .section-title {
                color: #000000;
                font-size: 18px;
                margin-bottom: 15px;
                border-bottom: 2px solid #000000;
                padding-bottom: 5px;
            }
            .customer-details p {
                margin: 5px 0;
                font-size: 14px;
                color: #555555;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 30px;
                table-layout: fixed;
            }
            table thead {
                background-color: #000000;
                color: #ffffff;
            }
            table th, table td {
                padding: 12px;
                text-align: left;
                font-size: 14px;
                word-wrap: break-word;
            }
            table tbody tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            .total-row td {
                font-weight: 700;
                color: #333333;
                border-top: 2px solid #000000;
            }
            .footer {
                text-align: center;
                font-size: 12px;
                color: #aaaaaa;
                border-top: 1px solid #eaeaea;
                padding-top: 15px;
                margin-top: 30px;
            }
            .footer p {
                margin: 2px;
            }
            a {
                color: #000000;
                text-decoration: none;
            }
            .text-ac{
            text-align: center;
            }
        </style></head><body>";

        $body .= "<div class='invoice-container'>";

        // Company header
        if (!empty($logo_path)) {
            $body .= "<img src='{$logo_path}' alt='Logo'><br>";
        }

        $body .= "<h1 class='text-ac'>{$companyName}</h1>";
        $body .= "<p class='text-ac'>{$address_line1}, {$address_line2}, {$city}</p>";
        $body .= "<p class='text-ac'>Phone: {$contact_phone}";
        if ($website) $body .= " | Website: <a href='{$website}'>{$website}</a>";
        $body .= "</p></div>";

        // Invoice Header with columns
        $body .= "<div class='invoice-header'>
            <div class='invoice-to'>
                <h2 class='section-title'>Invoice To</h2>
                <p><strong>Name:</strong> " . htmlspecialchars($customer['full_name']) . "</p>";
        if (!empty($customer['email'])) {
            $body .= "<p><strong>Email:</strong> " . htmlspecialchars($customer['email']) . "</p>";
        }
        if (!empty($customer['address'])) {
            $body .= "<p><strong>Address:</strong> " . htmlspecialchars($customer['address']) . "</p>";
        }
        $body .= "</div>";
        

        $body .= "<div class='invoice-details'>
            <p><span>Repair No:</span> {$repairNumber}</p>
            <p><span>Date:</span> " . date('Y-m-d') . "</p>
            <p><span>Invoice Total:</span> LKR " . number_format($payment['payable_amount'], 2) . "</p>
        </div></div>";

                $body .= "<div class='invoice-items'>
            <h2 class='section-title'>Device Details</h2>
            <table>
                <tr><td><strong>IMEI:</strong></td><td>" . htmlspecialchars($imei) . "</td></tr>
                <tr><td><strong>Brand:</strong></td><td>" . htmlspecialchars($brand) . "</td></tr>
                <tr><td><strong>Model:</strong></td><td>" . htmlspecialchars($model) . "</td></tr>
                <tr><td><strong>Problem:</strong></td><td>" . htmlspecialchars($reason) . "</td></tr>
                <tr><td><strong>Estimate Price:</strong></td><td>LKR " . number_format($estimate_price, 2) . "</td></tr>
            </table>
        </div>";


        // Invoice items
        $body .= "<div class='invoice-items'>
            <h2 class='section-title'>Repair Details</h2>
            <table>
            <thead>
                <tr>
                    <th>Item</th><th>Qty</th><th>Unit Price</th>";
        if ($payment['total_discount'] > 0) {
            $body .= "<th>Discount</th>";
        }
        $body .= "<th>Total</th></tr></thead><tbody>";

        foreach ($cart_items as $it) {
            $lineTotal = ($it['price'] - ($it['discount'] ?? 0)) * $it['quantity'];
            $body .= "<tr>
                <td>" . htmlspecialchars($it['item_name']) . "</td>
                <td>{$it['quantity']}</td>
                <td>LKR " . number_format($it['price'], 2) . "</td>";
            if ($payment['total_discount'] > 0) {
                $body .= "<td>LKR " . number_format($it['discount'], 2) . "</td>";
            }
            $body .= "<td>LKR " . number_format($lineTotal, 2) . "</td>
            </tr>";
        }
        $body .= "</tbody></table></div>";

        // Totals
        $body .= "<table>";
        $body .= "<tr><td style='text-align:right;' colspan='3'>Subtotal:</td>
                    <td>LKR " . number_format($payment['bill_amount'], 2) . "</td></tr>";
        if ($payment['total_discount'] > 0) {
            $body .= "<tr><td style='text-align:right;' colspan='3'>Discount:</td>
                    <td>-LKR " . number_format($payment['total_discount'], 2) . "</td></tr>";
        }
        $body .= "<tr class='total-row'><td style='text-align:right;' colspan='3'>Amount Paid:</td>
                    <td>LKR " . number_format($payment['payable_amount'], 2) . "</td></tr>";
        $body .= "</table>";

        // Footer
        $body .= "<div class='footer'>
            <p>Thank you for your business!</p>
            <p>If you have any questions, please contact us.</p>
        </div>";

        $body .= "</div></body></html>";


        $email_status = sendEmail($customer['email'], $subject, $body);
        if ($email_status) {
            $response['email_success'] = 'Email sent successfully.';
        } else {
            $response['email_error'] = 'Failed to send email.';
        }
    }

    echo json_encode($response);

} catch (Throwable $e) {
    if ($conn && $conn->errno) {
        $conn->rollback();
    }

    logDebug("❌ " . $e->getMessage() . " | FILE: " . $e->getFile() . " | LINE: " . $e->getLine());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Payment processing failed.',
        'error' => $e->getMessage()
    ]);
}
?>
