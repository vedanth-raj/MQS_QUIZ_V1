<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('SMTP_USERNAME') ?: 'mywork3410@gmail.com';
    $mail->Password   = getenv('SMTP_PASSWORD') ?: 'qsjd xzdq yova ctkn';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = getenv('SMTP_PORT') ?: 587;

    $mail->SMTPDebug = 0; // Disable debug output for production
    $mail->Timeout = 30;
    $mail->SMTPAutoTLS = true; // Enable auto TLS

    $mail->setFrom('mywork3410@gmail.com', 'MQS Quiz Test');
    $mail->isHTML(true);

    $mail->addAddress('test@example.com', 'Test User');
    $mail->Subject = 'Test Email';
    $mail->Body = '<h1>Test Email</h1><p>This is a test email.</p>';

    $mail->send();
    echo 'Test email sent successfully!';
} catch (Exception $e) {
    echo "Test email failed: {$mail->ErrorInfo}";
}
?>
