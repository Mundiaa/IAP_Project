<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enteredOtp = $_POST['otp'];

    if (isset($_SESSION['otp']) && time() < $_SESSION['otp_expire']) {
        if ($enteredOtp == $_SESSION['otp']) {
            echo "<h2>✅ Verification successful!</h2>";
            // redirect to dashboard.php or login
            // header("Location: dashboard.php");
            exit;
        } else {
            echo "<p style='color:red;'>❌ Invalid code. Try again.</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ OTP expired. Please sign up again.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
</head>
<body>
    <h2>Enter Verification Code</h2>
    <form method="post" action="">
        <input type="text" name="otp" placeholder="Enter OTP" required>
        <button type="submit">Verify</button>
    </form>
</body>
</html>
