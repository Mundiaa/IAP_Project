<?php
/**
 * API endpoint to fetch analytics data for charts
 */

session_start();
require_once 'conf.php';
require_once 'database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = [];

try {
    // Check if analytics table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'user_interactions'");
    $has_analytics_table = $table_check->num_rows > 0;

    // 1. Notes created over time (last 30 days)
    $notes_query = "
        SELECT DATE(created_at) AS date, COUNT(*) AS count
        FROM notes
        WHERE user_id = ? AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date ASC
    ";
    $stmt = $conn->prepare($notes_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $notes_over_time = [];
    while ($row = $result->fetch_assoc()) {
        $notes_over_time[] = [
            'date' => $row['date'],
            'count' => (int)$row['count']
        ];
    }
    $stmt->close();
    $data['notes_over_time'] = $notes_over_time;

    // 2. Total notes count
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM notes WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data['total_notes'] = (int)$result->fetch_assoc()['total'];
    $stmt->close();

    // 3. Notes by day of week
    $day_query = "
        SELECT DAYNAME(created_at) AS day_name, COUNT(*) AS count
        FROM notes
        WHERE user_id = ?
        GROUP BY DAYNAME(created_at)
        ORDER BY FIELD(day_name, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')
    ";
    $stmt = $conn->prepare($day_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $notes_by_day = [];
    while ($row = $result->fetch_assoc()) {
        $notes_by_day[] = [
            'day' => $row['day_name'],
            'count' => (int)$row['count']
        ];
    }
    $stmt->close();
    $data['notes_by_day'] = $notes_by_day;

    // 4. Notes by hour of day
    $hour_query = "
        SELECT HOUR(created_at) AS hour, COUNT(*) AS count
        FROM notes
        WHERE user_id = ?
        GROUP BY HOUR(created_at)
        ORDER BY hour ASC
    ";
    $stmt = $conn->prepare($hour_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $notes_by_hour = [];
    while ($row = $result->fetch_assoc()) {
        $notes_by_hour[] = [
            'hour' => (int)$row['hour'],
            'count' => (int)$row['count']
        ];
    }
    $stmt->close();
    $data['notes_by_hour'] = $notes_by_hour;

    // 5. Recent notes (last 7 days)
    $recent_query = "
        SELECT DATE(created_at) AS date, COUNT(*) AS count
        FROM notes
        WHERE user_id = ? AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date ASC
    ";
    $stmt = $conn->prepare($recent_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $recent_notes = [];
    while ($row = $result->fetch_assoc()) {
        $recent_notes[] = [
            'date' => $row['date'],
            'count' => (int)$row['count']
        ];
    }
    $stmt->close();
    $data['recent_notes'] = $recent_notes;

    // 6. User interactions (if table exists)
    if ($has_analytics_table) {
        // Interaction types distribution
        $interaction_query = "
            SELECT interaction_type, COUNT(*) AS count
            FROM user_interactions
            WHERE user_id = ?
            GROUP BY interaction_type
            ORDER BY count DESC
        ";
        $stmt = $conn->prepare($interaction_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $interactions = [];
        while ($row = $result->fetch_assoc()) {
            $interactions[] = [
                'type' => $row['interaction_type'],
                'count' => (int)$row['count']
            ];
        }
        $stmt->close();
        $data['interactions'] = $interactions;

        // Interactions over time (last 7 days)
        $interaction_time_query = "
            SELECT DATE(created_at) AS date, COUNT(*) AS count
            FROM user_interactions
            WHERE user_id = ? AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ";
        $stmt = $conn->prepare($interaction_time_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $interactions_over_time = [];
        while ($row = $result->fetch_assoc()) {
            $interactions_over_time[] = [
                'date' => $row['date'],
                'count' => (int)$row['count']
            ];
        }
        $stmt->close();
        $data['interactions_over_time'] = $interactions_over_time;

        // Total interactions
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM user_interactions WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data['total_interactions'] = (int)$result->fetch_assoc()['total'];
        $stmt->close();
    } else {
        $data['interactions'] = [];
        $data['interactions_over_time'] = [];
        $data['total_interactions'] = 0;
    }

    // 7. Average note length
    $avg_query = "
        SELECT AVG(LENGTH(content)) AS avg_length, AVG(LENGTH(title)) AS avg_title_length
        FROM notes
        WHERE user_id = ?
    ";
    $stmt = $conn->prepare($avg_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $avg = $result->fetch_assoc();
    $data['avg_note_length'] = (int)($avg['avg_length'] ?? 0);
    $data['avg_title_length'] = (int)($avg['avg_title_length'] ?? 0);
    $stmt->close();

    header('Content-Type: application/json');
    echo json_encode($data);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();
?>

