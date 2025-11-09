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

$message = '';
$messageType = '';

// Handle password update message from update_password.php
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success') {
        $message = 'Password updated successfully!';
        $messageType = 'success';
    } elseif ($_GET['status'] === 'error') {
        $message = 'Error updating password. Please check your current password and try again.';
        $messageType = 'error';
    } elseif ($_GET['status'] === 'mismatch') {
        $message = 'New passwords do not match.';
        $messageType = 'error';
    } elseif ($_GET['status'] === 'invalid') {
        $message = 'Invalid current password.';
        $messageType = 'error';
    }
}
?>
<!Doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Settings - Notez Wiz</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

   <!-- Sidebar -->
  <div class="sidebar">
    <h3>Notez Wiz</h3>
    <a href="dashboard.php">🏠 Home</a>
    <a href="profile.php">👤 Profile</a>
    <a href="dashboard.php">📝 Notes</a>
    <a href="settings.php" class="active">⚙️ Settings</a>
    <a href="logout.php">🚪 Logout</a>

    <div class="toggle-btn">
      <button id="darkModeToggle" class="btn btn-light btn-sm">🌙 Dark Mode</button>
    </div>
  </div>

  <!-- Main Content -->
  <div class="content">
    <div class="card p-4">
      <h4 class="mb-3">Settings</h4>

      <?php if ($message): ?>
        <div class="alert alert-<?= $messageType === 'success' ? 'success' : 'danger' ?>" role="alert">
          <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>

      <!-- Change Password Section -->
      <div class="mb-4">
        <h5 class="mb-3">Change Password</h5>
        <form id="passwordForm" method="POST" action="update_password.php">
          <div class="form-group mb-3">
            <label for="current_password">Current Password</label>
            <input 
              type="password" 
              id="current_password" 
              name="current_password" 
              class="form-control" 
              required
            >
          </div>

          <div class="form-group mb-3">
            <label for="new_password">New Password</label>
            <input 
              type="password" 
              id="new_password" 
              name="new_password" 
              class="form-control" 
              required
              minlength="6"
            >
          </div>

          <div class="form-group mb-3">
            <label for="confirm_password">Confirm New Password</label>
            <input 
              type="password" 
              id="confirm_password" 
              name="confirm_password" 
              class="form-control" 
              required
              minlength="6"
            >
          </div>

          <button type="submit" class="btn btn-primary btn-custom">Update Password</button>
        </form>
      </div>

      <hr class="my-4">

      <!-- Account Actions Section -->
      <div class="mb-4">
        <h5 class="mb-3">Account Actions</h5>
        <p class="text-muted">Manage your account settings and preferences.</p>
      </div>
    </div>
  </div>

  <script src="assets/js/dashboard.js"></script>
  <script>
    // Validate password match on form submission
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
      const newPassword = document.getElementById('new_password').value;
      const confirmPassword = document.getElementById('confirm_password').value;

      if (newPassword !== confirmPassword) {
        e.preventDefault();
        alert('New passwords do not match!');
        return false;
      }

      if (newPassword.length < 6) {
        e.preventDefault();
        alert('Password must be at least 6 characters long!');
        return false;
      }
    });
  </script>

</body>
</html>

