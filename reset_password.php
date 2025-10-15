<?php
require 'conf.php';
require 'database.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT user_id, expires_at FROM password_resets WHERE token=?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();

        if (strtotime($data['expires_at']) > time()) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $new_pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
                $user_id = $data['user_id'];

                // Update password
                $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
                $stmt->bind_param("si", $new_pass, $user_id);
                $stmt->execute();

                // Delete used token
                $conn->query("DELETE FROM password_resets WHERE token='$token'");

                echo "✅ Password updated successfully!";
                exit;
            }
        } else {
            echo "⚠ Token expired. Please request a new password reset.";
        }
    } else {
        echo "⚠ Invalid reset link.";
    }
} else {
    echo "⚠ No reset token provided.";
}
?>

<form method="POST">
  <h3>Reset Password</h3>
  <input type="password" name="password" placeholder="Enter new password" required />
  <button type="submit">Update Password</button>
</form>