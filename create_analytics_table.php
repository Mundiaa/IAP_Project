<?php
/**
 * Script to create the user_interactions table for analytics
 * Run this once to set up the analytics tracking table
 */

require_once 'conf.php';
require_once 'database.php';

// Create user_interactions table
$sql = "CREATE TABLE IF NOT EXISTS user_interactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    interaction_type VARCHAR(50) NOT NULL,
    interaction_details TEXT,
    page_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_interaction_type (interaction_type),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql)) {
    echo "✅ Analytics table 'user_interactions' created successfully!\n";
} else {
    echo "❌ Error creating table: " . $conn->error . "\n";
}

$conn->close();
?>

