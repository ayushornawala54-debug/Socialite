<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load Composer autoload
$config = require 'smtp_config.php'; // Load SMTP settings

function sendMail($to, $subject, $body) {
    global $config;
    
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['socilaite021@gmail.com'];
        $mail->Password = $config['password'];
        $mail->SMTPSecure = $config['encryption'];
        $mail->Port = $config['port'];

        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return "Email sent successfully!";
    } catch (Exception $e) {
        return "Mailer Error: " . $mail->ErrorInfo;
    }
}

// Example Usage
echo sendMail('recipient@example.com', 'Test Email', '<h1>Hello, this is a test email!</h1>');
