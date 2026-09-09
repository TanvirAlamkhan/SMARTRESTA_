/**
 * SMARTRESTA Reusable Modal & Drawer Controller
 */

const SmartModal = {
  open(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.add('active');
      document.body.style.overflow = 'hidden';
    }
  },

  close(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.remove('active');
      document.body.style.overflow = '';
    }
  },

  bindGlobalEvents() {
    document.addEventListener('click', (e) => {
      if (e.target.classList.contains('modal-backdrop')) {
        e.target.classList.remove('active');
        document.body.style.overflow = '';
      }
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        const activeModal = document.querySelector('.modal-backdrop.active');
        if (activeModal) {
          activeModal.classList.remove('active');
          document.body.style.overflow = '';
        }
      }
    });
  }
};

document.addEventListener('DOMContentLoaded', () => SmartModal.bindGlobalEvents());
window.SmartModal = SmartModal;
