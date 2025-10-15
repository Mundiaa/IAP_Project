//this file is used to confirm DB write access
<?php
require_once 'conf.php';
require_once 'database.php';

$sql = "INSERT INTO users (fullname, email, password) VALUES ('Test User', 'test@example.com', '123')";
if ($conn->query($sql)) {
    echo "✅ Insert success!";
} else {
    echo "❌ Error: " . $conn->error;
}
$conn->close();
?>
