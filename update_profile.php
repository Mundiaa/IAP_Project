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
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Validation
    if (empty($fullname) || empty($email)) {
        header("Location: profile.php?status=error");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: profile.php?status=error");
        exit;
    }

    // Check if email is already taken by another user
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $stmt->close();
        header("Location: profile.php?status=error");
        exit;
    }
    $stmt->close();

    // Update profile
    $stmt = $conn->prepare("UPDATE users SET fullname = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $fullname, $email, $user_id);

    if ($stmt->execute()) {
        // Update session fullname if it exists
        if (isset($_SESSION['fullname'])) {
            $_SESSION['fullname'] = $fullname;
        }
        header("Location: profile.php?status=success");
    } else {
        header("Location: profile.php?status=error");
    }

    $stmt->close();
} else {
    header("Location: profile.php");
}
exit;
?>

