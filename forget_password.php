<?php
require 'conf.php';
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $token = bin2hex(random_bytes(50)); // Secure random token
        $expires = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        // Store token
        $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user['id'], $token, $expires);
        $stmt->execute();

        // Send reset link
        $reset_link = "http://localhost/IAP_Project/reset_password.php?token=$token";

        $subject = "Password Reset Request";
        $message = "Click this link to reset your password: $reset_link";
        $headers = "From: noreply@notezwiz.com";

        mail($email, $subject, $message, $headers);

        echo "✅ A password reset link has been sent to your email.";
    } else {
        echo "⚠ No account found with that email.";
    }
}
?>

<form method="POST">
  <h3>Forgot Password</h3>
  <input type="email" name="email" placeholder="Enter your email" required />
  <button type="submit">Send Reset Link</button>
</form>