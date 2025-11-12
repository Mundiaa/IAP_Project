<?php
session_start();
require_once 'conf.php';
require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo "Unauthorized";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $user_id = $_SESSION['user_id'];

    if ($title && $content) {
        $stmt = $conn->prepare("INSERT INTO notes (user_id, title, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $title, $content);
        if ($stmt->execute()) {
            // Track note creation interaction
            $table_check = $conn->query("SHOW TABLES LIKE 'user_interactions'");
            if ($table_check->num_rows > 0) {
                $track_stmt = $conn->prepare("INSERT INTO user_interactions (user_id, interaction_type, interaction_details, page_url) VALUES (?, 'note_created', ?, ?)");
                $details = "Note created: " . substr($title, 0, 50);
                $url = $_SERVER['HTTP_REFERER'] ?? 'dashboard.php';
                $track_stmt->bind_param("iss", $user_id, $details, $url);
                $track_stmt->execute();
                $track_stmt->close();
            }
            echo "success";
        } else {
            echo "error";
        }
        $stmt->close();
    } else {
        echo "empty";
    }
}
?>
