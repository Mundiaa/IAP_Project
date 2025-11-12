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

// Get user info for sidebar
$user = null;
if (isset($conn) && isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT id, fullname, email FROM users WHERE id=?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $user = $res->fetch_assoc();
    }
    $stmt->close();
}
?>
<!Doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Settings - Notez Wiz</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="dashboard-body">

   <!-- Sidebar -->
  <div class="sidebar">
    <h3><i class="fas fa-sticky-note"></i> Notez Wiz</h3>
    
    <?php if (isset($user)): ?>
    <div class="user-info">
      <div class="user-avatar">
        <i class="fas fa-user"></i>
      </div>
      <div class="user-name"><?= htmlspecialchars($user['fullname'] ?? 'User') ?></div>
      <div class="user-email"><?= htmlspecialchars($user['email']) ?></div>
    </div>
    <?php endif; ?>

    <a href="dashboard.php">
      <i class="fas fa-home"></i>
      <span>Home</span>
    </a>
    <a href="profile.php">
      <i class="fas fa-user"></i>
      <span>Profile</span>
    </a>
    <a href="dashboard.php">
      <i class="fas fa-sticky-note"></i>
      <span>Notes</span>
    </a>
    <a href="analytics.php">
      <i class="fas fa-chart-bar"></i>
      <span>Analytics</span>
    </a>
    <a href="settings.php" class="active">
      <i class="fas fa-cog"></i>
      <span>Settings</span>
    </a>
    <a href="logout.php">
      <i class="fas fa-sign-out-alt"></i>
      <span>Logout</span>
    </a>

    <div class="toggle-btn">
      <button id="darkModeToggle" class="btn btn-light btn-sm">
        <i class="fas fa-moon"></i> Dark Mode
      </button>
    </div>
  </div>

  <!-- Main Content -->
  <div class="content fade-in">
    <!-- Content Header -->
    <div class="content-header">
      <h2><i class="fas fa-cog"></i> Settings</h2>
      <p>Manage your account settings and preferences.</p>
    </div>

    <?php if ($message): ?>
      <div class="alert alert-<?= $messageType === 'success' ? 'success' : 'danger' ?> fade-in" role="alert">
        <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <!-- Change Password Card -->
    <div class="card mb-4">
      <div class="card-header">
        <i class="fas fa-lock"></i> Change Password
      </div>
      <div class="card-body">
        <form id="passwordForm" method="POST" action="update_password.php">
          <div class="form-group">
            <label for="current_password">
              <i class="fas fa-key"></i> Current Password
            </label>
            <div class="input-group">
              <div class="input-group-icon">
                <i class="fas fa-lock"></i>
              </div>
              <input 
                type="password" 
                id="current_password" 
                name="current_password" 
                class="form-control" 
                required
                placeholder="Enter your current password"
              >
            </div>
          </div>

          <div class="form-group">
            <label for="new_password">
              <i class="fas fa-key"></i> New Password
            </label>
            <div class="input-group">
              <div class="input-group-icon">
                <i class="fas fa-lock"></i>
              </div>
              <input 
                type="password" 
                id="new_password" 
                name="new_password" 
                class="form-control" 
                required
                minlength="6"
                placeholder="Enter your new password (min. 6 characters)"
              >
            </div>
            <small class="form-text text-muted">
              <i class="fas fa-info-circle"></i> Password must be at least 6 characters long.
            </small>
          </div>

          <div class="form-group">
            <label for="confirm_password">
              <i class="fas fa-key"></i> Confirm New Password
            </label>
            <div class="input-group">
              <div class="input-group-icon">
                <i class="fas fa-lock"></i>
              </div>
              <input 
                type="password" 
                id="confirm_password" 
                name="confirm_password" 
                class="form-control" 
                required
                minlength="6"
                placeholder="Confirm your new password"
              >
            </div>
          </div>

          <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary btn-lg">
              <i class="fas fa-save"></i> Update Password
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Account Preferences Card -->
    <div class="card">
      <div class="card-header">
        <i class="fas fa-user-cog"></i> Account Preferences
      </div>
      <div class="card-body">
        <div class="section-header">
          <i class="fas fa-palette"></i>
          <h5>Appearance</h5>
        </div>
        <p class="text-muted">Customize the appearance of your application.</p>
        <div class="mb-3">
          <label class="form-check-label">
            <input type="checkbox" class="form-check-input" id="darkModeCheck">
            Enable dark mode
          </label>
        </div>

        <hr class="my-4">

        <div class="section-header">
          <i class="fas fa-info-circle"></i>
          <h5>Account Information</h5>
        </div>
        <p class="text-muted">View and manage your account details.</p>
        <div class="mt-3">
          <a href="profile.php" class="btn btn-outline-primary">
            <i class="fas fa-user-edit"></i> Edit Profile
          </a>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="assets/js/dashboard.js"></script>
  <script>
    // Validate password match on form submission
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
      const newPassword = document.getElementById('new_password').value;
      const confirmPassword = document.getElementById('confirm_password').value;

      if (newPassword !== confirmPassword) {
        e.preventDefault();
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger fade-in';
        alertDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> New passwords do not match!';
        document.querySelector('.content-header').after(alertDiv);
        setTimeout(() => alertDiv.remove(), 5000);
        return false;
      }

      if (newPassword.length < 6) {
        e.preventDefault();
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger fade-in';
        alertDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> Password must be at least 6 characters long!';
        document.querySelector('.content-header').after(alertDiv);
        setTimeout(() => alertDiv.remove(), 5000);
        return false;
      }
    });

    // Sync dark mode checkbox with toggle button
    const darkModeToggle = document.getElementById('darkModeToggle');
    const darkModeCheck = document.getElementById('darkModeCheck');
    if (darkModeToggle && darkModeCheck) {
      darkModeCheck.addEventListener('change', function() {
        if (this.checked) {
          document.body.classList.add('dark-mode');
          darkModeToggle.innerHTML = '<i class="fas fa-sun"></i> Light Mode';
          darkModeToggle.classList.remove('btn-light');
          darkModeToggle.classList.add('btn-dark');
        } else {
          document.body.classList.remove('dark-mode');
          darkModeToggle.innerHTML = '<i class="fas fa-moon"></i> Dark Mode';
          darkModeToggle.classList.remove('btn-dark');
          darkModeToggle.classList.add('btn-light');
        }
      });
    }
  </script>

</body>
</html>

