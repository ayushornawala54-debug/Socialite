<?php
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'socialite021@gmail.com';  // Your Gmail
    $mail->Password = 'wvbrgzwvtmtkevkl';  // Use a New Google App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Sender & Recipient
    $mail->setFrom('socialite021@gmail.com', 'Jari Enterprise');
    $mail->addAddress('kp515547@gmail.com');

    // Email Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email';
    $mail->Body = '<h3>This is a test email from PHPMailer SMTP!</h3>';

    // Send email
    $mail->send();
    echo "✅ Email sent successfully!";
} catch (Exception $e) {
    echo "❌ Email failed: {$mail->ErrorInfo}";
}
?>