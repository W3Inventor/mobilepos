<?php
include '../../../../config/dbconnect.php';
include '../payment-helper/send_email.php';
include '../payment-helper/send_sms.php';
include '../setting-helper/settings_helper.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

function generateTrackingCode($conn, $length = 6) {
    $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz123456789';
    do {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }
        $count = 0;

        // Check uniqueness
        $stmt = $conn->prepare("SELECT COUNT(*) FROM in_house_repair WHERE tracking_code = ?");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    } while ($count > 0);

    return $code;
}


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
$bill_type     = $_POST['bill_type'] ?? '';

$image_urls   = array_filter(array_map('trim', explode(',', $uploadedImagePaths)));
$images_column = !empty($image_urls) ? implode(',', $image_urls) : null;

$response = [];
$conn->begin_transaction();

try {
    // Ensure customer exists
    $stmt = $conn->prepare(
        "SELECT customer_id FROM customers WHERE nic = ? OR email = ? OR mobile_number = ? LIMIT 1"
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

    // Insert repair record
    $tracking_code = generateTrackingCode($conn);
    $status = 'Submitted';
    $technician_id = null;
    $stmt = $conn->prepare(
        "INSERT INTO in_house_repair 
         (imei, brand, model, reason, images, estimate_price, technician_id, status, customer_id, tracking_code)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("sssssdisss", 
        $imei, $brand, $phone_model, $reason, 
        $images_column, $estimate_price, 
        $technician_id, $status, $customer_id, $tracking_code
    );
    if (!$stmt->execute()) {
        throw new Exception("Failed to insert repair: " . $stmt->error);
    }

    $repair_id = $stmt->insert_id;
    $stmt->close();

    // Insert into repair_status_history
    $stmt = $conn->prepare("INSERT INTO repair_status_history (repair_id, status) VALUES (?, ?)");
    $stmt->bind_param("is", $repair_id, $status);
    if (!$stmt->execute()) {
        throw new Exception("Failed to record status history: " . $stmt->error);
    }
    $stmt->close();

    $conn->commit();

    $repair_number = 'Rep' . str_pad($repair_id, 4, '0', STR_PAD_LEFT);
    $domain   = $_SERVER['HTTP_HOST'];
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? "https" : "http";
    $base_url = "$protocol://$domain";
    $tracking_url = "$base_url/track-repair.php?code=$tracking_code";

    // Print: return redirect for frontend to open PDF
    if ($bill_type === 'print') {
        $response['redirect'] = "assets/php/helper/repair-helper/generate_repair_pdf.php?repair_id=$repair_id";
    }

    // SMS
    if ($bill_type === 'sms' && !empty($mobile_number)) {
        $sms_message = "Your repair request ($repair_number) has been submitted. Track it here: $tracking_url";
        $sms_result = sendSms($mobile_number, $sms_message);

        if (!empty($sms_result['success'])) {
            $response['sms_success'] = $sms_result['success'];
        } else {
            $response['sms_error'] = $sms_result['error'] ?? 'SMS failed.';
        }
    }

    // Email
    if ($bill_type === 'email' && !empty($email)) {
        $settings     = getSettings();
        $company_name = $settings['company_name'];
        $brand_color  = $settings['brand_color'];
        $logo_path    = $settings['logo_path'];
        $contact_phone= $settings['mobile'];
        $website      = $settings['website'];

        $subject = "Your Repair Ticket - $repair_number";
        $body  = "<h2 style='color:$brand_color;'>$company_name Repair Ticket</h2>";
        $body .= "<p>Dear $full_name,</p>";
        $body .= "<p>Your repair request <strong>$repair_number</strong> has been submitted.</p>";
        $body .= "<p><a href='$tracking_url'>Click here to track the repair</a></p>";
        $body .= "<p>Thank you,<br>$company_name</p>";

        $email_sent = sendEmail($email, $subject, $body);
        if (!$email_sent) {
            $response['email_error'] = 'Failed to send email.';
        } else {
            $response['success'] = 'Email sent successfully.';
        }
    }

    // Default success
    if (empty($response)) {
        $response['success'] = 'Repair submitted successfully.';
    }

} catch (Exception $e) {
    $conn->rollback();
    $response['error'] = 'Transaction failed: ' . $e->getMessage();
}

$conn->close();
echo json_encode($response);
