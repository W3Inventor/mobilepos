
<?php
// Updated submit_repair_payment.php - now with email and PDF generation logic from submit_payment.php

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../../logs/php_error.log');
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

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
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) throw new Exception('Invalid JSON input.');

    $repair_id   = $data['repair_id'] ?? null;
    $customer    = $data['customer'] ?? [];
    $payment     = $data['payment'] ?? [];
    $cart_items  = $data['cart_items'] ?? [];

    if (!$repair_id || empty($customer) || empty($payment) || empty($cart_items)) {
        throw new Exception("Missing required input fields.");
    }

    if (!isset($_SESSION['user_id'])) {
        throw new Exception("User session missing.");
    }

    $payment = array_merge([
        'bill_amount' => 0.00,
        'payable_amount' => 0.00,
        'cash_payment' => 0.00,
        'card_payment' => 0.00,
        'payment_cost_1' => 0.00,
        'payment_cost_2' => 0.00,
        'reference' => '',
        'method' => 'unknown',
        'bill_type' => 'print'
    ], $payment);

    $conn->begin_transaction();

    $stmt = $conn->prepare("UPDATE repair_invoices SET status = 'paid' WHERE invoice_id = ?");
    $stmt->bind_param("i", $repair_id);
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

            $stmt = $conn->prepare("UPDATE accessories SET quantity = quantity - ? WHERE accessory_id = ?");
            $stmt->bind_param("ii", $item['quantity'], $item['id']);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $conn->prepare("INSERT INTO repair_revenue (repair_id, item_name, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isid", $repair_id, $item['item_name'], $item['quantity'], $item['price']);
            $stmt->execute();
            $stmt->close();
        }
    }

    $conn->commit();

    $response = ['success' => 'Payment processed successfully.'];

    if ($payment['bill_type'] === 'print') {
        $response['redirect'] = "assets/php/helper/payment-helper/generate_pdf.php?sale_id=$sale_id";
    }

    if ($payment['bill_type'] === 'sms' && !empty($customer['mobile_number'])) {
        $message = "Repair invoice paid. View: {$base_url}/view_invoice.php?sale_id=$sale_id";
        $result = sendSms($customer['mobile_number'], $message);
        if (strpos($result['error'] ?? '', 'OK:1') !== false) {
            $response['sms_success'] = 'SMS sent successfully.';
        } else {
            $response['sms_error'] = $result['error'] ?? 'Failed to send SMS.';
        }
    }

    if ($payment['bill_type'] === 'email' && !empty($customer['email'])) {
        $subject = "Your Repair Invoice - #$sale_id";
        $body = "<p>Thank you for your payment.</p><p>Download PDF: <a href='{$base_url}/assets/php/helper/payment-helper/generate_pdf.php?sale_id=$sale_id'>Download Invoice</a></p>";
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
