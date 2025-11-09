// Dark Mode Toggle
const toggleBtn = document.getElementById("darkModeToggle");
if (toggleBtn) {
  toggleBtn.addEventListener("click", () => {
    document.body.classList.toggle("dark-mode");

    if (document.body.classList.contains("dark-mode")) {
      toggleBtn.innerHTML = '<i class="fas fa-sun"></i> Light Mode';
      toggleBtn.classList.remove("btn-light");
      toggleBtn.classList.add("btn-dark");
      // Save preference to localStorage
      localStorage.setItem('darkMode', 'enabled');
    } else {
      toggleBtn.innerHTML = '<i class="fas fa-moon"></i> Dark Mode';
      toggleBtn.classList.remove("btn-dark");
      toggleBtn.classList.add("btn-light");
      // Save preference to localStorage
      localStorage.setItem('darkMode', 'disabled');
    }
  });

  // Load dark mode preference from localStorage
  if (localStorage.getItem('darkMode') === 'enabled') {
    document.body.classList.add("dark-mode");
    toggleBtn.innerHTML = '<i class="fas fa-sun"></i> Light Mode';
    toggleBtn.classList.remove("btn-light");
    toggleBtn.classList.add("btn-dark");
  }
}

// Search Notes
const searchInput = document.getElementById("searchInput");
const notesContainer = document.getElementById("notesContainer");

if (searchInput && notesContainer) {
  searchInput.addEventListener("keyup", () => {
    const query = searchInput.value.toLowerCase();
    const notes = notesContainer.getElementsByClassName("note-card");
    let visibleCount = 0;

    Array.from(notes).forEach(note => {
      const text = note.innerText.toLowerCase();
      if (text.includes(query)) {
        note.style.display = "block";
        visibleCount++;
      } else {
        note.style.display = "none";
      }
    });

    // Show message if no results found
    let noResultsMsg = document.getElementById('noResultsMessage');
    if (query && visibleCount === 0) {
      if (!noResultsMsg) {
        noResultsMsg = document.createElement('div');
        noResultsMsg.id = 'noResultsMessage';
        noResultsMsg.className = 'alert alert-info text-center';
        noResultsMsg.innerHTML = '<i class="fas fa-search"></i> No notes found matching your search.';
        notesContainer.appendChild(noResultsMsg);
      }
    } else if (noResultsMsg) {
      noResultsMsg.remove();
    }
  });
}

// Filter Notes
const filterSelect = document.getElementById("filterSelect");
if (filterSelect && notesContainer) {
  filterSelect.addEventListener("change", () => {
    const filter = filterSelect.value;
    const notes = notesContainer.getElementsByClassName("note-card");

    Array.from(notes).forEach(note => {
      if (filter === "all" || note.dataset.type === filter) {
        note.style.display = "block";
      } else {
        note.style.display = "none";
      }
    });
  });
}

// Handle Note Submission
document.addEventListener('DOMContentLoaded', function() {
  const addButton = document.getElementById('addNoteBtn');
  const titleInput = document.getElementById('noteTitle');
  const contentInput = document.getElementById('noteContent');

  if (addButton && titleInput && contentInput) {
    addButton.addEventListener('click', async function() {
      const title = titleInput.value.trim();
      const content = contentInput.value.trim();

      if (!title || !content) {
        // Show styled alert instead of basic alert
        showAlert('Please fill in both title and content.', 'warning');
        return;
      }

      // Disable button during submission
      addButton.disabled = true;
      addButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

      const formData = new FormData();
      formData.append('title', title);
      formData.append('content', content);

      try {
        const response = await fetch('add_note.php', { method: 'POST', body: formData });
        const result = await response.text();

        if (result === "success") {
          showAlert('Note added successfully!', 'success');
          // Clear form
          titleInput.value = '';
          contentInput.value = '';
          // Reload after a short delay
          setTimeout(() => location.reload(), 1500);
        } else {
          showAlert('Error adding note: ' + result, 'danger');
          addButton.disabled = false;
          addButton.innerHTML = '<i class="fas fa-plus"></i> Add Note';
        }
      } catch (error) {
        showAlert('Error: ' + error.message, 'danger');
        addButton.disabled = false;
        addButton.innerHTML = '<i class="fas fa-plus"></i> Add Note';
      }
    });
  }
});

// Helper function to show alerts
function showAlert(message, type = 'info') {
  const alertDiv = document.createElement('div');
  alertDiv.className = `alert alert-${type} fade-in`;
  alertDiv.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'}"></i> ${message}`;
  
  const content = document.querySelector('.content');
  if (content) {
    const header = content.querySelector('.content-header');
    if (header && header.nextSibling) {
      content.insertBefore(alertDiv, header.nextSibling);
    } else {
      content.insertBefore(alertDiv, content.firstChild);
    }
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
      alertDiv.style.transition = 'opacity 0.5s';
      alertDiv.style.opacity = '0';
      setTimeout(() => alertDiv.remove(), 500);
    }, 5000);
  }
}

// Delete note
function deleteNote(noteId) {
  if (!confirm("Are you sure you want to delete this note? This action cannot be undone.")) return;
  
  fetch('delete_note.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'note_id=' + encodeURIComponent(noteId)
  })
  .then(res => res.text())
  .then(res => {
    if (res === 'success') {
      showAlert('Note deleted successfully!', 'success');
      setTimeout(() => location.reload(), 1500);
    } else {
      showAlert('Error deleting note: ' + res, 'danger');
    }
  })
  .catch(error => {
    showAlert('Error: ' + error.message, 'danger');
  });
}

// Edit note
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


