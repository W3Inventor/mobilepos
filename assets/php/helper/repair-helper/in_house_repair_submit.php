**assets/php/helper/repair-helper/in_house_repair_submit.php** (modified)
<?php
ob_clean(); // Clean any unexpected output
echo json_encode($response);
include '../../../../config/dbconnect.php';
// Include helpers for sending email/SMS and getting company settings
include '../payment-helper/send_email.php';
include '../payment-helper/send_sms.php';
include '../setting-helper/settings_helper.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// Collect form data
$nic           = $_POST['nic'] ?? '';
$full_name     = $_POST['full_name'] ?? '';
$mobile_number = $_POST['mobile_number'] ?? '';
$email         = $_POST['email'] ?? '';
$address       = $_POST['address'] ?? '';
$imei          = $_POST['imei'] ?? '';
$brand         = $_POST['brand_name'] ?? '';
$phone_model   = $_POST['phone_model'] ?? '';
$reason        = $_POST['reason'] ?? '';
$estimate_price= $_POST['estimate_amount'] ?? 0;
$uploadedImagePaths = $_POST['uploaded_image_paths'] ?? '';
$bill_type     = $_POST['bill_type'] ?? '';  // New: get the selected bill type

// Process uploaded image paths
$image_urls   = array_filter(array_map('trim', explode(',', $uploadedImagePaths)));
$images_column = !empty($image_urls) ? implode(',', $image_urls) : null;

$response = [];  // prepare response array
$conn->begin_transaction();
try {
    // 1. Check if customer exists
    $stmt = $conn->prepare(
        "SELECT customer_id FROM customers 
         WHERE nic = ? OR email = ? OR mobile_number = ? LIMIT 1"
    );
    $stmt->bind_param("sss", $nic, $email, $mobile_number);
    $stmt->execute();
    $stmt->bind_result($customer_id);
    $stmt->fetch();
    $stmt->close();

    // 2. Insert new customer if not found
    if (empty($customer_id)) {
        $stmt = $conn->prepare(
            "INSERT INTO customers (nic, full_name, mobile_number, email, address) 
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssss", $nic, $full_name, $mobile_number, $email, $address);
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert customer details: " . $stmt->error);
        }
        $customer_id = $stmt->insert_id;
        $stmt->close();
    }

    // 3. Insert repair record
    $technician_id = null;
    $status = 'Pending';
    $stmt = $conn->prepare(
        "INSERT INTO in_house_repair 
         (imei, brand, model, reason, images, estimate_price, technician_id, status, customer_id)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("sssssdsis", 
        $imei, $brand, $phone_model, $reason, 
        $images_column, $estimate_price, 
        $technician_id, $status, $customer_id
    );
    if (!$stmt->execute()) {
        throw new Exception("Failed to insert repair details: " . $stmt->error);
    }
    $repair_id = $stmt->insert_id;      // New: get the new repair record ID
    $stmt->close();

    // Commit the transaction (repair saved successfully)
    $conn->commit();

    // --- Trigger post-save actions based on bill_type ---
    // Prepare a repair reference number (e.g., "Rep0005")
    $repair_number = 'Rep' . str_pad($repair_id, 4, '0', STR_PAD_LEFT);
    // Base URL for links (if needed in SMS/email)
    $domain   = $_SERVER['HTTP_HOST'];
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? "https" : "http";
    $base_url = "$protocol://$domain";

    // 4.A: Print – generate PDF invoice for the repair
    if ($bill_type === 'print') {
        // Point to a new PDF-generation script with the repair ID
        $response['redirect'] = "assets/php/helper/repair-helper/generate_repair_pdf.php?repair_id=$repair_id";
    }

    // 4.B: SMS – send repair info via SMS (if mobile number is provided)
    if ($bill_type === 'sms' && !empty($mobile_number)) {
        // Compose an SMS message with repair details (e.g., acknowledgment and repair ID)
        $sms_message = "Thank you for your repair request! Your Repair ID is $repair_number. We will contact you when it is ready. - $full_name";
        // (Include more info or link if applicable. No external link included since a view page is not defined here.)

        $sms_result = sendSms($mobile_number, $sms_message);
        // Check SMS API response for success (mirror logic from POS)
        $sms_success_messages = [
            "OK:1-MSG_GSM-99 Uploaded_Successfully",
            "OK:1-MSG_GSM-71 Uploaded_Successfully"
        ];
        $is_sms_successful = false;
        foreach ($sms_success_messages as $success_msg) {
            if (isset($sms_result['error']) && strpos($sms_result['error'], $success_msg) !== false) {
                $is_sms_successful = true;
                break;
            }
        }
        if ($is_sms_successful) {
            $response['sms_success'] = 'SMS sent successfully.';
        } elseif (isset($sms_result['success'])) {
            $response['sms_success'] = $sms_result['success'];
        } else {
            // If no known success pattern, treat as error
            $response['sms_error'] = $sms_result['error'] ?? 'SMS sending failed.';
        }
    }

    // 4.C: Email – send an invoice/receipt via email
    if ($bill_type === 'email' && !empty($email)) {
        // Load company settings for email content (logo, name, etc.)
        $settings     = getSettings();
        $company_name = $settings['company_name'];
        $address1     = $settings['address_line1'];
        $address2     = $settings['address_line2'];
        $city         = $settings['city'];
        $brand_color  = $settings['brand_color'];
        $website      = $settings['website'];
        $contact_phone= $settings['mobile'];
        $logo_path    = $settings['logo_path'];

        // Email subject and introduction
        $subject = "Your Repair Ticket from $company_name (Repair #$repair_number)";
        // Build an HTML email body with repair details (similar structure to invoice email)
        $body  = "<!DOCTYPE html><html><head><meta charset='UTF-8'>";
        $body .= "<style>
                    body { font-family: Arial, sans-serif; color: #333; }
                    .company-header { text-align: center; }
                    .company-header img { max-width: 150px; }
                    .company-header h1 { color: $brand_color; margin-bottom: 5px; }
                    .company-header p { margin: 2px; font-size: 14px; color: #777; }
                    .section-title { color: $brand_color; font-size: 16px; margin-top: 20px; font-weight: bold; }
                    .detail-section p { margin: 4px 0; }
                  </style></head><body>";
        // Company header with logo and address
        $body .= "<div class='company-header'>";
        if (!empty($logo_path)) {
            $body .= "<img src='{$base_url}/$logo_path' alt='{$company_name} Logo'><br>";
        }
        $body .= "<h1>" . htmlspecialchars($company_name) . "</h1>";
        $body .= "<p>" . htmlspecialchars($address1);
        if (!empty($address2)) $body .= ", " . htmlspecialchars($address2);
        if (!empty($city))    $body .= ", " . htmlspecialchars($city);
        $body .= "</p>";
        $body .= "<p>Phone: " . htmlspecialchars($contact_phone);
        if (!empty($website)) {
            $body .= " | Website: <a href='" . htmlspecialchars($website) . "'>" . htmlspecialchars($website) . "</a>";
        }
        $body .= "</p></div>";
        // Repair details section
        $body .= "<h2 class='section-title'>Repair Receipt</h2>";
        $body .= "<div class='detail-section'><p><strong>Repair No:</strong> $repair_number</p>";
        $body .= "<p><strong>Date:</strong> " . date('Y-m-d') . "</p>";
        $body .= "<p><strong>Status:</strong> Pending</p></div>";
        // Customer details section
        $body .= "<div class='detail-section'><p class='section-title'>Customer Details</p>";
        $body .= "<p>Name: " . htmlspecialchars($full_name) . "</p>";
        if (!empty($nic))    $body .= "<p>NIC: " . htmlspecialchars($nic) . "</p>";
        $body .= "<p>Phone: " . htmlspecialchars($mobile_number) . "</p>";
        if (!empty($email))  $body .= "<p>Email: " . htmlspecialchars($email) . "</p>";
        $body .= "<p>Address: " . htmlspecialchars($address) . "</p></div>";
        // Device/Repair details section
        $body .= "<div class='detail-section'><p class='section-title'>Device Details</p>";
        $body .= "<p>Brand: " . htmlspecialchars($brand) . "</p>";
        $body .= "<p>Model: " . htmlspecialchars($phone_model) . "</p>";
        $body .= "<p>IMEI: " . htmlspecialchars($imei) . "</p>";
        $body .= "<p>Issue: " . htmlspecialchars($reason) . "</p>";
        $body .= "<p>Estimated Cost: LKR " . number_format((float)$estimate_price, 2) . "</p></div>";
        // Footer note
        $body .= "<p>If you have any questions, feel free to contact us. Thank you for choosing $company_name!</p>";
        $body .= "</body></html>";

        // Send the email
        $email_sent = sendEmail($email, $subject, $body);
        if (!$email_sent) {
            $response['email_error'] = 'Failed to send email.';
        } else {
            // Indicate success (record saved and email sent)
            $response['success'] = 'Repair record saved successfully and Email sent to customer.';
        }
    }

    // If no specific action was taken (fallback)
    if (empty($response)) {
        $response['success'] = 'Repair record has been added successfully.';
    }
} catch (Exception $e) {
    $conn->rollback();
    // On error, respond with error message
    $response['error'] = 'Transaction failed: ' . $e->getMessage();
}
$conn->close();
echo json_encode($response);
