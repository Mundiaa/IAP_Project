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
    <a href="profile.php" class="active">
      <i class="fas fa-user"></i>
      <span>Profile</span>
    </a>
    <a href="dashboard.php">
      <i class="fas fa-sticky-note"></i>
      <span>Notes</span>
    </a>
    <a href="settings.php">
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
      <h2><i class="fas fa-user-circle"></i> My Profile</h2>
      <p>Manage your profile information and account details.</p>
    </div>

    <?php if ($message): ?>
      <div class="alert alert-<?= $messageType === 'success' ? 'success' : 'danger' ?> fade-in" role="alert">
        <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <?php if ($user): ?>
      <!-- Profile Card -->
      <div class="card mb-4">
        <div class="card-header">
          <i class="fas fa-user-edit"></i> Profile Information
        </div>
        <div class="card-body">
          <!-- Profile Avatar -->
          <div class="text-center mb-4">
            <div class="profile-avatar">
              <i class="fas fa-user"></i>
            </div>
            <h4 class="mt-3"><?= htmlspecialchars($user['fullname']) ?></h4>
            <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>
          </div>

          <form id="profileForm" method="POST" action="update_profile.php">
            <div class="form-group">
              <label for="fullname">
                <i class="fas fa-signature"></i> Full Name
              </label>
              <div class="input-group">
                <div class="input-group-icon">
                  <i class="fas fa-user"></i>
                </div>
                <input 
                  type="text" 
                  id="fullname" 
                  name="fullname" 
                  class="form-control" 
                  value="<?= htmlspecialchars($user['fullname']) ?>" 
                  required
                  placeholder="Enter your full name"
                >
              </div>
            </div>

            <div class="form-group">
              <label for="email">
                <i class="fas fa-envelope"></i> Email Address
              </label>
              <div class="input-group">
                <div class="input-group-icon">
                  <i class="fas fa-at"></i>
                </div>
                <input 
                  type="email" 
                  id="email" 
                  name="email" 
                  class="form-control" 
                  value="<?= htmlspecialchars($user['email']) ?>" 
                  required
                  placeholder="Enter your email address"
                >
              </div>
            </div>

            <div class="text-center mt-4">
              <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save"></i> Update Profile
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Account Info Card -->
      <div class="card">
        <div class="card-header">
          <i class="fas fa-info-circle"></i> Account Information
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <strong><i class="fas fa-envelope text-primary"></i> Email:</strong>
                <p class="mt-1"><?= htmlspecialchars($user['email']) ?></p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <strong><i class="fas fa-user text-primary"></i> Full Name:</strong>
                <p class="mt-1"><?= htmlspecialchars($user['fullname']) ?></p>
              </div>
            </div>
          </div>
          <?php if (isset($user['created_at'])): ?>
          <div class="mt-3 pt-3 border-top">
            <strong><i class="fas fa-calendar-alt text-primary"></i> Account Created:</strong>
            <p class="mt-1"><?= date('F j, Y', strtotime($user['created_at'])) ?></p>
          </div>
          <?php endif; ?>
        </div>
      </div>
    <?php else: ?>
      <div class="card">
        <div class="card-body">
          <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> Error loading profile information.
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="assets/js/dashboard.js"></script>

</body>
</html>

