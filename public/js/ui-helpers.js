(function () {
  'use strict';

  // Confirmation dialog helper
  window.confirmAction = function (message) {
    return window.confirm(message || 'Are you sure you want to continue? This action cannot be undone.');
  };

  // Lightweight confirmation modal fallback (keeps UX modern on pages without Bootstrap modals)
  window.showConfirmModal = function (options) {
    options = options || {};
    var title = options.title || 'Confirm Action';
    var message = options.message || 'Are you sure you want to continue?';
    var confirmText = options.confirmText || 'Yes, proceed';
    var cancelText = options.cancelText || 'Cancel';
    var onConfirm = options.onConfirm || function () {};
    var onCancel = options.onCancel || function () {};

    var overlay = document.createElement('div');
    overlay.className = 'cscs-confirm-overlay';
    overlay.innerHTML =
      '<div class="cscs-confirm-modal" role="dialog" aria-modal="true" aria-labelledby="cscsConfirmTitle">' +
        '<div class="cscs-confirm-header"><div id="cscsConfirmTitle">' + title + '</div></div>' +
        '<div class="cscs-confirm-body"><p>' + message + '</p></div>' +
        '<div class="cscs-confirm-actions">' +
          '<button type="button" class="cscs-confirm-btn cscs-confirm-btn-cancel">' + cancelText + '</button>' +
          '<button type="button" class="cscs-confirm-btn cscs-confirm-btn-primary">' + confirmText + '</button>' +
        '</div>' +
      '</div>';

    document.body.appendChild(overlay);

    var close = function () {
      overlay.classList.remove('open');
      setTimeout(function () {
        if (overlay.parentNode) overlay.parentNode.removeChild(overlay);
      }, 200);
    };

    overlay.querySelector('.cscs-confirm-btn-cancel').addEventListener('click', function () {
      onCancel();
      close();
    });
    overlay.querySelector('.cscs-confirm-btn-primary').addEventListener('click', function () {
      onConfirm();
      close();
    });
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) {
        onCancel();
        close();
      }
    });

    requestAnimationFrame(function () {
      overlay.classList.add('open');
    });

    return {
      close: close,
      element: overlay
    };
  };

  // Toast notification helper (invisible until used)
  window.confirmFormAction = function (formOrEvent, message) {
    message = message || 'This action cannot be undone. Do you want to continue?';
    var event = typeof formOrEvent === 'object' ? formOrEvent : null;
    var form = event ? event.target : formOrEvent;

    if (event) {
      event.preventDefault();
      event.stopPropagation();
    }

    window.showConfirmModal({
      title: 'Confirm Action',
      message: message,
      confirmText: 'Yes, proceed',
      cancelText: 'Cancel',
      onConfirm: function () {
        if (form && typeof form.submit === 'function') {
          form.submit();
        }
      }
    });

    return false;
  };

  window.showToast = function (options) {
    options = options || {};
    var type = options.type || 'info'; // success, danger, info
    var title = options.title || '';
    var message = options.message || '';
    var duration = typeof options.duration === 'number' ? options.duration : 3500;

    var containerId = 'cscs-toast-container';
    var container = document.getElementById(containerId);
    if (!container) {
      container = document.createElement('div');
      container.id = containerId;
      container.className = 'cscs-toast-container';
      document.body.appendChild(container);
    }

    var toast = document.createElement('div');
    toast.className = 'cscs-toast cscs-toast-' + type;
    toast.setAttribute('role', 'status');
    toast.setAttribute('aria-live', 'polite');

    var icon = type === 'success' ? 'bi-check-circle-fill' : (type === 'danger' ? 'bi-exclamation-triangle-fill' : 'bi-info-circle-fill');

    toast.innerHTML =
      '<div class="cscs-toast-icon"><i class="bi ' + icon + '"></i></div>' +
      '<div class="cscs-toast-content">' +
        (title ? '<div class="cscs-toast-title">' + title + '</div>' : '') +
        (message ? '<div class="cscs-toast-message">' + message + '</div>' : '') +
      '</div>' +
      '<button type="button" class="cscs-toast-close" aria-label="Close notification"><i class="bi bi-x"></i></button>';

    container.appendChild(toast);

    var remove = function () {
      toast.classList.remove('show');
      setTimeout(function () {
        if (toast.parentNode) toast.parentNode.removeChild(toast);
      }, 250);
    };

    toast.querySelector('.cscs-toast-close').addEventListener('click', remove);
    if (duration > 0) {
      setTimeout(remove, duration);
    }

    requestAnimationFrame(function () {
      toast.classList.add('show');
    });

    return {
      close: remove,
      element: toast
    };
  };
})();