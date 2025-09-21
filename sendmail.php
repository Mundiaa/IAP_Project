<?php
require 'vendor/autoload.php'; // if using Composer
// or require 'PHPMailer/src/PHPMailer.php'; require 'PHPMailer/src/SMTP.php'; require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'emmanuel.wandibba@strathmore.edu';       // Your Gmail address
    $mail->Password   = 'eyol iqqe asob gkro';         // Gmail App Password (not your normal Gmail password)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    //Recipients
    $mail->setFrom('konghover@gmail.com', 'Emmanuel Wandibba');
    $mail->addAddress('konghover@gmail.com', 'Emmanuel Wandibba'); // Add a recipient

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Two Factor Authentication Test Email';
    $mail->Body    = '<h1>Hello!</h1><p>Here is a code to verify whether it is you.</p>';

    $mail->send();
    echo "✅ Email has been sent successfully!";
} catch (Exception $e) {
    echo "❌ Email could not be sent. Error: {$mail->ErrorInfo}";
}
