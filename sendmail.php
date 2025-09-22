<?php
session_start();
require 'vendor/autoload.php'; // Composer autoload for PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Make sure user email is in session
if (!isset($_SESSION['email'])) {
    die("❌ No email found in session. Please sign up or log in first.");
}

// Generate OTP
$otp = rand(100000, 999999);
$_SESSION['otp'] = $otp;
$_SESSION['otp_expire'] = time() + 300; // 5 minutes validity

$mail = new PHPMailer(true);

try {
    // SMTP settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'emmanuel.wandibba@strathmore.edu';
    $mail->Password   = 'eyol iqqe asob gkro'; // Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('emmanuel.wandibba@strathmore.edu', 'Notez Wiz');
    $mail->addAddress($_SESSION['email']); // send to signed-up user

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Your Notez Wiz Verification Code';
    $mail->Body    = "
        <h2>Welcome to Notez Wiz!</h2>
        <p>Here is your verification code (valid for 5 minutes):</p>
        <h1 style='color:#28a745;'>$otp</h1>
        <p>Please enter this code on the verification page to access your dashboard.</p>
    ";
    $mail->AltBody = "Your verification code is: $otp (valid for 5 minutes)";

    // Send email
    if ($mail->send()) {
        // Go directly to verify page, no extra output
        header("Location: verify_otp.php");
        exit;
    } else {
        echo "❌ Mail not sent.";
    }

} catch (Exception $e) {
    echo "❌ Email could not be sent. Error: {$mail->ErrorInfo}";
}



