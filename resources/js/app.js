import.meta.glob('./components/*.js', { eager: true });

(function () {
    const htmlEl = document.documentElement;
    const storageKey = 'dashboard-theme';
    const toggleBtn = document.getElementById('themeToggle');
    const iconEl = document.getElementById('themeToggleIcon');

    function applyTheme(theme) {
        htmlEl.setAttribute('data-bs-theme', theme);

        if (!iconEl) return;

        if (theme === 'dark') {
            iconEl.classList.remove('fa-moon');
            iconEl.classList.add('fa-sun');
        } else {
            iconEl.classList.remove('fa-sun');
            iconEl.classList.add('fa-moon');
        }
    }

    function getPreferredTheme() {
        const stored = localStorage.getItem(storageKey);
        if (stored === 'light' || stored === 'dark') {
            return stored;
        }
        return 'light';
    }

    const currentTheme = getPreferredTheme();
    applyTheme(currentTheme);

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            const newTheme =
                htmlEl.getAttribute('data-bs-theme') === 'dark'
                    ? 'light'
                    : 'dark';

            localStorage.setItem(storageKey, newTheme);
            applyTheme(newTheme);
        });
    }
})();