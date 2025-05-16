<?php
session_start();
include '../../../../config/dbconnect.php';
include 'send_email.php';
include 'send_sms.php';
include '../setting-helper/settings_helper.php';  


$stmt = $conn->prepare("SELECT invoice_date, invoice_number FROM invoices WHERE invoice_id = ?");
$stmt->bind_param("i", $invoice_id);
$stmt->execute();
$stmt->bind_result($invoice_date, $invoice_number);
$stmt->fetch();
$stmt->close();

// Split the invoice_date into date and time
$invoice_date = date('Y-m-d');
$invoice_time = date('H:i:s');



// Get settings data
$settings = getSettings();
$company_name = $settings['company_name'];
$address_line1 = $settings['address_line1'];
$address_line2 = $settings['address_line2'];
$city = $settings['city'];
$brand_color = $settings['brand_color'];
$website = $settings['website'];
$contact_phone = $settings['mobile'];
$logo_path = $settings['logo_path'];


$domain = $_SERVER['HTTP_HOST'];
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? "https" : "http";
$base_url = $protocol . "://" . $domain;

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in.']);
    exit;
}

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Collect customer details
$nic = $_POST['nic'] ?? '';
$full_name = $_POST['full_name'] ?? '';
$mobile_number = $_POST['mobile_number'] ?? '';
$email = $_POST['email'] ?? '';
$address = $_POST['address'] ?? '';

// Collect payment details
$bill_amount = $_POST['bill_amount'] ?? 0;
$total_discount = $_POST['total_discount'] ?? 0;
$payable_amount = $_POST['payable_amount'] ?? 0;
$payment_method = $_POST['payment_method'] ?? '';
$cash_payment = $_POST['cash_payment'] ?? 0;
$card_payment = $_POST['card_payment'] ?? 0;
$payment_cost_1 = $_POST['payment_cost_1'] ?? 0;
$payment_cost_2 = $_POST['payment_cost_2'] ?? 0;
$reference = $_POST['reference'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;
$bill_type = isset($_POST['bill_type']) ? $_POST['bill_type'] : [];


$cart_items = isset($_POST['cart_items']) ? json_decode($_POST['cart_items'], true) : null;
if (json_last_error() !== JSON_ERROR_NONE || !is_array($cart_items)) {
    echo json_encode(['error' => 'Invalid cart items format.']);
    exit;
}

// Begin transaction
$conn->begin_transaction();

try {
    // 1. Check if customer already exists
    $stmt = $conn->prepare("SELECT customer_id FROM customers WHERE nic = ? OR email = ? OR mobile_number = ?");
    $stmt->bind_param("sss", $nic, $email, $mobile_number);
    $stmt->execute();
    $stmt->bind_result($customer_id);
    $stmt->fetch();
    $stmt->close();

    // 2. If customer doesn't exist, insert a new one
    if (empty($customer_id)) {
        $stmt = $conn->prepare("INSERT INTO customers (nic, full_name, mobile_number, email, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nic, $full_name, $mobile_number, $email, $address);
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert customer details: " . $stmt->error);
        }
        $customer_id = $stmt->insert_id;
        $stmt->close();
    }

    // 2. Insert Sales Details
    $stmt = $conn->prepare("
        INSERT INTO sales (sale_date, customer_id, user_id, bill_amount, total_discount, payable_amount, payment_method, payment_1, payment_2, payment_cost_1, payment_cost_2, reference) 
        VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiidddsddds", $customer_id, $user_id, $bill_amount, $total_discount, $payable_amount, $payment_method, $cash_payment, $card_payment, $payment_cost_1, $payment_cost_2, $reference);
    if (!$stmt->execute()) {
        throw new Exception("Failed to insert sales details: " . $stmt->error);
    }
    $sale_id = $stmt->insert_id;
    $stmt->close();

    // 3. Insert Invoice
    $invoice_number = 'Inv' . str_pad($sale_id, 4, '0', STR_PAD_LEFT);
    $total_amount = $cash_payment + $card_payment;
    $payment_method_charge = $payment_cost_1 + $payment_cost_2;
    $invoice_token = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);


    $stmt = $conn->prepare("
    INSERT INTO invoices (sale_id, invoice_date, invoice_number, total_amount, payment_method_charge, token) 
    VALUES (?, NOW(), ?, ?, ?, ?)");
    $stmt->bind_param("isdds", $sale_id, $invoice_number, $total_amount, $payment_method_charge, $invoice_token);
    if (!$stmt->execute()) {
        throw new Exception("Failed to insert invoice: " . $stmt->error);
    }
    $stmt->close();

    // 4. Process Cart Items
    foreach ($cart_items as $item) {
        $item_type = $item['type'];
        $item_name = $item['item_name'];
        $quantity = $item['quantity'];
        $discount = $item['discount'];
        $total = $item['total'];
        $warranty = $item['warranty'];
        $price = $item['price'];

        if ($item_type === 'mobile') {
            $imei = $item['imei'];

            // Update Mobile Status to 'Out of Stock'
            $stmt = $conn->prepare("UPDATE mobile SET status = 'Out of Stock' WHERE imei = ?");
            $stmt->bind_param("s", $imei);
            $stmt->execute();


            // 2. Fetch vid_1 and vid_2 from the mobile table based on the IMEI
            $stmt = $conn->prepare("SELECT vid_1, vid_2 FROM mobile WHERE imei = ?");
            $stmt->bind_param("s", $imei);
            $stmt->execute();
            $stmt->bind_result($vid_1, $vid_2);
            $stmt->fetch();
            $stmt->close();

            // 3. Decrement quantity1 in variation_1 based on vid_1
            if (!empty($vid_1)) {
                $stmt = $conn->prepare("UPDATE variation_1 SET quantity1 = quantity1 - 1 WHERE vid_1 = ?");
                $stmt->bind_param("i", $vid_1);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to update quantity1 in variation_1: " . $stmt->error);
                }
            }

            // 4. Decrement quantity2 in variation_2 based on vid_2
            if (!empty($vid_2)) {
                $stmt = $conn->prepare("UPDATE variation_2 SET quantity2 = quantity2 - 1 WHERE vid_2 = ?");
                $stmt->bind_param("i", $vid_2);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to update quantity2 in variation_2: " . $stmt->error);
                }
            }

            // Fetch Buying and Selling Prices from variation_2
            $stmt = $conn->prepare("
                SELECT v2.buying, v2.selling 
                FROM mobile m 
                JOIN variation_2 v2 ON m.vid_2 = v2.vid_2 
                WHERE m.imei = ?");
            $stmt->bind_param("s", $imei);
            $stmt->execute();
            $stmt->bind_result($buying_price, $selling_price);
            $stmt->fetch();
            $stmt->close();

            $after_discount_price = $selling_price - $discount;
            $profit = $after_discount_price - $buying_price;

            // Insert into Sale Mobile
            $stmt = $conn->prepare("
                INSERT INTO sale_mobile (sale_id, item_name, imei, warranty, buying_price, selling_price, discount, after_discount_price, profit) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssddddd", $sale_id, $item_name, $imei, $warranty, $buying_price, $selling_price, $discount, $after_discount_price, $profit);
            $stmt->execute();
            
        } elseif ($item_type === 'accessory') {
            $accessory_id = $item['id'];
            $serial_number = $item['serial_number'] ?? '';
        
            // Handle Serial Number Update
            if (!empty($serial_number)) {
                $stmt = $conn->prepare("SELECT status FROM serial_numbers WHERE serial_number = ?");
                $stmt->bind_param("s", $serial_number);
                $stmt->execute();
                $stmt->store_result();
        
                if ($stmt->num_rows > 0) {
                    $stmt->close();
                    // Update the serial number status to 'Out of Stock'
                    $stmt = $conn->prepare("UPDATE serial_numbers SET status = 'Out of Stock' WHERE serial_number = ?");
                    $stmt->bind_param("s", $serial_number);
                    $stmt->execute();
                } else {
                    $stmt->close();
                    throw new Exception("Serial number not found: $serial_number");
                }
            }
        
            // Fetch Buying and Selling Prices for Accessory
            $stmt = $conn->prepare("SELECT buying, selling FROM accessories_price WHERE accessory_id = ? AND selling = ?");
            $stmt->bind_param("id", $accessory_id, $price);
            $stmt->execute();
            $stmt->bind_result($buying_price, $selling_price);
            $stmt->fetch();
            $stmt->close();
        
            $after_discount_price = $selling_price - $discount;
            $profit = $after_discount_price - $buying_price;
        
            // Insert into Sale Accessories for each quantity
            for ($i = 0; $i < $quantity; $i++) {
                $stmt = $conn->prepare("
                    INSERT INTO sale_accessories (sale_id, accessory_id, item_name, serial_number, warranty, buying_price, selling_price, discount, after_discount_price, profit) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("iisssddddd", $sale_id, $accessory_id, $item_name, $serial_number, $warranty, $buying_price, $selling_price, $discount, $after_discount_price, $profit);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to insert into sale_accessories: " . $stmt->error);
                }
            }
        
            // Decrement quantity in accessories_price and accessories tables
            $stmt = $conn->prepare("UPDATE accessories_price SET quantity = quantity - ? WHERE accessory_id = ? AND selling = ?");
            $stmt->bind_param("iid", $quantity, $accessory_id, $price);
            if (!$stmt->execute()) {
                throw new Exception("Failed to update quantity in accessories_price: " . $stmt->error);
            }
        
            $stmt = $conn->prepare("UPDATE accessories SET quantity = quantity - ? WHERE accessory_id = ?");
            $stmt->bind_param("ii", $quantity, $accessory_id);
            if (!$stmt->execute()) {
                throw new Exception("Failed to update quantity in accessories: " . $stmt->error);
            }
        }
        
    }

    // Commit the transaction
    $conn->commit();

    // Prepare the response
    $response = [];
    $pdf_path = "assets/php/helper/payment-helper/generate_pdf.php?sale_id=$sale_id";

    // Trigger actions based on the selected bill type
    if ($bill_type === 'print') {
        $response['redirect'] = $pdf_path; // URL to open the print popup
    }

    // Only send SMS if "sms" bill type is selected and mobile number is provided
    if ($bill_type === 'sms' && !empty($mobile_number)) {
        $sms_message = "Thank you for your purchase! View your invoice here: {$base_url}/view_invoice.php?token=$invoice_token";
        $sms_result = sendSms($mobile_number, $sms_message);

        $response['sms_raw_response'] = $sms_result;

        // Define an array of possible success messages
        $sms_success_messages = [
            "OK:1-MSG_GSM-99 Uploaded_Successfully",
            "OK:1-MSG_GSM-71 Uploaded_Successfully"
        ];

        // Check if any of the success messages are in the response
        $is_sms_successful = false;
        foreach ($sms_success_messages as $success_message) {
            if (isset($sms_result['error']) && strpos($sms_result['error'], $success_message) !== false) {
                $is_sms_successful = true;
                break;
            }
        }

        if ($is_sms_successful) {
            $response['sms_success'] = 'SMS sent successfully.';
        } elseif (isset($sms_result['success'])) {
            $response['sms_success'] = $sms_result['success'];
        } else {
            // If no success message is found, treat it as an error
            $response['sms_error'] = $sms_result['error'];
        }





    }





    if ($bill_type === 'email' && !empty($email)) {
        $subject = "Your Invoice from $company_name (Invoice Number: $invoice_number)";
    
        // Build company details dynamically
        $company_details = '
            <div class="company-details">
                <img src="' . htmlspecialchars($logo_path) . '" alt="' . htmlspecialchars($company_name) . ' Logo">
                <h1>' . htmlspecialchars($company_name) . '</h1>';
    
        // Build address parts
        $address_parts = [];
        if (!empty($address_line1)) {
            $address_parts[] = htmlspecialchars($address_line1);
        }
        if (!empty($address_line2)) {
            $address_parts[] = htmlspecialchars($address_line2);
        }
        if (!empty($city)) {
            $address_parts[] = htmlspecialchars($city);
        }
    
        if (!empty($address_parts)) {
            $company_details .= '<p>' . implode(', ', $address_parts) . '</p>';
        }
    
        // Build contact parts
        $contact_parts = [];
        if (!empty($contact_phone)) {
            $contact_parts[] = 'Phone: ' . htmlspecialchars($contact_phone);
        }
        if (!empty($website)) {
            $contact_parts[] = 'Website: <a href="' . htmlspecialchars($website) . '">' . htmlspecialchars($website) . '</a>';
        }
    
        if (!empty($contact_parts)) {
            $company_details .= '<p>' . implode(' | ', $contact_parts) . '</p>';
        }

        $footer_contact = '';
        if (!empty($contact_phone)) {
            $footer_contact .= 'Phone: <a href="tel:' . htmlspecialchars($contact_phone) . '">' . htmlspecialchars($contact_phone) . '</a>';
        }
        if (!empty($email)) {
            if (!empty($footer_contact)) {
                $footer_contact .= ' | ';
            }
            $footer_contact .= 'Email: <a href="mailto:' . htmlspecialchars($email) . '">' . htmlspecialchars($email) . '</a>';
        }
            
        $company_details .= '</div>';
    
        // HTML Invoice Content
        $body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                /* Import Google Font */
                @import url(\'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap\');
                
                body {
                    font-family: \'Roboto\', sans-serif;
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
                    color: ' . htmlspecialchars($brand_color) . ';
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
                .invoice-header .invoice-to, .invoice-header .invoice-details {
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
                    color: ' . htmlspecialchars($brand_color) . ';
                    font-size: 18px;
                    margin-bottom: 15px;
                    border-bottom: 2px solid ' . htmlspecialchars($brand_color) . ';
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
                    background-color: ' . htmlspecialchars($brand_color) . ';
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
                    border-top: 2px solid ' . htmlspecialchars($brand_color) . ';
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
                    color: ' . htmlspecialchars($brand_color) . ';
                    text-decoration: none;
                }
            </style>
        </head>
        <body>
            <div class="invoice-container">
                <!-- Company Details -->
                ' . $company_details . '
        
                <!-- Invoice Header -->
                <div class="invoice-header">
                    <div class="invoice-to">
                        <h2 class="section-title">Invoice To</h2>
                        <p><strong>Name:</strong> ' . htmlspecialchars($full_name) . '</p>';
    
        if (!empty($email)) {
            $body .= '<p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>';
        }
        if (!empty($address)) {
            $body .= '<p><strong>Address:</strong> ' . htmlspecialchars($address) . '</p>';
        }
    
        $body .= '</div>
                    <div class="invoice-details">
                        <p><span>Invoice Number:</span> ' . htmlspecialchars($invoice_number) . '</p>
                        <p><span>Date:</span> ' . htmlspecialchars($invoice_date) . '</p>
                        <p><span>Time:</span> ' . htmlspecialchars($invoice_time) . '</p>
                    </div>
                </div>
        
                <!-- Invoice Items -->
                <div class="invoice-items">
                    <h2 class="section-title">Invoice Details</h2>
                    <table>
                        <thead>
                            <tr>
                                <th style="width:40%;">Item</th>
                                <th style="width:15%;">Quantity</th>
                                <th style="width:15%;">Unit Price</th>
                                <th style="width:15%;">Discount</th>
                                <th style="width:15%;">Total</th>
                            </tr>
                        </thead>
                        <tbody>';
                        // Add cart items dynamically
                        foreach ($cart_items as $item) {
                            $item_total = ($item['price'] - $item['discount']) * $item['quantity'];
                            $body .= '
                            <tr>
                                <td>' . htmlspecialchars($item['item_name']) . '</td>
                                <td>' . htmlspecialchars($item['quantity']) . '</td>
                                <td>LKR ' . number_format($item['price'], 2) . '</td>
                                <td>LKR ' . number_format($item['discount'], 2) . '</td>
                                <td>LKR ' . number_format($item_total, 2) . '</td>
                            </tr>';
                        }
        $body .= '
                        </tbody>
                    </table>
                </div>
        
                <!-- Totals -->
                <div class="totals">
                    <table>
                        <tbody>
                            <tr class="total-row">
                                <td colspan="4" style="text-align:right;">Subtotal:</td>
                                <td>LKR ' . number_format($bill_amount, 2) . '</td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="4" style="text-align:right;">Total Discount:</td>
                                <td>LKR ' . number_format($total_discount, 2) . '</td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="4" style="text-align:right;">Payable Amount:</td>
                                <td>LKR ' . number_format($payable_amount, 2) . '</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
        
                <!-- Footer -->
                <div class="footer">
                    <p>If you have any questions, feel free to contact us.</p>
                    <p>Thank you for your business!</p>
                </div>
            </div>
        </body>
        </html>';
        

    // Send email with inline invoice
    $email_sent = sendEmail($email, $subject, $body);
        if (!$email_sent) {
            $response['email_error'] = 'Failed to send email.';
        } else {
            $response['success'] = 'Payment processed successfully and Email sent.';
        }
    }

    // If no specific error occurred and no other message is set, set a generic success message
    if (empty($response)) {
        $response['success'] = 'Payment processed successfully.';
    }

    echo json_encode($response);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['error' => 'Transaction failed: ' . $e->getMessage()]);
}

$conn->close();