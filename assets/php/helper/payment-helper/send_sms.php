<?php 
include_once '../sms-helper/config.php'; // Include the config file to load environment variables
include '../../../../config/dbconnect.php'; // Include the database connection file

function sendSms($phoneNumber, $message) {
    global $conn;

    // Fetch SMS settings from the database
    $query = "SELECT user_id, user_password FROM textit_gateway_settings LIMIT 1";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $smsSettings = $result->fetch_assoc();
        $userId = $smsSettings['user_id'];
        $password = $smsSettings['user_password'];
    } else {
        // Return an error if SMS settings are not found in the database
        return ['error' => 'SMS configuration not found in the database.'];
    }

    // Format phone number to start with "947" if it doesn't already
    if (preg_match('/^0/', $phoneNumber)) {
        $phoneNumber = '94' . substr($phoneNumber, 1);
    } elseif (preg_match('/^7/', $phoneNumber)) {
        $phoneNumber = '94' . $phoneNumber;
    }

    // HTTP API URL
    $apiUrl = "https://textit.biz/sendmsg/index.php";

    // Prepare the URL with query parameters
    $url = sprintf(
        "%s?id=%s&pw=%s&to=%s&text=%s",
        $apiUrl,
        urlencode($userId),
        urlencode($password),
        urlencode($phoneNumber),
        urlencode($message)
    );

    // Use cURL to send the request
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the request and capture the response
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    // Check for errors in the response
    if ($error) {
        return ['error' => 'Failed to send SMS: ' . $error];
    } else {
        // Check if the response contains a success code
        if (strpos($response, 'SUCCESS') !== false) {
            return ['success' => 'SMS sent successfully!'];
        } else {
            return ['error' => 'SMS API Error: ' . $response];
        }
    }
}

// Do not close the $conn connection here, as submit_payment.php needs it open
?>
