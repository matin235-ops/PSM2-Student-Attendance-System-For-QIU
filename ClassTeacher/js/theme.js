document.addEventListener('DOMContentLoaded', function() {
    // Check for saved theme preference
    const currentTheme = localStorage.getItem('theme') || 'light';
    if (currentTheme === 'dark') {
        document.body.classList.add('dark-mode');
    }
    updateThemeButton(currentTheme === 'dark');

    // Add click handler to theme toggle button
    const themeToggle = document.querySelector('.theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
    }
});

function toggleTheme() {
    const body = document.body;
    const isDarkMode = body.classList.contains('dark-mode');

    if (isDarkMode) {
        body.classList.remove('dark-mode');
        localStorage.setItem('theme', 'light');
    } else {
        body.classList.add('dark-mode');
        localStorage.setItem('theme', 'dark');
    }

    updateThemeButton(!isDarkMode);

    // Show save message
    const msg = document.getElementById('theme-save-msg');
    if (msg) {
        msg.style.display = 'block';
        setTimeout(() => {
            msg.style.display = 'none';
        }, 2000);
    }
}

function updateThemeButton(isDarkMode) {
    const icon = document.getElementById('theme-icon');
    const text = document.getElementById('theme-text');
    const button = document.querySelector('.theme-toggle');

    if (!icon || !text || !button) return;

    if (isDarkMode) {
        icon.className = 'fas fa-moon';
        text.textContent = 'Dark Mode';
        button.style.backgroundColor = '#ffffff';
        button.style.color = '#1a1a1a';
    } else {
        icon.className = 'fas fa-sun';
        text.textContent = 'Light Mode';
        button.style.backgroundColor = '#1a1a1a';
        button.style.color = '#ffffff';
    }
}