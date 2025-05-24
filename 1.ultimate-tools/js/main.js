// Handle switching between categories
const tabButtons = document.querySelectorAll('.main-tabs button');
const categories = document.querySelectorAll('.category-content');
const toolDisplay = document.getElementById('tool-display');

tabButtons.forEach(btn => {
  btn.addEventListener('click', () => {
    // Activate the clicked tab button
    tabButtons.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // Show the relevant category content and hide others
    const targetCat = btn.getAttribute('data-cat');
    categories.forEach(cat => {
      if (cat.id === targetCat) {
        cat.classList.add('active');
      } else {
        cat.classList.remove('active');
      }
    });

    // Reset tool display area on category switch
    toolDisplay.innerHTML = `
      <h2>Select a tool to start</h2>
      <p>Tool UI will appear here.</p>
    `;
  });
});

// Handle clicking tool buttons inside categories
const categorySections = document.querySelectorAll('.category-content');

categorySections.forEach(section => {
  section.addEventListener('click', (e) => {
    if (e.target.classList.contains('tool-btn')) {
      const toolName = e.target.getAttribute('data-tool');
      // Show placeholder tool UI
      toolDisplay.innerHTML = `
        <h2>${toolName}</h2>
        <p>This is where the <strong>${toolName}</strong> tool interface will be implemented.</p>
      `;
    }
  });
});
