/**
 * Theme Toggle Functionality
 * Handles switching between dark and light themes
 */

(function() {
    'use strict';

    const THEME_KEY = 'attendance_system_theme';
    const DEFAULT_THEME = 'dark';

    /**
     * Initialize theme on page load
     */
    function initTheme() {
        const savedTheme = localStorage.getItem(THEME_KEY) || DEFAULT_THEME;
        setTheme(savedTheme);
        updateThemeToggleButton(savedTheme);
    }

    /**
     * Set the theme
     * @param {string} theme - 'dark' or 'light'
     */
    function setTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem(THEME_KEY, theme);
        updateThemeToggleButton(theme);
    }

    /**
     * Toggle between dark and light themes
     */
    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme') || DEFAULT_THEME;
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        setTheme(newTheme);
    }

    /**
     * Update theme toggle button icon/text
     * @param {string} theme - Current theme
     */
    function updateThemeToggleButton(theme) {
        const toggleButtons = document.querySelectorAll('.theme-toggle');
        toggleButtons.forEach(button => {
            const icon = button.querySelector('.theme-icon');
            if (icon) {
                if (theme === 'dark') {
                    icon.textContent = '☀️';
                    icon.setAttribute('title', 'Switch to light theme');
                } else {
                    icon.textContent = '🌙';
                    icon.setAttribute('title', 'Switch to dark theme');
                }
            } else {
                // Fallback if no icon element
                button.textContent = theme === 'dark' ? '☀️ Light' : '🌙 Dark';
            }
        });
    }

    /**
     * Create theme toggle button HTML
     */
    function createThemeToggleButton() {
        const button = document.createElement('button');
        button.className = 'theme-toggle';
        button.setAttribute('aria-label', 'Toggle theme');
        button.innerHTML = '<span class="theme-icon">☀️</span>';
        button.addEventListener('click', toggleTheme);
        
        return button;
    }

    /**
     * Add theme toggle button to navbar if it doesn't exist
     */
    function addThemeToggleToNavbar() {
        // Check if toggle button already exists
        if (document.querySelector('.theme-toggle')) return;

        const navMenu = document.querySelector('.nav-menu');
        if (navMenu) {
            // Create toggle button
            const toggleButton = createThemeToggleButton();
            
            // Create list item wrapper
            const listItem = document.createElement('li');
            listItem.style.display = 'flex';
            listItem.style.alignItems = 'center';
            listItem.appendChild(toggleButton);
            
            // Insert before the last item (usually Logout)
            if (navMenu.children.length > 0) {
                navMenu.insertBefore(listItem, navMenu.lastElementChild);
            } else {
                navMenu.appendChild(listItem);
            }
        } else {
            // No navbar found, add to login card or body
            const loginCard = document.querySelector('.login-card');
            if (loginCard) {
                const toggleButton = createThemeToggleButton();
                toggleButton.style.position = 'absolute';
                toggleButton.style.top = '1rem';
                toggleButton.style.right = '1rem';
                loginCard.style.position = 'relative';
                loginCard.appendChild(toggleButton);
            } else {
                // Add to body as floating button
                const toggleButton = createThemeToggleButton();
                toggleButton.classList.add('theme-toggle-fixed');
                document.body.appendChild(toggleButton);
            }
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initTheme();
            addThemeToggleToNavbar();
        });
    } else {
        initTheme();
        addThemeToggleToNavbar();
    }

    // Export for manual use if needed
    window.ThemeToggle = {
        setTheme: setTheme,
        toggleTheme: toggleTheme,
        getCurrentTheme: function() {
            return document.documentElement.getAttribute('data-theme') || DEFAULT_THEME;
        }
    };
})();

