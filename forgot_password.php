<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'conf.php';
require_once 'database.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $token = bin2hex(random_bytes(50));
        $expires = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        // Delete any existing reset requests
        $conn->query("DELETE FROM password_resets WHERE user_id=" . $user['id']);

        // Store new token
        $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user['id'], $token, $expires);
        $stmt->execute();

        // Generate reset link
        $reset_link = $conf['site_url'] . "/reset_password.php?token=$token";

        // Send email
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = $conf['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $conf['smtp_user'];
            $mail->Password = $conf['smtp_pass'];
            $mail->SMTPSecure = $conf['smtp_secure'];
            $mail->Port = $conf['smtp_port'];

            //Recipients
            $mail->setFrom($conf['smtp_user'], 'Notez Wiz Support');
            $mail->addAddress($email);

            //Content
            $mail->isHTML(true);
            $mail->Subject = "Password Reset Request";
            $mail->Body = "
                <p>Hello,</p>
                <p>We received a password reset request for your account.</p>
                <p><a href='$reset_link'>Click here to reset your password</a></p>
                <p>This link will expire in 10 minutes.</p>
            ";

            $mail->send();
            echo "<div style='color:green;'>✅ Password reset link has been sent to your email.</div>";
        } catch (Exception $e) {
            echo "<div style='color:red;'>❌ Message could not be sent. Error: {$mail->ErrorInfo}</div>";
        }
    } else {
        echo "<div style='color:red;'>⚠ No account found with that email.</div>";
    }
}
?>

<form method="POST">
  <h3>Forgot Password</h3>
  <input type="email" name="email" placeholder="Enter your email" required />
  <button type="submit">Send Reset Link</button>
</form>
