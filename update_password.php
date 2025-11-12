<?php
session_start();
require_once 'conf.php';
require_once 'database.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        header("Location: settings.php?status=error");
        exit;
    }

    // Check if new passwords match
    if ($new_password !== $confirm_password) {
        header("Location: settings.php?status=mismatch");
        exit;
    }

    // Check password length
    if (strlen($new_password) < 6) {
        header("Location: settings.php?status=error");
        exit;
    }

    // Verify current password
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        header("Location: settings.php?status=error");
        exit;
    }

    $user = $result->fetch_assoc();
    $stmt->close();

    // Verify current password
    if (!password_verify($current_password, $user['password'])) {
        header("Location: settings.php?status=invalid");
        exit;
    }

    // Hash new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update password
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $user_id);

    if ($stmt->execute()) {
        // Track interaction
        if (isset($conn)) {
            $table_check = $conn->query("SHOW TABLES LIKE 'user_interactions'");
            if ($table_check->num_rows > 0) {
                $track_stmt = $conn->prepare("INSERT INTO user_interactions (user_id, interaction_type, interaction_details, page_url) VALUES (?, 'password_changed', 'Password changed successfully', ?)");
                $url = $_SERVER['HTTP_REFERER'] ?? 'settings.php';
                $track_stmt->bind_param("is", $user_id, $url);
                $track_stmt->execute();
                $track_stmt->close();
            }
        }
        header("Location: settings.php?status=success");
    } else {
        header("Location: settings.php?status=error");
    }

    $stmt->close();
} else {
    header("Location: settings.php");
}
exit;
?>

