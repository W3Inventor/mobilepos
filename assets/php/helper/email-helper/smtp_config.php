<?php
// smtp_config.php

// Include your database connection
include '../../../../config/dbconnect.php';

// Initialize the configuration array
$smtpConfig = [];

// Fetch SMTP settings from the database
$query = "SELECT * FROM smtp_settings LIMIT 1";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $smtpSettings = $result->fetch_assoc();

    // Populate the configuration array with database values
    $smtpConfig = [
        'host' => $smtpSettings['smtp_host'],
        'username' => $smtpSettings['smtp_username'],
        'password' => $smtpSettings['smtp_password'],
        'port' => $smtpSettings['smtp_port'],
        'encryption' => $smtpSettings['encryption']
    ];
} else {
    // Trigger an error if no SMTP settings found in the database
    die("Error: SMTP configuration not found in the database.");
}

// Close the database connection
$conn->close();

// Return the configuration array
return $smtpConfig;
?>
