<?php
/**
 * Track user interactions for analytics
 * Called via AJAX from JavaScript
 */

session_start();
require_once 'conf.php';
require_once 'database.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Get POST data
$interaction_type = $_POST['type'] ?? '';
$interaction_details = $_POST['details'] ?? '';
$page_url = $_POST['url'] ?? $_SERVER['HTTP_REFERER'] ?? '';

// Validate interaction type
$allowed_types = [
    'page_view', 'note_created', 'note_edited', 'note_deleted', 
    'note_favorited', 'note_unfavorited', 'search_performed', 
    'filter_applied', 'profile_updated', 'password_changed',
    'login', 'logout', 'button_click', 'form_submitted'
];

if (empty($interaction_type) || !in_array($interaction_type, $allowed_types)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid interaction type']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Insert interaction into database
try {
    // Check if table exists, if not create it
    $table_check = $conn->query("SHOW TABLES LIKE 'user_interactions'");
    if ($table_check->num_rows == 0) {
        // Table doesn't exist, create it
        $create_table = "CREATE TABLE IF NOT EXISTS user_interactions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            interaction_type VARCHAR(50) NOT NULL,
            interaction_details TEXT,
            page_url VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_id (user_id),
            INDEX idx_interaction_type (interaction_type),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $conn->query($create_table);
    }

    $stmt = $conn->prepare("INSERT INTO user_interactions (user_id, interaction_type, interaction_details, page_url) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $interaction_type, $interaction_details, $page_url);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to track interaction']);
    }
    
    $stmt->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();
?>

