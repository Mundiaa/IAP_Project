<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
//require_once 'vendor/autoload.php';
require_once 'conf.php';
require_once 'database.php';

// Redirect to login if not logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: index.php");
    exit;
}

if (isset($conn) && $conn) {
    // user
    if (isset($_SESSION['user_id'])) {
        $stmt = $conn->prepare("SELECT fullname, email FROM users WHERE id=?");
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
  <title>Dashboard - Notez Wiz</title>
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

    <a href="dashboard.php" class="active">
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
      <h2><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
      <p>Welcome back! Manage your notes and stay organized.</p>
    </div>

    <!-- Statistics Cards -->
    <?php
    $user_notes_count = 0;
    if (isset($conn) && isset($_SESSION['user_id'])) {
      $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM notes WHERE user_id=?");
      $stmt->bind_param("i", $_SESSION['user_id']);
      $stmt->execute();
      $res = $stmt->get_result();
      if ($res) $user_notes_count = (int)$res->fetch_assoc()['cnt'];
      $stmt->close();
    }
    ?>
    <div class="row mb-4">
      <div class="col-md-4">
        <div class="stats-card">
          <div class="stats-icon"><i class="fas fa-sticky-note"></i></div>
          <div class="stats-number"><?= $user_notes_count ?></div>
          <div class="stats-label">Total Notes</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
          <div class="stats-icon"><i class="fas fa-clock"></i></div>
          <div class="stats-number"><?= isset($recent_notes) ? count($recent_notes) : 0 ?></div>
          <div class="stats-label">Recent Notes</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
          <div class="stats-icon"><i class="fas fa-check-circle"></i></div>
          <div class="stats-number">Active</div>
          <div class="stats-label">Account Status</div>
        </div>
      </div>
    </div>

    <!-- Add Note Card -->
    <div class="card mb-4">
      <div class="card-header">
        <i class="fas fa-plus-circle"></i> Create New Note
      </div>
      <div class="card-body">
        <div class="form-group">
          <label for="noteTitle"><i class="fas fa-heading"></i> Note Title</label>
          <input 
            type="text" 
            id="noteTitle" 
            class="form-control" 
            placeholder="Enter a catchy title for your note..."
          >
        </div>
        
        <div class="form-group">
          <label for="noteContent"><i class="fas fa-align-left"></i> Note Content</label>
          <textarea 
            id="noteContent" 
            class="form-control note-textarea" 
            rows="5" 
            placeholder="Write your thoughts, ideas, or important information here..."
          ></textarea>
        </div>
        
        <button class="btn btn-primary" id="addNoteBtn">
          <i class="fas fa-plus"></i> Add Note
        </button>
      </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="search-filter-container">
          <div class="row">
            <div class="col-md-8">
              <div class="input-group">
                <div class="input-group-icon">
                  <i class="fas fa-search"></i>
                </div>
                <input 
                  type="text" 
                  id="searchInput" 
                  class="form-control" 
                  placeholder="Search notes by title or content..."
                >
              </div>
            </div>
            <div class="col-md-4">
              <select id="filterSelect" class="form-control">
                <option value="all">All Notes</option>
                <option value="recent">Recent</option>
                <option value="favorites">Favorites</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Notes List Card -->
    <div class="card">
      <div class="card-header">
        <i class="fas fa-list"></i> Your Notes
      </div>
      <div class="card-body">


        <!-- Notes List -->
        <div id="notesContainer">
          <?php
            if (isset($conn) && isset($_SESSION['user_id'])) {
              $stmt = $conn->prepare("SELECT id, title, content, created_at FROM notes WHERE user_id=? ORDER BY created_at DESC");
              $stmt->bind_param("i", $_SESSION['user_id']);
              $stmt->execute();
              $result = $stmt->get_result();

              if ($result->num_rows > 0):
                while ($note = $result->fetch_assoc()):
                  ?>
                    <div class="note-card card p-4 mb-3">
                      <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                          <h5 class="note-title">
                            <i class="fas fa-sticky-note text-primary"></i> 
                            <?= htmlspecialchars($note['title']) ?>
                          </h5>
                          <p class="note-content mt-3"><?= nl2br(htmlspecialchars($note['content'])) ?></p>
                          <div class="note-date mt-3">
                            <i class="fas fa-calendar-alt"></i>
                            <span class="ml-2">Created on <?= date('F j, Y g:i A', strtotime($note['created_at'])) ?></span>
                          </div>
                        </div>

                        <!-- Action buttons (Edit & Delete) -->
                        <div class="note-actions ml-3">
                          <button class="btn btn-sm btn-outline-primary btn-custom"
                                  onclick="editNote(<?= $note['id'] ?>, '<?= htmlspecialchars($note['title'], ENT_QUOTES) ?>', '<?= htmlspecialchars($note['content'], ENT_QUOTES) ?>')"
                                  title="Edit Note">
                            <i class="fas fa-edit"></i> Edit
                          </button>

                          <button class="btn btn-sm btn-outline-danger btn-custom"
                                  onclick="deleteNote(<?= $note['id'] ?>)"
                                  title="Delete Note">
                            <i class="fas fa-trash"></i> Delete
                          </button>
                        </div>
                      </div>
                    </div>
                  <?php
                  endwhile;
              else:
                ?>
                <div class="empty-state">
                  <i class="fas fa-inbox"></i>
                  <h5>No notes yet</h5>
                  <p>Start by creating your first note above!</p>
                </div>
                <?php
              endif;
              $stmt->close();
            }
          ?>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="assets/js/dashboard.js"></script>
  <script>
    function deleteNote(noteId) {
      if (!confirm("Are you sure you want to delete this note?")) return;
      
      fetch('delete_note.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'note_id=' + encodeURIComponent(noteId)
      })
      .then(res => res.text())
      .then(res => {
        if (res === 'success') {
          alert('Note deleted successfully!');
          location.reload();
        } else {
          alert('Error deleting note: ' + res);
        }
      });
    }

    function editNote(noteId, oldTitle, oldContent) {
      // Create a modal-like prompt for better UX
      const newTitle = prompt("Edit note title:", oldTitle);
      if (newTitle === null) return;
      const newContent = prompt("Edit note content:", oldContent);
      if (newContent === null) return;

      fetch('edit_note.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `note_id=${noteId}&title=${encodeURIComponent(newTitle)}&content=${encodeURIComponent(newContent)}`
      })
      .then(res => res.text())
      .then(res => {
        if (res === 'success') {
          // Show success message with better styling
          const alertDiv = document.createElement('div');
          alertDiv.className = 'alert alert-success fade-in';
          alertDiv.innerHTML = '<i class="fas fa-check-circle"></i> Note updated successfully!';
          document.querySelector('.content').insertBefore(alertDiv, document.querySelector('.content').firstChild);
          setTimeout(() => location.reload(), 1500);
        } else {
          alert('Error updating note: ' + res);
        }
      });
    }
  </script>

 </body>
</html>