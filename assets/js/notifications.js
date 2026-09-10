/**
 * SMARTRESTA Toast Notification Engine
 */

var SmartNotifications = window.SmartNotifications || {
  container: null,

  init() {
    if (!this.container) {
      this.container = document.createElement('div');
      this.container.className = 'toast-container';
      document.body.appendChild(this.container);
    }
  },

  show(message, type = 'info', duration = 4000) {
    this.init();
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    let icon = 'ℹ️';
    if (type === 'success') icon = '✓';
    if (type === 'danger') icon = '⚠️';
    if (type === 'warning') icon = '⚡';

    toast.innerHTML = `
      <span style="font-weight:700;">${icon}</span>
      <span style="font-size:0.875rem;">${message}</span>
    `;

    this.container.appendChild(toast);

    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(100%)';
      toast.style.transition = 'all 0.3s ease';
      setTimeout(() => toast.remove(), 300);
    }, duration);
  }
};

window.SmartNotifications = SmartNotifications;
