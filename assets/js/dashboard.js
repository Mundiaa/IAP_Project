const toggleBtn = document.getElementById("darkModeToggle");
    toggleBtn.addEventListener("click", () => {
      document.body.classList.toggle("dark-mode");

      if (document.body.classList.contains("dark-mode")) {
        toggleBtn.textContent = "☀️ Light Mode";
        toggleBtn.classList.remove("btn-light");
        toggleBtn.classList.add("btn-dark");
      } else {
        toggleBtn.textContent = "🌙 Dark Mode";
        toggleBtn.classList.remove("btn-dark");
        toggleBtn.classList.add("btn-light");
      }
    });

    // Search Notes
    const searchInput = document.getElementById("searchInput");
    const notesContainer = document.getElementById("notesContainer");

    searchInput.addEventListener("keyup", () => {
      const query = searchInput.value.toLowerCase();
      const notes = notesContainer.getElementsByClassName("note-card");

      Array.from(notes).forEach(note => {
        const text = note.innerText.toLowerCase();
        note.style.display = text.includes(query) ? "block" : "none";
      });
    });

    // Filter Notes
    const filterSelect = document.getElementById("filterSelect");
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

    //Handle Note Submission
    document.addEventListener('DOMContentLoaded', function() {
  const addButton = document.getElementById('addNoteBtn');
  const titleInput = document.getElementById('noteTitle');
  const contentInput = document.getElementById('noteContent');

  addButton.addEventListener('click', async function() {
    const title = titleInput.value.trim();
    const content = contentInput.value.trim();

    if (!title || !content) {
      alert("Please fill in both title and content.");
      return;
    }

    const formData = new FormData();
    formData.append('title', title);
    formData.append('content', content);

    const response = await fetch('add_note.php', { method: 'POST', body: formData });
    const result = await response.text();

    if (result === "success") {
      alert("Note added successfully!");
      location.reload();
    } else {
      alert("Error adding note: " + result);
    }
  });
});

// Delete note
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
