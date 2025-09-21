<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input_code = $_POST['code'];

    if (isset($_SESSION['2fa_code']) && isset($_SESSION['2fa_expire'])) {
        if (time() > $_SESSION['2fa_expire']) {
            echo "Code expired. Please login again.";
            session_destroy();
        } elseif ($input_code == $_SESSION['2fa_code']) {
            echo "✅ Verification successful! You are logged in.";
            // Mark user as logged in
            $_SESSION['logged_in'] = true;
            unset($_SESSION['2fa_code']);
            unset($_SESSION['2fa_expire']);
        } else {
            echo "❌ Invalid code.";
        }
    }
}
?>
<form method="POST">
    <input type="text" name="code" placeholder="Enter verification code" required /><br>
    <button type="submit">Verify</button>
</form>
