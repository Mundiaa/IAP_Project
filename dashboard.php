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
    <a href="logout.php">🚪 Logout</a>

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
        <input 
          type="text" 
          id="noteTitle" 
          class="form-control mb-2" 
          placeholder="Enter note title..."
        >
        
        <textarea 
          id="noteContent" 
          class="form-control note-textarea mb-3" 
          rows="4" 
          placeholder="Write your note content here..."
        ></textarea>
        
        <button class="btn btn-primary btn-custom" id="addNoteBtn">Add Note</button>
      </div>


      <!-- Notes List -->
      <div id="notesContainer">
        <?php
          $stmt = $conn->prepare("SELECT id, title, content, created_at FROM notes WHERE user_id=? ORDER BY created_at DESC");
          $stmt->bind_param("i", $_SESSION['user_id']);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($result->num_rows > 0):
            while ($note = $result->fetch_assoc()):
              ?>
                <div class="note-card card p-3 mb-3">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <h5><?= htmlspecialchars($note['title']) ?></h5>
                      <p><?= nl2br(htmlspecialchars($note['content'])) ?></p>
                      <small>Created on <?= htmlspecialchars($note['created_at']) ?></small>
                    </div>

                    <!-- Action buttons (Edit & Delete) -->
                    <div>
                      <button class="btn btn-sm btn-outline-secondary btn-custom"
                              onclick="editNote(<?= $note['id'] ?>, '<?= htmlspecialchars($note['title'], ENT_QUOTES) ?>', '<?= htmlspecialchars($note['content'], ENT_QUOTES) ?>')">
                        Edit
                      </button>

                      <button class="btn btn-sm btn-outline-danger btn-custom"
                              onclick="deleteNote(<?= $note['id'] ?>)">
                        Delete
                      </button>
                    </div>
                  </div>
                </div>
              <?php
              endwhile;

        else:
          echo "<p>No notes yet. Start by adding one above!</p>";
        endif;
        $stmt->close();
        ?>
      </div>

    </div>
  </div>
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
          alert('Note updated successfully!');
          location.reload();
        } else {
          alert('Error updating note: ' + res);
        }
      });
    }
  </script>

 </body>
</html>