<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'conf.php';
require_once 'database.php';

// Redirect to login if not logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: index.php");
    exit;
}

$user = null;
if (isset($conn) && $conn && isset($_SESSION['user_id'])) {
    // Try to get created_at if it exists, otherwise just get basic fields
    $stmt = $conn->prepare("SELECT id, fullname, email FROM users WHERE id=?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $user = $res->fetch_assoc();
    }
    $stmt->close();
}

$message = '';
$messageType = '';

// Handle profile update message from update_profile.php
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success') {
        $message = 'Profile updated successfully!';
        $messageType = 'success';
    } elseif ($_GET['status'] === 'error') {
        $message = 'Error updating profile. Please try again.';
        $messageType = 'error';
    }
}
?>
<!Doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Profile - Notez Wiz</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

   <!-- Sidebar -->
  <div class="sidebar">
    <h3>Notez Wiz</h3>
    <a href="dashboard.php">🏠 Home</a>
    <a href="profile.php" class="active">👤 Profile</a>
    <a href="dashboard.php">📝 Notes</a>
    <a href="settings.php">⚙️ Settings</a>
    <a href="logout.php">🚪 Logout</a>

    <div class="toggle-btn">
      <button id="darkModeToggle" class="btn btn-light btn-sm">🌙 Dark Mode</button>
    </div>
  </div>

  <!-- Main Content -->
  <div class="content">
    <div class="card p-4">
      <h4 class="mb-3">My Profile</h4>

      <?php if ($message): ?>
        <div class="alert alert-<?= $messageType === 'success' ? 'success' : 'danger' ?>" role="alert">
          <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>

      <?php if ($user): ?>
        <form id="profileForm" method="POST" action="update_profile.php">
          <div class="form-group mb-3">
            <label for="fullname">Full Name</label>
            <input 
              type="text" 
              id="fullname" 
              name="fullname" 
              class="form-control" 
              value="<?= htmlspecialchars($user['fullname']) ?>" 
              required
            >
          </div>

          <div class="form-group mb-3">
            <label for="email">Email</label>
            <input 
              type="email" 
              id="email" 
              name="email" 
              class="form-control" 
              value="<?= htmlspecialchars($user['email']) ?>" 
              required
            >
          </div>

          <?php if (isset($user['created_at'])): ?>
          <div class="form-group mb-3">
            <label>Account Created</label>
            <input 
              type="text" 
              class="form-control" 
              value="<?= htmlspecialchars($user['created_at']) ?>" 
              disabled
            >
          </div>
          <?php endif; ?>

          <button type="submit" class="btn btn-primary btn-custom">Update Profile</button>
        </form>
      <?php else: ?>
        <p class="text-danger">Error loading profile information.</p>
      <?php endif; ?>
    </div>
  </div>

  <script src="assets/js/dashboard.js"></script>

</body>
</html>

