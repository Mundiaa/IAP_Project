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

// Search Notes - works with filter
const searchInput = document.getElementById("searchInput");
const notesContainer = document.getElementById("notesContainer");

function applyFilters() {
  const filterSelect = document.getElementById("filterSelect");
  const searchQuery = searchInput ? searchInput.value.toLowerCase() : '';
  const filter = filterSelect ? filterSelect.value : 'all';
  const notes = notesContainer.getElementsByClassName("note-card");
  let visibleCount = 0;

  Array.from(notes).forEach(note => {
    let shouldShow = true;

    // Apply filter first
    if (filter === "recent") {
      const createdDate = new Date(note.dataset.createdDate);
      const now = new Date();
      const daysDiff = (now - createdDate) / (1000 * 60 * 60 * 24);
      shouldShow = daysDiff <= 7;
    } else if (filter === "favorites") {
      const noteId = note.dataset.noteId;
      const favorites = getFavorites();
      shouldShow = favorites.includes(noteId.toString());
    }

    // Then apply search if filter passed
    if (shouldShow && searchQuery) {
      const text = note.innerText.toLowerCase();
      shouldShow = text.includes(searchQuery);
    }

    if (shouldShow) {
      note.style.display = "block";
      visibleCount++;
    } else {
      note.style.display = "none";
    }
  });

  // Show message if no results found
  let noResultsMsg = document.getElementById('noResultsMessage');
  if (visibleCount === 0) {
    if (!noResultsMsg) {
      noResultsMsg = document.createElement('div');
      noResultsMsg.id = 'noResultsMessage';
      noResultsMsg.className = 'alert alert-info text-center mt-3';
      let message = '';
      if (searchQuery && filter !== 'all') {
        const filterText = filter === "recent" ? "recent notes" : "favorite notes";
        message = `<i class="fas fa-search"></i> No ${filterText} found matching "${searchQuery}".`;
      } else if (searchQuery) {
        message = '<i class="fas fa-search"></i> No notes found matching your search.';
      } else if (filter !== 'all') {
        const filterText = filter === "recent" ? "recent notes" : "favorite notes";
        message = `<i class="fas fa-info-circle"></i> No ${filterText} found.`;
      }
      if (message) {
        noResultsMsg.innerHTML = message;
        notesContainer.appendChild(noResultsMsg);
      }
    }
  } else if (noResultsMsg) {
    noResultsMsg.remove();
  }
}

if (searchInput && notesContainer) {
  searchInput.addEventListener("keyup", applyFilters);
}

// Filter Notes - works with search
const filterSelect = document.getElementById("filterSelect");
if (filterSelect && notesContainer) {
  filterSelect.addEventListener("change", applyFilters);
}

// Favorites Management
function getFavorites() {
  const favorites = localStorage.getItem('noteFavorites');
  return favorites ? JSON.parse(favorites) : [];
}

function saveFavorites(favorites) {
  localStorage.setItem('noteFavorites', JSON.stringify(favorites));
}

function toggleFavorite(noteId) {
  const favorites = getFavorites();
  const noteIdStr = noteId.toString();
  const noteCard = document.querySelector(`[data-note-id="${noteId}"]`);
  const favoriteBtn = noteCard ? noteCard.querySelector('.favorite-btn') : null;

  if (favorites.includes(noteIdStr)) {
    // Remove from favorites
    const index = favorites.indexOf(noteIdStr);
    favorites.splice(index, 1);
    if (noteCard) noteCard.dataset.isFavorite = "false";
    if (favoriteBtn) {
      favoriteBtn.innerHTML = '<i class="far fa-star"></i> Favorite';
      favoriteBtn.classList.remove('btn-warning');
      favoriteBtn.classList.add('btn-outline-warning');
      favoriteBtn.title = "Add to Favorites";
    }
    showAlert('Note removed from favorites', 'info');
  } else {
    // Add to favorites
    favorites.push(noteIdStr);
    if (noteCard) noteCard.dataset.isFavorite = "true";
    if (favoriteBtn) {
      favoriteBtn.innerHTML = '<i class="fas fa-star"></i> Favorited';
      favoriteBtn.classList.remove('btn-outline-warning');
      favoriteBtn.classList.add('btn-warning');
      favoriteBtn.title = "Remove from Favorites";
    }
    showAlert('Note added to favorites', 'success');
  }

  saveFavorites(favorites);

  // If favorites filter is active, refresh the filter
  if (filterSelect && filterSelect.value === "favorites") {
    applyFilters();
  }
}

// Initialize favorites on page load
document.addEventListener('DOMContentLoaded', function() {
  const favorites = getFavorites();
  const notes = document.querySelectorAll('.note-card');

  notes.forEach(note => {
    const noteId = note.dataset.noteId;
    const favoriteBtn = note.querySelector('.favorite-btn');

    if (favorites.includes(noteId)) {
      note.dataset.isFavorite = "true";
      if (favoriteBtn) {
        favoriteBtn.innerHTML = '<i class="fas fa-star"></i> Favorited';
        favoriteBtn.classList.remove('btn-outline-warning');
        favoriteBtn.classList.add('btn-warning');
        favoriteBtn.title = "Remove from Favorites";
      }
    }
  });
});

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
      // Remove from favorites if it was favorited
      const favorites = getFavorites();
      const noteIdStr = noteId.toString();
      if (favorites.includes(noteIdStr)) {
        const index = favorites.indexOf(noteIdStr);
        favorites.splice(index, 1);
        saveFavorites(favorites);
      }
      
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
      showAlert('Note updated successfully!', 'success');
      setTimeout(() => location.reload(), 1500);
    } else {
      showAlert('Error updating note: ' + res, 'danger');
    }
  })
  .catch(error => {
    showAlert('Error: ' + error.message, 'danger');
  });
}


