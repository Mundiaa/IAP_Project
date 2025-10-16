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

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note_id = intval($_POST['note_id'] ?? 0);
    $user_id = $_SESSION['user_id'];

    if ($note_id > 0) {
        // Delete only if the note belongs to this user
        $stmt = $conn->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $note_id, $user_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo "success";
            } else {
                echo "not_found"; // Note doesn't belong to this user or doesn't exist
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
