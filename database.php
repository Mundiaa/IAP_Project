<?php
session_start();


if (isset($conn) && $conn) {
    // user
    if (isset($_SESSION['user_id'])) {
        $stmt = $conn->prepare("SELECT fullname,email FROM users WHERE id=?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) $user = $res->fetch_assoc();
        $stmt->close();
    }


    $res = $conn->query("SELECT COUNT(*) AS cnt FROM notes");
    if ($res) $total_notes = (int)$res->fetch_assoc()['cnt'];

    $res = $conn->query("SELECT id,title,created_at FROM notes ORDER BY created_at DESC LIMIT 5");
    if ($res) $recent_notes = $res->fetch_all(MYSQLI_ASSOC);

    $res = $conn->query("
        SELECT DATE(created_at) AS d, COUNT(*) AS cnt
        FROM notes
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
        GROUP BY DATE(created_at)
        ORDER BY d
    ");
    if ($res) {
        $chart_labels = [];
        $chart_values = [];
        while ($row = $res->fetch_assoc()) {
            $chart_labels[] = $row['d'];
            $chart_values[] = (int)$row['cnt'];
        }
    }
}
?>
<!Doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Notez Wiz</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
     <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

   <!-- Sidebar -->
  <div class="sidebar">
    <h3>Notez Wiz</h3>
    <a href="#">🏠 Home</a>
    <a href="#">👤 Profile</a>
    <a href="#">📝 Notes</a>
    <a href="#">⚙️ Settings</a>
    <a href="#">🚪 Logout</a>

    <div class="toggle-btn">
      <button id="darkModeToggle" class="btn btn-light btn-sm">🌙 Dark Mode</button>
    </div>
  </div>

  <!-- Main Content -->
  <div class="content">
    <div class="card p-4">
      <h4 class="mb-3">Your Notes</h4>

      <!-- Search & Filter -->
      <div class="d-flex mb-3">
        <input 
          type="text" 
          id="searchInput" 
          class="form-control me-2" 
          placeholder="Search notes...">
        
        <select id="filterSelect" class="form-select" style="max-width:200px;">
          <option value="all">All Notes</option>
          <option value="recent">Recent</option>
          <option value="favorites">Favorites</option>
        </select>
      </div>

      <!-- Add Note -->
      <div class="mb-4">
        <textarea 
          class="form-control note-textarea mb-3" 
          rows="3" 
          placeholder="Write a new note..."></textarea>
        
        <button class="btn btn-primary btn-custom">Add Note</button>
      </div>

      <!-- Notes List -->
      <div id="notesContainer">
        <div class="note-card card p-3 mb-3" data-type="recent">
          <div class="d-flex justify-content-between align-items-center">
            <span>This is a sample note.</span>
            <div>
              <button class="btn btn-sm btn-outline-secondary btn-custom">Edit</button>
              <button class="btn btn-sm btn-outline-danger btn-custom">Delete</button>
            </div>
          </div>
        </div>

        <div class="note-card card p-3 mb-3" data-type="favorites">
          <div class="d-flex justify-content-between align-items-center">
            <span>Another example note here.</span>
            <div>
              <button class="btn btn-sm btn-outline-secondary btn-custom">Edit</button>
              <button class="btn btn-sm btn-outline-danger btn-custom">Delete</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="assets/js/dashboard.js"></script
 </body>
</html>