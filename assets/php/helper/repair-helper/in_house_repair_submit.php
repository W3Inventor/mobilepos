<?php
include '../../../../config/dbconnect.php';
// Include helpers for email, SMS, and settings (reused from POS)
include '../payment-helper/send_email.php';
include '../payment-helper/send_sms.php';
include '../setting-helper/settings_helper.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// 1. Collect form data (existing functionality)
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

// Process uploaded image paths into a single string (existing functionality)
$image_urls   = array_filter(array_map('trim', explode(',', $uploadedImagePaths)));
$images_column = !empty($image_urls) ? implode(',', $image_urls) : null;

$response = [];
$conn->begin_transaction();
try {
    // 2. Ensure customer exists (insert if new – existing functionality)
    $stmt = $conn->prepare(
        "SELECT customer_id FROM customers 
         WHERE nic = ? OR email = ? OR mobile_number = ? LIMIT 1"
    );
    $stmt->bind_param("sss", $nic, $email, $mobile_number);
    $stmt->execute();
    $stmt->bind_result($customer_id);
    $stmt->fetch();
    $stmt->close();

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

    // 3. Insert the new in-house repair record (existing functionality)
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
    $repair_id = $stmt->insert_id;
    $stmt->close();
    $conn->commit();  // Save the transaction

    // 4. Post-save actions based on selected bill_type
    $repair_number = 'Rep' . str_pad($repair_id, 4, '0', STR_PAD_LEFT);  // e.g., "Rep0005"
    $domain   = $_SERVER['HTTP_HOST'];
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? "https" : "http";
    $base_url = "$protocol://$domain";

    // 4A. If Print selected: generate PDF and provide URL for printing
    if ($bill_type === 'print') {
        // Use a PDF generator script (similar to POS's generate_pdf.php):contentReference[oaicite:8]{index=8}
        $response['redirect'] = "assets/php/helper/repair-helper/generate_repair_pdf.php?repair_id=$repair_id";
    }

    // 4B. If SMS selected: send SMS notification to customer
    if ($bill_type === 'sms' && !empty($mobile_number)) {
        // Prepare SMS message (e.g., confirmation with Repair ID)
        $sms_message = "Thank you for your repair request! Your Repair ID is $repair_number. "
                     . "We will contact you when it is ready.";
        $sms_result = sendSms($mobile_number, $sms_message);  // sendSms from included helper

        // Mirror POS logic to interpret SMS API response:contentReference[oaicite:9]{index=9}:contentReference[oaicite:10]{index=10}
        $sms_success_patterns = [
            "OK:1-MSG_GSM-99 Uploaded_Successfully",
            "OK:1-MSG_GSM-71 Uploaded_Successfully"
        ];
        $is_sms_success = false;
        foreach ($sms_success_patterns as $pattern) {
            if (isset($sms_result['error']) && strpos($sms_result['error'], $pattern) !== false) {
                $is_sms_success = true;
                break;
            }
        }
        if ($is_sms_success) {
            $response['sms_success'] = 'SMS sent successfully.';
        } elseif (!empty($sms_result['success'])) {
            $response['sms_success'] = $sms_result['success'];  // e.g., "SMS sent successfully!" from helper
        } else {
            // Treat any other outcome as error
            $response['sms_error'] = $sms_result['error'] ?? 'SMS sending failed.';
        }
    }

    // 4C. If Email selected: send an email with repair invoice/confirmation
    if ($bill_type === 'email' && !empty($email)) {
        // Fetch company settings for branding the email (logo, name, etc.)
        $settings     = getSettings();
        $company_name = $settings['company_name'];
        $address1     = $settings['address_line1'];
        $address2     = $settings['address_line2'];
        $city         = $settings['city'];
        $brand_color  = $settings['brand_color'];
        $website      = $settings['website'];
        $contact_phone= $settings['mobile'];
        $logo_path    = $settings['logo_path'];

        // Email subject 
        $subject = "Your Repair Ticket from $company_name (Repair #$repair_number)";
        // Begin building an HTML email body (modeled after the POS invoice email):contentReference[oaicite:11]{index=11}:contentReference[oaicite:12]{index=12}
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
        // Company header (logo and address)
        $body .= "<div class='company-header'>";
        if (!empty($logo_path)) {
            // Embed logo image from server
            $body .= "<img src='{$base_url}/$logo_path' alt='{$company_name} Logo'><br>";
        }
        $body .= "<h1>" . htmlspecialchars($company_name) . "</h1>";
        $body .= "<p>" . htmlspecialchars($address1);
        if (!empty($address2)) $body .= ", " . htmlspecialchars($address2);
        if (!empty($city))    $body .= ", " . htmlspecialchars($city);
        $body .= "</p>";
        $body .= "<p>Phone: " . htmlspecialchars($contact_phone);
        if (!empty($website)) {
            $body .= " | Website: <a href='" . htmlspecialchars($website) . "'>" 
                  . htmlspecialchars($website) . "</a>";
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
        // Device/Issue details section
        $body .= "<div class='detail-section'><p class='section-title'>Device Details</p>";
        $body .= "<p>Brand: " . htmlspecialchars($brand) . "</p>";
        $body .= "<p>Model: " . htmlspecialchars($phone_model) . "</p>";
        $body .= "<p>IMEI: " . htmlspecialchars($imei) . "</p>";
        $body .= "<p>Issue: " . htmlspecialchars($reason) . "</p>";
        $body .= "<p>Estimated Cost: LKR " . number_format((float)$estimate_price, 2) . "</p></div>";
        // Footer note
        $body .= "<p>If you have any questions, feel free to contact us. "
              . "Thank you for choosing $company_name!</p>";
        $body .= "</body></html>";

        // Send the email (using sendEmail helper)
        $email_sent = sendEmail($email, $subject, $body);
        if (!$email_sent) {
            $response['email_error'] = 'Failed to send email.';
        } else {
            // Indicate success if email was sent (record already saved above)
            $response['success'] = 'Repair record saved successfully and Email sent to customer.';
        }
    }

    // 5. Default success message if no specific bill_type action was triggered
    if (empty($response)) {
        $response['success'] = 'Repair record has been added successfully.';
    }
} catch (Exception $e) {
    $conn->rollback();
    $response['error'] = 'Transaction failed: ' . $e->getMessage();
}
$conn->close();
echo json_encode($response);
?>
