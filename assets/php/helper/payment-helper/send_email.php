<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include Composer's autoload.php
require __DIR__ . '/../../../../vendor/autoload.php';

function sendEmail($to, $subject, $body, $attachmentPath = null) {
    // Load SMTP configuration
    $smtpConfig = require __DIR__ . '/../email-helper/smtp_config.php';

    // Initialize PHPMailer
    $mail = new PHPMailer(true);

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host       = $smtpConfig['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpConfig['username'];
        $mail->Password   = $smtpConfig['password'];
        $mail->SMTPSecure = $smtpConfig['encryption'];
        $mail->Port       = $smtpConfig['port'];

        // Email settings
        $mail->setFrom($smtpConfig['username'], 'Exxplan POS System');
        $mail->addAddress($to); // Recipient's email
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->isHTML(true); // Set email format to HTML

        // Add attachment if provided
        if ($attachmentPath && file_exists($attachmentPath)) {
            $mail->addAttachment($attachmentPath);
        }

        // Send email
        $mail->send();
        return true;

    } catch (Exception $e) {
        // Handle exception (log or display error message as necessary)
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
