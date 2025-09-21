<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $_POST['otp'] ?? '';

    if (isset($_SESSION['otp'], $_SESSION['otp_expire']) 
        && time() < $_SESSION['otp_expire'] 
        && $otp == $_SESSION['otp']) {

        unset($_SESSION['otp'], $_SESSION['otp_expire']); // clear OTP
        header("Location: dashboard.php");
        exit;
    } else {
        $message = "Invalid or expired code.";
    }
}
?>
<form method="post">
    <label>Enter Verification Code:</label>
    <input type="text" name="otp" required>
    <button type="submit">Verify</button>
</form>
<?php if (!empty($message)) echo "<p style='color:red;'>$message</p>"; ?>
