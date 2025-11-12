<?php
session_start();
require_once 'conf.php';
require_once 'database.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo "Unauthorized";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note_id = intval($_POST['note_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $user_id = $_SESSION['user_id'];

    if ($note_id > 0 && $title && $content) {
        // Update note (only if it belongs to the user)
        $stmt = $conn->prepare("UPDATE notes SET title = ?, content = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssii", $title, $content, $note_id, $user_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                // Track note edit interaction
                $table_check = $conn->query("SHOW TABLES LIKE 'user_interactions'");
                if ($table_check->num_rows > 0) {
                    $track_stmt = $conn->prepare("INSERT INTO user_interactions (user_id, interaction_type, interaction_details, page_url) VALUES (?, 'note_edited', ?, ?)");
                    $details = "Note edited: " . substr($title, 0, 50);
                    $url = $_SERVER['HTTP_REFERER'] ?? 'dashboard.php';
                    $track_stmt->bind_param("iss", $user_id, $details, $url);
                    $track_stmt->execute();
                    $track_stmt->close();
                }
                echo "success";
            } else {
                echo "not_found"; // Note doesn't belong to user or no change made
            }
        } else {
            echo "error";
        }

        $stmt->close();
    } else {
        echo "invalid";
    }
}
?>
