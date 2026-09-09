/**
 * SMARTRESTA Core Application Controller
 * Handles application initialization, theme switching, global events, and state.
 */

document.addEventListener('DOMContentLoaded', () => {
  SMARTRESTA.init();
});

const SMARTRESTA = {
  init() {
    this.initTheme();
    this.bindEvents();
    console.log('SMARTRESTA Platform Initialized (Traditional Web App Mode - Vanilla JS / PHP / MySQL)');
  },

  updateThemeButton(theme) {
    const btn = document.getElementById('theme-toggle-btn');
    if (btn) {
      if (theme === 'dark') {
        btn.innerHTML = `<span>☀️</span><span>Toggle Light Mode</span>`;
      } else {
        btn.innerHTML = `<span>🌙</span><span>Toggle Dark Mode</span>`;
      }
    }
  },

  initTheme() {
    const savedTheme = localStorage.getItem('smartresta_theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    this.updateThemeButton(savedTheme);
  },

  toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('smartresta_theme', newTheme);
    this.updateThemeButton(newTheme);
    if (window.SmartNotifications) {
      SmartNotifications.show(`Switched to ${newTheme.toUpperCase()} mode`, 'info');
    }
  },

  bindEvents() {
    const themeToggleBtn = document.getElementById('theme-toggle-btn');
    if (themeToggleBtn) {
      themeToggleBtn.addEventListener('click', () => this.toggleTheme());
    }

    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const sidebar = document.querySelector('.sidebar');
    if (mobileMenuBtn && sidebar) {
      mobileMenuBtn.addEventListener('click', () => {
        sidebar.classList.toggle('mobile-active');
      });
    }
  }
};

window.SMARTRESTA = SMARTRESTA;
