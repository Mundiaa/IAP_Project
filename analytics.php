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
    $stmt = $conn->prepare("SELECT fullname, email FROM users WHERE id=?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) $user = $res->fetch_assoc();
    $stmt->close();
}
?>
<!Doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Analytics - Notez Wiz</title>
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
    <a href="analytics.php" class="active">
      <i class="fas fa-chart-bar"></i>
      <span>Analytics</span>
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
      <h2><i class="fas fa-chart-line"></i> Analytics & Reports</h2>
      <p>View detailed insights about your notes and activity.</p>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="stats-card">
          <div class="stats-icon"><i class="fas fa-sticky-note"></i></div>
          <div class="stats-number" id="totalNotes">0</div>
          <div class="stats-label">Total Notes</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
          <div class="stats-icon"><i class="fas fa-mouse-pointer"></i></div>
          <div class="stats-number" id="totalInteractions">0</div>
          <div class="stats-label">Total Interactions</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
          <div class="stats-icon"><i class="fas fa-text-height"></i></div>
          <div class="stats-number" id="avgNoteLength">0</div>
          <div class="stats-label">Avg Note Length</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
          <div class="stats-icon"><i class="fas fa-calendar-week"></i></div>
          <div class="stats-number" id="recentNotesCount">0</div>
          <div class="stats-label">Notes (Last 7 Days)</div>
        </div>
      </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row mb-4">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <i class="fas fa-chart-line"></i> Notes Created Over Time (Last 30 Days)
          </div>
          <div class="card-body">
            <canvas id="notesOverTimeChart" height="100"></canvas>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <i class="fas fa-chart-pie"></i> Notes by Day of Week
          </div>
          <div class="card-body">
            <canvas id="notesByDayChart" height="100"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row mb-4">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <i class="fas fa-clock"></i> Notes by Hour of Day
          </div>
          <div class="card-body">
            <canvas id="notesByHourChart" height="100"></canvas>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <i class="fas fa-chart-bar"></i> User Interactions Distribution
          </div>
          <div class="card-body">
            <canvas id="interactionsChart" height="100"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row 3 -->
    <div class="row mb-4">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <i class="fas fa-chart-area"></i> Activity Timeline (Last 7 Days)
          </div>
          <div class="card-body">
            <canvas id="activityTimelineChart" height="60"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading Indicator -->
    <div id="loadingIndicator" class="text-center" style="display: none;">
      <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
      <p class="mt-3">Loading analytics data...</p>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
  <script src="assets/js/dashboard.js"></script>
  <script src="assets/js/analytics.js"></script>

</body>
</html>

