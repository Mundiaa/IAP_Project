<?php
// Start or resume the session
session_start();

require_once 'conf.php';
require_once 'database.php';

// Track logout interaction before destroying session
if (isset($_SESSION['user_id']) && isset($conn)) {
    $user_id = $_SESSION['user_id'];
    // Check if table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'user_interactions'");
    if ($table_check->num_rows > 0) {
        $track_stmt = $conn->prepare("INSERT INTO user_interactions (user_id, interaction_type, interaction_details, page_url) VALUES (?, 'logout', 'User logged out', ?)");
        $url = $_SERVER['HTTP_REFERER'] ?? 'logout.php';
        $track_stmt->bind_param("is", $user_id, $url);
        $track_stmt->execute();
        $track_stmt->close();
    }
}

// Unset all session variables
session_unset();

// Destroy the session completely
session_destroy();

// Redirect user back to login page
header("Location: index.php");
exit;
