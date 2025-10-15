<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Include database config
require_once "conf.php"; // defines $conn
require_once 'database.php';
require 'vendor/autoload.php';

//require_once "vendor/PHPMailer/PHPMailer.php";
//require_once "vendor/PHPMailer/SMTP.php";
//require_once "vendor/PHPMailer/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email            = trim($_POST["email"]);
    $password         = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    // Basic validation
    if (empty($email) || empty($password) || empty($confirm_password)) {
        die("⚠️ Please fill in all fields.");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("⚠️ Invalid email format.");
    }
    if ($password !== $confirm_password) {
        die("⚠️ Passwords do not match.");
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $sql = "SELECT id FROM users WHERE email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->close();
            die("<br>⚠️ Email already registered.");
        }
        $stmt->close();
    } else {
        die("Database error: " . $conn->error);
    }

    // Insert new user (fullname left blank for now)
    $sql = "INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $fullname = "New User"; // default until profile is updated
        $stmt->bind_param("sss", $fullname, $email, $hashed_password);

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;

            // Start session immediately (auto-login)
            $_SESSION["loggedin"] = true;
            $_SESSION["user_id"]  = $user_id;

            // Send welcome email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'your_email@gmail.com';
                $mail->Password   = 'your_app_password';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('your_email@gmail.com', 'Notez-Wiz');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = "Welcome to Notez-Wiz!";
                $mail->Body    = "Hi,<br><br>Your account has been created successfully.<br>You can now log in and start keeping your notes securely with 2FA!";

                $mail->send();
            } catch (Exception $e) {
                error_log("Mailer Error: " . $mail->ErrorInfo);
            }

            header("Location: dashboard.php");
            exit;
        } else {
            die("❌ Something went wrong: " . $stmt->error);
        }
        $stmt->close();
    } else {
        die("Database error: " . $conn->error);
    }

    $conn->close();
}
?>
