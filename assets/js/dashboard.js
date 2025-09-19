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